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

App::uses('CakeEventListener', 'Event');

class SuggestdListener implements CakeEventListener {
    public function implementedEvents() {
        return array(
            'Model.Tag.tagAdded' => 'notifySuggestd',
        );
    }

    public function notifySuggestd($event) {
        extract($event->data); // $tagName

        // Send a request to suggestd (the auto-suggest daemon) to update its internal
        // table.
        // TODO only do this if we add a new ("dirty") tag.
        $dirty = fopen("http://127.0.0.1:8080/add?str=".urlencode($tagName)."&value=1", 'r');
        if ($dirty != null) {
            fclose($dirty);
        }
    }
}
