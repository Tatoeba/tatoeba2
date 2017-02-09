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

$languageName = $this->Languages->codeToNameToFormat($lang);

if ($filterAudioOnly === 'only-with-audio') {
    $title = format(__('All sentences in {language} with audio'),
                array('language' => $languageName));
} else {
    $title = format(__('All sentences in {language}'),
                array('language' => $languageName));
}

$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php
    $this->ShowAll->displayShowAllInSelect($lang);
    $this->ShowAll->displayShowOnlyTranslationInSelect($translationLang);
    $this->ShowAll->displayShowNotTranslatedInto();
    $this->ShowAll->displayFilterOrNotAudioOnly($filterAudioOnly);
    ?>


</div>
<div id="main_content">
    <div class="section">
    <?php
    if (!empty($results)) {

        echo $this->Pages->formatTitleWithResultCount($this->Paginator, $title);

        $paginationUrl = array(
            $lang,
            $translationLang,
            'none',
            $filterAudioOnly,
        );
        $this->Pagination->display($paginationUrl);

        if (!CurrentUser::isMember() || CurrentUser::getSetting('use_new_design')) {
            foreach ($results as $sentence) {
                $translations = isset($sentence['Translation']) ?
                    $sentence['Translation'] :
                    array();
                echo $this->element(
                    'sentences/sentence_and_translations',
                    array(
                        'sentence' => $sentence['Sentence'],
                        'translations' => $translations,
                        'user' => $sentence['User']
                    )
                );
            }
        } else {
            foreach ($results as $sentence) {
                if (!isset($sentence['Translation'])) {
                    $sentence['Translation'] = array();
                }
                $this->Sentences->displaySentencesGroup(
                    $sentence,
                    array('langFilter' => $translationLang)
                );
            }
        }


        $this->Pagination->display($paginationUrl);
    }
    ?>
    </div>
</div>
