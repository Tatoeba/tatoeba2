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
use Cake\I18n\I18n;
use App\Model\CurrentUser;

$this->set('title_for_layout', __('Tatoeba: Collection of sentences and translations'));

$selectedLanguage = $this->request->getSession()->read('random_lang_selected');

$moreContribUrl = $this->Url->build([
    'controller' => 'contributions',
    'action' => 'latest'
]);

$moreCommentsUrl = $this->Url->build([
    'controller' => 'sentence_comments'
]);
?>
<div id="annexe_content">
    <div class="stats annexe-menu md-whiteframe-1dp" layout="column" flex>
        <?php /* @translators: header text in the home page (noun) */ ?>
        <md-subheader><?= __('Stats')?></md-subheader>
        <?= $this->element('stats/homepage_stats',
                [ 'contribToday' => $contribToday,
                  'numberOfLanguages' => $numberOfLanguages,
                  'numSentences,' => $numSentences,
                ],
                [ 'cache' => [
                    'config' => 'stats',
                    'key' => 'homepage_stats_'.I18n::getLocale(),
                ]]
        ); ?>
    </div>
        
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
    <?php
    if (!CurrentUser::getSetting('use_new_design') && !CurrentUser::getSetting('hide_new_design_announcement')) {
        $this->Html->script('directives/info-banner.dir.js', ['block' => 'scriptBottom']); 
        ?>
        <div info-banner ng-init="vm.init('hide_new_design_announcement')" ng-cloak>
            <div class="md-whiteframe-1dp" layout-padding style="background: #fafafa" ng-if="vm.isInfoBannerVisible">
                <p><?= __(
                    'The new sentence design will soon completely replace the old one. Please try it out and let us know if you experience any issue. '.
                    'You can enable it with the option '.
                    '"Display sentences with the new design" in your Settings.'
                ) ?></p>
                <div layout="row" layout-align="end center">
                    <md-button class="md-primary" href="/user/settings"><?= __('Go to settings') ?></md-button>
                    <?php /* @translators: button to close the announcement about the new design (verb) */ ?>
                    <md-button class="md-primary" ng-click="vm.hideAnnouncement(true)"><?= __('Close') ?></md-button>
                </div>
            </div>
        </div>
        <?php 
    } 
    ?>

    <?php if(!isset($searchProblem) && !$hideRandomSentence) { ?>
        <section>
            <?php 
            echo $this->element('random_sentence_header');
            echo $this->element(
                'sentences/sentence_and_translations',
                array(
                    'sentence' => $random,
                    'translations' => $random->translations,
                    'user' => $random->user,
                )
            );
            ?>
     </section>
    <?php } ?>

    <section>
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2 flex><?= __('Latest contributions'); ?></h2>
                <md-button href="<?= $moreContribUrl ?>">
                    <?= __('show more...') ?>
                </md-button>
            </div>
        </md-toolbar>

        <?php echo $this->element('latest_contributions'); ?>
    </section>

    <section>
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2 flex><?= __('Latest comments'); ?></h2>
                <md-button href="<?= $moreCommentsUrl ?>">
                    <?= __('show more...') ?>
                </md-button>
            </div>
        </md-toolbar>
        
        <md-content class="md-whiteframe-1dp" flex>
        <?php
        echo $this->element(
            'latest_sentence_comments',
            array(
                'sentenceComments' => $sentenceComments,
                'commentsPermissions' => $commentsPermissions
            )
        ); 
        ?>
        </md-content>
    </section>
</div>

