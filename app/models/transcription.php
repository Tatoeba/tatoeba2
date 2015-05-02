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
    private $honorReadonly = true;
    private $availableTranscriptions = array(
        'jpn-Jpan' => array(
            'Hrkt' => array(
                'generator' => '_getFurigana',
            ),
            'Latn' => array(
                'chain' => array('jpn-Hrkt'),
                'readonly' => true,
            ),
        ),
        'jpn-Hrkt' => array(
            'Latn' => array(
                'generator' => 'tokenizedJapaneseWithReadingsToRomaji'
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
        'dirty' => array(
            'rule' => 'boolean',
            'required' => true,
            'on' => 'create',
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
            if (   isset($this->data[$this->alias]['sentence_id'])
                || isset($this->data[$this->alias]['script'])) {
                return false;
            }
        } else { // create
            if (   isset($this->data[$this->alias]['sentence_id'])
                || isset($this->data[$this->alias]['script'])) {
                return $this->_isUnique() && $this->_isTranscriptionAllowed();
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

    private function _isTranscriptionAllowed() {
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

        $transcriptions = $this->transcriptableToWhat($parentSentence, $this->honorReadonly);

        return (in_array($targetScript, $transcriptions));
    }

    private function getSourceLangScript($sourceSentence) {
        $sourceLang = $sourceSentence['lang'];
        $sourceScript = false;
        if (isset($sourceSentence['script']))
            $sourceScript = $sourceSentence['script'];
        if (!$sourceScript)
            $sourceScript = $this->getSourceScript($sourceLang);
        if (!$sourceScript)
            return false;

        return $sourceLang . '-' . $sourceScript;
    }

    private function getSourceScript($sourceLang) {
        if (isset($this->scriptsByLang[$sourceLang])) {
            if (count($this->scriptsByLang[$sourceLang]) == 1) {
                return $this->scriptsByLang[$sourceLang][0];
            }
        }
        return false;
    }

    public function transcriptableToWhat($sourceSentence, $excludeReadonly = false) {
        if (isset($sourceSentence['Sentence']))
            $sourceSentence = $sourceSentence['Sentence'];

        $langScript = $this->getSourceLangScript($sourceSentence);
        if (!$langScript)
            return array();

        if (!isset($this->availableTranscriptions[$langScript]))
            return array();

        $available = $this->availableTranscriptions[$langScript];
        if ($excludeReadonly) {
            $available = array_filter($available, function ($transcr) {
                return !(isset($transcr['readonly']) && $transcr['readonly']);
            });
        }
        return array_keys($available);
    }

    public function saveTranscription($sentenceId, $script, $isDirty, $text) {
        $transcription = array(
            'sentence_id' => $sentenceId,
            'script' => $script,
            'dirty' => $isDirty,
            'text' => $text,
        );
        return (bool)$this->save($transcription);
    }

    private function saveAllEvenReadonly($data) {
        $this->honorReadonly = false;
        $this->saveAll($data, array('validate' => true));
        $this->honorReadonly = true;
    }

    public function generateAndSaveAllTranscriptionsFor($sentence) {
        if (isset($sentence['Sentence']))
            $sentence = $sentence['Sentence'];

        $langScript = $this->getSourceLangScript($sentence);
        if (!isset($this->availableTranscriptions[$langScript]))
            return;

        $transcriptions = array();
        foreach ($this->availableTranscriptions[$langScript] as $targetScript => $process) {
            if (!isset($process['chain'])) {
                $transcription = $this->generateTranscription($sentence, $targetScript);
                if ($transcription)
                    $transcriptions[] = $transcription;
            }
        }
        $this->saveAllEvenReadonly($transcriptions);

        $transcriptions = array();
        foreach ($this->availableTranscriptions[$langScript] as $targetScript => $process) {
            if (isset($process['chain'])) {
                $transcription = $this->generateTranscription($sentence, $targetScript);
                if ($transcription)
                    $transcriptions[] = $transcription;
            }
        }
        $this->saveAllEvenReadonly($transcriptions);
    }

    public function generateTranscription($sentence, $targetScript) {
        if (isset($sentence['Sentence']))
            $sentence = $sentence['Sentence'];

        $langScript = $this->getSourceLangScript($sentence);
        if (!isset($this->availableTranscriptions[$langScript][$targetScript]))
            return false;

        $text = $this->_generateTranscription($sentence['id'], $sentence['text'], $langScript, $targetScript, $parentId);
        if (!$text)
            return false;

        $params = $this->availableTranscriptions[$langScript][$targetScript];
        $readonly = isset($params['readonly']) ? $params['readonly'] : false;

        return array(
            'sentence_id' => $sentence['id'],
            'parent_id' => $parentId,
            'script' => $targetScript,
            'text' => $text,
            'dirty' => false,
            'readonly' => $readonly,
        );
    }

    private function _generateTranscription($sentenceId, $text, $langScript, $targetScript, &$parent = null) {
        $process = $this->availableTranscriptions[$langScript][$targetScript];

        if (isset($process['generator']))
            return $this->autotranscription->{$process['generator']}($text);

        if (isset($process['chain'])) {
            $interLangScript = $process['chain'][0];
            $interScript = substr($interLangScript, strpos($interLangScript, '-') + 1);
            $intermediate = $this->find('first', array(
                'conditions' => array(
                    'sentence_id' => $sentenceId,
                    'script' => $interScript
                )
            ));
            if (!$intermediate)
                return false;
            $parent = $intermediate['Transcription']['id'];
            return $this->_generateTranscription(
                $sentenceId,
                $intermediate['Transcription']['text'],
                $interLangScript,
                $targetScript,
                $parent
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
        $scriptsToGenerate = array_diff($possibleScripts, $existingScripts);
        foreach ($scriptsToGenerate as $script) {
            $transcriptions[] = $this->generateTranscription($sentence, $script);
        }
        return $transcriptions;
    }
}
?>
