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
 
 $this->pageTitle = 'Tatoeba ' . __('Search sentences', true);
 ?>
<div id="annexe_content">
    <?php
    if (isset($mostFrequentWords) 
        && count($mostFrequentWords) > 0 
        && $resultsInfo['sentencesCount'] > 0
    ) {
            ?>
            <div class="module">
                <h2><?php __('Most frequent words in the target language'); ?></h2>
                <div id="mostFrequentWords">
                    <?php
                    foreach ($mostFrequentWords as $word) {
                        // TODO hack spotted css in the html
                        echo '<span style="font-size:'.$word['fontSize'].'%" title="'.$word['details'].'">';
                        echo $word['word'];
                        echo '</span> ';
                    }
                    ?>
                </div>
            </div>
    <?php
    }
    ?>
    
    
    <div class="module">
        <h2>
        <?php 
        __('Tips'); 
        ?>
        </h2>
        <p>
            <?php 
            __(
                'If you want to do an exact search, start your query with "=". '.
                'For instance: =singing.'
            ); 
            ?>
        </p>
    </div>
</div>
<div id="main_content">
    <div class="module">
    <?php
    // ?!!!?!!! gné ??!
    if (isset($query)) { 
        $query = stripslashes($query);
        
        if (isset($results)) {
            
            
            if (count($results) > 0) {
                
                $urls = $this->params['url']; $getv = "";
                foreach($urls as $key=>$value)
                {
                    if($key == 'url') continue; // we need to ignor the url field
                    $getv .= urlencode($key)."=".urlencode($value)."&"; // making the passing parameters
                }
                $getv = substr_replace($getv ,"",-1); // remove the last char '&'
                $paginator->options(array('url' => array("?"=>$getv)));
                
                ?>
                <h2>
                <?php 
                echo sprintf(
                    __('Search : %s', true),
                    htmlentities($query, ENT_QUOTES, 'UTF-8')
                );
                
                echo ' ';
                echo $paginator->counter(
                    array(
                        'format' => __('(%count% results)', true)
                    )
                ); 
                ?>
                </h2>
                
                
                <div class="paging">
                <?php 
                echo $paginator->prev(
                    '<< '.__('previous', true), 
                    array(), 
                    null, 
                    array('class'=>'disabled')
                ); 
                ?>
                <?php echo $paginator->numbers(array('separator' => '')); ?>
                <?php 
                echo $paginator->next(
                    __('next', true).' >>',
                    array(),
                    null, 
                    array('class'=>'disabled')
                ); 
                ?>
                </div>
                
                
                
                <?php
                foreach ($results as $sentence) {
                    $sentences->displaySentencesGroup(
                        $sentence['Sentence'], 
                        $sentence['Translations'], 
                        $sentence['User'],
                        $sentence['IndirectTranslations']
                    );
                }
                ?>
                
                
                <div class="paging">
                <?php 
                echo $paginator->prev(
                    '<< '.__('previous', true), 
                    array(), 
                    null, 
                    array('class'=>'disabled')
                ); 
                ?>
                <?php echo $paginator->numbers(array('separator' => '')); ?>
                <?php 
                echo $paginator->next(
                    __('next', true).' >>',
                    array(),
                    null, 
                    array('class'=>'disabled')
                ); 
                ?>
                </div>
                
                
                
                <?php
            } else {
                
                echo '<h2>';
                echo sprintf(
                    __('Add a sentence containing %s', true), 
                    htmlentities($query, ENT_QUOTES, 'UTF-8')
                );
                echo '</h2>';
                
                echo '<p>';
                __(
                    'There is no result for this search (yet) but you '.
                    'can help us feeding the corpus with new vocabulary!'
                );
                echo '</p>';
                
                if ($session->read('Auth.User.id')) { 
                    
                    echo '<p>';
                    __(
                        'Feel free to submit a sentence with '.
                        'the words you were searching.'
                    );
                    echo '</p>';
                    
                    echo $form->create(
                        'Sentence', 
                        array("action" => "add", "id" => "newSentence")
                    );
                    echo $form->input(
                        'text', 
                        array(
                            "label" => __('Sentence : ', true),
                            "type" => "text"
                        )
                    );
                    
                    $langArray = $languages->translationsArray();
                    $preSelectedLang = $session->read('contribute_lang');

                    if (empty($preSelectedLang)) {
                        $preSelectedLang = 'auto';
                    }
                    
                    echo '<div class="languageSelection">';
                    echo $form->select(
                        'contributionLang',
                        $langArray,
                        $preSelectedLang,
                        array("class"=>"translationLang"),
                        false
                    );
                    echo '</div>';
                    echo $form->end('OK');
                    
                    
                } else {
                
                    __('If you are interested, please register.');
                    
                    echo $html->link(
                        'register',
                        array("controller" => "users", "action" => "register"),
                        array("class"=>"registerButton")
                    );
                    
                }
                
            }

        } else {
            __('No results for this search');
        }
    }
    ?>
    </div>
</div>

