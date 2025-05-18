<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2020 Tatoeba Project
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Model\Exporter;

use App\Lib\LanguagesLib;
use App\Model\Entity\LanguageNameTrait;
use Cake\ORM\TableRegistry;
use Exception;

class PairsExporter
{
    use LanguageNameTrait;

    private $config;
    private $userId;

    public function __construct(array $config, $userId)
    {
        $this->config = $config;
        $this->userId = $userId;
    }

    private function validateFields(array $configFields)
    {
        $availableFields = [
            'id', 'text', 'trans_id', 'trans_text'
        ];
        foreach ($configFields as $field) {
            if (!in_array($field, $availableFields)) {
                return false;
            }
        }
        return true;
    }

    private function validateFormat(string $check)
    {
        $availableFormats = ['tsv', 'txt', 'shtooka'];
        return in_array($check, $availableFormats);
    }

    private function getCSVFields($fields, $entity)
    {
        return array_map(
            function ($field) use ($entity) {
                switch ($field) {
                    case 'id':         return $entity->sentence_id;
                    case 'text':       return $entity->_matchingData['Sentences']->text;
                    case 'trans_id':   return $entity->translation_id;
                    case 'trans_text': return $entity->_matchingData['Translations']->text;
                    default:           return '';
                }
            },
            $fields
        );
    }

    public function validates()
    {
        return isset($this->config['from'])
            && LanguagesLib::languageExists($this->config['from'])
            && isset($this->config['to'])
            && LanguagesLib::languageExists($this->config['to'])
            && isset($this->config['fields'])
            && is_array($this->config['fields'])
            && $this->validateFields($this->config['fields'])
            && isset($this->config['format'])
            && $this->validateFormat($this->config['format']);
    }

    public function getQuery()
    {
        $Links = TableRegistry::getTableLocator()->get('Links');
        $query = $Links->find()
            ->enableBufferedResults(false)
            ->select(['sentence_id', 'Sentences.text', 'translation_id', 'Translations.text'])
            ->where([
                'Links.sentence_lang' => $this->config['from'],
                'Links.translation_lang' => $this->config['to'],
            ])
            ->innerJoinWith('Sentences', function ($q) {
                return $q->where(['Sentences.correctness >' => '-1']);
            })
            ->innerJoinWith('Translations', function ($q) {
                return $q->where(['Translations.correctness >' => '-1']);
            });

        $query->formatResults(function($entities) {
            return $entities->map(function($entity) {
                return $this->getCSVFields($this->config['fields'], $entity);
            });
        });

        return $query;
    }

    public function getExportName()
    {
        $language1 = $this->codeToNameAlone($this->config['from']);
        $language2 = $this->codeToNameAlone($this->config['to']);
        return format(
           __('Sentence pairs in {language1}-{language2}'),
           compact('language1', 'language2')
        );
    }

    public function getExportDescription()
    {
        return __("Sentence id [tab] Sentence text [tab] Translation id [tab] Translation text");
    }
}
