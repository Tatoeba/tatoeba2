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
class AppModel extends Model {

    public $recursive = -1;

    /**
     * Function to be used in beforeSave().
     * Checks if the save() is about to update any of the provided $fields.
     * Precondition: the save() is NOT creating a new record, which means
     *               $this->data[$this->alias][$this->primaryKey] is set.
     */
    protected function isModifyingFields($fields) {
        foreach ($fields as $field) {
            if (array_key_exists($field, $this->data[$this->alias])) {
                $conditions = array(
                    $this->primaryKey => $this->data[$this->alias][$this->primaryKey]
                );
                $value = $this->field($field, $conditions);
                if ($this->data[$this->alias][$field] != $value)
                    return true;
            }
        }
        return false;
    }

    public function _getFieldFromDataOrDatabase($fieldName) {
        $data = $this->data[$this->alias];
        $fieldValue = false;
        if (array_key_exists($fieldName, $data)) {
            $fieldValue = $data[$fieldName];
        } elseif ($this->id) {
            $fieldValue = $this->field($fieldName);
        }
        return $fieldValue;
    }
}
