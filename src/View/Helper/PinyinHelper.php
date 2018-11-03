<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  SIMON Allan <allan.simon@supinfo.com>
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
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
namespace App\View\Helper;


/**
 * Helper for pinyin
 *
 * @category Utilities
 * @package  Helpers
 * @author   SIMON Allan <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class PinyinHelper extends AppHelper
{

    private $_constTone2ToneConsti_search = array(
        'n1',
        'n2',
        'n3',
        'n4',
        'ng1',
        'ng2',
        'ng3',
        'ng4',
        'r1',
        'r2',
        'r3',
        'r4'
    );

    private $_constTone2ToneConsti_replace = array(
        '1n',
        '2n',
        '3n',
        '4n',
        '1ng',
        '2ng',
        '3ng',
        '4ng',
        '1r',
        '2r',
        '3r',
        '4r'
    );


    private $_vowelVowelTone2VowelToneVowel_search = array(
        'ai1',
        'ai2',
        'ai3',
        'ai4',
        'ao1',
        'ao2',
        'ao3',
        'ao4',
        'ei1',
        'ei2',
        'ei3',
        'ei4',
        'ou1',
        'ou2',
        'ou3',
        'ou4'
    );

    private $_vowelVowelTone2VowelToneVowel_replace = array(
        'a1i',
        'a2i',
        'a3i',
        'a4i',
        'a1o',
        'a2o',
        'a3o',
        'a4o',
        'e1i',
        'e2i',
        'e3i',
        'e4i',
        'o1u',
        'o2u',
        'o3u',
        'o4u'
    );

    private $_vowelTone2Unicode_search = array(
        'a1',
        'a2',
        'a3',
        'a4',
        'e1',
        'e2',
        'e3',
        'e4',
        'i1',
        'i2',
        'i3',
        'i4',
        'o1',
        'o2',
        'o3',
        'o4',
        'u1',
        'u2',
        'u3',
        'u4',
        'v1',
        'v2',
        'v3',
        'v4'
    );

    private $_vowelTone2Unicode_replace = array(
        'ā',
        'á',
        'ǎ',
        'à',
        'ē',
        'é',
        'ě',
        'è',
        'ī',
        'í',
        'ǐ',
        'ì',
        'ō',
        'ó',
        'ǒ',
        'ò',
        'ū',
        'ú',
        'ǔ',
        'ù',
        'ǖ',
        'ǘ',
        'ǚ',
        'ǜ'
    );


    private $_remove5thToneNumber_search = array(
        'a5',
        'e5',
        'i5',
        'o5',
        'u5',
        'v5',
        'n5',
        'g5',
        'r5'
    );


    private $_remove5thToneNumber_replace = array(
        'a',
        'e',
        'i',
        'o',
        'u',
        'v',
        'n',
        'g',
        'r'
    );

    /**
     * Replace numeric pinyin by diacritical marks
     * Convert to php from a python script of Brian Vaughan
     * (http://brianvaughan.net)
     *
     * @param string $text Pinyin with tones in numeric notation (she3)
     *
     * @return string
     */

    public function numeric2diacritic($text)
    {
        $text = str_replace(
            $this->_constTone2ToneConsti_search,
            $this->_constTone2ToneConsti_replace,
            $text
        );

        $text = str_replace(
            $this->_vowelVowelTone2VowelToneVowel_search,
            $this->_vowelVowelTone2VowelToneVowel_replace,
            $text
        );

        $text = str_replace(
            $this->_vowelTone2Unicode_search,
            $this->_vowelTone2Unicode_replace,
            $text
        );


        $text = str_replace(
            $this->_remove5thToneNumber_search,
            $this->_remove5thToneNumber_replace,
            $text
        );

        return $text;
    }



}
?>
