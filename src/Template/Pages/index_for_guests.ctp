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
 * @link     https://tatoeba.org
 */

$this->set('title_for_layout', __('Tatoeba: Collection of sentences and translations'));
$statsUrl = $this->Url->build([
    'controller' => 'stats',
    'action' => 'sentences_by_language'
]);
?>

<div layout="row" layout-align="center center">
<div layout="column" flex="80">

<?php if(!isset($searchProblem)) { ?>
<div class="section">
    <h2><?= __('Random sentence'); ?></h2>
    <div class="random_sentences_set">
        <div id="random_sentence_display">
            <?php
            $sentence = $random;
            $translations = $random->translations;
            $sentenceOwner = $random->user;

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

<div layout="row" layout-margin="">
    <div class="section" md-whiteframe="1" flex>
        <?php
        echo $this->Html->tag('h2', __('Want to help?'));
        echo $this->Html->tag('p', __(
            'We are collecting sentences and their translations. '.
            'You can help us by translating or adding new sentences.', true
        ));
        $registerUrl = $this->Url->build(
            array(
                "controller" => "users",
                "action" => "register"
            )
        );
        ?>
        <div layout="row" layout-align="center center">
            <md-button class="md-primary" href="<?= $registerUrl; ?>">
                <?php echo __('Join the community'); ?>
                <md-icon>keyboard_arrow_right</md-icon>
            </md-button>
        </div>
    </div>

    <div class="section" md-whiteframe="1" flex>
        <?php
        echo $this->Html->tag('h2', __('Stats'));

        echo $this->Html->div('stat', format(
            __n('{number} contribution today',
                '{number} contributions today',
                $contribToday,
                true),
            ['number' => $this->Html->tag('strong', $this->Number->format($contribToday))]
        ));
        echo $this->Html->div('stat', format(
            __n('{number} supported language',
                '{number} supported languages',
                $numberOfLanguages,
                true),
            ['number' => $this->Html->tag('strong', $this->Number->format($numberOfLanguages))]
        ));
        echo $this->Html->div('stat', format(
            __n('{number} sentence',
                '{number} sentences',
                $numSentences,
                true),
            ['number' => $this->Html->tag('strong', $this->Number->format($numSentences))]
        ));
        ?>

        <div layout="row" layout-align="center center">
            <md-button class="md-primary" href="<?= $statsUrl ?>">
                <?= __('stats per languages') ?>
                <md-icon>keyboard_arrow_right</md-icon>
            </md-button>
        </div>
    </div>
</div>

</div>
</div>
