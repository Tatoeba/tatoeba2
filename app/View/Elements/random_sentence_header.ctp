<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

echo $javascript->link(JS_PATH . 'sentences.show_another.js', false);

$langArray = $this->Languages->languagesArrayAlone();
$selectedLanguage = $this->Session->read('random_lang_selected');

if ($selectedLanguage == null) {
    $selectedLanguage == 'und';
}
?>

<h2>
    <?php echo __('Random sentence'); ?>
    <span>
        <?php
        echo $this->Form->select(
            "randomLangChoice",
            $langArray,
            array(
                'value' => $selectedLanguage,
                'class' => 'language-selector',
                "empty" => false
            ),
            false
        );
        echo ' ';
        echo $this->Html->link(
            __('show another '),
            array(),
            array(
                "id" => "showRandom",
                "class" => "titleAnnexeLink",
                "onclick" => "return false;"
            )
        );
        ?>
    </span>
    <?php
    if ($this->Session->read('Auth.User.id')) {
        ?>
        <span>
            <?php
             echo $this->Html->link(
                 __('show more...'),
                 array(
                     "controller" => "sentences",
                     "action" => "several_random_sentences"
                 ),
                 array(
                     "class" => "titleAnnexeLink"
                 )
             );
             ?>
        </span>
    <?php
    }
    ?>
</h2>
