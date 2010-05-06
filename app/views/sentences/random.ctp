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


echo $javascript->link('sentences.add_translation.js', true);
//echo $javascript->link('sentences.switch_script.js', true);
echo $javascript->link('favorites.add.js', true);
echo $javascript->link('sentences_lists.menu.js', true);
echo $javascript->link('jquery.impromptu.js', true);
echo $javascript->link('sentences.adopt.js', true);
echo $javascript->link('jquery.jeditable.js', true);
echo $javascript->link('sentences.edit_in_place.js', true);
echo $javascript->link('sentences.play_audio.js', true);

$sentence = $random['Sentence'];
$sentenceId = $random['Sentence']['id'];
$ownerName = $random['User']['username'];
$specialOptions = $random['specialOptions'];

echo '<div class="sentences_set">';
    // TODO set up a better mechanism
    $sentenceOwner['canEdit'] = $specialOptions['canEdit']; 
    $sentenceOwner['canLinkAndUnlink'] = $specialOptions['canLinkAndUnlink']; 
    
    $menu->displayMenu(
        $sentenceId,
        $ownerName,
        $sentenceScript
    );
    $sentences->displayGroup(
        $sentence, 
        $translations, 
        $sentenceOwner,
        $indirectTranslations
    );
echo '</div>';
?>
