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
 * @author   SIMON Allan <simon.allan@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$currentUser =  CurrentUser::get('id');
?>
<div class="tagsListOnSentence" >
<?
foreach ($tagsArray as $tag) {
    ?>
    <span class="tag">
    <?php
    $tagName =  $tag['Tag']['name'];
    $tagInternalName =  $tag['Tag']['internal_name'];
    $taggerId = $tag['TagsSentences']['user_id'];
    $tagId = $tag['TagsSentences']['tag_id'];

    echo $html->link(
        $tagName,
        array(
            "controller" => "tags",
            "action" => "show_sentences_with_tag",
            $tagInternalName
        ),
        array(
            "class" => "tagName"
        )
    );
    
    if (CurrentUser::canRemoveTagFromSentence($taggerId)) {
        $removeTagFromSentenceAlt = sprintf(
            __("remove tag '%s' from this sentence.", true),
            $tagName
        );
        
        echo $html->link(
            'x',
            array(
                "controller" => "tags",
                "action" => "remove_tag_from_sentence",
                $tagId,
                $sentenceId
            ),
            array(
                "class" => "removeTagFromSentenceButton",
                "id" => 'deleteButton'.$tagId.$sentenceId,
                "title" => $removeTagFromSentenceAlt
            ),
            null,
            false
        );
    }
    ?></span>
    <?php
}

?>
</div>
