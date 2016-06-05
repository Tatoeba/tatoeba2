<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  SIMON Allan <allan.simon@supinfo.com>
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
 * @author   SIMON Allan <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Helper for tags.
 *
 * @category Default
 * @package  Helpers
 * @author   SIMON Allan <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class TagsHelper extends AppHelper
{
    public $helpers = array(
        'Html',
        'Form',
        'Javascript',
        'Sentences',
    );

    /**
     * Display all the tags in the Array
     *
     * @param array $tagsArray  Array with all the information to display
     *                          tags
     * @param int   $sentenceId Id of the sentence if tags are related
     *                          to a sentence
     *
     * @return void
     */
    public function displayTagsModule($tagsArray, $sentenceId = null)
    {
        $this->Javascript->link(JS_PATH . 'autocompletion.js', false);
        ?>

        <div class="module">
            <h2><?php __('Tags'); ?></h2>

            <div class="tagsListOnSentence" >
                <?php
                foreach ($tagsArray as $tagArray) {

                    $tagName =  $tagArray['Tag']['name'];
                    $userId = $tagArray['User']['id'];
                    $username = $tagArray['User']['username'];
                    $tagId = $tagArray['TagsSentences']['tag_id'];
                    $date = $tagArray['TagsSentences']['added_time'];

                    $this->displayTag(
                        $tagName, $tagId, $sentenceId, $userId, $username, $date
                    );

                }
                ?>
            </div>
            <?php
            if (CurrentUser::isTrusted()) {
                $this->displayAddTagForm($sentenceId);
            }
	    echo $this->Html->link(
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


    /**
     * @param $tagName
     * @param $tagId
     * @param null $username
     * @param null $date
     */
    public function displayTag(
        $tagName, $tagId, $sentenceId, $userId, $username = null, $date = null
    ) {
        ?>
        <span class="tag">
        <?php

        $this->displayTagLink(
            $tagName, $tagId, $username, $date
        );

        if (CurrentUser::canRemoveTagFromSentence($userId)) {
            $this->_displayRemoveLink($tagId, $tagName, $sentenceId);
        }
        ?>
        </span>
        <?php
    }


    /**
     *
     *
     */
    public function displayTagLink(
        $tagName, $tagId, $username = null, $date = null
    ) {
        $options = array(
            "class" => "tagName",
            "lang" => "",
            "dir" => "auto",
        );
        if ($username != null) {
            $options["title"] = format(
                __("Added by {username}, {date}", true),
                compact('username', 'date')
            );
        }
        echo $this->Html->link(
            $tagName,
            array(
                "controller" => "tags",
                "action" => "show_sentences_with_tag",
                $tagId
            ),
            $options
        );

    }


    /**
     * Display tag with the number of sentences tagged.
     *
     * @param string $tagName         Name of the tag.
     * @param string $tagId           Id of the tag, used in the URL.
     * @param string $count           Number of sentences tagged.
     *
     * @return void
     */
    public function displayTagInCloud($tagName, $tagId, $count) {
        $this->displayTagLink($tagName, $tagId);
        ?>
        <span class="numSentences"><?php echo '('.$count.')'; ?></span>
        <?php
    }

    /**
     * Display a little form to add a tag
     *
     * @param int $sentenceId If specified, will add the tag only
     *                        to this sentence.
     *
     * @return void
     */

    public function displayAddTagForm($sentenceId = null)
    {
        $this->Javascript->link(JS_PATH . 'tags.add.js', false);

        echo $this->Form->create(
            'Tag',
            array(
                "action" => "add_tag_post",
                "type" => "post",
            )
        );

        // TODO replace me I'm dirty
        // The idea is to mark a "dirty" tag (one not updated yet), 
        // but this has not been implemented. See models/tag.php.
        echo '<div id="autocompletionDiv">';
        echo '</div>';

        echo $this->Form->input(
            'tag_name',
            array(
                "label" => '',
                "lang" => '',
                "dir" => 'auto',
                "data-sentence-id" => $sentenceId
            )
        );
        echo $this->Form->hidden(
            'sentence_id',
            array('value' => $sentenceId)
        );

        echo '<div class="input">';
        echo $this->Form->button('+', array('id' => 'addNewTag'));
        echo '</div>';

        echo $this->Form->end();
    }


    /**
     * Display sentence for a list of tagged sentences.
     *
     * @param array $sentence             Sentence data.
     * @param array $transcriptions       Sentence transcriptions.
     * @param array $sentenceOwner        Array with Sentence owner info.
     * @param array $translations         Array with translations of this sentence
     *                                    (direct and indirect).
     * @param bool  $canCurrentUserRemove 'true' if user can remove tag from this
     *                                    sentence..
     * @param int   $tagId                Id of the tag.
     *
     * @return void
     */
    public function displaySentence(
        $sentence,
        $transcriptions,
        $sentenceOwner,
        $translations = array(),
        $canCurrentUserRemove = false,
        $tagId = null
    ) {
        if (empty($sentence['id'])) {
            // In case the sentence has been deleted, we don't want to display
            // it in the list.
            return;
        }
        $sentenceId = $sentence['id'];
        ?>
        <div id="sentence<?php echo $sentenceId; ?>" class="sentenceInList">

            <?php
            if ($canCurrentUserRemove) {
                // Remove from list button
                $this->_displayRemoveButton($sentenceId, $tagId);
            }
            // Sentences group
            $this->Sentences->displaySentencesGroup(
                $sentence,
                $transcriptions,
                $translations,
                $sentenceOwner,
                array('withAudio' => true)
            );
            ?>

        </div>
    <?php
    }

    private function _displayRemoveLink($tagId, $tagName, $sentenceId)
    {
        $removeTagFromSentenceAlt = format(
            __("remove tag '{tagName}' from this sentence.", true),
            compact('tagName')
        );
        // X link to remove tag from sentence
        echo $this->Html->link(
            'X',
            array(
                "controller" => "tags",
                "action" => "remove_tag_from_sentence",
                $tagId,
                $sentenceId
            ),
            array(
                "class" => "removeTagFromSentenceButton",
                "id" => 'deleteButton'.$tagId.$sentenceId,
                "title" => $removeTagFromSentenceAlt,
                "escape" => false
            ),
            null
        );
    }

    /**
     * Display an [X] button to remove the tag from the sentence.
     *
     * @param int $sentenceId Id of the sentence to remove the tag from.
     * @param int $tagId      Id of the tag to remove from the sentence.
     *
     * @return void
     */
    private function _displayRemoveButton($sentenceId, $tagId)
    {
        ?>
        <span class="removeFromList">
        <?php
        $removeFromListAlt = __("remove tag from sentence", true);
        $removeTagFromSentenceImg =  $this->Html->image(
            IMG_PATH . 'close.png',
            array(
                "class" => "removeTagButton",
                "id" => 'deleteButton'.$sentenceId,
                "alt" => $removeFromListAlt
            )
        );

        echo $this->Html->link(
            $removeTagFromSentenceImg,
            array(
                "controller" => "tags",
                "action" => "remove_tag_of_sentence_from_tags_show",
                $tagId,
                $sentenceId
            ),
            array("escape" => false),
            null,
            false
        );
        ?>
        </span>
        <?php
    }

}
