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
?>

<div class="topContent">
    <div class="description">
        <p>
            <?php __("Tatoeba is a collection of sentences and translations."); ?>
        </p>
        <p>
            <?php __("It's collaborative, open, free and even addictive."); ?>
        </p>
    </div><!--

    --><ul class="links">
        <?php
        echo '<li>';
        echo $html->link(
            __("How it works", true),
            'http://en.wiki.tatoeba.org/articles/show/quick-start',
            array(
                "class" => "learnMore"
            )
        );
        echo '</li>';


        echo '<li>';
        echo $html->link(
            __("About us", true),
            array(
                "controller" => "pages",
                "action" => "about"
            ),
            array(
                "class" => "learnMore"
            )
        );
        echo '</li>';


        echo '<li>';
        echo $html->link(
            __("Register", true),
            array(
                "controller" => "users",
                "action" => "register"
            ),
            array(
                "class" => "registerButton"
            )
        );
        echo '</li>';


        echo '<li>';
        echo $html->link(
            __("Log in", true),
            array(
                "controller" => "users",
                "action" => "login"
            ),
            array(
                "class" => "loginButton"
            )
        );
        echo '</li>';
        ?>
    </ul>
</div>