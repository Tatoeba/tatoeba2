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

class Transcription extends AppModel
{
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
                'generator' => null /* TODO */
            ),
            'Latn' => array(
                'chain' => array('jpn-Hrkt'),
            ),
        ),
        'jpn-Hrkt' => array(
            'Latn' => array(
                'generator' => null /* TODO */
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
            'validateUnicity' => array(
                'rule' => array('isUnique', array('sentence_id', 'script')),
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
        'user_modified' => array(
            'rule' => 'boolean',
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

    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->validate['script']['onUpdate']['rule']
            = $this->validate['script']['onCreation']['rule']
            = array('inList', $this->availableScripts);
    }

    private function getSourceScript($sourceLang, $sourceText = null) {
        if (isset($this->scriptsByLang[$sourceLang])) {
            if (count($this->scriptsByLang[$sourceLang]) == 1) {
                return $this->scriptsByLang[$sourceLang][0];
            } else {
                // TODO: need to do some further research based on $souceText
                return false;
            }
        } else {
            return false;
        }
    }

    public function transcriptableToWhat($sourceLang, $sourceText = null) {
        $sourceScript = $this->getSourceScript($sourceLang, $sourceText);
        if (!$sourceScript)
            return array();

        $sourceScript = $sourceLang . '-' . $sourceScript;
        if (!isset($this->availableTranscriptions[$sourceScript]))
            return array();

        $targetScripts = array_keys($this->availableTranscriptions[$sourceScript]);
        $targetScripts = array_flip($targetScripts);
        return $targetScripts;
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
}
?>
