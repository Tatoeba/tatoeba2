<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2016  Gilles Bedel

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

class Audio extends AppModel
{
    public $validate = array(
        'sentence_id' => array(
            'validateType' => array(
                'rule' => 'numeric',
                'required' => true,
                'on' => 'create',
            ),
        ),
        'user_id' => array(
            'rule' => 'numeric',
            'allowEmpty' => true,
        ),
        'licence_id' => array(
            'validateType' => array(
                'rule' => 'numeric',
                'required' => true,
                'on' => 'create',
            ),
        ),
        'created' => array(
            'rule' => 'notEmpty',
        ),
        'modified' => array(
            'rule' => 'notEmpty',
        ),
    );

    public $actsAs = array('Containable');

    public $belongsTo = array(
        'Sentence',
        'User',
    );

    public function beforeSave() {
        if (isset($this->data[$this->alias]['id']) &&
            isset($this->data[$this->alias]['sentence_id'])) {
            // save the previous sentence_id before updating it
            $result = $this->findById($this->data[$this->alias]['id'], 'sentence_id');
            if (isset($result[$this->alias]['sentence_id'])) {
                $this->data['PrevSentenceId'] = $result[$this->alias]['sentence_id'];
            }
        }

        $ok = true;
        $user_id = $this->_getFieldFromDataOrDatabase('user_id');
        $author  = $this->_getFieldFromDataOrDatabase('author');
        if (!($user_id xor !empty($author))) {
            $ok = false;
        }
        return $ok;
    }

    public function afterSave($created) {
        if (isset($this->data[$this->alias]['sentence_id'])) {
            $this->Sentence->flagSentenceAndTranslationsToReindex(
                $this->data[$this->alias]['sentence_id']
            );
            if (isset($this->data['PrevSentenceId']) &&
                $this->data['PrevSentenceId'] != $this->data[$this->alias]['sentence_id']) {
                $this->Sentence->flagSentenceAndTranslationsToReindex(
                    $this->data['PrevSentenceId']
                );
                unset($this->data['PrevSentenceId']);
            }
        }
    }

    public function beforeDelete($cascade = true) {
        // remember the sentence id before deleting the record,
        // so that we can get it from afterDelete()
        $result = $this->findById($this->id, 'sentence_id');
        if (isset($result[$this->alias]['sentence_id'])) {
            $this->data['PrevSentenceId'] = $result[$this->alias]['sentence_id'];
        }
        return true;
    }

    public function afterDelete() {
        if (isset($this->data['PrevSentenceId'])) {
            $this->Sentence->flagSentenceAndTranslationsToReindex(
                $this->data['PrevSentenceId']
            );
            unset($this->data['PrevSentenceId']);
        }
    }
}
?>
