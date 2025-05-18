<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2022  Gilles Bedel

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
namespace App\Model\Table;

class DisabledAudiosTable extends AudiosTable
{
    public function initialize(array $config) {
        parent::initialize($config);
        $this->setEntityClass('Audio');
    }

    public function afterSave($event, $entity, $options = array()) {
        if ($entity->enabled) {
            $this->moveRecordToOtherTable($entity, $this->Sentences->Audios);
        }
    }

    public function afterDelete($event, $entity, $options) {
        $this->removeAudioFile($entity, $options);
    }
}
