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
* Page that lists all the members who know a certain language.
*
* @category Users
* @package  Views
* @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
* @license  Affero General Public License
* @link     http://tatoeba.org
*/
?>

<div id="annexe_content">
    <div class="module">
        <h2><?php __('Languages'); ?></h2>

        <p>
        <?php
        echo $html->link(
            __('Back to global stats', true),
            array(
                'controller' => 'stats',
                'action' => 'users_languages'
            )
        );
        ?>
        </p>

        <table class="usersLanguagesStats">
        <?php
        foreach($usersLanguages as $language) {
            $langCode = $language['UsersLanguages']['language_code'];
            $langName = $html->link(
                $languages->codeToNameAlone($langCode),
                array(
                    'controller' => 'users',
                    'action' => 'for_language',
                    $langCode
                )
            );
            $total = $language[0]['total'];
            $numberOfMembers = format(
                __n('{total} member', '{total} members', $total, true),
                array('total' => $total)
            );
            $selected = '';
            if ($lang == $langCode) {
                $selected = 'selected';
            }

            echo '<tr class="'.$selected.'">';
            echo $html->tag('td', $languages->icon($langCode, array()));
            echo $html->tag('td', $langName);
            echo $html->tag('td', $numberOfMembers);
            echo '</tr>';
        }
        ?>
        </table>
    </div>
</div>


<div id="main_content">
    <div class="module">
        <?php
        $total = count($users);
        $title = format(
            __n('{lang} ({total} member)', '{lang} ({total} members)', $total, true),
            array('lang' => $languages->codeToNameAlone($lang), 'total' => $total)
        );
        ?>
        <h2><?php echo $title; ?></h2>
        <div class="usersLanguages">
        <?php
        foreach($users as $user) {
            $username = $user['User']['username'];
            $userImage = $user['User']['image'];
            $languageLevel = $user['UsersLanguages']['level'];

            echo '<div class="user">';
            echo '<div class="profilePicture">';
                $members->image($username, $userImage);
            echo '</div>';
            echo '<div class="usernameAndLevel">';
                echo $html->link(
                    $username,
                    array(
                        'controller' => 'user',
                        'action' => 'profile',
                        $username
                    )
                );
                echo $members->displayLanguageLevel($languageLevel);
            echo '</div>';
            echo '</div>';
        }
        ?>
        </div>
    </div>
</div>