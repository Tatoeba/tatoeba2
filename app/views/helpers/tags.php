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
        $currentUser =  CurrentUser::get('id');
        ?>
        
        <div class="module">
            <h2><?php __('Tags'); ?></h2> 

            <div class="tagsListOnSentence" >
                <?php
                if (count($tagsArray) == 0) {
                    __('No tag on this sentence yet');
                }

                foreach ($tagsArray as $tagArray) {
                    ?>
                    <span class="tag">
                    <?php
                    $tagName =  $tagArray['Tag']['name'];
                    $tagInternalName =  $tagArray['Tag']['internal_name'];
                    $taggerId = $tagArray['TagsSentences']['user_id'];
                    $tagId = $tagArray['TagsSentences']['tag_id'];
                    
                    $this->displayTagLink($tagName, $tagInternalName);
                    if (CurrentUser::canRemoveTagFromSentence($taggerId)) {
                        $this->_displayRemoveLink($tagId, $tagName, $sentenceId);
                    }
                    ?>
                    </span>
                <?php
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
     *
     *
     */
    public function displayTagLink($tagName, $tagInternalName)
    {
        echo $this->Html->link(
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

    }

    /**
     * Display a little form to add a tag
     *
     * @param int $sentenceId If precise will add the tag only
     *                        To this sentence.
     *
     * @return void
     */

    public function displayAddTagForm($sentenceId = null)
    {
        echo $this->Form->create(
            'Tag',
            array(
                "action" => "add_tag",
                "type" => "post",
            )
        );
        echo $this->Form->input(
            'tag_name', 
            array(
                "label" => ''
            )
        );
        
        echo '<div>';
        echo $this->Form->hidden(
            'sentence_id',
            array('value' => $sentenceId)
        );
        echo '</div>';
        
        echo $this->Form->end('+');
    }

    
    /**
     * Display sentence for a list of tagged sentences.
     *
     * @param array $sentence             Sentence data.
     * @param array $sentenceOwner        Array with Sentence owner info. 
     * @param array $translations         Array with translations of this sentence.
     * @param array $indirectTranslations Array with Ind. translations of this
     *                                    sentence.
     * @param bool  $canCurrentUserRemove 'true' if user can remove tag from this
     *                                    sentence..
     * @param int   $tagId                Id of the tag.
     * 
     * @return void
     */
    public function displaySentence(
        $sentence,
        $sentenceOwner,
        $translations = array(),
        $indirectTranslations = array(),
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
            $withAudio = false;
            $this->Sentences->displaySentencesGroup(
                $sentence,
                $translations, 
                $sentenceOwner, 
                $indirectTranslations,
                $withAudio
            );
            ?>
            
        </div>
    <?php
    }

    private function _displayRemoveLink($tagId, $tagName, $sentenceId)
    {
        $removeTagFromSentenceAlt = sprintf(
            __("remove tag '%s' from this sentence.", true),
            $tagName
        );
        // X link to remove tag from sentence 
        echo $html->link(
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
                "title" => $removeTagFromSentenceAlt
            ),
            null,
            false
        );
    }
        
    /**
     * Display a [X] button to remove the tag from the sentence.
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
        <script type='text/javascript'>
        $(document).ready(function() {
            $('#deleteButton<?php echo $sentenceId; ?>').data(
                'sentenceId',
                <?php echo $sentenceId; ?>
            );
        });
        </script>
        
        <?php
        $removeFromListAlt = sprintf(
            __("remove tag from sentence", true)
        );

        $removeTagFromSentenceImg =  $this->Html->image(
            'close.png',
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
            array(),
            null,
            false
        );    
        ?>
        </span>
        <?php
    }
 
}
