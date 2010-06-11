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

    public function displayTagsModule($sentenceId = null, $tagsArray)
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
            ?>
            <?php
            if (CurrentUser::isMember()) {
                $this->displayAddTagForm($sentenceId);
            }
            ?>
        </div>
        <?php
    }

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
     * Display sentence.
     *
     * @param array  $sentence           Sentence data.
     * @param string $translationsLang   Language of the translations.
     * @param bool   $canCurrentUserEdit 'true' if user remove sentence from list.
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
            // We may also want to run the script to update the count of sentences
            // in the lists, and remove unnecessary entries in the
            // sentences_sentences_lists table.
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
    
    
    private function _displayRemoveButton($sentenceId, $tagId) {
        ?>
        <span class="removeFromList">
        <script type='text/javascript'>
        $(document).ready(function() {
            $('#deleteButton<?php echo $sentenceId?>').data(
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
                "action" => "remove_tag_from_sentence",
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
