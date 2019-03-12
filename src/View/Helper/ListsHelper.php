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
namespace App\View\Helper;

use App\Model\CurrentUser;
use App\View\Helper\AppHelper;
use Cake\Utility\Hash;

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
        'Form',
        'Languages',
        'Sentences',
        'Date',
        'Images',
        'Pages',
        'Url'
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
		//Do not display the table if there is nothing to display
        if(count($arrayOfLists) == 0){
	        return;
        }
        ?>
        <table class="listIndex noNameMinLength">
             <tr class="listSummary noBorder">
                 <td></td>
                 <td></td>
                <td class="date createdDate">
                     <?php
                     echo __('created');
                     ?>
                </td>
                <td class="date lastUpdatedDate">
                    <?php
                     echo __('last updated');
                     ?>
                </td>
                <td></td>
            </tr>
        <?php
        foreach ($arrayOfLists as $list) {
            $this->displayRow(
                $list->id,
                $list->name,
                $list->user->username,
                $list->created,
                $list->modified,
                $list->numberOfSentences,
                $list->visibility,
                $list->editable_by
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
        $modifiedDate,
        $count = 0,
        $visibility = 'private',
        $editableBy = 'creator'
    ) {
        $listStatus = 'activeList';

        if ($editableBy == 'no_one') {
            $listStatus = 'inactiveList';
        }
        ?>
        <tr class="listSummary <?php echo $listStatus; ?>">

        <td class="icon">
            <?php
            if ($editableBy == 'anyone') {
                echo $this->Images->svgIcon('users');
            }
            if ($visibility == 'private') {
                echo $this->Images->svgIcon('lock');
            } else if ($visibility == 'unlisted') {
                echo $this->Images->svgIcon('hidden');
            }
            ?>
        </td>

        <td>
            <div class="name">
            <?php
            /* @translators: string used as a placeholder for
               the name of a list when it happens to be empty */
            $name = __('(unnamed list)');
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
            echo format(__('created by {listAuthor}'),
                         array('listAuthor' => $link));

             ?>
             </div>
         </td>

         <td>
            <div class='createdDate'>
                <?php
                 echo $this->Html->tag(
                    'span',
                    $this->Date->ago($createdDate),
                    array(
                        'class' => 'date'
                    )
                );
            ?>
            </div>
        </td>
         <td class="date lastUpdatedDate">
             <?php
             echo $this->Html->tag(
                 'span',
                 $this->Date->ago($modifiedDate),
                 array(
                     'class' => 'date'
                 )
             );
            ?>
        </td>

        <td>
            <div class="count" title="<?php echo __('Number of sentences') ?>">
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
            __('Back to all lists'),
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
            __('Back to this list'),
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
        $url = $this->Url->build(
            array(
                "controller" => "sentences_lists",
                "action" => "download",
                $listId
            )
        );
        ?>
        <md-button class="md-raised md-primary" href="<?= $url ?>">
            <?php echo __('Download this list'); ?>
        </md-button>
        <?php
    }


    /**
     * Display dropdownlist for translations.
     *
     * @param int    $listId           Id of the list.
     * @param string $translationsLang Language of the translations for the
     *                                 'correction version'.
     *
     * @return void
     */
    public function displayTranslationsDropdown($listId, $translationsLang = null) {
        echo __('Show translations :') . ' ';

        // TODO User $this->Url->build()
        $path = '/';
        if (!empty($this->request->params['lang'])) {
            $path .= $this->request->params['lang'] . '/';
        }
        $path .= 'sentences_lists/show/'. $listId.'/';

        // TODO onChange should be defined in a separate js file
        echo $this->Form->select(
            "translationLangChoice",
            $this->Languages->languagesArrayForPositiveLists(),
            array(
                "value" => $translationsLang,
                "onchange" => "$(location).attr('href', '".$path."' + this.value);",
                "class" => "language-selector",
                "empty" => false
            ),
            false
        );


    }

    public function displayVisibilityOption($listId, $value)
    {
        $this->Pages->appendDeferredScript(JS_PATH . 'sentences_lists.set_option.js');
        ?>
        <dl>
            <?php
            $title = __('List visibility');
            $loader = "<md-progress-circular class='is-public loader-container' md-diameter='16' style='display: none'> </md-progress-circular>";
            echo $this->Html->tag('dt', $title . $loader);
            ?>
            <input type="radio"  name="visibility" data-list-id='<?= $listId ?>'  value="{{visibility}}" checked hidden ng-init="visibility = '<?= $value ?>';"/>
            <md-radio-group ng-controller='optionsCtrl' ng-model='visibility' ng-change='visibilityChanged()'>
                <md-radio-button value='public' class='md-primary'>
                    <?=  __('Public') ?>
                </md-radio-button>
                <md-radio-button value='unlisted' class='md-primary'>
                    <?=  __('Unlisted') ?>
                </md-radio-button>
                <md-radio-button value='private' class='md-primary'>
                    <?=  __('Private') ?>
                </md-radio-button>
            </md-radio-group>
        </dl>
        <?php
    }

    /**
     * Display editable_by options for sentence lists.
     *
     * @param int    $listId List ID.
     * @param string $value  Currently set option.
     */
    public function displayEditableByOptions($listId, $value)
    {
        $this->Pages->appendDeferredScript(JS_PATH.'sentences_lists.set_option.js');
        ?>
        <dl>
            <?php
                if(empty($value)){
                    $value = "no_one";
                }
                $title = __('Who can add/remove sentences');
                $loader = "<md-progress-circular class='is-editable loader-container' md-diameter='16' style='display: none'> </md-progress-circular>";
                echo $this->Html->tag('dt', $title.$loader);
            ?>
            <input type="radio"  name="editable_by" data-list-id='<?= $listId ?>'  value="{{editable}}" checked hidden ng-init="editable = '<?= $value ?>';"/>
            <md-radio-group ng-controller='optionsCtrl' ng-model='editable' ng-change='editableChanged()'>
                <md-radio-button value='anyone' class='md-primary'>
                    <?=  __('Anyone') ?>
                </md-radio-button>
                <md-radio-button value='creator' class='md-primary'>
                    <?= __('Only me') ?>
                </md-radio-button>
                <md-radio-button value='no_one' class='md-primary'>
                    <?= __('No one (list inactive)') ?>
                </md-radio-button>
            </md-radio-group>
        </dl>
        <?php
    }

    public function displayDeleteButton($listId)
    {
        $url = $this->Url->build(
            array(
                "controller" => "sentences_lists",
                "action" => "delete",
                $listId
            )
        );
        $confirmation = __('Are you sure?');
        ?>
        <md-button type="submit" class="md-raised md-warn"
                   href="<?= $url; ?>"
                   onclick="return confirm('<?= $confirmation; ?>');">
            <?php echo __('Delete this list'); ?>
        </md-button>
        <?php
    }


    /**
     * Display sentence.
     *
     * @param array  $sentence           Sentence data.
     * @param bool   $canCurrentUserEdit 'true' if user remove sentence from list.
     *
     * @return void
     */
    public function displaySentence(
        $sentence,
        $canCurrentUserEdit = false
    ) {
        $sentenceId = $sentence->id;
        if (!$sentence) {
            // In case the sentence has been deleted, we don't want to display
            // it in the list.
            // We may also want to run the script to update the count of sentences
            // in the lists, and remove unnecessary entries in the
            // sentences_sentences_lists table.
            return;
        }
        ?>
        <div id="sentence<?php echo $sentenceId; ?>" class="sentenceInList">

            <?php
            // Remove from list button
            if ($canCurrentUserEdit) {
                $this->_displayRemoveButton($sentenceId);
            }

            // Sentences group
            $withAudio = true;
            if (!isset($sentence['Translation'])) {
                $sentence['Translation'] = array();
            }
            $this->Sentences->displaySentencesGroup($sentence);
            ?>
        </div>
        <?php
    }


    private function _displayRemoveButton($sentenceId) {
        ?>
        <span class="removeFromList">

        <?php
        $removeFromListAlt = format(
            __("Remove sentence {number} from list"),
            array('number' => $sentenceId)
        );

        echo $this->Html->image(
            IMG_PATH . 'close.png',
            array(
                "class" => "removeFromListButton",
                "alt" => $removeFromListAlt,
                "title" => __("Remove from list"),
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
        $this->Pages->appendDeferredScript('sentences_lists.add_new_sentence_to_list.js');
        ?>

        <div id="newSentenceInList">
        <?php
        echo $this->Form->input(
            'text',
            array(
                'label' => __('Add a sentence to this list : '),
                'class' => 'new-sentence-input'
            )
        );
        echo $this->Form->button(
            __('OK'),
            array(
                'id' => 'submitNewSentenceToList',
                'class' => 'submit'
            )
        );
        echo "<md-progress-circular id='list_add_loader' md-diameter='16' style='display: none;'></md-progress-circular>";
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
                'url' => $this->Url->build(array(
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
            echo $this->Html->tag('h2', __('Lists'));
            echo '<ul class="sentence-lists">';
            foreach($listsArray as $list) {
                $list = $list->sentences_list;
                if ($list['visibility'] == 'public') {
                    $class = 'public-list';
                } else {
                    $class = 'personal-list';
                }
                echo '<li class="'.$class.'">';
                echo $this->Html->link(
                    $list['name'],
                    array(
                        'controller' => 'sentences_lists',
                        'action' => 'show',
                        $list['id']
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
            <h2><?php echo __('Create a new list'); ?></h2>
            <?php
            echo $this->Form->create(
                'SentencesList',
                array(
                    "url" => array("action" => "add"),
                    "type" => "post",
                )
            );
            echo $this->Form->control('name', [
                'label' => __x('list', 'Name')
            ]);
            echo $this->Form->button(__('create'));
            echo $this->Form->end();
            ?>
        </div>
        <?php
    }


    public function displaySearchForm($search, $extraHiddenParams = null)
    {
        ?>
        <div class="module">
            <?php
            echo $this->Html->tag('h2', __('Search'));

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

            echo $this->Form->submit(__('Search'));

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
            echo $this->Html->tag('h2', __('Lists'));
            echo '<ul class="annexeMenu">';

            echo '<li class="item">';
            $listScope = __('All public lists');
            echo $this->Html->link(
                $listScope,
                array(
                    'controller' => 'sentences_lists',
                    'action' => 'index'
                )
            );
            echo '</li>';

            echo '<li class="item">';
            echo $this->Html->link(
                __('Collaborative lists'),
                array(
                    'controller' => 'sentences_lists',
                    'action' => 'collaborative'
                )
            );
            echo '</li>';

            if (CurrentUser::isMember()) {
                echo '<li class="item">';
                echo $this->Html->link(
                    __('My lists'),
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

    /**
     * Returns the selectable lists for the current user
     */
    public function listsAsSelectable($lists)
    {
        $unspecified = __x('list', 'Unspecified');
        if (CurrentUser::isMember()) {
            $sortedLists = array(0 => array(), 1 => array());
            $currentUserId = CurrentUser::get('id');
            foreach ($lists as $list) {
                $where = (int)($list['user_id'] != $currentUserId);
                $listId   = $list['id'];
                $listName = $list['name'];
                $sortedLists[$where][$listId] = $listName;
            }

            $listsOfCurrentUser = __('My lists');
            $othersLists        = __('Other lists');
            return array(
                '' => $unspecified,
                $listsOfCurrentUser => $sortedLists[0],
                $othersLists        => $sortedLists[1],
            );
        } else {
            $allLists = Hash::combine(
                $lists,
                '{n}.id',
                '{n}.name'
            );
            return array('' => $unspecified) + $allLists;
        }
    }
}
?>
