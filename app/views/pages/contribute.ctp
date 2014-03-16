<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

$this->pageTitle = 'Tatoeba - ' . __('How to contribute', true);
?>


<div id="annexe_content">
    <?php
    if (!$session->read('Auth.User.id')) {
        ?>
        <div class="module">
        <h2><?php __("Register"); ?></h2>
        <?php    
        __('If you are interested, please register.');
        echo $html->link(
            __('Register', true),
            array("controller" => "users", "action" => "register"),
            array("class"=>"registerButton")
        );
        ?>
        </div>
        <?php
    }
    ?>
    
    <?php
    $attentionPlease->tatoebaNeedsYou();
    ?>
    
    <div class="module">
        <h2><?php __('Important to read'); ?></h2>
        <ol>
            <li>
            <a href="http://blog.tatoeba.org/2010/02/how-to-be-good-contributor-in-tatoeba.html">
            <?php __('Guide to being a good contributor'); ?>
            </a>
            </li>
            
            <li>
            <a href="http://blog.tatoeba.org/2010/05/moderators-in-tatoeba.html">
            <?php __('Role of moderators'); ?>
            </a>
            </li>
            
            <li>
            <a href="http://blog.tatoeba.org/2010/04/reliability-of-sentences-how-will-we.html">
            <?php __('Reliability of the sentences'); ?>
            </a>
            </li>
        </ol>
    </div>
</div>

<div id="main_content">  
    <div class="module">
        <h2><?php __("Contribute"); ?></h2>
        <?php
        // Detecting language for "browse by language"
        $currentLanguage = $session->read('random_lang_selected');
        if (empty($currentLanguage) || $currentLanguage == 'und') {
            $currentLanguage = $languages->i18nCodeToISO($this->params['lang']);
        }
        $subMenu = array(
            __('Add sentences', true) => array(
                "controller" => "sentences",
                "action" => "add"
            ),
            __('Translate sentences', true) => array(
                "controller" => "activities",
                "action" => "translate_sentences"
            ),
            __('Adopt sentences', true) => array(
                "controller" => "activities",
                "action" => "adopt_sentences",
                $currentLanguage
            ),
            __('Improve sentences', true) => array(
                "controller" => "activities",
                "action" => "improve_sentences"
            ),
            __('Discuss sentences', true) => array(
                "controller" => "sentence_comments",
                "action" => "index"
            )
        );
        ?>
        <ul>
        <?php
        foreach ($subMenu as $title => $route) {
            echo '<li>';
            echo $html->link($title, $route);
            echo '</li>';
        }
        ?>
        </ul>
    </div>
    
    
    <div class="module">
    <h2><?php __("How can you contribute?"); ?></h2>
    <p>
    <?php
    __(
        "Tatoeba is primarily about collecting sentences and translating them ".
        "into many, many languages. So one obvious thing you can do to contribute ".
        "is to <strong>translate sentences</strong>."
    );
    ?>
    </p>
    
    <p>
    <?php
    __(
        "But you don't have to be a polyglot in order to contribute! You can help ".
        "us a lot simply by <strong>checking sentences</strong> and ".
        "<strong>reporting mistakes</strong>. This is something everyone can do!"
    );
    ?>
    </p>
    
    <p>
    <?php
    __(
        "If you have a good microphone, you can also bring more audio to Tatoeba ".
        "by <strong>recording sentences</strong>."
    );
    ?>
    </p>
    
    <p>
    <?php
    __(
        "You can help us promote the project. <strong>Tell people around ".
        "you</strong> that Tatoeba exists. This is a very ambitious project, so we ".
        "will need as much help as possible."
    );
    ?>
    </p>
    
    <p>
    <?php
    __(
        "And finally, you can <strong>join the team</strong>. You can help us ".
        "code new features, improve usability, debug, optimize, keep ".
        "the site secure, and more."
    );
    ?>
    </p>
    </div>
</div>
