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

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->set('title_for_layout', $this->Pages->formatTitle(__('Download sentences')));

// URLs
$iso_code_url = "http://en.wikipedia.org/wiki/List_of_ISO_639-3_codes";
$tanaka_url = "http://www.edrdg.org/wiki/index.php/Tanaka_Corpus#Current_Format_.28WWWJDIC.29";
$tanaka_url2 = "http://www.edrdg.org/wiki/index.php/Tanaka_Corpus";
$download_url = Configure::read('Downloads.url');
$audio_url_1234 = $this->Url->build(
    ['lang' => '', 'controller' => 'audio', 'action' => 'download', '1234'],
    ['fullBase' => true]
);

// Section Headers
/* @translators: header text on Downloads page */
$filename =  __('Filename');
/* @translators: header text on Downloads page */
$format = __('Fields and structure');
/* @translators: header text on Downloads page */
$description = __('File description');

// Field names
/* @translators: field name in Fields and structure on Downloads page */
$sentence_id = __('Sentence id');
/* @translators: field name in Fields and structure on Downloads page */
$sentence_base = __('Base field');
/* @translators: field name in Fields and structure on Downloads page (noun) */
$review = __('Review');
/* @translators: field name in Fields and structure on Downloads page (noun) */
$text = __('Text');
/* @translators: field name in Fields and structure on Downloads page */
$lang = __('Lang');
/* @translators: field name in Fields and structure on Downloads page */
$username = __('Username');
/* @translators: field name in Fields and structure on Downloads page */
$date_added = __('Date added');
/* @translators: field name in Fields and structure on Downloads page */
$date_created = __('Date created');
/* @translators: field name in Fields and structure on Downloads page */
$date_modified = __('Date last modified');
/* @translators: field name in Fields and structure on Downloads page */
$translation_id = __('Translation id');
/* @translators: field name in Fields and structure on Downloads page */
$tag_name = __('Tag name');
/* @translators: field name in Fields and structure on Downloads page */
$list_id = __('List id');
/* @translators: field name in Fields and structure on Downloads page */
$list_name = __('List name');
/* @translators: field name in Fields and structure on Downloads page */
$list_editable_by = __('Editable by');
/* @translators: field name in Fields and structure on Downloads page */
$meaning_id = __('Meaning id');
/* @translators: field name in Fields and structure on Downloads page */
$skill_level = __('Skill level');
/* @translators: field name in Fields and structure on Downloads page */
$details = __('Details');
/* @translators: field name in Fields and structure on Downloads page (noun) */
$license = __('License');
/* @translators: field name in Fields and structure on Downloads page */
$attribution_url = __('Attribution URL');
/* @translators: field name in Fields and structure on Downloads page */
$script = __('Script name');
/* @translators: field name in Fields and structure on Downloads page */
$transcription = __('Transcription');
/* @translators: field name in Fields and structure on Downloads page */
$audio_id = __('Audio id');

// Examples in description
$link_sample = $this->Downloads->fileFormat(['1', '77']);
$link_sample_reversed = $this->Downloads->fileFormat(['77', '1']);
$tag_sample = $this->Downloads->fileFormat(['381279', 'proverb']);
$list_sample = $this->Downloads->fileFormat(['13', '381279']);

// Dropdown for selections
$sentencesOptions = $this->Downloads->createOptions('sentences');
$sentencesDetailedOptions = $this->Downloads->createOptions('sentences_detailed');
$sentencesCC0Options = $this->Downloads->createOptions('sentences_CC0');
$transcriptionsOptions = $this->Downloads->createOptions('transcriptions');
?>

<div id="annexe_content">
    <div class="section md-whiteframe-1dp">
        <?php /* @translators: header text in the Downloads page on the sidebar (noun) */ ?>
        <h2><?= __('Note') ?></h2>
        <p>
            <?= __(
                'The data you will find here will NOT be useful unless you are coding a '.
                'language tool or processing data.'
            ) ?>
        </p>
        <p>
            <?= format(
                __(
                    'If you simply want sentences that you can use to learn a language, '.
                    'check out the <a href="{}">sentence lists</a>. '.
                    'You can build your own, or view the ones that others have created. '.
                    'The lists can be downloaded and printed.'
                ),
                $this->Url->build(array("controller"=>"sentences_lists"))
            ) ?>
        </p>
    </div>

    <div class="section md-whiteframe-1dp">
        <h2><?= __('General information about the files') ?></h2>
        <p>
            <?= format(
                __(
                    'Many of the Japanese and English sentences are from the '.
                    '<a href="{}">Tanaka Corpus</a>, which belongs to the public domain.'
                ),
                $tanaka_url2
            ); ?>
        </p>
    </div>

    <div class="section md-whiteframe-1dp">
        <?php /* @translators: header text in the Downloads page on the sidebar */ ?>
        <h2><?= __('Creative commons') ?></h2>
        <p><?= __('These files are released under CC BY 2.0 FR.') ?></p>
        <a rel="license" href="https://creativecommons.org/licenses/by/2.0/fr/">
            <img alt="Creative Commons License CC-BY" style="border-width:0"
                src="/img/cc-by-2.0-88x31.png" />
        </a>
        <p><?= __('A part of our sentences are also available under CC0 1.0.') ?></p>
        <a rel="license" href="https://creativecommons.org/publicdomain/zero/1.0/legalcode">
            <img alt="Creative Commons License CC0" style="border-width:0"
                src="/img/cc0-1.0-88x31.png" />
        </a>
    </div>

    <div class="section md-whiteframe-1dp">
        <?php /* @translators: header text in the Downloads page on the sidebar */ ?>
        <h2><?= __('Licenses covering audio') ?></h2>
        <p>
            <?= __(
                'The license covering an audio file is chosen by the '.
                'contributor, and is indicated on the page that lists '.
                'the audio files that he or she has contributed.'
            ) ?>
        </p>
    </div>

    <div class="section md-whiteframe-1dp">
        <?php /* @translators: header text in the Downloads page on the sidebar */ ?>
        <h2><?= __('Questions?') ?></h2>
        <p>
            <?= format(
                __(
                   'If you have questions or requests, feel free to '.
                   '<a href="{}">contact us</a>. In general, we answer quickly.'
                ),
                $this->Url->build(array("controller"=>"pages", "action"=>"contact"))
            ) ?>
        </p>
    </div>
</div>

<div id="main_content">
    <?php /* @translators: title of the Downloads page */ ?>
    <h1><?= __('Downloads') ?></h1>

    <?= $this->element('custom_export') ?>

    <div id="section" class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2><?= __('Weekly exports') ?></h2>
            </div>
        </md-toolbar>

        <md-content>
            <div class="weekly-exports-info">
                <md-icon>info</md-icon>
                <?= __(
                    'The files provided below are updated every Saturday at 6:30 a.m. (UTC).'
                ) ?>
            </div>

            <!-- Sentences -->
            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('Sentences') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <?= $this->element(
                        'per_language_files',
                        [
                            'model' => 'sentences',
                            'options' => $sentencesOptions
                        ]
                    ) ?>
                    <dt><?= $description ?></dt>
                    <dd>
                        <?= format(
                            __(
                                'Contains all the sentences in the selected language. '.
                                'Each sentence is associated with a unique id and an '.
                                '<a href="{}">ISO 639-3</a> language code.'
                            ),
                            $iso_code_url
                        ) ?>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd><?= $this->Downloads->fileFormat([$sentence_id, $lang, $text]) ?></dd>
                </dl>
            </div>

            <!-- Sentences detailed-->
            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('Detailed Sentences') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <?= $this->element(
                        'per_language_files',
                        [
                            'model' => 'sentencesDetailed',
                            'options' => $sentencesDetailedOptions
                        ]
                    ) ?>
                    <dt><?= $description ?></dt>
                    <dd>
                        <?= format(
                            __(
                                'Contains additional fields for each sentence '.
                                '(owner name, date created/modified).'
                            )
                        ) ?>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd>
                        <?= $this->Downloads->fileFormat(
                            [$sentence_id, $lang, $text, $username, $date_added, $date_modified]
                        ) ?>
                    </dd>
                </dl>
            </div>

            <!-- Sentences based on id  -->
            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('Original and Translated Sentences') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <dd>
                        <a href="<?= $download_url ?>sentences_base.tar.bz2">sentences_base.tar.bz2</a>
                    </dd>
                    <dt><?= $description ?></dt>
                    <dd>
                        <?= format(__(
                            'Each sentence is listed as original or a translation of another. '
                           .'The "base" field can have the following values:'
                        )) ?>
                        <ul>
                            <li><?= __('zero: The sentence is original, not a translation of another.') ?></li>
                            <li><?= __('greater than zero: The id of the sentence from which it was translated.') ?></li>
                            <li><?= __('\N: Unknown (rare).') ?></li>
                        </ul>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd><?= $this->Downloads->fileFormat([$sentence_id, $sentence_base]) ?></dd>
                </dl>
            </div>

            <!-- Sentences CC0 -->
            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('Sentences (CC0)') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <?= $this->element(
                        'per_language_files',
                        [
                            'model' => 'sentencesCC0',
                            'options' => $sentencesCC0Options
                        ]
                    ) ?>
                    <dt><?= $description ?></dt>
                    <dd>
                    <?= __('Contains all the sentences available under CC0.') ?>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd>
                        <?= $this->Downloads->fileFormat([$sentence_id, $lang, $text, $date_modified]) ?>
                    </dd>
                </dl>
            </div>

            <!-- Links -->
            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('Links') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <dd>
                        <a href="<?= $download_url ?>links.tar.bz2">links.tar.bz2</a>
                    </dd>
                    <dt><?= $description ?></dt>
                    <dd>
                        <?= format(
                            __('Contains the links between the sentences. {sampleLinkLine} '.
                            'means that sentence #77 is the translation of sentence #1. '.
                            'The reciprocal link is also present, so the file will '.
                            'also contain a line that says {sampleLinkLineReversed}.'),
                            [
                                'sampleLinkLine' => $link_sample,
                                'sampleLinkLineReversed' => $link_sample_reversed
                            ]
                        ) ?>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd><?= $this->Downloads->fileFormat([$sentence_id, $translation_id]) ?></dd>
                </dl>
            </div>

            <!-- Tags -->
            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('Tags') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <dd>
                        <a href="<?= $download_url ?>tags.tar.bz2">tags.tar.bz2</a>
                    </dd>
                    <dt><?= $description ?></dt>
                    <dd>
                        <?= format(
                            __('Contains the list of <a href="{url}">tags</a> associated with '.
                               'each sentence. {sampleTagLine} means that sentence #381279 has '.
                               'been assigned the "proverb" tag.'),
                            [
                                'url' => $this->Url->build(['controller' => 'tags', 'action' => 'view_all']),
                                'sampleTagLine' => $tag_sample
                            ]
                        ) ?>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd><?= $this->Downloads->fileFormat([$sentence_id, $tag_name]) ?></dd>
                </dl>
            </div>

            <!-- Lists -->
            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('Lists') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <dd>
                        <a href="<?= $download_url ?>user_lists.tar.bz2">user_lists.tar.bz2</a>
                    </dd>
                    <dt><?= $description ?></dt>
                    <dd>
                        <?= format(
                            __('Contains the list of <a href="{}">sentence lists</a>.'),
                            $this->Url->build(['controller' => 'sentences_lists', 'action' => 'index'])
                        ) ?>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd>
                        <?= $this->Downloads->fileFormat(
                            [$list_id, $username, $date_created, $date_modified, $list_name, $list_editable_by]
                        ) ?>
                    </dd>
                </dl>
            </div>

            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('Sentences in lists') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <dd>
                        <a href="<?= $download_url ?>sentences_in_lists.tar.bz2">
                                sentences_in_lists.tar.bz2
                        </a>
                    </dd>
                    <dt><?= $description ?></dt>
                    <dd>
                        <?= format(
                            __(
                                'Indicates the sentences that are contained by '.
                                'any lists. {sampleListLine} means that sentence #381279 is contained '.
                                'by the list that has an id of 13.'
                            ),
                            ['sampleListLine' => $list_sample]
                        ) ?>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd>
                        <?= $this->Downloads->fileFormat([$list_id, $sentence_id]) ?>
                    </dd>
                </dl>
            </div>

            <!-- Indices -->
            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('Japanese indices') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <dd>
                        <a href="<?= $download_url ?>jpn_indices.tar.bz2">jpn_indices.tar.bz2</a>
                    </dd>
                    <dt><?= $description ?></dt>
                    <dd>
                        <?= format(
                            __(
                                'Contains the equivalent of the "B lines" in the Tanaka Corpus '.
                                'file distributed by Jim Breen. See <a href="{url}">this page</a> '.
                                'for the format. Each entry is associated with a pair of '.
                                'Japanese/English sentences. {sentenceId} refers to the id of the '.
                                'Japanese sentence. {meaningId} refers to the id of the English sentence.'
                            ),
                            [
                                'url' => $tanaka_url,
                                'sentenceId' => '<span class="param">'.$sentence_id.'</span>',
                                'meaningId'  => '<span class="param">'.$meaning_id.'</span>'
                            ]
                        ) ?>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd><?= $this->Downloads->fileFormat([$sentence_id, $meaning_id, $text]) ?></dd>
                </dl>
            </div>

            <!-- Sentences with audio -->
            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('Sentences with audio') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <dd>
                        <a href="<?= $download_url ?>sentences_with_audio.tar.bz2">
                            sentences_with_audio.tar.bz2
                        </a>
                    </dd>
                    <dt><?= $description ?></dt>
                    <dd>
                        <?= __(
                            'Contains the ids of the sentences, in all languages, for '.
                            'which audio is available. Other fields indicate who recorded '.
                            'the audio, its license and a URL to attribute the author. If '.
                            'the license field is empty, you may not reuse the audio '.
                            'outside the Tatoeba project.'
                        ) ?>
                    </dd>
                    <dt><?= __('Downloading audio') ?></dt>
                    <dd>
                        <?= format(
                                __('A single sentence can have one or more audio, each from a '.
                                   'different voice. To download a particular audio, use its audio '.
                                   'id to compute the download URL. For example, to download the '.
                                   'audio with the id 1234, the URL is {url}.'
                                ),
                                ['url' => $this->Html->link($audio_url_1234, $audio_url_1234)]
                        ) ?>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd>
                        <?= $this->Downloads->fileFormat(
                            [$sentence_id, $audio_id, $username, $license, $attribution_url]
                        ) ?>
                    </dd>
                </dl>
            </div>

            <!-- User skill level per language -->
            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('User skill level per language') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <dd>
                        <a href="<?= $download_url ?>user_languages.tar.bz2">user_languages.tar.bz2</a>
                    </dd>
                    <dt><?= $description ?></dt>
                    <dd>
                        <?= __('Indicates the self-reported skill levels of members in individual languages.') ?>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd>
                        <?= $this->Downloads->fileFormat([$lang, $skill_level, $username, $details]) ?>
                    </dd>
                </dl>
            </div>

            <!-- Users' reviews -->
            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('Users\' sentence reviews') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <dd>
                        <a href="<?= $download_url ?>users_sentences.csv">users_sentences.csv</a>
                    </dd>
                    <dt><?= $description ?></dt>
                    <dd>
                        <?= __(
                            'Contains sentences reviewed by users. The value of the review ' .
                            'can be -1 (sentence not OK), 0 (undecided or unsure), ' .
                            'or 1 (sentence OK). Warning: this data is still experimental.'
                        ) ?>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd>
                        <?= $this->Downloads->fileFormat(
                            [$username, $sentence_id, $review, $date_added, $date_modified]
                        ) ?>
                    </dd>
                </dl>
            </div>

            <!-- Transcriptions -->
            <div class="section weekly-export">
                <?php /* @translators: section title in the Downloads page */ ?>
                <h2><?= __('Transcriptions') ?></h2>
                <dl>
                    <dt><?= $filename ?></dt>
                    <?= $this->element(
                        'per_language_files',
                        [
                            'model' => 'transcriptions',
                            'options' => $transcriptionsOptions
                        ]
                    ) ?>
                    <dt><?= $description ?></dt>
                    <dd>
                        <?= __(
                            'Contains all transcriptions in auxiliary or alternative scripts. '.
                            'A username associated with a transcription indicates the user '.
                            'who last reviewed and possibly modified it. A transcription '.
                            'without a username has not been marked as reviewed. '.
                            'The script name is defined according to the ISO 15924 standard.'
                        ) ?>
                    </dd>
                    <dt><?= $format ?></dt>
                    <dd>
                        <?= $this->Downloads->fileFormat(
                            [$sentence_id, $lang, $script, $username, $transcription]
                        ) ?>
                    </dd>
                </dl>
            </div>
        </md-content>
    </div>
</div>
