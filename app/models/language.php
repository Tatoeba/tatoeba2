<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 SIMON   Allan   <allan.simon@supinfo.com>
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
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   SIMON   Allan   <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Model for Languages.
 *
 * @category Language
 * @package  Models
 * @author   SIMON   Allan   <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class Language extends AppModel
{
    public $name = 'Language';
    public $useTable = 'langStats';
    public $actsAs = array("Containable");
    public $hasMany = array(
        'Sentence' => array(
            'className'  => 'Sentence',
            'foreignKey' => 'lang_id'
        )
    );

    /**
     * Return the id associated to the given lang string
     *
     */
    public function getIdFromLang($lang) {
        $result = $this->find(
            'first',
            array(
                'fields' => array('id'),
                'contain' => array(),
                'conditions' => array ('lang' => $lang),
            )
        );
        return $result['Language']['id'];
    }


}
