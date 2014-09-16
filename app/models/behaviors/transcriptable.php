<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang (tranglich@gmail.com)
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
 * @link *   http://tatoeba.org
 */

/**
 * Model behavior for transcriptions/transliterations.
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link *   http://tatoeba.org
 */

class TranscriptableBehavior extends ModelBehavior
{
    /**
     * Autotranscription class
     */
    var $autotranscription;

    /**
     * Languages that have a transcription available.
     */
    var $availableLanguages;

    function setup(&$model, $config = array())
    {
        if (!isset($this->autotranscription)) {
            App::import('Vendor', 'autotranscription');
            $this->autotranscription = new Autotranscription();
            $this->availableLanguages = $this->autotranscription->availableLanguages;
        }
    }

    /**
     *
     */
    function afterFind(&$model, $results, $primary = false)
    {
        foreach ($results as $key => $row) {
            
            $result = &$results[$key];
            if (!isset($result[$model->alias])) {
                return $results;
            }

            $sentence = $result[$model->alias];
            if (!isset($sentence['lang'])) {
                break;
            }

            $lang = $sentence['lang'];
            if (in_array($lang, $this->availableLanguages)) {
                $text = $sentence['text'];
                $result['Transcription'] = $this->_getTranscriptions(
                    $text, $lang
                );
            }
            
        }

        return $results;
    }


    function _getTranscriptions($text, $lang)
    {
        switch ($lang) {
            
            case 'cmn':
                return $this->_getCmnTranscriptions($text);

            case 'jpn':
                return $this->_getJpnTranscriptions($text);

            case 'kat':
                return $this->_getKatTranscription($text);

            case 'uzb':
                return $this->_getUzbTranscription($text);
                
            case 'wuu':
                return $this->_getWuuTranscription($text);
                
            case 'yue':
                return $this->_getYueTranscription($text);
                
            default:
                return;
        }
    }

    function _getCmnTranscriptions($text)
    {
        $result = array();

        $result[] = $this->autotranscription->cmn(
            $text, CMN_PINYIN
        );
        $result[] = $this->autotranscription->cmn(
            $text, CMN_OTHER_SCRIPT
        );

        return $result;
    }

    function _getJpnTranscriptions($text)
    {
        $result = array();

        $result[] = $this->autotranscription->jpn(
            $text, JPN_FURIGANA
        );
        $result[] = $this->autotranscription->jpn(
            $text, JPN_ROMAJI
        );

        return $result;
    }

    function _getKatTranscription($text)
    {
        $result = array();

        $result[] = $this->autotranscription->kat($text);

        return $result;
    }

    function _getUzbTranscription($text)
    {
        $result = array();

        $result[] = $this->autotranscription->uzb($text);

        return $result;
    }

    function _getWuuTranscription($text)
    {
        $result = array();

        $result[] = $this->autotranscription->wuu($text);

        return $result;
    }

    function _getYueTranscription($text)
    {
        $result = array();

        $result[] = $this->autotranscription->yue($text);

        return $result;
    }
}