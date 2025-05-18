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

use App\Model\CurrentUser;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

class NativeFinderBehavior extends Behavior
{
    public function findNativeMarker(Query $query, array $options)
    {
        if (!CurrentUser::getSetting('native_indicator')) {
            return $query;
        }

        $query->join([
            'table' => 'users_languages',
            'alias' => 'UsersLanguages',
            'type' => 'LEFT',
            'conditions' => [
                'Sentences.user_id = UsersLanguages.of_user_id',
                'Sentences.lang = UsersLanguages.language_code',
                'UsersLanguages.level' => 5
            ]
        ]);
        $isNative = $query->newExpr()
                          ->isNotNull('UsersLanguages.id')
                          ->notEq('Users.role', 'spammer')
                          ->gt('Users.level', '-1');
        $query->select(['Users__is_native' => $isNative]);
        return $query;
    }
}
