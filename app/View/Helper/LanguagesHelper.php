<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

App::import('Model', 'CurrentUser');
App::import('Lib', 'LanguagesLib');

/**
 * Helper for languages
 *
 * @category Default
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class LanguagesHelper extends AppHelper
{
    public $helpers = array('Html', 'Session');

    /* Memoization of languages code and their localized names */
    private $__languages_alone;


    private function langAsAlone($name)
    {
        return format(
        /* @translators: this special string allows you to tweak how language
           names are displayed when they are not used inside another string.
           For instance, in language lists, on flag mouseover or on the stats
           page. You may translate this string using a declension modifier,
           for instance {language.alone} */
            __('{language}'),
            array('language' => $name)
        );
    }

    public function localizedAsort(&$array)
    {
        if (class_exists('Collator')) {
            $isoLang = Configure::read('Config.language');
            $coll = new Collator($isoLang);
            $coll->asort($array);
        } else {
            asort($array);
        }
    }

    public function preferredLanguageFilter() {
        if (CurrentUser::isMember()) {
            return CurrentUser::getProfileLanguages();
        } else {
            return $this->Session->read('last_used_lang');
        }
    }

    private function separatePreferredLanguages($languages)
    {
        $filter = $this->preferredLanguageFilter();
        if (!$filter) {
            return $languages;
        }
        $preferred = array();
        foreach ($filter as $prefLang) {
            if (isset($languages[$prefLang])) {
                $preferred[$prefLang] = $languages[$prefLang];
                unset($languages[$prefLang]);
            }
        }
        $this->localizedAsort($preferred);

        if (CurrentUser::isMember()) {
            $filterName = __('Profile languages');
        } else {
            $filterName = __('Last used languages');
        }
        return array(
            $filterName                 => $preferred,
            __('Other languages') => $languages,
        );
    }

    public function onlyLanguagesArray($split = true)
    {
        if (!$this->__languages_alone) {
            $this->__languages_alone = array_map(
                array($this, 'langAsAlone'),
                LanguagesLib::languagesInTatoeba()
            );
            $this->localizedAsort($this->__languages_alone);
        }

        $languages = $this->__languages_alone;
        if ($split) {
            $languages = $this->separatePreferredLanguages($languages);
        }
        return $languages;
    }


    /**
     * Returns array of languages set in the user's options.
     */
    public function userLanguagesArray()
    {
        $languages = $this->onlyLanguagesArray();

        if (CurrentUser::isMember()) {
            $userLangs = CurrentUser::getLanguages();
            if (!empty($userLangs)) {
                $filteredLangs = array();
                foreach($userLangs as $langCode) {
                    $filteredLangs[$langCode] = $languages[$langCode];
                }
                $languages = $filteredLangs;
            }
        }

        return $languages;
    }

    /**
     * Returns array of languages set in the user's profile.
     *
     * @param bool   $withAutoDetection Set to true if "Auto detect" should be one of the options.
     * @param bool   $withOther Set to true if "Other language" should be one of the options.
     *
     * @return array
     */

    public function profileLanguagesArray($withAutoDetection, $withOther)
    {
        $languages = array_intersect_key(
            $this->onlyLanguagesArray(false),
            array_flip(CurrentUser::getProfileLanguages())
        );

        if (count($languages) > 1 && $withAutoDetection) {
            array_unshift($languages, array('auto' => __('Auto detect')));
        }
        if ($withOther) {
            array_unshift($languages, array('' => __('other language')));
        }

        return $languages;
    }

    /**
     * Return array of languages in Tatoeba + all languages, formatted
     * like it's displayed when alone on the UI (on lists or flags).
     *
     * @return array
     */
    public function languagesArrayAlone()
    {
        $languages = $this->onlyLanguagesArray();
        array_unshift($languages, array(
            'und' => $this->langAsAlone(__('All languages'))
        ));
        return $languages;
    }

    /**
     * Return array of languages in Tatoeba + all languages, ready
     * to be used inside a format() call. You MUST use the return
     * value as a variable inside a format() call. If not,
     * use languagesArrayAlone() instead.
     *
     * @return array
     */
    public function languagesArrayToFormat()
    {
        $languages = LanguagesLib::languagesInTatoeba();

        array_unshift($languages, array('und' => __('All languages')));

        return $languages;
    }

    /**
     * Return array of languages in Tatoeba. + 'unknown language'
     *
     * @return array
     */
    public function unknownLanguagesArray()
    {
        $languages = $this->onlyLanguagesArray();

        array_push($languages, array(
            'unknown' => __x('dropdown-list', 'Unknown language')
        ));

        return $languages;
    }


    /**
     * Return array of languages in Tatoeba + 'other language'. 'other language' is
     * used to set the language to null, in case there was a misdetection and the
     * language in which the user is writing is not supported yet.
     *
     * @return array
     */
    public function otherLanguagesArray()
    {
        $languages = $this->onlyLanguagesArray();

        array_unshift($languages, array('' => __('other language')));

        return $languages;
    }


    /**
     * Return array of language + "auto"
     * used to know if the user want the language of a contribution
     * to be manualy set or auto detect
     *
     * @return array
     */

    public function translationsArray()
    {
        $languages = $this->userLanguagesArray();

        array_unshift($languages, array('auto' => __('Auto detect')));
        return $languages;
    }


    /**
     * Return array of languages, with "None" and "All languages" options.
     * Applies to a positive phrase (for example, "Show translations in").
     *
     * @return array
     */
    public function languagesArrayForPositiveLists()
    {
        $languages = $this->onlyLanguagesArray();

        array_unshift(
            $languages,
            array(
                'none' => __('None'),
                'und' => __('All languages')
            )
        );

        return $languages;
    }


    /**
    * Return array of languages, with "--" and "Any languages" options.
    * Applies to a negative phrase (for example, "Not directly translated into").
    *
    * @return array
    */
    public function languagesArrayForNegativeLists()
    {
        $languages = $this->onlyLanguagesArray();

        array_unshift(
            $languages,
            array(
                'none' => '—',
                'und' => __('Any language')
            )
        );

        return $languages;
    }


    /**
     * Return array of languages with, "None" option.
     *
     * @return array
     */
    public function languagesArrayWithNone()
    {
        $languages = $this->onlyLanguagesArray();

        array_unshift(
            $languages,
            array(
                'none' => __('None')
            )
        );

        return $languages;
    }


    /**
     * Return array of languages in which you can search.
     *
     * @return array
     */
    public function getSearchableLanguagesArray()
    {
        $languages = $this->onlyLanguagesArray();
        array_unshift(
            $languages, array('und' => __x('searchbar', 'Any language'))
        );

        return $languages;
    }

    /**
     * Return name of the language from the ISO code, formatted
     * like it's displayed when alone on the UI (on lists or flags).
     *
     * @param string $code ISO-639-3 code.
     *
     * @return string
     */
    public function codeToNameAlone($code) {
        return $this->langAsAlone($this->codeToNameToFormat($code));
    }

    /**
     * Return name of the language from the ISO code, ready to
     * be used inside a format() call. You MUST use the return
     * value as a variable inside a format() call. If not,
     * use codeToNameAlone() instead.
     *
     * @param string $code ISO-639-3 code.
     *
     * @return string
     */
    public function codeToNameToFormat($code)
    {
        $languages = $this->languagesArrayToFormat();
        if (isset($languages["$code"])) {
            return $languages["$code"];
        } else {
            return __('unknown');
        }
    }

    /**
     * Return number of languages
     *
     * @return int
     */

    public function getNumberOfLanguages()
    {
        $languages = $this->onlyLanguagesArray();
        $numberOfLanguages = count($languages);
        return $numberOfLanguages;
    }

    /**
     * Display flag and number of sentences in the "sentences stats" block.
     *
     * @param string $langCode          Language code.
     * @param int    $numberOfSentences Number of sentences.
     * @param array  $link              Path to page with sentences in given language.
     *
     * @return void
     */
    function stat($langCode, $numberOfSentences, $link)
    {
        $flagImage = $this->icon(
            $langCode,
            array(
                'width' => 30,
                'height' => 20
            )
        );
        $numberOfSentencesHtml = '<span class="total">'.$numberOfSentences.'</span>';

        if (empty($langCode)) {
            $langCode = 'unknown';
        }
        $linkToAllSentences = $this->Html->link(
            $flagImage . $numberOfSentencesHtml,
            $link,
            array(
                "escape" => false
            ),
            null
        );

        ?>
        <li class="stat" title="<?php echo $this->codeToNameAlone($langCode); ?>">
        <?php echo $linkToAllSentences; ?>
        </li>
        <?php
    }

    /**
     * Display language icon.
     *
     * @param string $lang    Language code.
     * @param array  $options Options for Html::image().
     *
     * @return string
     */
    public function icon($lang, $options)
    {
        if (empty($lang)) {
            $lang = 'unknown';
        }

        $options["title"] = $this->codeToNameAlone($lang);
        $options["alt"] = $lang;

        return $this->Html->image(
            IMG_PATH . 'flags/'.$lang.'.png',
            $options
        );
    }

    public function tagWithLang($tag, $lang, $text, $options = array(), $script = '')
    {
        $direction = empty($lang) ? 'auto' : LanguagesLib::getLanguageDirection($lang);
        $options = array_merge(
            array(
                'lang' => LanguagesLib::languageTag($lang, $script),
                'dir'  => $direction,
                'escape' => true,
                'ng-non-bindable' => ''
            ),
            $options
        );
        return $this->Html->tag($tag, $text, $options);
    }


    public function displayAddLanguageMessage($isNewSentence)
    {
        if ($isNewSentence) {
            $warningMessage = __(
                'You cannot add sentences because you did not add any '.
                'language in your profile.', true
            );
        } else {
            $warningMessage = __(
                'You cannot translate sentences because you did not add any '.
                'language in your profile.', true
            );
        }

        $newLangUrl = $this->Html->url(array(
            'controller' => 'user',
            'action' => 'language'
        ));
        ?>
        <p><?= $warningMessage ?></p>

        <div layout="row" layout-align="center center">
            <md-button class="md-raised md-primary" href="<?= $newLangUrl ?>">
                <? echo __('Add a language'); ?>
            </md-button>
        </div>
        <?php
    }


    public function getLevelsLabels($index = null)
    {
        if (!isset($__languagesLevels)) {
            $__languagesLevels = array(
                -1 => __x('level', 'Unspecified'),
                0 => __('0: Almost no knowledge'),
                1 => __('1: Beginner'),
                2 => __('2: Intermediate'),
                3 => __('3: Advanced'),
                4 => __('4: Fluent'),
                5 => __('5: Native level')
            );
        }

        if (isset($index)) {
            return $__languagesLevels[$index];
        } else {
            return $__languagesLevels;
        }
    }


    public function smallLevelBar($level)
    {
        $opacity = $opacity = 0.5 + 0.5 * ($level / Language::MAX_LEVEL);
        $size = ($level / Language::MAX_LEVEL) * 100;
        $levelDiv = $this->Html->div(
            null,
            "",
            array(
                'style' => 'opacity:'.$opacity.'; width:'.$size.'%;',
                'class' => 'level'
            )
        );
        $levelDivContainer = $this->Html->div(
            'languageLevel',
            $levelDiv,
            array('title' => $this->getLevelsLabels($level))
        );

        return $levelDivContainer;
    }
}
