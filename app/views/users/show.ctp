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

// TODO create an helper for this 

$userName = $user['User']['username'];
$userId =  $user['User']['id'];
$this->pageTitle = sprintf(
    __('Tatoeba user: %s', true),
    $userName
);
//$javascript->link('users.followers_and_following.js', false);

$navigation->displayUsersNavigation(
    $userId,
    $userName
);
?>
<div id="annexe_content">

    <div class="module">
    <h2><?php __('Contact'); ?></h2>
    <?php
    echo $html->link(
        __('Contact this user', true),
        array(
            'controller' => 'private_messages',
            'action' => 'write',
            $userName
        )
    );
    ?>
    </div>
    
    <?php
    /* Latest contributions from the user */
    if (count($user['Contributions']) > 0) {
        ?>
        <div class="module">
            <h2><?php __('Latest contributions'); ?></h2>
            <div id="logs">
            <?php
            foreach ($user['Contributions'] as $contribution) {
                $logs->annexeEntry($contribution);
            }
            ?>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<div id="main_content">
    <div class="module">
    <h2><?php echo sprintf(__('About %s', true), $userName); ?></h2>
    <ul>
        <li>
        <?php
        echo sprintf(
            __('Member since %s', true),
            $date->ago($user['User']['since'])
        );
        ?>
        </li>
        <li>
            <?php
            echo $html->link(
                __("View this user's profile", true),
                array(
                    "controller" => "user",
                    "action" => "profile",
                    $user['User']['username']
                )
            );
            ?>
        </li>
    </ul>
    </div>

    <?php
    /* People that the user is following */

    if (count($user['Following']) > 0) {
        echo '<div class="module">';
            echo '<h2>';
            __('Following');
            echo '</h2>';

            echo '<div class="following">';
            echo '<ul>';
            foreach ($user['Following'] as $following) {
                echo '<li>'.$following['username'].'</li>';
            }
            echo '<ul>';
            echo '</div>';
        echo '</div>';
    }


    /* People that are following the user */
    if (count($user['Follower']) > 0) {
        echo '<div class="module">';
            echo '<h2>';
            __('Followers');
            echo '</h2>';

            echo '<div class="followers">';
            echo '<ul>';
            foreach ($user['Follower'] as $follower) {
                echo '<li>'.$follower['username'].'</li>';
            }
            echo '<ul>';
            echo '</div>';
        echo '</div>';
    }

    /* Latest favorites from the user */
    if (count($user['Favorite']) > 0) {
        echo '<div class="module">';
            echo '<h2>';
            __('Favorite sentences');
            echo ' (';
            echo $html->link(
                __('view all', true),
                array(
                    "controller" => "favorites",
                    "action" => "of_user",
                    $user['User']['id']
                )
            );
            echo ')';
            echo '</h2>';

            echo '<table id="logs">';
            foreach ($user['Favorite'] as $favorite) {
                $sentences->displaySentence($favorite);
            }
            echo '</table>';

        echo '</div>';
    }

    /* Latest sentences, translations or adoptions from the user */
    if (count($user['Sentences']) > 0) {
        echo '<div class="module">';
            echo '<h2>';
            __('Latest sentences');
            echo '</h2>';

            foreach ($user['Sentences'] as $sentence) {
                $sentences->displaySentence($sentence);
            }
        echo '</div>';
    }

    /* Latest comments from the user */
    if (count($user['SentenceComments']) > 0) {
        echo '<div class="module">';
            echo '<h2>';
            __('Latest comments');
            echo ' (';
            echo $html->link(
                __('view all', true),
                array(
                    "controller" => "sentence_comments",
                    "action" => "of_user",
                    $userName
                )
            );
            echo ')';

            echo '</h2>';

            echo '<ol class="comments">';
            foreach ($user['SentenceComments'] as $comment) {
                $comment['User'] = $user['User'];
                $comments->displaySentenceComment($comment, true);
            }
            echo '</ol>';
        echo '</div>';
    }
    ?>

</div>

