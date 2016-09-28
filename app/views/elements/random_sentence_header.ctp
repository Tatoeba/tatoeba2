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

$langArray = $languages->languagesArrayAlone();
$selectedLanguage = $session->read('random_lang_selected');

if ($selectedLanguage == null) {
    $selectedLanguage == 'und';
}
?>
<div layout="row" layout-align="start center">
    <h2 layout="row" layout-align="start center" flex>
        <?php __('Random sentence'); ?>
        <? if ($session->read('Auth.User.id')) {
            $showMoreUrl = $html->url(array(
                'controller' => 'sentences',
                'action' => 'several_random_sentences'
            ));
            ?>
            <md-button class="md-icon-button" href="<?= $showMoreUrl ?>">
                <md-icon>more_horiz</md-icon>
            </md-button>
        <? } ?>
    </h2>
    <?php
    echo $form->select(
        "randomLangChoice",
        $langArray,
        $selectedLanguage,
        array(
            'class' => 'language-selector',
            "empty" => false
        ),
        false
    );
    ?>
    <md-button class="md-icon-button" id="showRandom" onclick="return false;">
        <md-icon>refresh</md-icon>
    </md-button>
</div>

