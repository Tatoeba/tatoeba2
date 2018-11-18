<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 HO Ngoc Phuong Trang
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
namespace App\Model\Entity;

use Cake\ORM\Entity;

class PrivateMessage extends Entity
{
    protected function _getOldFormat() 
    {
        return [
            'PrivateMessage' => [
                'id' => $this->id,
                'recpt' => $this->recpt,
                'sender' => $this->sender,
                'user_id' => $this->user_id,
                'date' => $this->date->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                'folder' => $this->folder,
                'title' => $this->title,
                'content' => $this->content,
                'sent' => $this->sent,
                'isnonread' => $this->isnonread,
                'draft_recpts' => $this->draft_recpts,
            ]
        ];
    }
}