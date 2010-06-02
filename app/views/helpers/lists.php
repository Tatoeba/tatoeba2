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

/**
 * Helper for sentences lists.
 *
 * @category Default
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class ListsHelper extends AppHelper
{
    public $helpers = array(
        'Html', 
        'Javascript', 
        'Form', 
        'Languages',
        'Sentences'
    );
    
    /** 
     * Display item of a list of lists.
     *
     * @param int     $listId          Id of the list to display.
     * @param string  $listName        Name of the list.
     * @param string  $listCreatorName Name of the list's creator.
     * @param boolean $isPublic        If the list is public or not.
     * @param int     $count           Number of sentences in the list.
     *
     * @return void
     */
    public function displayItem(
        $listId,
        $listName,
        $listCreatorName,
        $isPublic,
        $count = 0
    ) {
        echo '<li>';
        echo '<span id="_'.$listId.'" class="listName">';
        $name = '('.__('unnamed list', true).')';
        if (trim($listName) != '') {
            $name = $listName;
        }
        echo $this->Html->link(
            $name,
            array(
                "controller" => "sentences_lists",
                "action" => "edit",
                $listId
            )
        );
        echo '</span><span class="listInfo"> - ';
        echo sprintf(
            __('created by <a href="%s">%s</a>', true),
            $this->Html->url(
                array(
                    "controller"=>"user",
                    "action"=>"profile",
                    $listCreatorName
                )
            ),
            $listCreatorName
        );
        if ($isPublic) {
            echo ' <span class="publicList">'.__('(public list)', true) .'</span>';
        }
        echo '</span>';
        echo '</li>';
    }
    
    
    /**
     * display an array of lists in an HTML table
     *
     * @param array $arrayOfLists Terrible array of lists with array of array in it
     *
     * @return void
     */
    public function displayListTable($arrayOfLists)
    {
        ?>
        <table class="listIndex">
        <?php
        foreach ($arrayOfLists as $list) {
            $this->displayRow(
                $list['SentencesList']['id'],
                $list['SentencesList']['name'],
                $list['User']['username'],
                $list['SentencesList']['is_public'],
                $list['SentencesList']['numberOfSentences']
            );
        }
        ?>
        </table>
        <?php
    }


    /** 
     * Display row of a list of lists.
     *
     * @param int     $listId          Id of the list to display.
     * @param string  $listName        Name of the list.
     * @param string  $listCreatorName Name of the list's creator.
     * @param boolean $isPublic        If the list is public or not.
     * @param int     $count           Number of sentences in the list.
     *
     * @return void
     */
    public function displayRow(
        $listId,
        $listName,
        $listCreatorName,
        $isPublic,
        $count = 0
    ) {
        if (!CurrentUser::isMember()){
            $canEdit = false;
        } else {
            $belongsToCurrentUser = (CurrentUser::get('username') == $listCreatorName);
            $canEdit = $isPublic || $belongsToCurrentUser;
        }
        ?>
        <tr class="listSummary">
        
        <td class="nameAndCreator">
            <div class="name">
            <?php
            $name = '('.__('unnamed list', true).')';
            if (trim($listName) != '') {
                $name = $listName;
            }
            
            echo $this->Html->link(
                $name,
                array(
                    "controller" => "sentences_lists",
                    "action" => "show",
                    $listId
                )
            );
            ?>
            </div>
            
            <div class="creator">
            <?php
            $link = $this->Html->link(
                $listCreatorName,
                array(
                    "controller"=>"user",
                    "action"=>"profile",
                    $listCreatorName
                )
            );
            echo sprintf(__('created by %s', true), $link);
            ?>
            </div>
        </td>
        
        <td>
            <div class="count" title="<?php __('Number of sentences') ?>">
                <?php echo $count; ?>
            </div>
        </td>
        
        <td class="options">
            <span class="optionsContainer">
            <?php
            echo $this->Html->link(
                __('Show', true),
                array(
                    "controller" => "sentences_lists",
                    "action" => "show",
                    $listId
                )
            );
            
            if ($canEdit) {
                echo $this->Html->link(
                    __('Edit', true),
                    array(
                        "controller" => "sentences_lists",
                        "action" => "edit",
                        $listId
                    )
                );
            }
            ?>
            </span>
        </td>    
        
        </tr>
        <?php
    }
    
    
    /**
     * Display 'back to index' link.
     *
     * @return void
     */
    public function displayBackToIndexLink()
    {
        ?>
        <li>
        <?php
        echo $this->Html->link(
            __('Back to all the lists', true),
            array(
                "controller"=>"sentences_lists", 
                "action"=>"index"
            )
        );
        ?>
        </li>
        <?php
    }
    
    
    /**
     * Display 'back to this list' link.
     *
     * @return void
     */
    public function displayBackToListLink($listId)
    {
        ?>
        <li>
        <?php
        echo $this->Html->link(
            __('Back to this list', true),
            array(
                "controller"=>"sentences_lists", 
                "action"=>"show",
                $listId
            )
        );
        ?>
        </li>
        <?php
    }
    
    
    /**
     * Display 'download' link.
     *
     * @return void
     */
    public function displayDownloadLink($listId)
    {
        ?>
        <div class="download">
        <?php
        echo $this->Html->link(
            __('Download this list', true),
            array(
                "controller"=>"sentences_lists", 
                "action"=>"download",
                $listId
            )
        );
        ?>
        </div>
        <?php
    }
    
    
    /** 
     * Display actions that can be done by everyone.
     *
     * @param int    $listId           Id of the list.
     * @param string $translationsLang Language of the translations for the
     *                                 'correction version'.
     * @param string $action           Can be 'show' or 'edit'.
     *
     * @return void
     */
    public function displayPublicActions(
        $listId, $translationsLang = null, $action = null
    ) {
        
        $this->displayBackToIndexLink();
        ?>

        <li>
        <?php
        __('Show translations :'); echo ' ';
        
        $path = '/';
        if (!empty($this->params['lang'])) {
            $path .= $this->params['lang'] . '/';
        }
        $path .= 'sentences_lists/'.$action.'/'. $listId.'/';
        
        // TODO onChange should be define in a separate js file
        echo $this->Form->select(
            "translationLangChoice",
            $this->Languages->languagesArrayForLists(),
            $translationsLang,
            array(
                "onchange" => "$(location).attr('href', '".$path."' + this.value);"
            ),
            false
        );
        ?>
        </li>
        <?php
    }
    
    /** 
     * Display actions that are restricted to the creator of the list.
     *
     * @param int $listId       Id of the list.
     * @param string $action    Can be 'show' or 'edit'.
     * @param int $isListPublic true if list is public. false otherwise.
     *
     * @return void
     */
    public function displayRestrictedActions(
        $listId,
        $action,
        $isListPublic = false
    ) {
        ?>
        <li>
        <label for="isPublic"><?php __('Set list as public'); ?></label>
        <?php
        $this->Javascript->link('sentences_lists.set_as_public.js', false);
        if ($isListPublic) {
            $checkboxValue = 'checked';
        } else {
            $checkboxValue = '';
        }
        
        echo $this->Form->checkbox(
            'isPublic',
            array(
                "name" => "isPublic", 
                "checked" => $checkboxValue,
            )
        );
        echo $this->Html->image(
            'loading-small.gif',
            array("id"=>"inProcess", "style"=>"display:none;")
        );
        echo $this->Html->link(
            '[?]',
            array(
                "controller"=>"pages", 
                "action"=>"help#sentences_lists"
            )
        );
        ?>
        </li>

        <li class="otherAction" >
        <?php
        if ($action == "show") {
            $otherAction = "edit";
            $otherActionText = __("Edit this list", true);
        } else {
            $otherAction = "show";
            $otherActionText = __("View this list", true);
        }
        echo $this->Html->link(
            $otherActionText,
            array(
                "controller"=>"sentences_lists", 
                "action"=>$otherAction,
                $listId
            )
        );

        ?>
        </li>

        <li class="deleteList">
        <?php
        echo $this->Html->link(
            __('Delete this list', true),
            array(
                "controller" => "sentences_lists",
                "action" => "delete",
                $listId
            ),
            null,
            __('Are you sure?', true)
        );
        ?>
        </li>
        <?php
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
        $sentence, $translations = array(), $canCurrentUserEdit = false
    ) {
        if (empty($sentence['id'])) {
            // In case the sentence has been deleted, we don't want to display
            // it in the list.
            // We may also want to run the script to update the count of sentences
            // in the lists, and remove unnecessary entries in the
            // sentences_sentences_lists table.
            return;
        }
        ?>
        <div id="sentence<?php echo $sentence['id']; ?>" class="sentenceInList">        
        
            <?php
            if ($canCurrentUserEdit) {
                // Remove from list button
                $this->_displayRemoveButton($sentence['id']);
                
                // Sentences group
                $user = $sentence['User'];
                $withAudio = false;
                $indirectTranslations = array();
                $this->Sentences->displaySentencesGroup(
                    $sentence,
                    $translations, 
                    $user, 
                    $indirectTranslations,
                    $withAudio
                );
            } else {
                $this->Sentences->displaySimpleSentencesGroup(
                    $sentence, 
                    $translations
                );
            }
            ?>
            
        </div>
        <?php
    }
    
    
    private function _displayRemoveButton($sentenceId) {
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
        echo $this->Html->image(
            'close.png',
            array(
                "class" => "removeFromListButton",
                "id" => 'deleteButton'.$sentenceId
            )
        );
        ?>
        </span>
        <?php
    }
    
    /**
     * Form to add a new sentence to a list.
     *
     * @return void
     */
    public function displayAddSentenceForm($listId)
    {
        $this->Javascript->link(
            'sentences_lists.add_new_sentence_to_list.js', false
        );
        ?>
        <script type='text/javascript'>
        $(document).ready(function() {
            $('#sentencesList').data(
                'id', <?php echo $listId; ?>
            );
        });
        </script>
        
        <div id="newSentenceInList">
        <?php
        echo $this->Form->input(
            'text',
            array(
                "label" => __('Add a sentence to this list : ', true)
            )
        );
        echo $this->Form->button(
            'OK', array(
                "id" => "submitNewSentenceToList"
            )
        );
        ?>
        
        <p>
        <?php
        echo sprintf(
            __(
                'NOTE : You can also add existing sentences with this icon %s '.
                '(while <a href="%s">browsing</a> for instance).', true
            ),
            $this->Html->image('add_to_list.png'),
            $this->Html->url(array("controller"=>"sentences", "action"=>"show", "random"))
        );
        ?>
        </p>
        </div>
        
        <div class="sentencesListLoading" style="display:none">
        <?php echo $this->Html->image('loading.gif'); ?>
        </div>
        <?php
    }
}
?>
