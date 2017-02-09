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

$this->set('title_for_layout', $this->Pages->formatTitle(__('How to contribute')));
?>


<div id="annexe_content">
    <?php
    if (!$this->Session->read('Auth.User.id')) {
        ?>
        <div class="module">
        <h2><?php echo __("Register"); ?></h2>
        <?php echo __('If you are interested, please register.');
        echo $this->Html->link(
            __('Register'),
            array("controller" => "users", "action" => "register"),
            array("class"=>"registerLink")
        );
        ?>
        </div>
        <?php
    }
    ?>

    <div class="module">
        <h2><?php echo __('Important to read'); ?></h2>
        <ol>
            <li>
            <a href="http://blog.tatoeba.org/2010/02/how-to-be-good-contributor-in-tatoeba.html">
            <?php echo __('Guide to being a good contributor'); ?>
            </a>
            </li>

            <li>
            <a href="http://blog.tatoeba.org/2010/04/reliability-of-sentences-how-will-we.html">
            <?php echo __('Reliability of the sentences'); ?>
            </a>
            </li>
        </ol>
    </div>
</div>

<div id="main_content">
    <div class="module">
        <h2><?php echo __("Contribute"); ?></h2>
        <?php
        // Detecting language for "browse by language"
        $currentLanguage = $this->Session->read('random_lang_selected');
        if (empty($currentLanguage) || $currentLanguage == 'und') {
            $currentLanguage = $this->request->params['lang'];
        }
        $subMenu = array(
            __('Add sentences') => array(
                "controller" => "sentences",
                "action" => "add"
            ),
            __('Translate sentences') => array(
                "controller" => "activities",
                "action" => "translate_sentences"
            ),
            __('Adopt sentences') => array(
                "controller" => "activities",
                "action" => "adopt_sentences",
                $currentLanguage
            ),
            __('Improve sentences') => array(
                "controller" => "activities",
                "action" => "improve_sentences"
            ),
            __('Discuss sentences') => array(
                "controller" => "sentence_comments",
                "action" => "index"
            )
        );
        ?>
        <ul>
        <?php
        foreach ($subMenu as $title => $route) {
            echo '<li>';
            echo $this->Html->link($title, $route);
            echo '</li>';
        }
        ?>
        </ul>
    </div>


    <div class="module">
    <h2><?php echo __("How can you contribute?"); ?></h2>
    <p>
    <?php
    echo __(
        "Tatoeba is primarily about collecting sentences and translating them ".
        "into many, many languages. So one obvious thing you can do to contribute ".
        "is to <strong>translate sentences</strong>."
    );
    ?>
    </p>

    <p>
    <?php
    echo __(
        "But you don't have to be a polyglot in order to contribute! You can help ".
        "us a lot simply by <strong>checking sentences</strong> and ".
        "<strong>reporting mistakes</strong>. This is something everyone can do!"
    );
    ?>
    </p>

    <p>
    <?php
    echo __(
        "If you have a good microphone, you can also bring more audio to Tatoeba ".
        "by <strong>recording sentences</strong>."
    );
    ?>
    </p>

    <p>
    <?php
    echo __(
        "You can help us promote the project. <strong>Tell people around ".
        "you</strong> that Tatoeba exists. This is a very ambitious project, so we ".
        "will need as much help as possible."
    );
    ?>
    </p>

    <p>
    <?php
    echo __(
        "And finally, you can <strong>join the team</strong>. You can help us ".
        "code new features, improve usability, debug, optimize, keep ".
        "the site secure, and more."
    );
    ?>
    </p>
    </div>
</div>
