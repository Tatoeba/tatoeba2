<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
namespace App\Model\Table;

use Cake\ORM\Table;


class LastContributionsTable extends Table
{
    public function initialize(array $config)
    {
        $this->belongsTo('Sentences');
        $this->belongsTo('Users');
    }

    /**
     * Get the top contributors from the latest contributions.
     *
     * @param int $limit Number of top contributors.
     *
     * @return array
     */
    public function getCurrentContributors()
    {
        $result = $this->find()
            ->contain([
                'Users' => [
                    'fields' => [
                        'username', 'role', 'image'
                    ]
                ]
            ])
            ->select([
                'total' => 'COUNT(LastContributions.id)',
                'Users.username',
                'Users.role',
                'Users.image'
            ])
            ->order(['total' => 'DESC'])
            ->group(['LastContributions.user_id'])
            ->toList();

        return $result;
    }

    /**
     * Get the top contributors from the latest contributions in a specified language.
     *
     * @param string $lang 3-letter language code of selected language.
     * @param int $limit Number of top contributors.
     *
     * @return array
     */
    public function getCurrentContributorsInLang($lang)
    {
        $result = $this->find()
            ->contain([
                'Users' => [
                    'fields' => [
                        'id', 'username', 'role', 'image'
                    ]
                ]
            ])
            ->select([
                'total' => 'COUNT(LastContributions.id)',
                'Users.username',
                'Users.role',
                'Users.image'
            ])
            ->where(['LastContributions.sentence_lang' => $lang])
            ->order(['total' => 'DESC'])
            ->group(['LastContributions.user_id'])
            ->toList();

        return $result;
    }


    /**
     * [getTotalLastContributions description]
     * @param  [type] $contributors [description]
     * @return [type]               [description]
     */
    public function getTotal($contributors)
    {
        $total = 0;
        foreach ($contributors as $contributor) {
            $total += $contributor->total;
        }

        return $total;
    }

}
