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
use Cake\ORM\TableRegistry;
use Exception;

class ListExporter
{
    private $config;
    private $userId;
    private $listName;

    public function __construct(array $config, $userId)
    {
        $this->config = $config;
        $this->userId = $userId;
    }

    private function validateFields(array $configFields)
    {
        $availableFields = [
            'id', 'lang', 'text', 'trans_text',
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
                    case 'id':         return $entity->id;
                    case 'lang':       return $entity->_matchingData['Sentences']->lang;
                    case 'text':       return $entity->_matchingData['Sentences']->text;
                    case 'trans_text': return $entity->_matchingData['Translations']->text;
                    default:           return '';
                }
            },
            $fields
        );
    }

    public function validates()
    {
        if (isset($this->config['list_id'])
            && isset($this->config['fields'])
            && is_array($this->config['fields'])
            && $this->validateFields($this->config['fields'])
            && isset($this->config['format'])
            && $this->validateFormat($this->config['format'])
            && (!isset($this->config['trans_lang'])
                || LanguagesLib::languageExists($this->config['trans_lang']))) {

            $SL = TableRegistry::get('SentencesLists');
            $listId = $this->config['list_id'];
            try {
                $list = $SL->getListWithPermissions($listId, $this->userId);
            }
            catch (Exception $e) {
                return false;
            }
            if ($list['Permissions']['canView']) {
                $this->listName = $list->name;
                return true;
            }
        }
        return false;
    }

    public function getQuery()
    {
        $SSL = TableRegistry::get('SentencesSentencesLists');
        $query = $SSL->find()
            ->enableBufferedResults(false)
            ->where(['SentencesSentencesLists.sentences_list_id' => $this->config['list_id']])
            ->matching('Sentences', function ($q) {
                if (in_array('lang', $this->config['fields'])) {
                    $q->select('Sentences.lang');
                }
                if (in_array('text', $this->config['fields'])) {
                    $q->select('Sentences.text');
                }
                if (in_array('trans_text', $this->config['fields'])) {
                    $q->matching('Translations', function ($q) {
                        $q->select(['Translations.text']);
                        $q->where(['Translations.correctness >' => '-1']);
                        if (isset($this->config['trans_lang'])) {
                            $q->where(['SentencesTranslations.translation_lang' => $this->config['trans_lang']]);
                        }
                        return $q;
                    });
                }
                return $q;
            });

        if (in_array('id', $this->config['fields'])) {
            $query->select(['id' => 'SentencesSentencesLists.sentence_id']);
        }

        $query->formatResults(function($entities) {
            return $entities->map(function($entity) {
                return $this->getCSVFields($this->config['fields'], $entity);
            });
        });

        return $query;
    }

    public function getExportName()
    {
        if ($this->listName) {
            return format(__('List {listName}'), ['listName' => $this->listName]);
        }
    }

    public function getExportDescription()
    {
        return __("Sentence id [tab] Sentence text");
    }
}
