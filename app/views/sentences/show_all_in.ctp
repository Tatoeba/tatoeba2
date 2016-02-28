<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$languageName = $languages->codeToNameToFormat($lang);

if ( $filterAudioOnly == 'Yes' ) {
    $title = format(__('{language} sentences with audio', true),
        	    array('language' => $languageName));
} else {
    $title = format(__('{language} sentences', true),
        	    array('language' => $languageName));
}
if (!empty($notTranslatedInto) && $notTranslatedInto != 'none') {
    if ($notTranslatedInto == 'und') {
        $notTranslatedIntoName = __('any language', true);
    } else {
        $notTranslatedIntoName = $languages->codeToNameToFormat($notTranslatedInto);
    }
    if ( $filterAudioOnly == 'Yes' ) {
        $title = format(
            __('{language} sentences with audio that are not translated into {translationLanguage}', true),
            array('language' => $languageName, 'translationLanguage' => $notTranslatedIntoName));
    } else {
        $title = format(
            __('{language} sentences that are not translated into {translationLanguage}', true),
            array('language' => $languageName, 'translationLanguage' => $notTranslatedIntoName));
    }
}

$this->set('title_for_layout', $pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php
    $showAll->displayShowAllInSelect($lang);
    $showAll->displayShowOnlyTranslationInSelect($translationLang);
    $showAll->displayShowNotTranslatedInto($notTranslatedInto);
    $showAll->displayFilterOrNotAudioOnly($filterAudioOnly);
    ?>


</div> 
<div id="main_content">
    <div class="module">
    <?php
    if (!empty($results)) {

        echo $this->Pages->formatTitleWithResultCount($paginator, $title);

        $paginationUrl = array(
            $lang,
            $translationLang,
            $notTranslatedInto,
            $filterAudioOnly,
        );
        $pagination->display($paginationUrl);

        foreach ($results as $sentence) {
            $translations = isset($sentence['Translation']) ?
                            $sentence['Translation'] :
                            array();
            $sentences->displaySentencesGroup(
                $sentence['Sentence'], 
                $sentence['Transcription'],
                $translations,
                $sentence['User'],
                array('langFilter' => $translationLang)
            );
        }
        
        $pagination->display($paginationUrl);
    } 
    ?>
    </div>
</div>
