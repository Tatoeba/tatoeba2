<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2025  Gilles Bedel

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;

class ExposedOnApiBehavior extends Behavior
{
    const EXPOSED_FIELDS_CACHE_KEY = '_';

    public function initialize(array $config)
    {
        // Temporary introduction of new code, we should get rid of this
        // once the association is directly used on the SentencesTable model
        if ($this->getTable()->getAlias() == 'Sentences') {
            $this->getTable()->belongsToManyMany('Translations', [
                'className' => 'Translations',
                'joinTable' => 'sentences_translations',
                'foreignKey' => 'sentence_id',
                'targetForeignKey' => 'translation_id',
            ]);
        }
    }

    /**
     * This allows to set which fields will be exported in json,
     * regardless of the fields being virtual or not,
     * in a similar fashion as contain().
     */
    public function findExposedFields(Query $query, array $options)
    {
        $query->formatResults(function($results) use ($query) {
            return $results->map(function($result) use ($query) {
                $exposedFields = $query->getOptions()['exposedFields'];
                return $this->setExposedFields($result, $exposedFields);
            });
        });
        return $query;
    }

    public function setExposedFields($entity, array &$toExpose)
    {
        if (is_array($entity)) {
            foreach ($entity as &$e) {
                $this->setExposedFields($e, $toExpose);
            }
        } elseif ($entity instanceof Entity) {
            if (isset($toExpose[self::EXPOSED_FIELDS_CACHE_KEY])) {
                list($toHide, $notExposed) = $toExpose[self::EXPOSED_FIELDS_CACHE_KEY];
                $entity->setHidden($toHide);
                $entity->setVirtual($notExposed, true);
            }
            else if (isset($toExpose['fields'])) {
                $fieldsToExpose = $toExpose['fields'];

                $assocs = array_keys($toExpose);
                $hidden = $entity->getHidden();
                $toHide = array_diff($hidden, $fieldsToExpose);
                $exposed = $entity->getVisible();
                $unwantedExposed = array_diff($exposed, $fieldsToExpose, $assocs);
                $toHide = array_merge($toHide, $unwantedExposed);
                $entity->setHidden($toHide);

                $notExposed = array_diff($fieldsToExpose, $exposed);
                $entity->setVirtual($notExposed, true);

                $toExpose[self::EXPOSED_FIELDS_CACHE_KEY] = [$toHide, $notExposed];
            }
            foreach ($toExpose as $assoc => &$fieldsToExpose) {
                $contained = $entity->get($assoc);
                if (!is_null($contained)) {
                    $this->setExposedFields($contained, $fieldsToExpose);
                }
            }
        }
        return $entity;
    }

    /**
     * Custom finder to turn a datetime string such as
     * "2000-01-01T01:23:45+00:00"
     * into a truncated date string such as
     * "2000-01-01"
     * on the proveded fields.
     */
    public function findDatetime2date(Query $query, array $options) {
        $query->formatResults(function($entities) use ($options) {
            return $entities->map(function($entity) use ($options) {
                foreach ($options['datetimefields'] as $field) {
                    $entity->{$field} = $entity->{$field}->toDateString();
                }
                return $entity;
            });
        });
        return $query;
    }

    /**
     * Helper to include related entities on the main entity.
     * Basically a ->contain() with API-specific stuff around.
     */
    public function findContainOnApi(Query $query, array $options)
    {
        foreach ($options['containOnApi'] as $assoc => $value) {
            // make sure we can run any find*OnApi finder on the related table
            $query->getRepository()->{$assoc}->addBehavior('ExposedOnApi');
            // actually include the related entities
            $query->contain([$assoc => $value]);
            // add the property name of the asssociation to the list of exposed fields
            $propName = $query->getRepository()->getAssociation($assoc)->getProperty();
            $options['exposedFields']['fields'][] = $propName;
        }
        // keep track of modified $options['exposedFields']
        $query->applyOptions($options);
        return $query;
    }

    /**
     * Finder for the Sentence objects served on API.
     */
    public function findSentencesOnApi(Query $query, array $options) {
        $exposedFields = [
            'fields' => ['id', 'text', 'lang', 'script', 'license', 'owner']
        ];
        $fields = ['id', 'text', 'lang', 'user_id', 'correctness', 'script', 'license'];

        $query
            ->find('exposedFields', compact('exposedFields'))
            ->select($fields)
            ->where(['license !=' => '']) // FIXME use Manticore filter instead
            ->contain(['Users' => ['fields' => ['id', 'username']]]);

        return $query;
    }

    /**
     * Finder for the Transcription objects served on API.
     */
    public function findTranscriptionsOnApi(Query $query, array $options) {
        $exposedFields = [
            'fields' => ['script', 'text', 'needsReview', 'type', 'html']
        ];
        $fields = ['sentence_id', 'script', 'text', 'needsReview'];
        $query
            ->find('exposedFields', compact('exposedFields'))
            ->select($fields);
        return $query;
    }

    /**
     * Finder for the Audio objects served on API.
     */
    public function findAudiosOnApi(Query $query, array $options) {
        $exposedFields = [
            'fields' => [
                'created', 'author', 'license', 'attribution_url', 'download_url', 'created', 'modified'
            ]
        ];
        $fields = ['id', 'external', 'created', 'modified', 'sentence_id'];
        $query
            ->find('exposedFields', compact('exposedFields'))
            ->select($fields)
            ->where(['audio_license !=' => '']) # exclude audio that cannot be reused outside of Tatoeba
            ->contain(['Users' => ['fields' => ['username', 'audio_license', 'audio_attribution_url']]]);
        return $query;
    }

    /**
     * Finder for the Translation objects served on API.
     */
    public function findTranslationsOnApi(Query $query, array $options) {
        $query
            ->find('sentencesOnApi')
            ->find('containOnApi', ['containOnApi' => [
                'Transcriptions' => ['finder' => 'transcriptionsOnApi'],
                'Audios'         => ['finder' => 'audiosOnApi'],
            ]])
            ->select('is_direct');

        // Make is_direct visible
        $exposedFields = $query->getOptions()['exposedFields'];
        $exposedFields['fields'][] = 'is_direct';
        $query->applyOptions(compact('exposedFields'));

        // Apply showtrans filters
        $showtrans = $options['showtrans'] ?? [];
        if (!empty($showtrans['lang'])) {
            $query->where(['lang IN' => $showtrans['lang']]);
        }
        if (is_bool($showtrans['is_direct'] ?? null)) {
            $query->where(['is_direct' => $showtrans['is_direct']]);
        }

        return $query;
    }
}
