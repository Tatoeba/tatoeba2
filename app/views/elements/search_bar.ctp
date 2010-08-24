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


App::import('Core', 'Sanitize');

if (isset($this->params['lang'])) {
    Configure::write('Config.language', $this->params['lang']);
}
?>
<div class="search_bar_container">
<div class="search_bar">

<?php
$languages = $languages->getSearchableLanguagesArray();

$selectedLanguageFrom = $session->read('search_from');
$selectedLanguageTo = $session->read('search_to');
$searchQuery = $session->read('search_query');
Sanitize::html($searchQuery);
$searchQuery = htmlentities($searchQuery, ENT_QUOTES, 'UTF-8');


if ($selectedLanguageFrom == null) {
    $selectedLanguageFrom = 'und';
}

if ($selectedLanguageTo == null) {
    $selectedLanguageTo = 'und';
}
echo $form->create(
    'Sentence',
    array(
        "action" => "search",
        "type" => "get"
    )
);
?>
<script type="text/javascript">
$(document).ready(function(){
    $("#SentenceQuery").focus();
});
</script>

<fieldset class="input text">
    <label for="SentenceQuery">
        <?php __('Example sentences with the words :'); ?>
    </label>
    <?php
    echo $form->input(
        'query',
        array(
            'id' => 'SentenceQuery',
            'value' => $searchQuery,
            'label' => ''
        )
    );
    ?>
</fieldset>

<fieldset class="select">
    <label><?php __('From'); ?></label>
    <?php
    echo $form->select(
        'from',
        $languages,
        $selectedLanguageFrom,
        null,
        false
    );
    ?>
</fieldset>

<fieldset class="into">
    <span id="into">&raquo;</span>
</fieldset>
    
<fieldset class="select">
    <label><?php __('To'); ?></label>
    <?php
    echo $form->select(
        'to',
        $languages,
        $selectedLanguageTo,
        null, 
        false
    );
    ?>
</fieldset>

<fieldset class="submit">
    <input type="submit" value="<?php echo __('search'); ?>"/>
</fieldset>

<?php
echo $form->end();
?>
</div>

<div class="search_bar_left"></div>
<div class="search_bar_right"></div>
</div>
