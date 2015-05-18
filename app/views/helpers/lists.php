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
        'Sentences',
        'Date',
        'Images'
    );

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
                $list['SentencesList']['created'],
                $list['SentencesList']['numberOfSentences'],
                $list['SentencesList']['is_public']
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
     * @param string  $createdDate     If the list is public or not.
     * @param int     $count           Number of sentences in the list.
     *
     * @return void
     */
    public function displayRow(
        $listId,
        $listName,
        $listCreatorName,
        $createdDate,
        $count = 0,
        $isPublic = false
    ) {
        if ($isPublic) {
            $type = 'collaborative';
        } else {
            $type = 'personal';
        }
        ?>
        <tr class="listSummary">

        <td class="<?php echo $type ?>">
            <?php
            if ($isPublic) {
                echo $this->Images->svgIcon('users');
            }
            ?>
        </td>

        <td>
            <div class="name">
            <?php
            /* @translators: string used as a placeholder for
               the name of a list when it happens to be empty */
            $name = __('(unnamed list)', true);
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
            echo format(__('created by {listAuthor}', true),
                        array('listAuthor' => $link));
            ?>
            </div>
        </td>

        <td class="date">
            <?php
            echo $this->Html->tag(
                'span',
                $this->Date->ago($createdDate),
                array(
                    'class' => 'date'
                )
            );
            ?>
        </td>

        <td>
            <div class="count" title="<?php __('Number of sentences') ?>">
                <?php echo $count; ?>
            </div>
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
            __('Back to all lists', true),
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
        echo $this->Html->link(
            __('Download this list', true),
            array(
                "controller" => "sentences_lists",
                "action" => "download",
                $listId
            ),
            array(
                'class' => 'downloadLink'
            )
        );
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
        ?>

        <li>
        <?php
        __('Show translations :'); echo ' ';

        // TODO User $html->url()
        $path = '/';
        if (!empty($this->params['lang'])) {
            $path .= $this->params['lang'] . '/';
        }
        $path .= 'sentences_lists/'.$action.'/'. $listId.'/';
        
        // TODO onChange should be defined in a separate js file
        echo $this->Form->select(
            "translationLangChoice",
            $this->Languages->languagesArrayForPositiveLists(),
            $translationsLang,
            array(
                "onchange" => "$(location).attr('href', '".$path."' + this.value);",
                "class" => "language-selector",
                "empty" => false
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
        <label for="isPublicCheckbox"><?php __('Set list as collaborative'); ?></label>
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
                "id" => "isPublicCheckbox",
                "name" => "isPublic",
                "checked" => $checkboxValue,
                "data-list-id" => $listId
            )
        );
        echo $this->Images->svgIcon(
            'loading',
            array(
                'id' => 'inProcess',
                'class' => 'loading',
                'height' => 16,
                'width' => 16
            )
        );
        echo $this->Html->link(
            '[?]',
            array(
                "controller"=>"pages", 
                "action"=>"help#sentences_lists_help"
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
     * @param array  $transcriptions     Sentence transcriptions.
     * @param string $translationsLang   Language of the translations.
     * @param bool   $canCurrentUserEdit 'true' if user remove sentence from list.
     *
     * @return void
     */
    public function displaySentence(
        $sentence,
        $transcriptions,
        $translations = array(),
        $canCurrentUserEdit = false
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
            // Remove from list button
            if ($canCurrentUserEdit) {
                $this->_displayRemoveButton($sentence['id']);
            }

            // Sentences group
            $user = $sentence['User'];
            $withAudio = true;
            $indirectTranslations = array();
            $this->Sentences->displaySentencesGroup(
                $sentence,
                $transcriptions,
                $translations,
                $user,
                $indirectTranslations,
                array('withAudio' => false)
            );
            ?>
        </div>
        <?php
    }


    private function _displayRemoveButton($sentenceId) {
        ?>
        <span class="removeFromList">

        <?php
        $removeFromListAlt = format(
            __("Remove sentence {number} from list", true),
            array('number' => $sentenceId)
        );

        echo $this->Html->image(
            IMG_PATH . 'close.png',
            array(
                "class" => "removeFromListButton",
                "alt" => $removeFromListAlt,
                "title" => __("Remove from list", true),
                "data-sentence-id" => $sentenceId
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

        <div id="newSentenceInList">
        <?php
        echo $this->Form->input(
            'text',
            array(
                'label' => __('Add a sentence to this list : ', true),
                'class' => 'new-sentence-input'
            )
        );
        echo $this->Form->button(
            __('OK', true),
            array(
                'id' => 'submitNewSentenceToList',
                'class' => 'submit'
            )
        );

        echo $this->Images->svgIcon(
            'loading',
            array(
                'width' => 20,
                'height' => 20,
                'class' => 'adding-new-sentence-in-list'
            )
        );
        ?>

        <p>
        <?php
        echo format(
            __(
                'NOTE : You can also add existing sentences with this icon {addToListButton} '.
                '(while <a href="{url}">browsing</a> for instance).', true
            ),
            array(
                'addToListButton' => $this->Html->image(IMG_PATH . 'list.svg', array('height' => 16)),
                'url' => $this->Html->url(array(
                    'controller' => 'sentences',
                    'action' => 'show',
                    'random'
                ))
            )
        );
        ?>
        </p>
        </div>
        <?php
    }


    public function displayListsModule($listsArray)
    {
        if (count($listsArray) > 0) {
            echo '<div class="module">';
            echo $this->Html->tag('h2', __('Lists', true));
            echo '<ul class="sentence-lists">';
            foreach($listsArray as $list) {
                if ($list['SentencesList']['is_public']) {
                    $class = 'public-list';
                } else {
                    $class = 'personal-list';
                }
                echo '<li class="'.$class.'">';
                echo $this->Html->link(
                    $list['SentencesList']['name'],
                    array(
                        'controller' => 'sentences_lists',
                        'action' => 'show',
                        $list['SentencesList']['id']
                    )
                );
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
    }


    public function displayCreateListForm()
    {
        ?>
        <div class="module">
            <h2><?php __('Create a new list'); ?></h2>
            <?php
            echo $this->Form->create(
                'SentencesList',
                array(
                    "action" => "add",
                    "type" => "post",
                )
            );
            echo $this->Form->input(
                'name',
                array(
                    'type' => 'text',
                    'label' => __p('list', 'Name', true)
                )
            );
            echo $this->Form->end(__('create', true));
            ?>
        </div>
        <?php
    }


    public function displaySearchForm($search, $extraHiddenParams = null)
    {
        ?>
        <div class="module">
            <?php
            echo $this->Html->tag('h2', __('Search', true));

            echo $this->Form->create('SentencesList', array('type' => 'get'));

            if (!empty($extraHiddenParams)) {
                foreach ($extraHiddenParams as $key => $value) {
                    echo $this->Form->hidden($key, array('value' => $value));
                }
            }

            echo $this->Form->input(
                'search',
                array(
                    'value' => $search,
                    'label' => false
                )
            );

            echo $this->Form->submit(__('Search', true));

            echo $this->Form->end();
            ?>
        </div>
        <?php
    }

    public function displayListsLinks()
    {
        ?>
        <div class="module">
            <?php
            echo $this->Html->tag('h2', __('Lists', true));
            echo '<ul class="annexeMenu">';

            echo '<li class="item">';
            echo $this->Html->link(
                __('All lists', true),
                array(
                    'controller' => 'sentences_lists',
                    'action' => 'index'
                )
            );
            echo '</li>';

            echo '<li class="item">';
            echo $this->Html->link(
                __('Collaborative lists', true),
                array(
                    'controller' => 'sentences_lists',
                    'action' => 'collaborative'
                )
            );
            echo '</li>';

            if (CurrentUser::isMember()) {
                echo '<li class="item">';
                echo $this->Html->link(
                    __('My lists', true),
                    array(
                        'controller' => 'sentences_lists',
                        'action' => 'of_user',
                        CurrentUser::get('username')
                    )
                );
                echo '</li>';
            }
            echo '</ul>';
            ?>
        </div>
        <?php
    }
}
?>
