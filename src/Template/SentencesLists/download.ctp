<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

/**
 * Page for people to export lists.
 *
 * @category Wall
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */

$this->Html->script('sentences_lists/download.ctrl.js', ['block' => 'scriptBottom']);
$this->set('title_for_layout', $this->Pages->formatTitle(__('Download list: ') . $listName));
?>
<div id="annexe_content">
    <?php $this->Lists->displayListsLinks(); ?>

    <div class="section md-whiteframe-1dp">
    <?php /* @translators: header text in the side bar of the download list page */ ?>
    <h2><?php echo __('Actions'); ?></h2>
    <ul class="sentencesListActions">
    <?php
        $this->Lists->displayBackToListLink($listId);
    ?>
    </ul>
    </div>
</div>

<div id="main_content">
    <div class="section md-whiteframe-1dp">
    <h2><?= $this->safeForAngular($listName) ?></h2>

    <?php /* @translators: subheader text in the download list page (noun) */ ?>
    <h3><?php echo __x('header', 'Download'); ?></h3>

    <div id="SentencesListExportToCsvForm" ng-controller="SentencesListsDownloadCtrl">
    <table>
        <tr>
            <td><?php echo __('File format'); ?></td>
            <td colspan="2">
            <?php
            echo $this->Form->select(
                'FileFormat',
                array(
                    'txt' => __('Raw text file'),
                    'tsv' => __('Tab-separated file'),
                    'shtooka' => __('List for the Shtooka recorder'),
                ),
                array(
                    'empty' => false,
                    'ng-model' => 'format',
                    'ng-init' => 'format = "txt"',
                )
            );
            ?>
            </td>
            <td></td>
        </tr>

        <tr ng-show="format === 'tsv'">
            <td><?php echo __('Fields and structure'); ?></td>
            <td colspan="2">
                <?= $this->Downloads->fileFormat([
                    __('Sentence id'),
                    __('Text'),
                    __('Translation'),
                ]) ?>
            </td>
        </tr>

        <tr ng-show="format === 'txt' || format === 'tsv'">
            <td><?php echo __('Translation (optional)'); ?></td>
            <td>
            <?php
            $langArray = $this->Languages->onlyLanguagesArray();
            echo $this->element(
                'language_dropdown',
                array(
                    'name' => 'TranslationsLang',
                    'languages' => $langArray,
                    /* @translators: placeholder in language dropdown of list download page */
                    'placeholder' => __('None'),
                    'selectedLanguage' => 'trans_lang',
                )
            );
            ?>
            </td>
            <td>
            <?php
            $image = $this->Html->image(
                'anki-logo.png',
                array(
                    'alt' => 'Anki',
                    'title' => 'Anki'
                )
            );
            $link = $this->Html->link(
                $image,
                'http://www.ichi2.net/anki/',
                array(
                    "escape" => false
                )
            );
            echo format(
                __(
                    'If you select a language, the translation of each sentence into that language '.
                    '(if it exists) will be written to your output. '.
                    'You can then import the file to produce a deck of flash cards, using the {Anki} program.', true
                ),
                array('Anki' => $link)
            );
            ?>
            </td>
        </tr>

        <tr ng-cloak>
            <td></td>

            <td id="downloadButton">
                <md-button ng-click="addListExport(<?= $listId ?>)"
                           ng-disabled="preparingDownload"
                           class="md-raised md-primary">
                   <?php /* @translators: button to download on the download list page (verb) */ ?>
                    <span><?php echo __x('button', 'Download'); ?></span>
                </md-button>
                <md-progress-circular md-diameter="16" ng-if="preparingDownload"/>
                </md-progress-circular>
            </td>

            <td>
                <span ng-if="preparingDownload"><?= __('Preparing download, please wait.'); ?></span>
                <span ng-if="export.status == 'failed'"><?= __('Failed to prepare download, please try again.'); ?></span>
            </td>
        </tr>
    </table>
    </div>

    </div>

</div>
