<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

$username = $user['User']['username'];
$userId =  $user['User']['id'];
$this->set('title_for_layout', $this->Pages->formatTitle(format(
    __('Tatoeba user: {username}'),
    compact('username')
)));
?>
<div id="annexe_content">

    <?php
        echo $this->element(
        'users_menu',
        array('username' => $username)
    );
    ?>

    <?php
    /* Latest contributions from the user */
    if (count($user['Contributions']) > 0) {
        ?>
        <div class="section" md-whiteframe="1">
            <h2><?php echo __('Latest contributions'); ?></h2>
            <md-list id="logs">
            <?php
            foreach ($user['Contributions'] as $userContribution) {
                $contribution = array(
                    'Contribution' => $userContribution,
                    'User' => $user['User']
                );
                echo $this->element(
                    'logs/log_entry_annexe',
                    array('log' => $contribution)
                );
            }
            ?>
            </md-list>
        </div>
        <?php
    }
    ?>
</div>

<div id="main_content">
    <?php
    /* Latest sentences, translations or adoptions from the user */
    if (count($user['Sentences']) > 0) {
        echo '<div class="module">';
            echo '<h2>';
            echo __('Latest sentences');

            echo $this->Html->link(
                __('view all'),
                array(
                    "controller" => "sentences",
                    "action" => "of_user",
                    $username
                ),
                array(
                    'class' => 'titleAnnexeLink'
                )
            );
            echo '</h2>';

            $type = 'mainSentence';
            $parentId = null;
            $withAudio = false;
            foreach ($user['Sentences'] as $sentence) {
                $this->Sentences->displayGenericSentence(
                    $sentence,
                    $type,
                    $parentId,
                    $withAudio
                );
            }
        echo '</div>';
    }

    /* Latest comments from the user */
    if (count($user['SentenceComments']) > 0) {
        echo '<div class="module">';
            echo '<h2>';
            echo __('Latest comments');

            echo $this->Html->link(
                __('view all'),
                array(
                    "controller" => "sentence_comments",
                    "action" => "of_user",
                    $username
                ),
                array(
                    'class' => 'titleAnnexeLink'
                )
            );
            echo '</h2>';

            echo '<div class="comments">';
            foreach ($user['SentenceComments'] as $i => $sentenceComment) {
                $comment['SentenceComment'] = $sentenceComment;
                $comment['User'] = $user['User'];
                $currentUserIsMember = CurrentUser::isMember();

                $menu = $this->Comments->getMenuForComment(
                    $sentenceComment,
                    $commentsPermissions[$i],
                    $currentUserIsMember
                );

                echo $this->element(
                    'messages/comment',
                    array(
                        'comment' => $comment,
                        'menu' => $menu,
                        'replyIcon' => $currentUserIsMember
                    )
                );
            }
            echo '</div>';
        echo '</div>';
    }


    /* Latest messages on the Wall */
    if (count($user['Wall']) > 0) {
        echo '<div class="module">';
            echo '<h2>';
            echo __('Latest Wall messages');

            echo $this->Html->link(
                __('view all'),
                array(
                    "controller" => "wall",
                    "action" => "messages_of_user",
                    $username
                ),
                array(
                    'class' => 'titleAnnexeLink'
                )
            );
            echo '</h2>';

            echo '<div class="wall">';
            foreach ($user['Wall'] as $comment) {
                $this->Wall->createThread(
                    $comment,
                    $user['User'],
                    null,
                    null
                );
            }
            echo '</div>';
        echo '</div>';
    }
    ?>

</div>
