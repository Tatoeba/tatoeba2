<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009 DEPARIS Étienne <etienne.deparis@umaneti.net>
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
 * @author   DEPARIS Étienne <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
if (!isset($ajax)) {
    $navigation->displayUsersNavigation(
        $user['User']['id'],
        $user['User']['username']
    );

    echo '<h3>';
    __('User following');
    echo '</h3>';
}

if (count($user['Following']) > 0) {
    echo '<ul>';
    foreach ($user['Following'] as $following) {
        /**
         * quick fix in order to have custom resolution on image and
         * have image in link
         */
        echo '<li class="followingAvatar">
                <a href="/user/profile/'
                . $following['username']
                . '" title="'
                . $following['username'].'">
                    <img src="/img/profiles/';

        if (empty($following['image'])) {
            echo 'unknown-avatar.jpg';
        } else {
            echo $following['image'];
        }

        echo '" alt="'.$following['username'].'" ';

        if (isset($ajax)) {
            echo 'style="width:50px;"/>';
        } else {
            echo '/><br/>' . $following['username'];
        }

        echo '</a>
               </li>';
    }
    echo '</ul>';
    if (isset($ajax)) {
        echo '<p style="clear:both;">'
        . $html->link(
            __(
                'Display more following',
                true
            ),
            array('action' => 'following', $user['User']['id'])
        ) . '</p>';
    }
} else {
    __('This user does not follow any users.');
}
?>
