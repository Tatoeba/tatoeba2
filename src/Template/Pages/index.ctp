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

$this->set('title_for_layout', __('Tatoeba: Collection of sentences and translations'));

$selectedLanguage = $this->request->getSession()->read('random_lang_selected');
?>
<div id="annexe_content">
    <?= $this->element('stats/homepage_stats', [
        'contribToday' => $contribToday,
        'numberOfLanguages' => $numberOfLanguages,
        'numSentences' => $numSentences,
        'cache' => array(
            'time' => '+15 minutes',
            'key' => Configure::read('Config.language')
        )
    ]); ?>
        
    <md-list class="annexe-menu md-whiteframe-1dp">
        <md-subheader><?= __('Latest messages'); ?></md-subheader>

        <?php
        foreach ($latestMessages as $message) {
            $messageOwner = $message->user->username;
            $messageContent = $message->content;
            $messageDate = $message->date;
            $messageId = $message->id;
            ?>
            <md-list-item>
                <p>
                <?php 
                $this->Wall->messagePreview(
                    $messageId, $messageOwner, $messageContent, $messageDate
                );
                ?>
                </p>
            </md-list-item>
            <?php            
        }
        ?>
    </md-list>
    
</div>

<div id="main_content">
    <?php if(!isset($searchProblem) && !$hideRandomSentence) { ?>
        <div class="section">
            <?php echo $this->element('random_sentence_header'); ?>
            <div class="random_sentences_set">
                <md-progress-circular md-mode="indeterminate" class="block-loader" id="random-progress" style="display: none;"></md-progress-circular>
                <div id="random_sentence_display" class="md-whiteframe-1dp">
                    <?php
                    $this->Sentences->displaySentencesGroup($random);
                    ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="section">
        <h2>
        <?php echo __('Latest contributions');

        echo $this->Html->link(
            __('show more...'),
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
        <?php echo __('Latest comments');

        echo $this->Html->link(
            __('show more...'),
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

