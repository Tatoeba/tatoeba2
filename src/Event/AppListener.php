<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2018  Gilles Bedel

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

/**
 * How to use: make class Whatever listen to Foobar's events:
 *
 * // Lib/Event/WhateverListener.php
 * class WhateverListener extends AppListener implements CakeEventListener {
 *     public function implementedEvents() {
 *         return array(
 *             'Model.Foobar.event' => 'whateverCallback',
 *         );
 *     }
 * }
 *
 * // Model/Foobar.php
 * class Foobar extends AppModel {
 *     public function __construct($id = false, $table = null, $ds = null) {
 *         parent::__construct($id, $table, $ds);
 *         $this->getEventManager()->attach(new WhateverListener());
 *     }
 *
 *     public function doStuff() {
 *         $event = new CakeEvent('Model.Foobar.event', $this, array(...));
 *         $this->getEventManager()->dispatch($event);
 *     }
 * }
 *
 * // Model/Whatever.php
 * class Whatever extends AppModel {
 *     public function whateverCallback($event) {
 *         // whatever needs to be done upon Model.Foobar.event
 *     }
 * }
 */

abstract class AppListener {
    public function __call($method, $params) {
        $className = substr(get_class($this), 0, -8);
        $object = ClassRegistry::init($className);
        return call_user_func_array(array($object, $method), $params);
    }
}
