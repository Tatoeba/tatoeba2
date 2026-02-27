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
 * @link     https://tatoeba.org
 */
use App\Model\CurrentUser;

$languageName = $this->Languages->codeToNameToFormat($lang);

$title = format(
    __('Sentences in {language}'), 
    array('language' => $languageName)
);

$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div id="annexe_content">
    <?php
    $this->ShowAll->displayShowAllInSelect($lang);
    $this->ShowAll->displayShowOnlyTranslationInSelect($translationLang);
    ?>
</div>

<div id="main_content">

<section class="md-whiteframe-1dp">
    <md-toolbar class="md-hue-2">
        <div class="md-toolbar-tools">
            <?= $this->Pages->formatTitleWithResultCount($this->Paginator, $title, $total, true); ?>

            <?= $this->element('sentences/expand_all_menus_button'); ?>
        </div>
    </md-toolbar>

    <md-content>
    <?php
    if (!empty($results)) {
        $this->Pagination->warnLimitedResults($totalLimit, $total);

        $this->Pagination->display();

        if (!CurrentUser::isMember() || CurrentUser::getSetting('use_new_design')) {
            foreach ($results as $sentence) {
                echo $this->element(
                    'sentences/sentence_and_translations',
                    array(
                        'sentence' => $sentence,
                        'translations' => $sentence->translations,
                        'user' => $sentence->user,
                        'translationLang' => $translationLang
                    )
                );
            }
        } else {
            foreach ($results as $sentence) {
                $this->Sentences->displaySentencesGroup(
                    $sentence
                );
            }
        }

        $this->Pagination->display();
    }
    ?>
    </md-content>
</section>

</div>
