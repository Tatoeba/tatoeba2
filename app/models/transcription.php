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

App::import('Vendor', 'autotranscription');

class Transcription extends AppModel
{
    /**
     * Autotranscription class
     */
    private $autotranscription;

    public $availableScripts = array( /* ISO 15924 */
        'Cyrl', 'Hrkt', 'Jpan', 'Latn',
    );
    private $scriptsByLang = array(
        'jpn' => array('Jpan'),
        'uzb' => array('Cyrl', 'Latn'),
    );
    private $availableTranscriptions = array(
        'jpn-Jpan' => array(
            'Hrkt' => array(
                'generator' => '_getFurigana',
                'autogenerates' => 'Latn',
            ),
            'Latn' => array(
                'readonly' => true,
            ),
        ),
        'jpn-Hrkt' => array(
            'Latn' => array(
                'generator' => 'tokenizedJapaneseWithReadingsToRomaji',
                'readonly' => true,
            ),
        ),
        'uzb-Latn' => array(
            'Cyrl' => array(
                'generator' => null /* TODO */
            ),
        ),
        'uzb-Cyrl' => array(
            'Latn' => array(
                'generator' => null /* TODO */
            ),
        ),
    );

    public $actsAs = array('Containable');
    public $recursive = -1;

    public $validate = array(
        'sentence_id' => array(
            'validateType' => array(
                'rule' => 'numeric',
                'required' => true,
                'on' => 'create',
            ),
        ),
        'parent_id' => array(
            'rule' => 'numeric',
            'allowEmpty' => true,
        ),
        'text' => array(
            'onCreation' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'on' => 'create',
            ),
            'onUpdate' => array(
                'rule' => 'notEmpty',
                'on' => 'update',
            ),
        ),
        'script' => array(
            'onCreation' => array(
             /* 'rule' =>  see __construct() */
                'required' => true,
                'on' => 'create',
            ),
            'onUpdate' => array(
             /* 'rule' =>  see __construct() */
                'on' => 'update',
            ),
        ),
        'user_id' => array(
            'rule' => 'numeric',
            'allowEmpty' => true,
        ),
        'created' => array(
            'rule' => 'notEmpty',
        ),
        'modified' => array(
            'rule' => 'notEmpty',
        ),
    );

    public $belongsTo = array(
        'Sentence',
        'SourceTranscription' => array(
            'className' => 'Transcription',
            'foreignKey' => 'parent_id',
        ),
    );

    public function setAutotranscription($object) {
        $this->autotranscription = $object;
    }

    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->validate['script']['onUpdate']['rule']
            = $this->validate['script']['onCreation']['rule']
            = array('inList', $this->availableScripts);
        $this->setAutotranscription(new Autotranscription());
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

    public function beforeSave() {
        if (isset($this->data[$this->alias]['id'])) { // update
            if ($this->isModifyingFields(array('sentence_id', 'script')))
                return false;
        } else { // create
            if (   isset($this->data[$this->alias]['sentence_id'])
                || isset($this->data[$this->alias]['script'])) {
                return $this->_isUnique() && $this->_getTranscriptionRule();
            }
        }
        return true;
    }

    private function _getFieldFromDataOrDatabase($fieldName) {
        $data = $this->data[$this->alias];
        $fieldValue = false;
        if (isset($data[$fieldName])) {
            $fieldValue = $data[$fieldName];
        } elseif (isset($data['id'])) {
            $fieldValue = $this->field($fieldName,
                array('id' => $data['id'])
            );
        }
        return $fieldValue;
    }

    private function _getTranscriptionRule() {
        $targetScript = $this->_getFieldFromDataOrDatabase('script');
        if (!$targetScript)
            return false;

        $parentSentenceId = $this->_getFieldFromDataOrDatabase('sentence_id');
        if (!$parentSentenceId)
            return false;
        $parentSentence = $this->Sentence->find('first', array(
            'conditions' => array('Sentence.id' => $parentSentenceId),
            'contain' => array(),
        ));
        if (!$parentSentence)
            return false;

        $transcriptions = $this->transcriptableToWhat($parentSentence);
        if (isset($transcriptions[$targetScript])) {
            return $transcriptions[$targetScript];
        } else {
            return false;
        }
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

    private function getSourceScript($sourceLang) {
        if (isset($this->scriptsByLang[$sourceLang])) {
            if (count($this->scriptsByLang[$sourceLang]) == 1) {
                return $this->scriptsByLang[$sourceLang][0];
            }
        }
        return false;
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

        return $this->generateTranscription(
            $sentence, $transcr['script'], true, $transcr
        );
    }

    public function generateAndSaveAllTranscriptionsFor($sentence) {
        if (isset($sentence['Sentence']))
            $sentence = $sentence['Sentence'];

        $langScript = $this->getSourceLangScript($sentence);
        if (!$langScript)
            return;

        foreach ($this->availableTranscriptions[$langScript] as $targetScript => $process) {
            $this->generateTranscription($sentence, $targetScript, true);
        }
    }

    public function generateTranscription($sentence, $targetScript, $save = false, $transcr = array()) {
        if (isset($sentence['Sentence']))
            $sentence = $sentence['Sentence'];

        $langScript = $this->getSourceLangScript($sentence);
        if (!$langScript || !isset($this->availableTranscriptions[$langScript][$targetScript]))
            return array();

        $params = $this->availableTranscriptions[$langScript][$targetScript];
        if (isset($params['readonly']) && $params['readonly'])
            return array();

        $result = array();
        if (!$transcr) {
            $transcr = $this->_generateTranscription(
                $sentence['id'],
                $sentence['text'],
                $langScript,
                $targetScript
            );
            if (!$transcr)
                return array();
        }

        if ($save) {
            if (!$this->save($transcr))
                return array();
            if (!isset($transcr['id']))
                $transcr['id'] = $this->getLastInsertID();
        } else {
            $transcr['id'] = 'autogenerated';
        }
        $result[] = $transcr;

        if (isset($params['autogenerates'])) {
            $chainedLangScript = $sentence['lang'].'-'.$targetScript;
            $chainedTargetScript = $params['autogenerates'];
            $chainedTranscr = $this->_generateTranscription(
                $sentence['id'],
                $result[0]['text'],
                $chainedLangScript,
                $chainedTargetScript
            );
            if ($chainedTranscr) {
                $chainedTranscr['parent_id'] = $transcr['id'];
                $chainedTranscr['id'] = $this->findTranscriptionId(
                    $chainedTranscr['sentence_id'],
                    $chainedTranscr['script']
                );
                if ($save) {
                    $this->create();
                    if ($this->save($chainedTranscr))
                        $result[] = $chainedTranscr;
                } else {
                    $result[] = $chainedTranscr;
                }
            }
        }
        return $result;
    }

    private function _generateTranscription($sentenceId, $text, $langScript, $targetScript) {
        $process = $this->availableTranscriptions[$langScript][$targetScript];

        if (isset($process['generator'])) {
            $transcrText = $this->autotranscription->{$process['generator']}($text);
            if (!$transcrText)
                return false;

            $readonly = isset($process['readonly']) ? $process['readonly'] : false;
            return array(
                'sentence_id' => $sentenceId,
                'parent_id' => null,
                'script' => $targetScript,
                'text' => $transcrText,
                'readonly' => $readonly,
                'user_id' => null,
            );
        }
        return false;
    }

    public function getTranscriptionOwner($transcriptionId) {
        $transc = $this->find('first', array(
            'conditions' => array(
                $this->alias.'.'.$this->primaryKey => $transcriptionId,
            ),
            'contain' => array('Sentence')
        ));

        if ($transc)
            return $transc['Sentence']['user_id'];
        else
            return false;
    }

    public function findTranscriptionId($sentenceId, $script) {
        $result = $this->find('first', array(
            'conditions' => array(
                'sentence_id' => $sentenceId,
                'script' => $script
            ),
            'fields' => array('id')
        ));
        return $result ? $result['Transcription']['id'] : null;
    }

    public function addGeneratedTranscriptions($transcriptions, $sentence) {
        $possibleScripts = $this->transcriptableToWhat($sentence);
        $existingScripts = Set::classicExtract($transcriptions, '{n}.script');
        $scriptsToGenerate = array_diff_key($possibleScripts, array_flip($existingScripts));
        foreach ($scriptsToGenerate as $script => $process) {
            if (isset($process['autogenerates']) && isset($scriptsToGenerate[$process['autogenerates']]))
{
                unset($scriptsToGenerate[$process['autogenerates']]);
}
        }

        foreach ($scriptsToGenerate as $script => $process) {
            $transcriptions = array_merge(
                $transcriptions,
                $this->generateTranscription($sentence, $script)
            );
        }
        return $transcriptions;
    }
}
?>
