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

$this->set('title_for_layout', $pages->formatTitle(__('Download sentences', true)));
?>

<div id="annexe_content">
    <?php
        $attentionPlease->tatoebaNeedsYou();
    ?>
    
    <div class="module">
    <h2><?php __('Warning'); ?></h2>
    <p>
    <?php __(
        'The data you will find here will NOT be useful unless you are coding a '.
        'language tool or processing data.'
        ); 
    ?>
    </p>
    <p>
    <?php 
        echo sprintf(__(
                    'If you simply want sentences that you can use to learn a language, '.
                    'check out the <a href="%s">sentence lists</a>. '.
                    'You can build your own, or view the ones that others have created. '. 
                    'The lists can be downloaded and printed.', true), 
                    $html->url(array("controller"=>"sentences_lists"))
                    );
    ?>
    </p>
    </div>
    
    <div class="module">
    <h2><?php __('Creative commons'); ?></h2>
    <p><?php __('These files are released under CC-BY.'); ?></p>
    <a rel="license" href="http://creativecommons.org/licenses/by/2.0/fr/">
    <img alt="Creative Commons License" style="border-width:0"
        src="http://i.creativecommons.org/l/by/2.0/fr/88x31.png" />
    </a>
    <p>
    <?php 
        $explanation = "http://blog.tatoeba.org/2009/12/tatoeba-update-dec-12th-2009.html";
        echo sprintf( __('For those who wonder why we\'re not leaving the data in the public '.
              'domain, some explanation '.
              '<a href="%s">here</a>.',
              true), $explanation); 
    ?>
    <h2><?php __('Questions?'); ?></h2>
    <p>
    <?php
        echo sprintf(
            __(
               'If you have questions or requests, feel free to '.
               '<a href="%s">contact us</a>. ' , true),
                   $html->url(array("controller"=>"pages", "action"=>"contact"))
        );
        __('In general, we answer quickly.'); 
    ?>
    </p>
    </div>
</div>

<div id="main_content">
    <?php
        $download_str =  __('Download', true);
        $downloads_str =  __('Downloads', true);
        $field_struct_str = __('Fields and structure', true);
        $file_desc_str = __('File description', true);
        $sent_id_str = __('Sentence id', true);
        $text_str = __('Text', true);
        $lang_str = __('Lang', true);
        $username_str = __('Username', true);
        $date_added_str = __('Date added', true);
        $date_created_str = __('Date created', true);
        $date_last_mod_str = __('Date last modified', true);
        $trans_id_str = __('Translation id', true);
        $tag_name_str = __('Tag name', true);
        $list_id_str = __('List id', true);
        $list_name_str = __('List name', true);
        $meaning_id_str = __('Meaning id', true);
        $tab_str = __('tab', true);
    ?>
    <div class="module">
        <h2><?php echo $downloads_str; ?></h2>
        <p><strong><?php __('Attention: '); ?></strong>
        <?php __('As of 2014-08-16, the URL to download the '.
               'latest files has changed and the new export files are provided in a compressed format. The old '.
               'URL is still available, but will not contain the latest data.'); 
        ?>
        </p>
        
        <!-- Sentences -->
        <h3><?php __('Sentences'); ?></h3>
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
                echo sprintf(
                    __('Contains all the sentences. Each sentence is associated with a '.
                       'unique id and an <a href="%s">ISO 639-3</a> language code. ',
                       true), $iso_code_list); 
                __('The first file (sentences.tar.bz2) contains this information alone. '.
                'The second file (sentences_detailed.tar.bz2) contains additional fields '.
                'for those who would like to filter the sentences based on the contributor '.
                'who owns the sentence, or the date when it was added or last modified.'); 
            ?>
            </dd>
        </dl>
        
        <!-- Links -->
        <h3><?php __('Links'); ?></h3>
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
        
        
        <!-- Tags -->
        <h3><?php __('Tags'); ?></h3>
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
                $tag_url = $this->Html->url(array(
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
        
        
        <!-- Lists -->
        <h3><?php __('Lists'); ?></h3>
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
            </dd>
            
            <dt><?php echo $file_desc_str; ?></dt>
            <dd>
                <?php
                $list_url = $this->Html->url(array(
                    'controller' => 'sentences_lists',
                    'action' => 'index'
                ));
                echo sprintf(__('Contains the list of <a href="%s">sentence lists</a>.', true), 
                                    $list_url); 
                ?>
            </dd>
        </dl>
          
        <h3><?php __('Sentences in lists'); ?></h3>
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
                    printf(__('Indicates the sentences that are contained by '.
                        'any lists. %s means that sentence #381279 is contained '.
                        'by the list that has an id of 13.', true), $sample_line);
                ?>
            </dd>
        </dl>
            
            
        <!-- Indices -->
        <h3><?php __('Japanese indices'); ?></h3>
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
        
        <!-- Sentences with audio -->
        <h3><?php __('Sentences with audio'); ?></h3>
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
            </dd>
                    
            <dt><?php echo $file_desc_str; ?></dt>
            <dd>
            <?php 
             __('Contains the ids of the sentences, in all languages, for which audio is available.'); ?>
            </dd>  
        </dl>
    </div>
    
    <div class="module">
    <h2><?php __('General information about the files'); ?></h2>
        <p>
        <?php __('The files provided here are updated every <strong>Saturday at 9 a.m.</strong>, '.
        'France time.'); 
        ?>
        </p>
        
        <p>
        <?php $tanaka_url2 = "http://www.edrdg.org/wiki/index.php/Tanaka_Corpus"; 
        echo sprintf( __('Many of the Japanese and English sentences are from the '.
                         '<a href="%s">Tanaka Corpus</a>, which belongs to the public domain.', true), $tanaka_url2); 
        ?>
        </p>
    </div>
</div>
