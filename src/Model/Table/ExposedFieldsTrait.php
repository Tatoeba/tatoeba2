<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2023  Gilles Bedel

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

use Cake\ORM\Entity;
use Cake\ORM\Query;

trait ExposedFieldsTrait
{
    /**
     * This allows to set which fields will be exported in json,
     * regardless of the fields being virtual or not,
     * in a similar fashion as contain().
     */
    public function findExposedFields(Query $query, array $options)
    {
        $query->formatResults(function($results) use ($options) {
            return $results->map(function($result) use ($options) {
                return $this->setExposedFields($result, $options['exposedFields']);
            });
        });
        return $query;
    }

    public function setExposedFields($entity, array $toExpose)
    {
        if (is_array($entity)) {
            foreach ($entity as $e) {
                $this->setExposedFields($e, $toExpose);
            }
        } elseif ($entity instanceof Entity) {
            if (isset($toExpose['fields'])) {
                $fieldsToExpose = $toExpose['fields'];
                unset($toExpose['fields']);

                $assocs = array_keys($toExpose);
                $hidden = $entity->getHidden();
                $toHide = array_diff($hidden, $fieldsToExpose);
                $exposed = $entity->getVisible();
                $unwantedExposed = array_diff($exposed, $fieldsToExpose, $assocs);
                $toHide = array_merge($toHide, $unwantedExposed);
                $entity->setHidden($toHide);

                $notExposed = array_diff($fieldsToExpose, $exposed);
                $entity->setVirtual($notExposed, true);
            }
            foreach ($toExpose as $assoc => $fieldsToExpose) {
               $contained = $entity->get($assoc);
               if (!is_null($contained)) {
                   $this->setExposedFields($contained, $fieldsToExpose);
               }
            }
        }
        return $entity;
    }
}
