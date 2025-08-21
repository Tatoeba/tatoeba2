<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2025  Gilles Bedel

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
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Entity;
use Cake\ORM\Query;

class ExposedOnApiBehavior extends Behavior
{
    const EXPOSED_FIELDS_CACHE_KEY = '_';

    /**
     * This allows to set which fields will be exported in json,
     * regardless of the fields being virtual or not,
     * in a similar fashion as contain().
     */
    public function findExposedFields(Query $query, array $options)
    {
        $query->formatResults(function($results) use (&$options) {
            return $results->map(function($result) use (&$options) {
                return $this->setExposedFields($result, $options['exposedFields']);
            });
        });
        return $query;
    }

    public function setExposedFields($entity, array &$toExpose)
    {
        if (is_array($entity)) {
            foreach ($entity as &$e) {
                $this->setExposedFields($e, $toExpose);
            }
        } elseif ($entity instanceof Entity) {
            if (isset($toExpose[self::EXPOSED_FIELDS_CACHE_KEY])) {
                list($toHide, $notExposed) = $toExpose[self::EXPOSED_FIELDS_CACHE_KEY];
                $entity->setHidden($toHide);
                $entity->setVirtual($notExposed, true);
            }
            else if (isset($toExpose['fields'])) {
                $fieldsToExpose = $toExpose['fields'];

                $assocs = array_keys($toExpose);
                $hidden = $entity->getHidden();
                $toHide = array_diff($hidden, $fieldsToExpose);
                $exposed = $entity->getVisible();
                $unwantedExposed = array_diff($exposed, $fieldsToExpose, $assocs);
                $toHide = array_merge($toHide, $unwantedExposed);
                $entity->setHidden($toHide);

                $notExposed = array_diff($fieldsToExpose, $exposed);
                $entity->setVirtual($notExposed, true);

                $toExpose[self::EXPOSED_FIELDS_CACHE_KEY] = [$toHide, $notExposed];
            }
            foreach ($toExpose as $assoc => &$fieldsToExpose) {
                $contained = $entity->get($assoc);
                if (!is_null($contained)) {
                    $this->setExposedFields($contained, $fieldsToExpose);
                }
            }
        }
        return $entity;
    }

    /**
     * Custom finder to turn a datetime string such as
     * "2000-01-01T01:23:45+00:00"
     * into a truncated date string such as
     * "2000-01-01"
     * on the proveded fields.
     */
    public function findDatetime2date(Query $query, array $options) {
        $query->formatResults(function($entities) use ($options) {
            return $entities->map(function($entity) use ($options) {
                foreach ($options['datetimefields'] as $field) {
                    $entity->{$field} = $entity->{$field}->toDateString();
                }
                return $entity;
            });
        });
        return $query;
    }
}
