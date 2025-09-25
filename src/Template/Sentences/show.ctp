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
use App\Model\CurrentUser;

if (!CurrentUser::isMember() || CurrentUser::getSetting('use_new_design')) {
    $this->set('isResponsive', true);
}

if (!isset($searchProblem)) {
if (isset($sentence)) {
    $sentenceId = $sentence->id;
    $sentenceLang = $sentence->lang;
    $sentenceText = $sentence->text;
    $sentenceCorrectness = $sentence->correctness;

    $languageName = $this->Languages->codeToNameToFormat($sentenceLang);
    $title = format(__('{sentence} - {language} example sentence'),
                    array('language' => $languageName, 'sentence' => $sentenceText));
    $this->set('title_for_layout', $this->Pages->formatTitle($title));

    $this->Html->meta(
        'description',
        format(
            __(
                "Browse translated example sentences. ".
                "This page shows translations and information about the sentence: {sentenceText}"
                , true
            ),
            compact('sentenceText')
        ),
        array('inline' => false)
    );
} else {
    // Case where the sentence has been deleted
    $this->set('title_for_layout', $this->Pages->formatTitle(
        __('Sentence does not exist: ') . $this->request->params['pass'][0]
    ));
    $sentenceId = $this->request->params['pass'][0];
}


// navigation (previous, random, next)
echo $this->element('/sentences/navigation', [
    'currentId' => $sentenceId,
    'next' => $nextSentence,
    'prev' => $prevSentence
]);
?>
<br>

<section layout="row" ng-cloak>
<md-content class="md-whiteframe-1dp" flex>
    <section>
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <h2 flex><?= format(__('Sentence #{number}'), array('number' => $sentenceId)); ?></h2>
                <md-button hide-gt-sm ng-controller="SidenavController" ng-click="toggle('metadata')">
                    <md-icon>info_outline</md-icon>
                    <?php /* @translators: button to open the sidebar on the sentence page on mobile */ ?>
                    <?= __('Metadata') ?>
                </md-button>
            </div>
        </md-toolbar>

        <?php
        if (isset($sentence)) {
            if (CurrentUser::isMember() && !CurrentUser::getSetting('use_new_design')) {
                ?><div class="section md-whiteframe-1dp"><?php
                $this->Sentences->displaySentencesGroup($sentence);
                ?></div><?php
            } else {
                echo $this->element(
                    'sentences/sentence_and_translations',
                    array(
                        'sentence' => $sentence,
                        'translations' => $sentence->translations,
                        'user' => $sentence->user
                    )
                );
            }
        } else {
            echo '<div class="error">';
                echo format(
                    __(
                        'There is no sentence with id {number}',
                        true
                    ),
                    array('number' => $this->request->params['pass'][0])
                );
            echo '</div>';
        }
        ?>
    </section>

    <section class="md-whiteframe-1dp">
        <md-toolbar class="md-hue-2">
            <div class="md-toolbar-tools">
                <?php /* @translators: header text in sentence page */ ?>
                <h2><?= __('Comments'); ?></h2>
            </div>
        </md-toolbar>

        <md-content>
        <?php
        if (!$sentenceComments->isEmpty()) {
            foreach ($sentenceComments as $i=>$comment) {
                $menu = $this->Comments->getMenuForComment(
                    $comment,
                    $commentsPermissions[$i],
                    false
                );

                echo '<a id="comment-'.$comment->id.'"></a>';

                echo $this->element(
                    'sentence_comments/comment',
                    array(
                        'comment' => $comment,
                        'menu' => $menu,
                        'replyIcon' => false,
                        'hideSentence' => true
                    )
                );
            }
        } else {
            ?>
            <div layout-padding class="center">
                <p><?= __('There are no comments for now.') ?></p>
            </div>
            <?php
        }

        if ($canComment) {
            echo $this->element('sentence_comments/add_form', [
                'sentenceId' => $sentenceId,
                'text' => $unsentComment,
            ]);
        }
        ?>
        </md-content>
    </section>
</md-content>

<md-sidenav class="md-sidenav-right md-whiteframe-1dp"
            md-component-id="metadata"
            md-disable-scroll-target="body"
            md-is-locked-open="$mdMedia('gt-sm')">
    <md-toolbar>
        <div class="md-toolbar-tools" ng-controller="SidenavController">
            <?php /* @translators: title for the sidebar on the sentence page */ ?>
            <h2 flex><?= __('Metadata'); ?></h2>
            <md-button class="close md-icon-button" ng-click="toggle('metadata')">
                <md-icon>close</md-icon>
            </md-button>
        </div>
    </md-toolbar>
    <?php
    if (CurrentUser::get('settings.users_collections_ratings')) {
        echo '<div class="section correctness-info md-whiteframe-1dp">';

        echo $this->Html->tag('h2', __('Reviewed by'));
        foreach($correctnessArray as $correctness) {
            echo '<div>';
            echo $this->Images->correctnessIcon(
                $correctness->correctness
            );
            echo $this->Html->link(
                $correctness->user->username,
                array(
                    'controller' => 'user',
                    'action' => 'profile',
                    $correctness->user->username
                ),
                array(
                    'class' => 'username',
                    'title' => $this->Date->nice($correctness->modified)
                )
            );
            if ($correctness->dirty != 0) {
                echo ' <span class="info">'. __('(outdated)'). '</span>';
            }
            echo '</div>';
        }

        echo '</div>';
    }
    ?>

    <?php
    if (isset($sentence)){
        $this->Tags->displayTagsModule($tagsArray, $sentenceId, $sentenceLang);

        $this->Lists->displayListsModule($listsArray, $sentence);

        echo $this->element(
            'sentences/license',
            array(
                'sentenceId' => $sentenceId,
                'license' => $sentence->license,
                'canEdit' => CurrentUser::canEditLicenseOfSentence($sentence),
            )
        );

        echo $this->element(
            'sentences/audio',
            array(
                'sentence' => $sentence,
            )
        );

        if (CurrentUser::isAdmin()) {
            // TODO For the beginning we'll restrict this to admins.
            // Later we'll want CurrentUser::isModerator();
            echo $this->element(
                'sentences/correctness',
                array(
                    'sentenceId' => $sentenceId,
                    'sentenceCorrectness' => $sentenceCorrectness
                )
            );
        }
    }
    ?>

    <div class="section md-whiteframe-1dp">
        <?php
        echo '<h2>';
        /* @translators: header text in sentence page */
        echo __('Logs');
        echo '</h2>';

        if (isset($sentence)) {
            echo $this->Sentences->originText($sentence);
        }

        if ($contributions->count() > 0) {
            if (!empty($sentence->translations[0])) {
                $this->Html->script(['jquery.scrollTo.min.js', 'sentences.logs.js'],
                                    ['block' => 'scriptBottom']);
            }
            echo '<md-list id="logs">';
            foreach ($contributions as $contribution) {
                echo $this->element(
                    'logs/log_entry_annexe',
                    array('log' => $contribution)
                );
            }
            echo '</md-list>';
        } else {
            echo '<em>'. __('There is no log for this sentence') .'</em>';
        }
        ?>
    </div>
</md-sidenav>
</section>
<?php
} else {
?>
    <div id="main_content">
        <div class="section">
            <?php
            echo $this->Html->tag('h2', __('Random Sentence'));
            if($searchProblem == 'disabled') {
                echo $this->Html->tag('p', __('The random sentence feature is currently disabled, please try again later.'));
            } else if ($searchProblem == 'error') {
                echo $this->Html->tag('p', format(__('An error occurred while fetching random sentences. '.
                                               'If this persists, please <a href="{}">let us know</a>.', true),
                                     $this->Url->build(array("controller"=>"pages", "action" => "contact"))
                ));
            }
            ?>
        </div>
    </div>
<?php } ?>
