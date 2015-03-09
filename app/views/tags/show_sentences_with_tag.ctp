<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 Allan SIMON <allan.simon@supinfo.com>
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
 * @author   Allan SIMON <allan.simon@supinfo.com> 
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

if ($tagExists) {
    $tagName = Sanitize::html($tagName);
    $title = format(__('Sentences with tag {tagName}', true), compact('tagName'));
    ?>

    <div id="annexe_content">
        <?php $commonModules->createFilterByLangMod(2); ?>
        <div class="module">
            <?php
            echo $html->link(
                __('View all tags', true),
                array(
                    "controller" => "tags",
                    "action" => "view_all",
                )
            );
            ?>
        </div>
    </div>

    <div id="main_content">
        <div class="module">
            <h2><?php echo $tagName; ?></h2>
            <?php
            $url = array($tagId, $langFilter);
            $pagination->display($url);
            ?>

            <div class="sentencesList" id="sentencesList">
                <?php
                foreach ($allSentences as $i=>$sentence) {
                    // this should be done in the controller but this way
                    // we avoid another full loop on the sentence Array
                    $canUserRemove = CurrentUser::canRemoveTagFromSentence(
                        $taggerIds[$i]
                    );
                    $tags->displaySentence(
                        $sentence['Sentence'],
                        $sentence['User'],
                        $sentence['Translations'],
                        $sentence['IndirectTranslations'],
                        $canUserRemove,
                        $tagId
                    );
                }
                ?>
            </div>

            <?php
            $pagination->display($url);
            ?>

        </div>
    </div>
    <?php
} else {
    $title = format(__('No tag with id {tagId}', true), compact('tagId'));
    ?>
    <div id="main_content">
        <?php
        echo $html->link(
            __('View all tags', true),
            array(
                "controller" => "tags",
                "action" => "view_all",
            )
        );
        ?>
    </div>
    <?php
}
$this->set('title_for_layout', $pages->formatTitle($title));
?>
