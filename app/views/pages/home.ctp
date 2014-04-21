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

$this->pageTitle = __('Tatoeba: Collecting example sentences', true);
$html->meta(
    'description', 
    __(
        "Search example sentences translated into many languages. ".
        "Add and translate your own sentences. ".
        "It's collaborative, open, free, and even addictive.",
        true
    ), 
    array(), 
    false
);

$selectedLanguage = $session->read('random_lang_selected');
?>
<div id="annexe_content">    
    <?php
    if (!$isLogged) {
        echo $this->element('join_us', array(
            'cache' => array(
                'time' => '+1 day',
                'key' => Configure::read('Config.language')
            )
        ));
    }
    ?>
    
    
    <?php 
    echo $this->element('sentences_statistics', array(
        'cache' => array(
            'time' => '+15 minutes',
            'key' => Configure::read('Config.language')
        )
    ));
    ?>
    
    <?php
    $attentionPlease->tatoebaNeedsYou();
    ?>
        
    <div class="module">
        <h2><?php __('Latest messages'); ?></h2>
        <?php 
        // TODO to extract
        foreach ($latestMessages as $message) {

            $messageOwner = $message['User']['username'];
            $messageContent = $message['Wall']['content'];
            $messageDate = $message['Wall']['date'];
            $messageId = $message['Wall']['id'];
            
            $wall->messagePreview(
                $messageId, $messageOwner, $messageContent, $messageDate
            );
            
        }
        ?>
    </div>
    
</div>

<div id="main_content">

    <div class="module">
        <?php echo $this->element('random_sentence_header'); ?>
        <div class="random_sentences_set">
        <?php
        $sentence = $random['Sentence'];
        $sentenceOwner = $random['User'];

        $sentences->displaySentencesGroup(
            $sentence, 
            $translations, 
            $sentenceOwner,
            $indirectTranslations
        );
        ?>
        </div>
    </div>

    <div class="module">
        <h2>
            <?php __('Latest contributions'); ?> 
            <span class="annexe">
                (
                    <?php
                    echo $html->link(
                        __('show more...', true),
                        array(
                            'controller' => 'contributions',
                            'action' => 'latest'
                        )
                    ); 
                    ?>
                ) (
                    <?php 
                    echo $html->link(
                        __('show activity timeline', true),
                        array(
                            "controller"=>"contributions",
                            "action"=>"activity_timeline"
                        )
                    );
                    ?>
                )
            </span>
        </h2>
            <?php echo $this->element('latest_contributions'); ?>
    </div>
    <div class="module">
        <h2>
            <?php __('Latest comments'); ?>
            <span class="annexe">
                (
                    <?php
                    echo $html->link(
                        __('show more...', true),
                        array("controller"=>"sentence_comments")
                    ); 
                    ?>
                )
            </span>
        </h2>
        <?php
        echo $this->element(
            'latest_sentence_comments',
            array(
                'sentenceComments' => $sentenceComments,
                'commentsPermissions' => $commentsPermissions
            )
        ); 
        ?>
    </div>
</div>

