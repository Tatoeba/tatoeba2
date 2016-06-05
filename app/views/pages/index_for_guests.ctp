<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

App::import('Vendor', 'LanguagesLib');

$this->set('title_for_layout', __('Tatoeba: Collection of sentences and translations', true));
?>

<div id="annexe_content">
    <div class="module join-us">
        <?php
        echo $html->tag('h2', __('Want to help?', true));
        echo $html->tag('p', __(
            'We are collecting sentences and their translations. '.
            'You can help us by translating or adding new sentences.', true
        ));
        $registerUrl = $html->url(
            array(
                "controller" => "users",
                "action" => "register"
            )
        );
        ?>
        <md-button class="md-raised md-primary" href="<?= $registerUrl; ?>">
            <?php __('Join the community'); ?>
        </md-button>
    </div>

    <div class="module stats">
        <?php
        echo $html->tag('h2', __('Stats', true));

        $numberOfLanguages = count(LanguagesLib::languagesInTatoeba());

        echo $html->div('stat', format(
            __n('{number} contribution today',
                '{number} contributions today',
                $contribToday,
                true),
            array('number' => $html->tag('strong', $contribToday))
        ));
        echo $html->div('stat', format(
            __n('{number} supported language',
                '{number} supported languages',
                $numberOfLanguages,
                true),
            array('number' => $html->tag('strong', $numberOfLanguages))
        ));
        echo $html->div('stat', format(
            __n('{number} sentence',
                '{number} sentences',
                $numSentences,
                true),
            array('number' => $html->tag('strong', $numSentences))
        ));
        ?>

        <ul class="guest-sentences-stats">
            <?php
            foreach ($stats as $stat) {
                $langCode = $stat['Language']['code'];
                $numberOfSentences = $stat['Language']['sentences'];
                $link = array(
                    "controller" => "sentences",
                    "action" => "show_all_in",
                    $langCode,
                    'none',
                    'none',
                    'indifferent',
                );
                $numberOfSentencesLabel = format(
                    __n('{number} sentence in {lang}',
                        '{number} sentences in {lang}',
                        $numberOfSentences,
                        true),
                    array(
                        'number' => $numberOfSentences,
                        'lang' => $languages->codeToNameAlone($langCode)
                    )
                );
                $languages->stat($langCode, $numberOfSentencesLabel, $link);
            }
            ?>

            <li>
                <?php
                echo $html->link(
                    __('show all languages', true),
                    array(
                        'controller' => 'stats',
                        'action' => 'sentences_by_language'
                    )
                );
                ?>
            </li>
        </ul>
    </div>
</div>

<div id="main_content">
    <?php if(!isset($searchProblem)) { ?>
    <div class="module">
        <?php echo $this->element('random_sentence_header'); ?>
        <div class="random_sentences_set">
            <?php
            $sentence = $random['Sentence'];
            $transcrs = $random['Transcription'];
            $translations = $random['Translation'];
            $sentenceOwner = $random['User'];

            $sentences->displaySentencesGroup(
                $sentence,
                $transcrs,
                $translations,
                $sentenceOwner
            );
            ?>
        </div>
    </div>
    <?php } ?>
</div>
