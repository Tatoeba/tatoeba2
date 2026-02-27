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
 * @link     https://tatoeba.org
 */
namespace App\View\Helper;

use App\Lib\LanguagesLib;
use App\Model\CurrentUser;
use App\View\Helper\AppHelper;
use Cake\I18n\I18n;
use App\Model\Entity\Language;
use App\Model\Entity\LanguageNameTrait;


/**
 * Helper for languages
 *
 * @category Default
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class LanguagesHelper extends AppHelper
{
    use LanguageNameTrait;

    public $helpers = ['AssetCompress.AssetCompress', 'Html', 'Url', 'Number'];

    /* Memoization of languages code and their localized names */
    private $__languages_alone;

    public function preferredLanguageFilter() {
        if (CurrentUser::isMember()) {
            return CurrentUser::getProfileLanguages();
        } else {
            return $this->request->getSession()->read('last_used_lang');
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
     * Returns array of languages set in the user's profile.
     *
     * @param bool   $withAutoDetection Set to true if "Auto detect" should be one of the options.
     * @param array  $with Additional options
     *
     * @return array
     */

    public function profileLanguagesArray($withAutoDetection = false, $with = [])
    {
        $languages = array_intersect_key(
            $this->onlyLanguagesArray(false),
            array_flip(CurrentUser::getProfileLanguages())
        );

        if (count($languages) > 1 && $withAutoDetection) {
            $with['auto'] = __('Auto detect');
        }
        if (!empty($with)) {
            $languages = array(
                __('Profile languages') => $languages
            );
        }

        return $with + $languages;
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
        $options = ['und' => $this->langAsAlone(__('All languages'))];
        
        return $options + $languages;
    }

    /**
     * Return array of languages in Tatoeba + 'Unknown language'
     *
     * @param Boolean $split Whether the languages should be split into
     *                       'Profile languages' and 'Other languages'.
     * @return array
     */
    public function unknownLanguagesArray($split = true)
    {
        $languages = $this->onlyLanguagesArray($split);
        $options = ['unknown' => __x('dropdown-list', 'Unknown language')];

        return $languages + $options;
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
        $options = ['' => __('other language')];

        return $options + $languages;
    }


    /**
     * Return array of languages, with "None" option.
     *
     * @param $withAllLanguages Include the 'All languages' option too
     * @return array
     */
    public function languagesArrayShowTranslationsIn($withAllLanguages = true)
    {
        $languages = $this->onlyLanguagesArray();
        $options = [
            /* @translators: option used in language selection dropdown for "Show translations in" in advanced search form */
            'none' => __('None'),
        ];
        if ($withAllLanguages) {
            /* @translators: option used in language selection dropdown for "Show translations in" in advanced search form */
            $options['und'] = __x('show-translations-in', 'All languages');
        }

        return $options + $languages;
    }


    /**
     * Return array of languages in which you can search.
     *
     * @param string $anyOption (optional) String for option "Any language" (und)
     * @return array
     */
    public function getSearchableLanguagesArray($anyOption = null)
    {
        $languages = $this->onlyLanguagesArray();
        if (!is_null($anyOption)) {
            $languages = array('und' => $anyOption) + $languages;
        }
        return $languages;
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
        $flagImage = $this->icon($langCode);
        $numberOfSentencesHtml = '<span class="total">' .
                                 $this->Number->format($numberOfSentences) .
                                 '</span>';

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

    private function _iconOptions(&$lang, &$options) {
        if (empty($lang)) {
            $lang = 'unknown';
        }

        if (isset($options['class'])) {
            $options['class'] .= ' language-icon';
        } else {
            $options['class'] = 'language-icon';
        }

        $options["width"] = 30;
        $options["height"] = 20;
    }

    /**
     * Display language icon.
     *
     * @param string $lang    Language code.
     * @param array  $options Options for Html::image().
     *
     * @return string
     */
    public function icon($lang, $options = array())
    {
        $this->_iconOptions($lang, $options);

        $options["alt"] = $lang;
        $options["title"] = $this->codeToNameAlone($lang);
        return $this->Html->image(
            IMG_PATH . 'flags/'.$lang.'.svg',
            $options
        );
    }

    /**
     * Display language icon from SVG sprite.
     *
     * @param string $lang    Language code.
     *
     * @return string
     */
    public function spriteIcon($lang)
    {
        $options = [];
        $this->_iconOptions($lang, $options);

        $svgInner = $this->Html->tag('title', $this->codeToNameAlone($lang));
        $spriteUrl = $this->Url->assetUrl($this->AssetCompress->url('allflags.svg'));
        $svgInner .= $this->Html->tag('use', null, [
            'href' => "$spriteUrl#$lang",
        ]);

        $options['role'] = 'img';
        $options['escape'] = false;
        return $this->Html->tag('svg', $svgInner, $options);
    }

    public function tagWithLang($tag, $lang, $text, $options = array(), $script = '')
    {
        $direction = empty($lang) ? 'auto' : LanguagesLib::getLanguageDirection($lang);
        $options = array_merge(
            array(
                'lang' => LanguagesLib::languageTag($lang, $script),
                'dir'  => $direction,
                'escape' => true,
            ),
            $options
        );
        return $this->Html->tag($tag, $this->_View->safeForAngular($text), $options);
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

        $newLangUrl = $this->Url->build(array(
            'controller' => 'user',
            'action' => 'language'
        ));
        ?>
        <p layout-margin><?= $warningMessage ?></p>

        <div layout="row" layout-align="center center">
            <md-button class="md-raised md-primary" href="<?= $newLangUrl ?>">
                <?= __('Add a language'); ?>
            </md-button>
        </div>
        <?php
    }


    public function getLevelsLabels($index = null)
    {
        if (!isset($__languagesLevels)) {
            $__languagesLevels = array(
                /* @translators: language level */
                '' => __x('level', 'Unspecified'),
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
        if ($level === '') {
            $text = '?';
            $options = ['class' => 'unknownLevel'];
        } else {
            $text = '';
            $opacity = $opacity = 0.5 + 0.5 * ($level / Language::MAX_LEVEL);
            $size = ($level / Language::MAX_LEVEL) * 100;
            $options = [
                'class' => 'level',
                'style' => 'opacity:'.$opacity.'; width:'.$size.'%;',
            ];
        }
        $levelDiv = $this->Html->div(null, $text, $options);
        $levelDivContainer = $this->Html->div(
            'languageLevel',
            $levelDiv,
            array('title' => $this->getLevelsLabels($level))
        );

        return $levelDivContainer;
    }

    public function getInterfaceLanguage()
    {
        $langCode = I18n::getLocale();
        $UiLangs = LanguagesLib::activeUiLanguages();
        return $UiLangs[$langCode][0];
    }

    public function languageExists($lang)
    {
        return LanguagesLib::languageExists($lang);
    }
}
