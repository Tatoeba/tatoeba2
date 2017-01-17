<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2014  Gilles Bedel

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

App::import('Lib', 'autotranscription');

class Transcription extends AppModel
{
    /**
     * Autotranscription class
     */
    private $autotranscription;

    private $scriptsByLang = array( /* ISO 15924 */
        'jpn' => array('Jpan'),
        'uzb' => array('Cyrl', 'Latn'),
        'cmn' => array('Hans', 'Hant', 'Latn'),
        'yue' => array('Hans', 'Hant', 'Latn'),
    );
    private $availableTranscriptions = array(
        'jpn-Jpan' => array(
            'Hrkt' => array(
                'type' => 'altscript',
            ),
        ),
        'cmn-Hans' => array(
            'Hant' => array(
                'type' => 'altscript',
                'readonly' => true,
            ),
            'Latn' => array(
            ),
        ),
        'cmn-Hant' => array(
            'Hans' => array(
                'type' => 'altscript',
                'readonly' => true,
            ),
            'Latn' => array(
            ),
        ),
        'yue-Hans' => array(
            'Latn' => array(
                'readonly' => true,
            ),
        ),
        'yue-Hant' => array(
            'Latn' => array(
                'readonly' => true,
            ),
        ),
        'uzb-Latn' => array(
            'Cyrl' => array(
                'type' => 'altscript',
                'readonly' => true,
            ),
        ),
        'uzb-Cyrl' => array(
            'Latn' => array(
                'type' => 'altscript',
                'readonly' => true,
            ),
        ),
    );
    private $defaultFlags = array(
        'readonly' => false,
        'type' => 'transcription',
    );

    public $actsAs = array('Containable');

    public $validate = array(
        'sentence_id' => array(
            'validateType' => array(
                'rule' => 'numeric',
                'required' => true,
                'on' => 'create',
            ),
        ),
        'text' => array(
            'onCreation' => array(
                'rule' => 'notBlank',
                'required' => true,
                'on' => 'create',
            ),
            'onUpdate' => array(
                'rule' => 'notBlank',
                'on' => 'update',
            ),
        ),
        'script' => array(
            'rule' => 'notBlank',
            'required' => true,
            'on' => 'create',
        ),
        'user_id' => array(
            'rule' => 'numeric',
            'allowEmpty' => true,
        ),
        'created' => array(
            'rule' => 'notBlank',
        ),
        'modified' => array(
            'rule' => 'notBlank',
        ),
    );

    public $belongsTo = array(
        'Sentence',
        'User',
    );

    /* Transcription-specific validation error messages
       of the last transcription save operation */
    public $validationErrors = array();

    public function setAutotranscription($object) {
        $this->autotranscription = $object;
    }

    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->setAutotranscription(new Autotranscription());
    }

    public function afterFind($results, $primary = false) {
        $this->setTranscriptionsFlags($results);
        return $results;
    }

    private function setTranscriptionsFlags(&$results) {
        $sentenceIds = array();
        foreach ($results as $result) {
            if (!isset($result['Transcription']['sentence_id']) ||
                !isset($result['Transcription']['script']))
                return;

            $sentenceIds[ $result['Transcription']['sentence_id'] ] = true;
        }
        $sentenceIds = array_keys($sentenceIds);

        // Calling find() inside afterFind() causes problems with the
        // containable behavior, so let's avoid it
        $query = array(
            'conditions' => array('Sentence.id' => $sentenceIds),
            'fields' => array('id', 'lang', 'script'),
            'callbacks' => false,
        );
        $dbo = $this->Sentence->getDataSource();
        $sentences = $dbo->read($this->Sentence, $query, -1);

        $sentenceById = array();
        foreach ($sentences as $sentence) {
           $id = $sentence['Sentence']['id'];
           $sentenceById[$id] = $sentence['Sentence'];
        }
        foreach ($results as &$result) {
            $id = $result['Transcription']['sentence_id'];
            $script = $result['Transcription']['script'];
            $to = $this->transcriptableToWhat($sentenceById[$id]);
            if (isset($to[$script])) {
                $flags = array_intersect_key($to[$script], $this->defaultFlags);
                $result['Transcription'] = array_merge(
                    $result['Transcription'],
                    $this->defaultFlags,
                    $flags
                );
            }
        }
    }

    public function _isUnique() {
        $script = $this->_getFieldFromDataOrDatabase('script');
        if (!$script)
            return false;
        $sentenceId = $this->_getFieldFromDataOrDatabase('sentence_id');
        if (!$sentenceId)
            return false;

        $conditions = array(
            'script' => $script,
            'sentence_id' => $sentenceId,
        );
        if (!empty($this->id)) {
            $conditions['id !='] = $this->id;
        }

        return ($this->find('count', array('conditions' => $conditions)) == 0);
    }

    public function beforeSave($options = array()) {
        $ok = true;
        if (isset($this->data[$this->alias]['id'])) { // update
            if ($this->isModifyingFields(array('sentence_id', 'script')))
                $ok = false;
            else
                $ok = $this->_checkTranscriptionRules();
        } else { // create
            if (   isset($this->data[$this->alias]['sentence_id'])
                || isset($this->data[$this->alias]['script'])) {
                $ok = $this->_isUnique() && $this->_checkTranscriptionRules();
            }
        }
        return $ok;
    }

    private function _checkTranscriptionRules() {
        $targetScript = $this->_getFieldFromDataOrDatabase('script');
        if (!$targetScript)
            return false;

        $parentSentenceId = $this->_getFieldFromDataOrDatabase('sentence_id');
        if (!$parentSentenceId)
            return false;
        $parentSentence = $this->Sentence->find('first', array(
            'conditions' => array('Sentence.id' => $parentSentenceId),
        ));
        if (!$parentSentence)
            return false;

        $langScript = $this->getSourceLangScript($parentSentence['Sentence']);
        if (!$langScript)
            return false;
        if (!isset($this->availableTranscriptions[$langScript][$targetScript]))
            return false;

        $userId = $this->_getFieldFromDataOrDatabase('user_id');
        $transcrValidateMethod = sprintf(
            '%s_to_%s_validate',
            strtr($langScript, '-', '_'),
            $targetScript
        );
        if ($userId &&
            method_exists($this->autotranscription, $transcrValidateMethod)) {
            $this->validationErrors = array();
            $ok = $this->autotranscription->{$transcrValidateMethod}(
                $parentSentence['Sentence']['text'],
                $this->_getFieldFromDataOrDatabase('text'),
                $this->validationErrors
            );
            if (!$ok) {
                return false;
            }
        }

        return true;
    }

    private function getSourceLangScript($sourceSentence) {
        if (!isset($sourceSentence['lang']))
            return false;
        $sourceLang = $sourceSentence['lang'];
        $sourceScript = false;
        if (isset($sourceSentence['script']))
            $sourceScript = $sourceSentence['script'];
        if (!$sourceScript)
            $sourceScript = $this->getSourceScript($sourceLang);
        if (!$sourceScript)
            return false;

        $langScript = $sourceLang . '-' . $sourceScript;
        if (!isset($this->availableTranscriptions[$langScript]))
            return false;
        return $langScript;
    }

    public function transcriptableLanguages() {
        return array_keys($this->scriptsByLang);
    }

    public function langsInNeedOfScriptAutodetection() {
        $inNeed = array();
        foreach ($this->scriptsByLang as $lang => $scripts) {
            if (count($scripts) > 1)
                $inNeed[] = $lang;
        }
        return $inNeed;
    }

    private function getSourceScript($sourceLang) {
        if (isset($this->scriptsByLang[$sourceLang])) {
            if (count($this->scriptsByLang[$sourceLang]) == 1) {
                return $this->scriptsByLang[$sourceLang][0];
            }
        }
        return false;
    }

    public function detectScript($lang, $text) {
        if (isset($this->scriptsByLang[$lang])
            && count($this->scriptsByLang[$lang]) > 1) {
            $detectScriptMethod = sprintf('%s_detectScript', $lang);
            if (method_exists(
                    $this->autotranscription,
                    $detectScriptMethod
                )
            ) {
                return $this->autotranscription->{$detectScriptMethod}($text);
            }
        }
        return null;
    }

    public function isValidScriptForLanguage($lang, $script) {
        if (isset($this->scriptsByLang[$lang])
            && count($this->scriptsByLang[$lang]) > 1) {
            return in_array($script, $this->scriptsByLang[$lang], true);
        }
        return $script === null;
    }

    public function transcriptableToWhat($sourceSentence) {
        if (isset($sourceSentence['Sentence']))
            $sourceSentence = $sourceSentence['Sentence'];

        $langScript = $this->getSourceLangScript($sourceSentence);
        if (!$langScript)
            return array();

        return $this->availableTranscriptions[$langScript];
    }

    public function saveTranscription($transcr) {
        $sentence = $this->Sentence->findById($transcr['sentence_id']);
        if (!$sentence)
            return false;

        $targetScript = $transcr['script'];
        $langScript = $this->getSourceLangScript($sentence['Sentence']);
        if (!$langScript || !isset($this->availableTranscriptions[$langScript][$targetScript]))
            return false;

        $params = $this->availableTranscriptions[$langScript][$targetScript];
        if (isset($params['readonly']) && $params['readonly'])
            return false;

        return $this->generateTranscription(
            $sentence, $targetScript, true, $transcr
        );
    }

    public function generateAndSaveAllTranscriptionsFor($sentence) {
        if (isset($sentence['Sentence']))
            $sentence = $sentence['Sentence'];

        $langScript = $this->getSourceLangScript($sentence);
        if (!$langScript)
            return;

        $generated = 0;
        foreach ($this->availableTranscriptions[$langScript] as $targetScript => $process) {
            $generated += (int)$this->generateTranscription(
                $sentence,
                $targetScript,
                true
            );
        }
        return $generated;
    }

    public function generateTranscription($sentence, $targetScript, $save = false, $transcr = array()) {
        if (isset($sentence['Sentence']))
            $sentence = $sentence['Sentence'];

        $langScript = $this->getSourceLangScript($sentence);
        if (!$transcr) {
            $transcr = $this->_generateTranscription(
                $sentence['id'],
                $sentence['text'],
                $langScript,
                $targetScript
            );
            if (!$transcr)
                return false;
        } else {
            if (isset($transcr['user_id']))
                $transcr['needsReview'] = false;
        }

        $params = $this->availableTranscriptions[$langScript][$targetScript];
        if ($save) {
            $this->create();
            if (!$this->save($transcr))
                return false;
            $this->read();
            $transcr = $this->data[$this->alias];
        } else {
            $transcr['id'] = 'autogenerated';
        }
        return $transcr;
    }

    private function _generateTranscription($sentenceId, $text, $langScript, $targetScript) {
        $process = $this->availableTranscriptions[$langScript][$targetScript];

        $generatorMethod = sprintf(
            '%s_to_%s_generate',
            strtr($langScript, '-', '_'),
            $targetScript
        );
        if (method_exists($this->autotranscription, $generatorMethod)) {
            $needsReview = true;
            $transcrText = $this->autotranscription->{$generatorMethod}(
                $text,
                $needsReview
            );
            if (!$transcrText)
                return false;

            $transcr = array(
                'sentence_id' => $sentenceId,
                'script' => $targetScript,
                'text' => $transcrText,
                'user_id' => null,
            );
            $flags = array_intersect_key($process, $this->defaultFlags);
            $flags['needsReview'] = $needsReview;
            return array_merge($transcr, $this->defaultFlags, $flags);
        }
        return false;
    }

    public function getOwners($transcriptionId) {
        $transc = $this->find('first', array(
            'conditions' => array(
                'Transcription.id' => $transcriptionId,
            ),
            'fields' => array('Sentence.user_id', 'Transcription.user_id'),
            'contain' => array('Sentence'),
        ));

        if ($transc)
            return array(
                $transc['Transcription']['user_id'],
                $transc['Sentence']['user_id']
            );
        else
            return array(false, false);
    }

    public function findTranscription($sentenceId, $script) {
        return $this->find('first', array(
            'conditions' => array(
                'sentence_id' => $sentenceId,
                'script' => $script
            ),
        ));
    }

    private function insertTranscriptionOrdered(&$transcriptions, $newTranscr) {
        if (!$newTranscr)
            return;

        /* Hopefully the alphabetical order of script codes is the same as
         * the order we want transcriptions to be displayed. */
        $insertPos = 0;
        foreach ($transcriptions as $transcr) {
            if (strcmp($transcr['script'], $newTranscr['script']) > 0)
                break;
            $insertPos++;
        }
        array_splice($transcriptions, $insertPos, 0, array($newTranscr));
    }

    public function addGeneratedTranscriptions($transcriptions, $sentence) {
        $possibleScripts = $this->transcriptableToWhat($sentence);
        $existingScripts = Set::classicExtract($transcriptions, '{n}.script');
        $scriptsToGenerate = array_diff_key($possibleScripts, array_flip($existingScripts));

        foreach ($scriptsToGenerate as $script => $process) {
            $newTranscr = $this->generateTranscription($sentence, $script);
            $this->insertTranscriptionOrdered($transcriptions, $newTranscr);
        }
        return $transcriptions;
    }
}
?>
