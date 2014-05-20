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

class CommonSentenceComponent extends Object
{

    public $components = array(
        'LanguageDetection',
        'Cookie'
    );

    /**
     * all the stuff to save a sentence is made here
     * whatever it is editing / saving a translation etc..
     *
     * this function is never called directly and is only here
     * to factorize :)
     *
     * @param string $lang                   The lang of the sentence to be saved,
     *                                       if lang is 'auto' then we will try to
     *                                       auto detect it
     * @param string $text                   The sentence text.
     * @param int    $userId                 The Id of the user who add/edit this
     *                                       sentence.
     * @param int    $sentenceId             If we edit the sentence, it's the id of
     *                                       the sentence.
     * @param int    $translatedSentenceId   If the sentence is a translation of an
     *                                       other, id of the translated sentence
     * @param string $translatedSentenceLang Lang of the translated sentence if the
     *                                       Sentence to add is a translation.
     *
     * @return boolean
     *
     */
    public function wrapper_save_sentence(
        $lang,
        $text,
        $userId,
        $sentenceId = null,
        $translatedSentenceId = null,
        $translatedSentenceLang = null,
        $userName = "",
        $correctness = 0
    ) {
        $this->Cookie->write('contribute_lang', $lang, false, "+1 month");
        
        if ($lang === 'auto') {
            $lang = $this->LanguageDetection->detectLang(
                $text,
                $userName
            );
        }
        if (empty($lang)) {
            $lang = null;
        }

        $this->data['Sentence']['id'] = $sentenceId; 
        $this->data['Sentence']['lang'] = $lang;
        $this->data['Sentence']['user_id'] = $userId;
        $this->data['Sentence']['text'] = $text;
        $this->data['Sentence']['correctness'] = $correctness;

        $Sentence = ClassRegistry::init('Sentence');

        $isSaved = $Sentence->save($this->data);
        
        return $isSaved;
    }
       
    public function getAllNeededForSentences($sentenceIds, $lang = null)
    {
        $allSentences = array();
        
        $Sentence = ClassRegistry::init('Sentence');
        foreach ($sentenceIds as $i=>$sentenceId) {

            $sentence = $Sentence->getSentenceWithId($sentenceId);
            

            $alltranslations = $Sentence->getTranslationsOf(
                $sentenceId,
                $lang
            );
            $translations = $alltranslations['Translation'];
            $indirectTranslations = $alltranslations['IndirectTranslation'];

            $allSentences[$i] = array (
                "Sentence" => $sentence['Sentence'],
                "User" => $sentence['User'],
                "Translations" => $translations,
                "IndirectTranslations" => $indirectTranslations
            );
        }
        return $allSentences;
    }




}
