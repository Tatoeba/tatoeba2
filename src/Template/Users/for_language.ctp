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
$title = format(
    __('Members: {lang}'),
    array('lang' => $this->Languages->codeToNameToFormat($lang))
);
$this->set('title_for_layout', $this->Pages->formatTitle($title));
?>

<div id="annexe_content">
    <div class="section md-whiteframe-1dp">
        <h2><?php echo __('Languages'); ?></h2>

        <p>
        <?php
        echo $this->Html->link(
            __('Back to global stats'),
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
            $langCode = $language->language_code;
            $langName = $this->Html->link(
                $this->Languages->codeToNameAlone($langCode),
                array(
                    'controller' => 'users',
                    'action' => 'for_language',
                    $langCode
                )
            );
            $total = $language[0]['total'];
            $selected = '';
            if ($lang == $langCode) {
                $selected = 'selected';
            }

            echo '<tr class="'.$selected.'">';
            echo $this->Html->tag('td', $this->Languages->icon($langCode, array()));
            echo $this->Html->tag('td', $langName);
            echo $this->Html->tag('td', $total);
            echo '</tr>';
        }
        ?>
        </table>
    </div>
</div>


<div id="main_content">
    <div class="section md-whiteframe-1dp">
        <?php
        $total = $this->Paginator->param('count');
        $title = format(
            __n('{lang} ({total} member)', '{lang} ({total} members)', $total),
            array('lang' => $this->Languages->codeToNameAlone($lang), 'total' => $total)
        );
        ?>
        <h2><?php echo $title; ?></h2>
        <div class="usersLanguages">
        <?php
        $this->Pagination->display();

        foreach($users as $user) {
            
            $username = $user->user->username;
            $userImage = $user->user->image;
            $languageLevel = $user->level;

            echo '<div class="user">';
            echo '<div class="profilePicture">';
            echo $this->Members->image($username, $userImage);
            echo '</div>';
            echo '<div class="usernameAndLevel">';
                echo $this->Html->link(
                    $username,
                    array(
                        'controller' => 'user',
                        'action' => 'profile',
                        $username
                    )
                );
                echo $this->Members->displayLanguageLevel($languageLevel);
            echo '</div>';
            echo '</div>';
        }

        $this->Pagination->display();
        ?>
        </div>
    </div>
</div>