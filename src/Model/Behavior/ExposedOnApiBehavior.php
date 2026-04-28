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
use Cake\ORM\Association;
use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\Query;

class ExposedOnApiBehavior extends Behavior
{
    public function initialize(array $config): void
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
     * Traverses a tree of unhydrated (array) entities.
     * Each $entity and all associated entities down the tree are hydrated,
     * and turned back to arrays according to visible fields defined in $exposer.
     */
    private function exposeEntities(Table $table, array $entity, ?Exposer $exposer) {
        $assocs = $table->associations();
        foreach ($entity as $prop => $value) {
            if (is_array($value)) {
                $assoc = $assocs->getByProperty($prop);
                if ($assoc) {
                    $assocTable = $assoc->getTarget();
                    $containedExposer = $exposer ? $exposer->getContain($prop) : null;
                    if (in_array($assoc->type(), [Association::ONE_TO_MANY, Association::MANY_TO_MANY])) {
                        $entity[$prop] = array_map(
                            fn ($e) => $this->exposeEntities($assocTable, $e, $containedExposer),
                            $value
                        );
                    } else {
                        $entity[$prop] = $this->exposeEntities($assocTable, $value, $containedExposer);
                    }
                }
            }
        }
        $class = $table->getEntityClass();
        $options = [
            'useSetters' => false,
            'markClean' => true,
            'markNew' => false,
            'guard' => false,
        ];
        $entity = new $class($entity, $options);
        if (is_null($exposer)) {
            return $entity;
        } else {
            return $entity->extract($exposer->getFields());
        }
    }

    /**
     * Deactivate CakePHP hydration on $query and setup an Exposer to
     * perform some pseudo-hydration at the end, as a way to compute
     * visible fields in the final json response.
     */
    private function initExposer(Query $query)
    {
        $exposer = new Exposer();
        $query->applyOptions(['fieldsExposer' => $exposer]);
        $query->enableHydration(false);
        $query->formatResults(function($dryEntities) use ($query, $exposer) {
            return $dryEntities->map(function($entity) use ($query, $exposer) {
                return $this->exposeEntities($query->getRepository(), $entity, $exposer);
            });
        });
        return $exposer;
    }

    /**
     * This allows to set which fields will be exported in json,
     * regardless of the fields being virtual or not.
     */
    public function findExposedFields(Query $query, array $options)
    {
        $options = $query->getOptions();
        $exposer = $options['fieldsExposer'] ?? $this->initExposer($query);
        $exposer->addFields($options['exposedFields']);
        return $query;
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
                    $entity[$field] = substr($entity[$field], 0, 10);
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
        $exposer = $options['fieldsExposer'] ?? null;
        foreach ($options['containOnApi'] as $assoc => $value) {
            // make sure we can run any find*OnApi finder on the related table
            $query->getRepository()->{$assoc}->addBehavior('ExposedOnApi', $this->getConfig());
            if ($exposer) {
                // wrap container to pass on fieldsExposer as containment query option
                $propName = $query->getRepository()->getAssociation($assoc)->getProperty();
                $newOptions = ['fieldsExposer' => $exposer->in($propName)];
                if (is_array($value) && is_string($value['finder'] ?? null)) {
                    $value = fn (Query $q) => $q
                        ->applyOptions($newOptions)
                        ->find($value['finder']);
                } elseif (is_callable($value)) {
                    $value = fn (Query $q) =>
                        $value($q->applyOptions($newOptions));
                } else {
                    throw new \RuntimeException("Unsupported value for containOnApi containment $assoc, must be a callable or ['finder' => '...']");
                }
            }
            // actually include the related entities
            $query->contain([$assoc => $value]);
        }
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
     *   }),
     *   @OA\Property(property="is_unapproved", type="boolean",
     *     description="Whether this sentence is marked as <a href=""https://en.wiki.tatoeba.org/articles/show/faq#why-are-some-sentences-in-red?"">unapproved</a> (if value is true) or not (if value is false)"
     *   )
     * )
     */
    public function findSentencesOnApi(Query $query, array $options) {
        $exposedFields = [
            'id', 'text', 'lang', 'script', 'license', 'owner', 'is_unapproved'
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

        if ($this->getConfig('transcriptions')) {
            $containOnApi['transcriptions'] = ['finder' => 'transcriptionsOnApi'];
        }
        if ($this->getConfig('audios')) {
            $containOnApi['audios'] = ['finder' => 'audiosOnApi'];
        }
        if (isset($containOnApi)) {
            $query->find('containOnApi', compact('containOnApi'));
        }

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
            'script', 'text', 'needsReview', 'type', 'html', 'editor', 'modified'
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
            'id', 'created', 'author', 'license', 'attribution_url', 'download_url', 'created', 'modified'
        ];
        $fields = ['id', 'external', 'source', 'created', 'modified', 'sentence_id'];
        $query
            ->find('exposedFields', compact('exposedFields'))
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
            ->select('is_direct')
            ->find('exposedFields', ['exposedFields' => ['is_direct']]);

        // Apply showtrans filters
        $showtrans = $options['showtrans'] ?? null;
        if ($showtrans) {
            $showtrans->limitTranslations($query);
        }

        return $query;
    }
}
