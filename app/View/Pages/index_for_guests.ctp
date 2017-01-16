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

App::import('Lib, 'LanguagesLib');

$this->set('title_for_layout', __('Tatoeba: Collection of sentences and translations'));
?>

<div id="annexe_content">
    <div class="module join-us">
        <?php
        echo $this->Html->tag('h2', __('Want to help?'));
        echo $this->Html->tag('p', __(
            'We are collecting sentences and their translations. '.
            'You can help us by translating or adding new sentences.', true
        ));
        $registerUrl = $this->Html->url(
            array(
                "controller" => "users",
                "action" => "register"
            )
        );
        ?>
        <md-button class="md-raised md-primary" href="<?= $registerUrl; ?>">
            <?php echo __('Join the community'); ?>
        </md-button>
    </div>

    <div class="module stats">
        <?php
        echo $this->Html->tag('h2', __('Stats'));

        $numberOfLanguages = count(LanguagesLib::languagesInTatoeba());

        echo $this->Html->div('stat', format(
            __n('{number} contribution today',
                '{number} contributions today',
                $contribToday,
                true),
            array('number' => $this->Html->tag('strong', $contribToday))
        ));
        echo $this->Html->div('stat', format(
            __n('{number} supported language',
                '{number} supported languages',
                $numberOfLanguages,
                true),
            array('number' => $this->Html->tag('strong', $numberOfLanguages))
        ));
        echo $this->Html->div('stat', format(
            __n('{number} sentence',
                '{number} sentences',
                $numSentences,
                true),
            array('number' => $this->Html->tag('strong', $numSentences))
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
                        'lang' => $this->Languages->codeToNameAlone($langCode)
                    )
                );
                $this->Languages->stat($langCode, $numberOfSentencesLabel, $link);
            }
            ?>

            <li>
                <?php
                echo $this->Html->link(
                    __('show all languages'),
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
    <div class="section">
        <h2><? echo __('Random sentence'); ?></h2>
        <div class="random_sentences_set">
            <div id="random_sentence_display">
                <?php
                $sentence = $random['Sentence'];
                $translations = $random['Translation'];
                $sentenceOwner = $random['User'];

                echo $this->element(
                    'sentences/sentence_and_translations',
                    array(
                        'sentence' => $sentence,
                        'translations' => $translations,
                        'user' => $sentenceOwner
                    )
                );
                ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
