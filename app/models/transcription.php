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
        'Latn', 'Hrkt', 
    );

    public $actsAs = array();

    public $validate = array(
        'sentence_id' => array(
            'rule' => 'numeric',
            'required' => true,
        ),
        'parent_id' => array(
            'rule' => 'numeric',
            'allowEmpty' => true,
        ),
        'text' => array(
            'rule' => 'notEmpty',
            'required' => true,
        ),
        'script' => array(
         /* 'rule' =>  see __construct() */
            'required' => true,
        ),
        'dirty' => array(
            'rule' => 'boolean',
            'required' => true,
        ),
        'created' => array(
            'rule' => 'notEmpty',
            'required' => true,
        ),
        'modified' => array(
            'rule' => 'notEmpty',
            'required' => true,
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
        $this->validate['script']['rule'] = array('inList', $this->availableScripts);
    }
}
?>
