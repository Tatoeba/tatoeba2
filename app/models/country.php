<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  BEN YAALA Salem <salem.benyaala@gmail.com>
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
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Model for Country.
 *
 * @category API
 * @package  Models
 * @author   BEN YAALA Salem <salem.benyaala@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class Country extends AppModel
{
    /**
     *
     * @var string
     */
    public $name = 'Country';

    /**
     *
     * @var array
     */
    public $actsAs = array('Containable');

    public $useTable = false;

    public $_schema = array(
        'id' => array(
            'type' => 'string',
            'length' => 2,
            'null' => false,
        ),
        'name' => array(
            'type' => 'text',
            'length' => 80,
            'null' => false,
        ),
    );

    public $data; // memoizes the country list

    public function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
        if (!$this->data) {
            App::import('Model', 'Country_en');
            $Country_en = new Country_en();
            $this->data = $Country_en->data;
        }
        return $this->data;
    }
}
?>
