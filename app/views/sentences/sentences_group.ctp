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

/**
 * This view displays the HTML inside of a sentences group. 
 *
 * It is used by the adopt() and let_go() actions in the controller. There are no 
 * action called sentences_group() in the controller.
 *
 * @category Sentences
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */  

echo $javascript->link('sentences.add_translation.js', true);
echo $javascript->link('favorites.add.js', true);
echo $javascript->link('sentences_lists.menu.js', true);
echo $javascript->link('sentences.adopt.js', true);
echo $javascript->link('jquery.jeditable.js', true);
echo $javascript->link('sentences.edit_in_place.js', true);
echo $javascript->link('sentences.play_audio.js', true);
echo $javascript->link('sentences.change_language.js', true);
 
$sentenceId = $sentence['Sentence']['id'];
$username = $sentence['User']['username'];
$chineseScript = $sentence['Sentence']['script'];
?>

<?php
$menu->displayMenu(
    $sentenceId, $username, $chineseScript
);

// for edit in place...
// TODO set up a better mechanism
$sentence['User']['canEdit'] = $specialOptions['canEdit']; 

// display sentence and translations
$sentences->displayGroup(
    $sentence['Sentence'],
    $translations,
    $sentence['User'],
    $indirectTranslations
);    
?> 