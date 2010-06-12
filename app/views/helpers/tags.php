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
        ?>
        <div class="module">
            <h2><?php __('Tags'); ?></h2> 

            <?php
            echo ClassRegistry::init('View')->element(
                'tags',
                array(
                    "tagsArray" => $tagsArray,
                    "sentenceId" => $sentenceId
                )
            );
            
            if (CurrentUser::isMember()) {
                $this->displayAddTagForm($sentenceId);
            }
            ?>
        </div>
    <?php
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
