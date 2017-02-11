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

/**
 * Display all comments of a user
 *
 * @category SentenceComments
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
$userName = Sanitize::paranoid($userName, array("_"));
$this->set('title_for_layout', $this->Pages->formatTitle(
    format(__("{user}'s comments"), array('user' => $userName))
));

// create an helper a lot of the code is the same of "on_sentences_of_user"
?>
<div id="annexe_content">
    <?php
        echo $this->element(
        'users_menu', 
        array('username' => $userName)
    );
    ?>
</div>

<div id="main_content">
    <div class="section">
    <?php
    if ($userExists === false) {
        $this->CommonModules->displayNoSuchUser($userName, $backLink);
    } elseif ($noComment === true) {
        echo '<h2>';
        echo format(
            __("{user} has posted no comment"),
            array('user' => $userName)
        );
        echo '</h2>';

        echo $this->Html->link(__('Go back to previous page'), $backLink);

    } else {
        ?>
        <h2>
            <?php 
            echo $this->Paginator->counter(
                array(
                    'format' => format(
                        __('{user}\'s comments (total&nbsp;{n})'),
                        array('user' => $userName, 'n' => '%count%')
                    )
                )
            ); 
            ?>
        </h2>
        
        <?php
        $paginatorUrl = array($userName);
        $this->Pagination->display($paginatorUrl);
        ?>
        
        <div class="comments">
        <?php
        $currentUserIsMember = CurrentUser::isMember();
        foreach ($userComments as $i => $comment) {
            $menu = $this->Comments->getMenuForComment(
                $comment['SentenceComment'],
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
        ?>
        </div>
        
       <?php
        $this->Pagination->display($paginatorUrl);
        
    }
    ?>
    </div>
</div>



