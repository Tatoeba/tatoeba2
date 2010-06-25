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

$lang = 'eng';
if (isset($this->params['lang'])) {
    Configure::write('Config.language', $this->params['lang']);
    $lang = $this->params['lang'];
}

$langArray = $languages->languagesArray();
$selectedLanguage = $session->read('random_lang_selected');

if ($selectedLanguage == null) {
    $selectedLanguage == 'und';
}
?>

<h2>
    <?php __('Random sentence'); ?> 
    <span class="annexe">
        (<?php
        echo $form->select(
             "randomLangChoice", $langArray, $selectedLanguage, null, false
        );
        echo ' ';
        echo $html->link(
            __('show another ', true),
            array(),
            array(
                "id" => "showRandom",
                "onclick" => "return false;"
            )
        );
        ?>)
    </span>
    <?php
    if ($session->read('Auth.User.id')) {
        ?>
        <span class="annexe">
            (<?php
             echo $html->link(
                 __('show more...', true),
                 array(
                     "controller" => "sentences",
                     "action" => "several_random_sentences"
                 )
             );
             ?>)
        </span>
    <?php
    }
    ?>
</h2>