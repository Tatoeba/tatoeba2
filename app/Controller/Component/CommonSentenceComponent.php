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

/**
 * Component for saving sentence
 *
 * @category Utilities
 * @package  Helpers
 * @author   SIMON Allan <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class CommonSentenceComponent extends Component
{
    public $components = array(
        'LanguageDetection',
        'Cookie'
    );

    /**
     * Stuff to do when saving a new sentence. This function is never called
     * directly and is only here to factorize :)
     *
     * @param string $lang        The lang of the sentence to be saved,
     *                            if lang is 'auto' then we will try to
     *                            auto detect it
     * @param string $text        The sentence text.
     * @param int    $userId      The Id of the user who add/edit this
     *                            sentence.
     * @param string $username    Username of user who added sentence.
     * @param int    $correctness Correctness level of sentence.
     *
     * @return bool
     */
    public function addNewSentence(
        $lang,
        $text,
        $userId,
        $username = "",
        $correctness = 0
    ) {
        $this->Cookie->write('contribute_lang', $lang, false, "+1 month");

        $lang = $this->_setLanguage($lang, $text, $username);

        $Sentence = ClassRegistry::init('Sentence');

        return $Sentence->saveNewSentence($text, $lang, $userId, $correctness);
    }

    /**
     * Set the language if auto or empty.
     *
     * @param string $lang     Language string.
     * @param string $text     Text given by user.
     * @param string $username Username.
     *
     * @return string
     */
    private function _setLanguage($lang, $text, $username)
    {
        if ($lang === 'auto') {
            $lang = $this->LanguageDetection->detectLang(
                $text,
                $username
            );
        }

        if (empty($lang)) {
            $lang = null;
        }

        return $lang;
    }
}
