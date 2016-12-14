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

$this->set('title_for_layout', __('Tatoeba: Collection of sentences and translations', true));

$selectedLanguage = $session->read('random_lang_selected');
?>
<div id="annexe_content">
    <?php 
    echo $this->element('sentences_statistics', array(
        'stats' => $stats,
        'numSentences' => $numSentences,
        'cache' => array(
            'time' => '+15 minutes',
            'key' => Configure::read('Config.language')
        )
    ));
    ?>
        
    <div class="module">
        <h2><?php __('Latest messages'); ?></h2>
        <?php
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
    <?php if(!isset($searchProblem) && !$hideRandomSentence) { ?>
        <div class="module">
            <?php echo $this->element('random_sentence_header'); ?>
            <div class="random_sentences_set">
                <md-progress-circular md-mode="indeterminate" class="block-loader" id="random-progress" style="display: none;"></md-progress-circular>
                <div id="random_sentence_display">
                    <?php
                    $sentences->displaySentencesGroup($random);
                    ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="section">
        <h2>
        <?php
        __('Latest contributions');

        echo $html->link(
            __('show more...', true),
            array(
                'controller' => 'contributions',
                'action' => 'latest'
            ),
            array(
                'class' => 'titleAnnexeLink'
            )
        );
        ?>
        </h2>
        <?php echo $this->element('latest_contributions'); ?>
    </div>
    <div class="section">
        <h2>
        <?php
        __('Latest comments');

        echo $html->link(
            __('show more...', true),
            array(
                'controller' => 'sentence_comments'
            ),
            array(
                'class' => 'titleAnnexeLink'
            )
        );
        ?>
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

