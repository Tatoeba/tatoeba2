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

$this->pageTitle = 'Tatoeba - ' . __('Download sentences', true);
?>

<div id="annexe_content">
    <?php
    $attentionPlease->tatoebaNeedsYou();
    ?>
    
    <div class="module">
        <h2>Warning</h2>
        <p>
            The data you will find here will NOT be useful unless you are coding a
            language tool or doing some work on data processing.
        </p>
        <p>
            If you want data that you can use as a humble language learner, you can
            check out the 
            <?php 
            echo $html->link(
                'lists section', array("controller"=>"sentences_lists")
            );
            ?> 
            where you can build your own lists of sentences or view others' lists 
            and print them.
        </p>
    </div>
    
    <div class="module">
        <h2>Creative commons</h2>
        <p>These files are released under CC-BY.</p>
        <a rel="license" href="http://creativecommons.org/licenses/by/2.0/fr/">
            <img alt="Creative Commons License" style="border-width:0"
                src="http://i.creativecommons.org/l/by/2.0/fr/88x31.png" />
        </a>
    
        <p>
            For those who wonder why we're not leaving the data in the public
            domain, some explanations
            <a href="http://blog.tatoeba.org/2009/12/tatoeba-update-dec-12th-2009.html">
            here</a>.
        </p>
    </div>
    
    <div class="module">
        <h2>Questions?</h2>
        <p>
            If you have questions or requests, feel free to 
            <?php
            echo $html->link(
                "contact us", array("controller"=>"pages", "action"=>"contact")
            );
            ?>. In general we answer quickly.
        </p>
    </div>
</div>

<div id="main_content">
    
    <div class="module">
        <h2>Downloads</h2>
        
        <!-- Sentences -->
        <h3>Sentences</h3>
        <dl>
            <dt>Download</dt>
            <dd>
                1. <a href="http://tatoeba.org/files/downloads/sentences.csv">
                http://tatoeba.org/files/downloads/sentences.csv
                </a>
            </dd>
            <dd>
                2. <a href="http://tatoeba.org/files/downloads/sentences_detailed.csv">
                http://tatoeba.org/files/downloads/sentences_detailed.csv
                </a>
            </dd>
            <dt>Fields and structure</dt>
            <dd>
                1. <span class="param">id</span>
                <span class="symbol">[tab]</span>
                <span class="param">lang</span>
                <span class="symbol">[tab]</span>
                <span class="param">text</span>
            </dd>
            <dd>
                2. <span class="param">id</span>
                <span class="symbol">[tab]</span>
                <span class="param">lang</span>
                <span class="symbol">[tab]</span>
                <span class="param">text</span>
                <span class="symbol">[tab]</span>
                <span class="param">username</span>
                <span class="symbol">[tab]</span>
                <span class="param">date_added</span>
                <span class="symbol">[tab]</span>
                <span class="param">date_last_modified</span>                
            </dd>


            <dt>Description</dt>
            <dd>
                Contains all the sentences. Each sentence is associated to a
                unique id and a language code 
                (<a href="http://en.wikipedia.org/wiki/List_of_ISO_639-3_codes">ISO 639-3</a>).
                <br/>
                We provide two files. The first file (sentences.csv) only contains the minimum.
                The second file (sentences_detailed.csv) contains more information, for those 
                who would like to filter the sentences based, for instance, on the contributor 
                who owns the sentence or on the date when it was added.
            </dd>
        </dl>
        
        <!-- Links -->
        <h3>Links</h3>
        <dl>
            <dt>Download</dt>
            <dd>
                <a href="http://tatoeba.org/files/downloads/links.csv">
                http://tatoeba.org/files/downloads/links.csv
                </a>
            </dd>
            
            <dt>Fields and structure</dt>
            <dd>
                <span class="param">sentence_id</span>
                <span class="symbol">[tab]</span>
                <span class="param">translation_id</span>
            </dd>
            
            <dt>Description</dt>
            <dd>
                Contains the links between the sentences. 
                <span class="param">1</span>
                <span class="symbol">[tab]</span>
                <span class="param">77</span> 
                means that sentence nº77 is the translation of sentence nº1. 
                The reciprocal link is also present. 
                In other words, you will also have a line that says
                <span class="param">77</span>
                <span class="symbol">[tab]</span>
                <span class="param">1</span>.
            </dd>
        </dl>
        
        
        <!-- Tags -->
        <h3>Tags</h3>
        <dl>
            <dt>Download</dt>
            <dd>
                <a href="http://tatoeba.org/files/downloads/tags.csv">
                http://tatoeba.org/files/downloads/tags.csv
                </a>
            </dd>
            
            <dt>Fields and structure</dt>
            <dd>
                <span class="param">sentence_id</span>
                <span class="symbol">[tab]</span>
                <span class="param">tag_name</span>
            </dd>
            
            <dt>Description</dt>
            <dd>
                Contains the list of tags associated to each sentence. 
                <span class="param">381279</span>
                <span class="symbol">[tab]</span>
                <span class="param">proverb</span> 
                means that sentence nº381279 has been tagged with "proverb".
            </dd>
        </dl>
        
        
        <!-- Lists -->
        <h3>Lists</h3>
        <dl>
            <dt>Download</dt>
            <dd>
                <a href="http://tatoeba.org/files/downloads/user_lists.csv">
                http://tatoeba.org/files/downloads/user_lists.csv
                </a>
            </dd>
            <dt>Fields and structure</dt>
            <dd>
                <span class="param">id</span>
                <span class="symbol">[tab]</span>
                <span class="param">username</span>
                <span class="symbol">[tab]</span>
                <span class="param">date_created</span>
                <span class="symbol">[tab]</span>
                <span class="param">date_modified</span>
                <span class="symbol">[tab]</span>
                <span class="param">list_name</span>
            </dd>
            
            <dt>Description</dt>
            <dd>
                Contains the list of <a href="http://tatoeba.org/sentences_lists/index">
                lists</a> created.
            </dd>
          </dl>
          
          <h3>Sentences in lists</h3>
          <dl>       
            <dt>Download</dt>   
            <dd>
                <a href="http://tatoeba.org/files/downloads/sentences_in_lists.csv">
                http://tatoeba.org/files/downloads/sentences_in_lists.csv
                </a>
            </dd>
            
            <dt>Fields and structure</dt>
            <dd>
                <span class="param">list_id</span>
                <span class="symbol">[tab]</span>
                <span class="param">sentence_id</span>
            </dd>
            
            <dt>Description</dt>
            <dd>
                Indicates the sentences that are in each of the lists. 
                <span class="param">13</span>
                <span class="symbol">[tab]</span>
                <span class="param">381279</span> 
                means that sentence nº381279 is part of the list of id 13.
            </dd>
        </dl>
            
            
        <!-- Indices -->
        <h3>Japanese indices</h3>
        <dl>
            <dt>Download</dt>
            <dd>
                <a href="http://tatoeba.org/files/downloads/jpn_indices.csv">
                http://tatoeba.org/files/downloads/jpn_indices.csv
                </a>
            </dd>
            

            <dt>Fields and structure</dt>
            <dd>
                <span class="param">sentence_id</span>
                <span class="symbol">[tab]</span>
                <span class="param">meaning_id</span>
                <span class="symbol">[tab]</span>
                <span class="param">text</span>
            </dd>
            
            <dt>Description</dt>
            <dd>
                Contains the equivalent of the "B lines" in the file of the 
                Tanaka Corpus distributed by Jim Breen. See 
                <a href="http://www.edrdg.org/wiki/index.php/Tanaka_Corpus#Current_Format_.28WWWJDIC.29">
                this page</a> to learn the format. 
                Each entry is associated to a pair of Japanese/English 
                sentences. <span class="param">sentence_id</span> refers to the id 
                of the Japanese sentence. <span class="param">meaning_id</span> 
                refers to the id of the English sentence.
            </dd>
        </dl>
        
    </div>
    
    <div class="module">
        <h2>General information about the files</h2>
        <p>
        The files provided here are updated every <strong>Saturday at 9AM</strong>,
        France time.
        </p>
        
        <p>
        Most of the Japanese and English sentences are from the 
        <a href="http://www.edrdg.org/wiki/index.php/Tanaka_Corpus">Tanaka Corpus</a>,
        which belongs to the public domain.
        </p>
    </div>
</div>
