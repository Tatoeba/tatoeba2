<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 SIMON   Allan   <allan.simon@supinfo.com>
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
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
namespace App\Model\Entity;

use Cake\ORM\Entity;

class UsersLanguage extends Entity
{
    protected function _getLanguageInfo() 
    {
        return [
            'language_code' => $this->language_code,
            'by_user_id' => $this->by_user_id
        ];
    }

    protected function _getOldFormat() 
    {
        return [
            'UsersLanguages' => [
                'id' => $this->id,
                'language_code' => $this->language_code,
                'level' => $this->level,
                'of_user_id' => $this->of_user_id,
                'by_user_id' => $this->by_user_id,
                'details' => $this->details
            ]
        ];
    }
}