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

$this->set('title_for_layout', $this->Pages->formatTitle(__('Download sentences')));
?>

<div id="annexe_content">
    <div class="section md-whiteframe-1dp">
    <h2><?php echo __('Note'); ?></h2>
    <p>
    <?php
    __(
        'The data you will find here will NOT be useful unless you are coding a '.
        'language tool or processing data.'
    );
    ?>
    </p>
    <p>
    <?php
    echo format(__(
        'If you simply want sentences that you can use to learn a language, '.
        'check out the <a href="{}">sentence lists</a>. '.
        'You can build your own, or view the ones that others have created. '.
        'The lists can be downloaded and printed.', true),
        $this->Url->build(array("controller"=>"sentences_lists")
    ));
    ?>
    </p>
    </div>

    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('General information about the files'); ?></h2>
        <p>
            <?php
            echo __(
                'The files provided here are updated every <strong>Saturday at 9 a.m.</strong> '.
                '(GMT).'
            );
            ?>
        </p>

        <p>
            <?php $tanaka_url2 = "http://www.edrdg.org/wiki/index.php/Tanaka_Corpus";
            echo format(
                __(
                    'Many of the Japanese and English sentences are from the '.
                    '<a href="{}">Tanaka Corpus</a>, which belongs to the public domain.', true
                ),
                $tanaka_url2
            );
            ?>
        </p>
    </div>
    
    <div class="section md-whiteframe-1dp">
    <h2><?php echo __('Creative commons'); ?></h2>
    <p><?php echo __('These files are released under CC BY 2.0 FR.'); ?></p>
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
        <h2><?= __('Licenses covering audio') ?></h2>
        <p>
            <?= __(
                'The license covering an audio file is chosen by the '.
                'contributor, and is indicated on the page that lists '.
                'the audio files that he or she has contributed.'
            ); ?>
        </p>
    </div>

    <div class="section md-whiteframe-1dp">
    <h2><?php echo __('Questions?'); ?></h2>
    <p>
    <?php
        $firstSentence = format(__(
           'If you have questions or requests, feel free to '.
           '<a href="{}">contact us</a>.' , true),
           $this->Url->build(array("controller"=>"pages", "action"=>"contact")
       ));
        $secondSentence = __('In general, we answer quickly.');
        echo format(
            __('{firstSentence} {secondSentence}'),
            compact('firstSentence', 'secondSentence')
        );
    ?>
    </p>
    </div>
</div>

<div id="main_content">
    <?php
        $download_str =  __('Download');
        $downloads_str =  __('Downloads');
        $field_struct_str = __('Fields and structure');
        $file_desc_str = __('File description');
        $sent_id_str = __('Sentence id');
        $rating_str = __('Rating');
        $text_str = __('Text');
        $lang_str = __('Lang');
        $username_str = __('Username');
        $date_added_str = __('Date added');
        $date_created_str = __('Date created');
        $date_last_mod_str = __('Date last modified');
        $trans_id_str = __('Translation id');
        $tag_name_str = __('Tag name');
        $list_id_str = __('List id');
        $list_name_str = __('List name');
        $list_editable_by = __('Editable by');
        $meaning_id_str = __('Meaning id');
        $skill_level_str = __('Skill level');
        $details_str = __('Details');
        $license_str = __('License');
        $attribution_url_str = __('Attribution URL');
        $tab_str = __('tab');
    ?>
    <div>
        <h1><?php echo $downloads_str; ?></h1>

        <!-- Sentences -->
        <div  class="section md-whiteframe-1dp">
        <h2><?php echo __('Sentences'); ?></h2>
        <dl>
            <dt><?php echo $download_str; ?></dt>
            <dd>
                1. <a href="http://downloads.tatoeba.org/exports/sentences.tar.bz2">
                http://downloads.tatoeba.org/exports/sentences.tar.bz2
                </a>
            </dd>
            <dd>
                2. <a href="http://downloads.tatoeba.org/exports/sentences_detailed.tar.bz2">
                http://downloads.tatoeba.org/exports/sentences_detailed.tar.bz2
                </a>
            </dd>
            <dt><?php echo $field_struct_str; ?></dt>
            <dd>
                1. <span class="param"><?php echo $sent_id_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $lang_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $text_str; ?></span>
            </dd>
            <dd>
                2. <span class="param"><?php echo $sent_id_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $lang_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $text_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $username_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $date_added_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $date_last_mod_str; ?></span>
            </dd>


        <dt><?php echo $file_desc_str; ?></dt>
            <dd>
            <?php 
                $iso_code_list = "http://en.wikipedia.org/wiki/List_of_ISO_639-3_codes";
                echo format(
                    __('Contains all the sentences. Each sentence is associated with a '.
                       'unique id and an <a href="{}">ISO 639-3</a> language code. ',
                       true), $iso_code_list); 
                __('The first file (sentences.tar.bz2) contains this information alone. '.
                'The second file (sentences_detailed.tar.bz2) contains additional fields '.
                'for those who would like to filter the sentences based on the contributor '.
                'who owns the sentence, or the date when it was added or last modified.'); 
            ?>
            </dd>
        </dl>
        </div>

        <!-- Sentences -->
        <div  class="section md-whiteframe-1dp">
        <h2><?php echo __('Sentences (CC0)'); ?></h2>
        <dl>
            <dt><?php echo $download_str; ?></dt>
            <dd>
                <a href="http://downloads.tatoeba.org/exports/sentences_CC0.tar.bz2">
                http://downloads.tatoeba.org/exports/sentences_CC0.tar.bz2
                </a>
            </dd>
            <dt><?php echo $field_struct_str; ?></dt>
            <dd>
                <span class="param"><?php echo $sent_id_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $lang_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $text_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $date_last_mod_str; ?></span>
            </dd>
            
            <dt><?php echo $file_desc_str; ?></dt>
            <dd>
            <?= __('Contains all the sentences available under CC0.') ?>
            </dd>
        </dl>
        </div>

        <!-- Links -->
        <div  class="section md-whiteframe-1dp">
        <h2><?php echo __('Links'); ?></h2>
        <dl>
            <dt><?php echo $download_str; ?></dt>
            <dd>
                <a href="http://downloads.tatoeba.org/exports/links.tar.bz2">
                http://downloads.tatoeba.org/exports/links.tar.bz2
                </a>
            </dd>
            
            <dt><?php echo $field_struct_str; ?></dt>
            <dd>
            <span class="param"><?php echo $sent_id_str; ?></span>
            <span class="symbol">[<?php echo $tab_str; ?>]</span>
            <span class="param"><?php echo $trans_id_str; ?></span>
            </dd>
            
            <dt><?php echo $file_desc_str; ?></dt>
            <dd>
                <?php 
                $sample_line = sprintf(
                    '<span class="param">1</span>'.
                    '<span class="symbol"> [%s] </span>'.
                    '<span class="param">77</span> ',
                    $tab_str
                );
                $sample_line_rev = sprintf(
                    '<span class="param">77</span>'.
                    '<span class="symbol"> [%s] </span>'.
                    '<span class="param">1</span> ',
                    $tab_str
                );

                echo format(
                    __('Contains the links between the sentences. {sampleLinkLine} '.
                    'means that sentence #77 is the translation of sentence #1. '. 
                    'The reciprocal link is also present, so the file will '.
                    'also contain a line that says {sampleLinkLineReversed}.', true), 
                    array(
                        'sampleLinkLine' => $sample_line,
                        'sampleLinkLineReversed' => $sample_line_rev
                    )
                );
                ?>
            </dd>
        </dl>
        </div>

        <!-- Tags -->
        <div  class="section md-whiteframe-1dp">
        <h2><?php echo __('Tags'); ?></h2>
        <dl>
            <dt><?php echo $download_str; ?></dt>
            <dd>
                <a href="http://downloads.tatoeba.org/exports/tags.tar.bz2">
                http://downloads.tatoeba.org/exports/tags.tar.bz2
                </a>
            </dd>
            
            <dt><?php echo $field_struct_str; ?></dt>
            <dd>
                <span class="param"><?php echo $sent_id_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $tag_name_str; ?></span>
            </dd>
            
            <dt><?php echo $file_desc_str; ?></dt>
            <dd>
                <?php
                $tag_url = $this->Url->build(array(
                    'controller' => 'tags',
                    'action' => 'view_all'
                ));
                $sample_line = sprintf(
                    '<span class="param">381279</span>'.
                    '<span class="symbol"> [%s] </span>'.
                    '<span class="param">proverb</span>',
                    $tab_str
                );
                echo format( 
                    __('Contains the list of <a href="{url}">tags</a> associated with '.
                       'each sentence. {sampleTagLine} means that sentence #381279 has '.
                       'been assigned the "proverb" tag.', true),
                    array('url' => $tag_url, 'sampleTagLine' => $sample_line)
                );
                ?>
            </dd>
        </dl>
        </div>

        <!-- Lists -->
        <div  class="section md-whiteframe-1dp">
        <h2><?php echo __('Lists'); ?></h2>
        <dl>
            <dt><?php echo $download_str; ?></dt>
            <dd>
                <a href="http://downloads.tatoeba.org/exports/user_lists.tar.bz2">
                http://downloads.tatoeba.org/exports/user_lists.tar.bz2
                </a>
            </dd>
            <dt><?php echo $field_struct_str; ?></dt>
            <dd>
                <span class="param"><?php echo $list_id_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $username_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $date_created_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $date_last_mod_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $list_name_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $list_editable_by; ?></span>
            </dd>
            
            <dt><?php echo $file_desc_str; ?></dt>
            <dd>
                <?php
                $list_url = $this->Url->build(array(
                    'controller' => 'sentences_lists',
                    'action' => 'index'
                ));
                echo format(__('Contains the list of <a href="{}">sentence lists</a>.'), 
                            $list_url);
                ?>
            </dd>
        </dl>
        </div>

        <div  class="section md-whiteframe-1dp">
        <h2><?php echo __('Sentences in lists'); ?></h2>
        <dl>       
            <dt><?php echo $download_str; ?></dt>   
            <dd>
                <a href="http://downloads.tatoeba.org/exports/sentences_in_lists.tar.bz2">
                http://downloads.tatoeba.org/exports/sentences_in_lists.tar.bz2
                </a>
            </dd>
            
            <dt><?php echo $field_struct_str; ?></dt>
            <dd>
                <span class="param"><?php echo $list_id_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $sent_id_str; ?></span>
            </dd>
            
            <dt><?php echo $file_desc_str; ?></dt>
            <dd>
                <?php
                    $sample_line = sprintf(
                        '<span class="param">13</span>'.
                        '<span class="symbol"> [%s] </span>'.
                        '<span class="param">381279</span> ',
                        $tab_str
                    );
                    echo format(__('Indicates the sentences that are contained by '.
                                   'any lists. {sampleListLine} means that sentence #381279 is contained '.
                                   'by the list that has an id of 13.', true),
                                array('sampleListLine' => $sample_line));
                ?>
            </dd>
        </dl>
        </div>

        <!-- Indices -->
        <div  class="section md-whiteframe-1dp">
        <h2><?php echo __('Japanese indices'); ?></h2>
        <dl>
            <dt><?php echo $download_str; ?></dt>
            <dd>
                <a href="http://downloads.tatoeba.org/exports/jpn_indices.tar.bz2">
                http://downloads.tatoeba.org/exports/jpn_indices.tar.bz2
                </a>
            </dd>
            

            <dt><?php echo $field_struct_str; ?></dt>
            <dd>
                <span class="param"><?php echo $sent_id_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $meaning_id_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $text_str; ?></span>
            </dd>
            
            <dt><?php echo $file_desc_str; ?></dt>
            <dd>
                <?php 
                $tanaka_url = "http://www.edrdg.org/wiki/index.php/Tanaka_Corpus#Current_Format_.28WWWJDIC.29"; 
                echo format(
                    __('Contains the equivalent of the "B lines" in the Tanaka Corpus '. 
                       'file distributed by Jim Breen. See <a href="{url}">this page</a> '.
                       'for the format. Each entry is associated with a pair of '.
                       'Japanese/English sentences. {sentenceId} refers to the id of the '.
                       'Japanese sentence. {meaningId} refers to the id of the English '.
                       'sentence.', true),
                    array(
                        'url' => $tanaka_url,
                        'sentenceId' => '<span class="param">'.$sent_id_str.'</span>',
                        'meaningId'  => '<span class="param">'.$meaning_id_str.'</span>')
                );
                ?>
            </dd>
        </dl>
        </div>

        <!-- Sentences with audio -->
        <div  class="section md-whiteframe-1dp">
        <h2><?php echo __('Sentences with audio'); ?></h2>
        <dl>
            <dt><?php echo $download_str; ?></dt>
            <dd>
            <a href="http://downloads.tatoeba.org/exports/sentences_with_audio.tar.bz2">
             http://downloads.tatoeba.org/exports/sentences_with_audio.tar.bz2
            </a>
            </dd>

            <dt><?php echo $field_struct_str; ?></dt>
            <dd>
            <span class="param"><?php echo $sent_id_str; ?></span>
            <span class="symbol">[<?php echo $tab_str; ?>]</span>
            <span class="param"><?php echo $username_str; ?></span>
            <span class="symbol">[<?php echo $tab_str; ?>]</span>
            <span class="param"><?php echo $license_str; ?></span>
            <span class="symbol">[<?php echo $tab_str; ?>]</span>
            <span class="param"><?php echo $attribution_url_str; ?></span>
            </dd>
                    
            <dt><?php echo $file_desc_str; ?></dt>
            <dd>
            <?php 
            echo __(
                'Contains the ids of the sentences, in all languages, for '.
                'which audio is available. Other fields indicate who recorded '.
                'the audio, its license and a URL to attribute the author. If '.
                'the license field is empty, you may not reuse the audio '.
                'outside the Tatoeba project.'
            ); 
            ?>
            </dd>  
        </dl>
        </div>

        <!-- User skill level per language -->
        <div  class="section md-whiteframe-1dp">
        <h2><?php echo __('User skill level per language'); ?></h2>
        <dl>
            <dt><?php echo $download_str; ?></dt>
            <dd>
            <a href="http://downloads.tatoeba.org/exports/user_languages.tar.bz2">
             http://downloads.tatoeba.org/exports/user_languages.tar.bz2
            </a>
            </dd>

            <dt><?php echo $field_struct_str; ?></dt>
            <dd>
                <span class="param"><?php echo $lang_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $skill_level_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $username_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $details_str; ?></span>
            </dd>

            <dt><?php echo $file_desc_str; ?></dt>
            <dd>
            <?php echo __('Indicates the self-reported skill levels of members in individual languages.'); ?>
            </dd>  
        </dl>
        </div>

        <!-- Users' collections/ratings -->
        <div  class="section md-whiteframe-1dp">
        <h2><?php echo __('Users\' sentence ratings'); ?></h2>
        <dl>
            <dt><?php echo $download_str; ?></dt>
            <dd>
            <a href="http://downloads.tatoeba.org/exports/users_sentences.csv">
             http://downloads.tatoeba.org/exports/users_sentences.csv
            </a>
            </dd>

            <dt><?php echo $field_struct_str; ?></dt>
            <dd>
                <span class="param"><?php echo $username_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $lang_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $sent_id_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $rating_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $date_added_str; ?></span>
                <span class="symbol">[<?php echo $tab_str; ?>]</span>
                <span class="param"><?php echo $date_last_mod_str; ?></span>
            </dd>

            <dt><?php echo $file_desc_str; ?></dt>
            <dd>
            <?php 
            echo __(
                'Contains sentences rated by users. The value of the rating ' .
                'can be -1 (sentence not OK), 0 (undecided or unsure), ' .
                'or 1 (sentence OK). Warning: this data is still experimental.'
            );
            ?>
            </dd>  
        </dl>
        </div>
    </div>
</div>
