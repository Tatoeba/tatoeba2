<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2020 Tatoeba Project
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
 */
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * Behavior for handling autocompletion
 *
 * This behavior adds the method 'Autocomplete' to a table which returns
 * the top n results that contain a given query
 */
class AutocompletableBehavior extends Behavior
{
     /**
     * Default config
     *
     * index    column to search from
     * fields   columns to return
     *
     * @var array
     */
    protected $_defaultConfig = [
        'implementedMethods' =>
            ['Autocomplete' => 'Autocomplete'],
        'index' => 'name',
        'fields' => ['id', 'name'],
        'order' => [],
        'limit' => 10
    ];

    public function initialize(array $config)
    {
        foreach (['index', 'fields', 'order', 'limit'] as $conf) {
            if (isset($config[$conf])) {
                $this->_config[$conf] = $config[$conf];
            }
        }
    }

    public function Autocomplete($search)
    {
        $query = $this->getTable()->find();
        $query->select($this->getConfig('fields'));

        if (!empty($search)) {
            $pattern = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $search).'%';
            $query->where(["{$this->getConfig('index')} LIKE" => $pattern]);
        }

        $query->order($this->getConfig('order'));
        $query->limit($this->getConfig('limit'));

        return $query->all();
    }
}
