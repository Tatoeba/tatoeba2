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

use App\Model\Search\LicenseFilter;
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
     *
     * @OA\Schema(
     *   schema="Sentence",
     *   description="A sentence object that contains both sentence text and metadata about the sentence.",
     *   @OA\Property(property="id", description="The sentence identifier", type="integer", example="1234"),
     *   @OA\Property(property="text", description="The sentence text", type="string", example="Everything will be okay."),
     *   @OA\Property(property="lang", anyOf={
     *     @OA\Schema(ref="#/components/schemas/LanguageCode"),
     *     @OA\Schema(type="null", description="Contains null when the language has been assigned an ISO 639-3 code, but it is not yet supported on Tatoeba. Such sentences are often part of a list that describes the language.")
     *   }),
     *   @OA\Property(property="script", description="The sentence script", anyOf={
     *     @OA\Schema(ref="#/components/schemas/ScriptCode",
     *       description="If more than one script is supported for the language of this sentence, this contains the script code in ISO 15924 standard.",
     *     ),
     *     @OA\Schema(type="null",
     *       description="If the sentence language is only written in a single obvious script (such as Latin script for English), this contains null. Also contains null when different scripts are in use on Tatoeba, but Tatoeba does not perform script autodetection (example: Algerian Arabic (<code>arq</code>) or Baluchi (<code>bal</code>).",
     *     )
     *   }),
     *   @OA\Property(property="license", ref="#/components/schemas/SentenceLicense", description="The license of the sentence"),
     *   @OA\Property(property="owner", description="The owner of the sentence", anyOf={
     *     @OA\Schema(type="string", description="User name of the sentence owner.", example="kevin"),
     *     @OA\Schema(type="null", description="Contains null when the sentence is orphan."),
     *   })
     * )
     */
    public function findSentencesOnApi(Query $query, array $options) {
        $exposedFields = [
            'fields' => ['id', 'text', 'lang', 'script', 'license', 'owner']
        ];
        $fields = ['id', 'text', 'lang', 'user_id', 'correctness', 'script', 'license'];

        $query
            ->find('exposedFields', compact('exposedFields'))
            ->select($fields)
            ->contain(['Users' => ['fields' => ['id', 'username']]])
            ->formatResults(function($results) use ($query) {
                return $results->map(function($result) {
                    if ($result['license'] == '') {
                        $result['license'] = LicenseFilter::LICENSING_ISSUE;
                    }
                    return $result;
                });
            });

        return $query;
    }

    /**
     * Finder for the Transcription objects served on API.
     *
     * @OA\Schema(
     *   schema="Transcription",
     *   description="The sentence written in an alternative form or script.",
     *   @OA\Property(property="text",
     *     type="string", example="Wo3men5 hai2you3 hen3 duo1gong1 zuo4 yao4 zuo4.",
     *     description="The transcription text. May use some ad-hoc markup syntax aimed at easing manual edit and machine readability."
     *   ),
     *   @OA\Property(property="script", ref="#/components/schemas/ScriptCode",
     *     description="Script code of the transcription in ISO 15924 standard"
     *   ),
     *   @OA\Property(
     *     property="needsReview", type="bool", example=false,
     *     description="Whether we think this transcription should be reviewed by a human. It is false when the transcription was autogenerated by an algorithm of low confidence. It is true when a human reviewed it, or when the algorithm is considered very accurate already."
     *   ),
     *   @OA\Property(
     *     property="type", type="enum", example="transcription", enum={"transcription", "altscript"},
     *     description="Transcription type. <em>altscript</em> means the transcription is made into a script representative of the language (for example simplified Chinese characters transcribed into traditional). <em>transcription</em> means the target script is not representative (for example simplified Chinese into Latin characters).",
     *   ),
     *   @OA\Property(
     *     property="html", type="string", example="Wǒmen h&aacute;iyǒu hěn duōgōng zu&ograve; y&agrave;o zu&ograve;.",
     *     description="An HTML-valid, human-readable and good-looking representation of the <em>text</em> field"
     *   ),
     *   @OA\Property(property="editor", description="Last editor of the transcription.", anyOf={
     *     @OA\Schema(type="string", description="User who last reviewed the sentence text.", example="kevin"),
     *     @OA\Schema(type="null", description="Contains null when the transcription text was autogenerated by a machine. This happens when the transcription text was never reviewed, or was reset to its autogenerated state by a user."),
     *   }),
     *   @OA\Property(
     *     property="modified", type="datetime", example="2020-02-20T02:20:00+00:00",
     *     description="Last time (in ISO 8601 format) the transcription text was edited. If <code>editor</code> is null, it is the datetime of autogeneration, otherwise it is the datetime of user review."
     *   )
     * )
     */
    public function findTranscriptionsOnApi(Query $query, array $options) {
        $exposedFields = [
            'fields' => ['script', 'text', 'needsReview', 'type', 'html', 'editor', 'modified']
        ];
        $fields = ['sentence_id', 'script', 'text', 'needsReview', 'modified'];
        $query
            ->find('exposedFields', compact('exposedFields'))
            ->select($fields)
            ->contain(['Users' => ['fields' => ['username']]]);
        return $query;
    }

    /**
     * Finder for the Audio objects served on API.
     *
     * @OA\Schema(
     *   schema="Audio",
     *   description="An audio object that contains metadata about a recording.",
     *   @OA\Property(property="id", description="The audio identifier", type="integer", example="4321"),
     *   @OA\Property(property="author", description="Name of user who contributed the audio recording",
     *                type="string", example="kevin62"),
     *   @OA\Property(property="licence", description="License of the audio recording", type="string",
     *                example="CC0 1.0"),
     *   @OA\Property(property="attribution_url", type="string", example="https://example.com/audio/kevin",
     *                description="URL to give attribution to the author. If you want to re-use the audio in your project, you need to mention the author name along with this URL."),
     *   @OA\Property(property="download_url", description="URL to download the audio file", type="string",
     *                example="https://example.com/audio/1234.mp3"),
     *   @OA\Property(property="created", description="Audio creation datetime in ISO 8601 format",
     *                type="datetime", example="2020-02-20T02:20:00+00:00"),
     *   @OA\Property(property="modified", description="Audio last modification datetime in ISO 8601 format",
     *                type="datetime", example="2020-02-20T02:20:00+00:00")
     * )
     */
    public function findAudiosOnApi(Query $query, array $options) {
        $exposedFields = [
            'fields' => [
                'id', 'created', 'author', 'license', 'attribution_url', 'download_url', 'created', 'modified'
            ]
        ];
        $fields = ['id', 'external', 'created', 'modified', 'sentence_id'];
        $query
            ->find('exposedFields', compact('exposedFields'))
            ->find('hasLicense')
            ->select($fields)
            ->contain('Users', function(Query $q) {
                return $q->select(['username', 'audio_license', 'audio_attribution_url']);
            });
        return $query;
    }

    /**
     * Finder for the Translation objects served on API.
     *
     * @OA\Schema(
     *   schema="Translation",
     *   description="A sentence object that is a direct or indirect translation of another sentence.",
     *   allOf={
     *     @OA\Schema(
     *       @OA\Property(property="is_direct", type="boolean",
     *         description="Whether this translation is direct (if value is true) or indirect (if value is false)"
     *       )
     *     ),
     *     @OA\Schema(ref="#/components/schemas/SentenceWithExtraInfo")
     *   }
     * )
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
