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
use App\Model\Entity\HashTrait;

class Sentence extends Entity
{
    use HashTrait;

    public function __construct($properties = [], $options = []) {
        parent::__construct($properties, $options);
        $this->setHashFields(['lang', 'text']);
    }

    protected function _setLang($value)
    {
        $this->updateHash();
        return empty($value) ? null : $value;
    }

    protected function _setText($value)
    {
        $this->updateHash();
        return $this->_clean($value);
    }

    private function _clean($text)
    {
        $text = trim($text);
        // Strip out any byte-order mark that might be present.
        $text = preg_replace("/\xEF\xBB\xBF/", '', $text);
        // Replace any series of spaces, newlines, tabs, or other
        // ASCII whitespace characters with a single space.
        $text = preg_replace('/\s+/', ' ', $text);
        // MySQL will truncate to a byte length of 1500, which may split
        // a multibyte character. To avoid this, we preemptively
        // truncate to a maximum byte length of 1500. If a multibyte
        // character would be split, the entire character will be
        // truncated.
        $text = mb_strcut($text, 0, 1500, "UTF-8");
        return $text;
    }

    protected function _getOldFormat() 
    {
        $result['Sentence'] = [
            'id' => $this->id,
            'lang' => $this->lang,
            'text' => $this->text,
            'hash' => $this->hash,
            'script' => $this->script,
            'user_id' => $this->user_id
        ];
        
        if ($this->user) {
            $result['User'] = [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'image' => $this->user->image
            ];
        }

        return $result;
    }
}
