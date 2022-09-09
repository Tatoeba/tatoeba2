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
 * @link     https://tatoeba.org
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
 * @link     https://tatoeba.org
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
        'Url',
        'Number'
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
                     /* @translators: sort option in list of lists */
                     echo __('created');
                     ?>
                </td>
                <td class="date lastUpdatedDate">
                    <?php
                     /* @translators: sort option in list of lists */
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
                $this->_View->safeForAngular($name),
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
                <?php echo $this->Number->format($count); ?>
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
    public function displayTranslationsDropdown($listId,$filterLanguage, $translationsLang = null) {
        ?>
        <div class="section md-whiteframe-1dp">
            <h2><?php echo __('Show translations in:'); ?></h2>
            <?php
            $path = $this->Url->build(['action' => 'show', $listId]) . '/';
            // TODO onSelectedLanguageChange should be defined in a separate js file
            echo $this->_View->element(
                'language_dropdown',
                array(
                    'name' => 'translationLangChoice',
                    'languages' => $this->Languages->languagesArrayShowTranslationsIn(),
                    'initialSelection' => $translationsLang,
                    'onSelectedLanguageChange' => "window.location.pathname = '$path' + '$filterLanguage' + '/'+language.code",
                    'forceItemSelection' => true,
                )
            );
            ?>
            </div>
        <?php
    }

    /**
     * Display dropdown for selecting the language/s used to filter list
     *
     * @param [int] $listId 
     * @param [string] $translationsLang Translation languages for each sentence
     * @param [string] $filterLanguage Language whose sentences will only be displayed
     * @return void
     */
    public function displayFilterByLangDropdown($listId, $filterLanguage, $translationsLang)
    {
        ?>
        <div class="section md-whiteframe-1dp">
            <h2><?php echo __('Sentences in:'); ?></h2>
            <?php
            $path = $this->Url->build(['action' => 'show', $listId]) . '/';
            echo $this->_View->element(
                'language_dropdown',
                array(
                    'name' => 'filterLanguageSelect',
                    'languages' => $this->Languages->languagesArrayAlone(),
                    'initialSelection' => $filterLanguage,
                    'onSelectedLanguageChange' => "window.location.pathname = '$path' +language.code +'/'+ '$translationsLang'",
                    'forceItemSelection' => true,
                )
            );
            ?>
        </div>
    <?php
    }

    public function displayVisibilityOption($listId, $value)
    {
        $this->Html->script(
            JS_PATH . 'sentences_lists.set_option.js', array('block' => 'scriptBottom')
        );
        ?>
        <dl>
            <?php
            $title = __('List visibility');
            $loader = $this->Html->tag(
                'md-progress-circular',
                '',
                [
                    'class' => 'loader-container',
                    'md-diameter' => '16',
                    'ng-show' => 'visibilityProgress',
                ]
            );
            echo $this->Html->tag('dt', $title . $loader);
            ?>
            <input type="radio"  name="visibility" data-list-id='<?= $listId ?>'  value="{{visibility}}" checked hidden ng-init="visibility = '<?= $value ?>';"/>
            <md-radio-group ng-model='visibility' ng-change='visibilityChanged()'>
                <md-radio-button value='public'
                                 class='md-primary'
                                 title='<?= h(__(
                                     "The list is accessible to anyone and is listed on the "
                                    ."'Browse by list' page, as well as on the sentence page "
                                    ."for every sentence it contains.")) ?>'>
                    <?php /* @translators: visibility option of a list */ ?>
                    <?= __('Public') ?>
                </md-radio-button>
                <md-radio-button value='listed' class='md-primary' title=
                                 '<?= h(__("The list is accessible to anyone and is "
                                          ."listed on the 'Browse by list' page.")) ?>'>
                    <?php /* @translators: visibility option of a list */ ?>
                    <?=  __('Listed');?>
                </md-radio-button>
                <md-radio-button value='unlisted' class='md-primary' title=
                                 '<?= h(__("The list is accessible to anyone but is not "
                                          ."listed on the 'Browse by list' page."))?>'>
                    <?php /* @translators: visibility option of a list */ ?>
                    <?=  __('Unlisted') ?>
                </md-radio-button>
                <md-radio-button value='private' class='md-primary' title=
                                 '<?= h(__("The list is accessible only to you."))?>'>
                    <?php /* @translators: visibility option of a list */ ?>
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
        $this->Html->script(
            JS_PATH.'sentences_lists.set_option.js', array('block' => 'scriptBottom')
        );
        ?>
        <dl>
            <?php
                if(empty($value)){
                    $value = "no_one";
                }
                $title = __('Who can add/remove sentences');
                $loader = $this->Html->tag(
                    'md-progress-circular',
                    '',
                    [
                        'class' => 'loader-container',
                        'md-diameter' => '16',
                        'ng-if' => 'editableProgress',
                    ]
                );
                echo $this->Html->tag('dt', $title.$loader);
            ?>
            <input type="radio"  name="editable_by" data-list-id='<?= $listId ?>'  value="{{editable}}" checked hidden ng-init="editable = '<?= $value ?>';"/>
            <md-radio-group ng-model='editable' ng-change='editableChanged("{{editable}}")'>
                <md-radio-button value='anyone' class='md-primary'>
                    <?php /* @translators: option when choosing who can edit a list */ ?>
                    <?=  __('Anyone') ?>
                </md-radio-button>
                <md-radio-button value='creator' class='md-primary'>
                    <?php /* @translators: option when choosing who can edit a list */ ?>
                    <?= __('Only me') ?>
                </md-radio-button>
                <md-radio-button value='no_one' class='md-primary'>
                    <?php /* @translators: option when choosing who can edit a list */ ?>
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
      * Function shows X behind the List to remove it
      */
    private function _displayRemoveLink($listId, $sentenceId, $listName)
    {
        $removeSentenceFromListAlt = format(
            __("Remove this sentence from '{listName}'."),
                compact('listName')
        );

        $removeSentenceFromListImg =  $this->Html->image(
            IMG_PATH . 'close.png',
            array(
                "class" => "removeFromListButton",
                "id" => 'deleteFromListButton'.$listId,
                "alt" => $removeSentenceFromListAlt
            )
        );
        // X link to remove sentence from List
        echo $this->Html->link(
            $removeSentenceFromListImg,
            array(
                "controller" => "sentences_lists",
                "action" => "remove_sentence_from_list",
                $sentenceId,
                $listId
            ),
            array(
                "class" => "removeSentenceFromListButton",
                "id" => 'deleteButton'.$listId.$sentenceId,
                "title" => h($removeSentenceFromListAlt),
                "escape" => false
            )
        );
    }
    /**
     * Form to add a new sentence to a list.
     *
     * @return void
     */
    public function displayAddSentenceForm($listId)
    {
        $this->Html->script(
            'sentences_lists.add_new_sentence_to_list.js', array('block' => 'scriptBottom')
        );
        ?>

        <div id="newSentenceInList" class="section md-whiteframe-1dp">
        <?php
        echo $this->Form->input(
            'text',
            array(
                'label' => __('Add a sentence to this list : '),
                'class' => 'new-sentence-input'
            )
        );
        echo $this->Form->button(
            /* @translators: submit button of sentence addition form on list page */
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


    public function displayListsModule($listsArray, $sentences)
    {
        $sentenceId = $sentences->id;
        $currentUserId = CurrentUser::get('id');

        echo '<div class="section md-whiteframe-1dp">';
        /* @translators: header text on the sidebar of a sentence page */
        echo $this->Html->tag('h2', __('Lists'));

        if (count($listsArray) > 0) {
            echo '<ul class="sentence-lists">';
            foreach($listsArray as $list) {
                $list = $list->sentences_list;
                $listName = $this->_View->safeForAngular($list['name']);
                if ($list['visibility'] == 'public') {
                    $class = 'public-list';
                } else {
                    $class = 'personal-list';
                }
                echo '<li class="'.$class.'">';
                echo $this->Html->link(
                    $listName,
                    array(
                        'controller' => 'sentences_lists',
                        'action' => 'show',
                        $list['id']
                    )
                );
                if (CurrentUser::isMember()) {
                    if (($list['user_id'] == $currentUserId &&
                         $list['editable_by'] != 'no_one')
                        || $list['editable_by'] == 'anyone')
                    {
                       echo $this->_displayRemoveLink($list['id'], $sentenceId, $listName);
                    }
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        echo '</div>';
    }


    public function displayCreateListForm()
    {
        ?>
        <div class="section md-whiteframe-1dp">
            <h2><?php echo __('Create a new list'); ?></h2>
            <?php
            echo $this->Form->create('SentencesList', [
                'url' => ['action' => 'add'],
                'type' => 'post',
            ]);
            ?>

            <md-input-container layout="column">
                <?php
                echo $this->Form->control('name', [
                    /* @translators: field for the name of a list to create (noun) */
                    'label' => __x('list', 'Name')
                ]);
                ?>
                <md-button type="submit" class="md-raised md-primary">
                    <?php /* @translators: button to create a new list from the lists of list page (verb) */ ?>
                    <?= __('Create') ?>
                </md-button>
            </md-input-container>

            <?php
            echo $this->Form->end();
            ?>
        </div>
        <?php
    }


    public function displaySearchForm($search, $extraHiddenParams = null)
    {
        ?>
        <div class="section md-whiteframe-1dp">
            <?php
            /* @translators: header text in List of lists page (noun) */
            echo $this->Html->tag('h2', __x('header', 'Search'));

            echo $this->Form->create('SentencesList', ['type' => 'get']);

            if (!empty($extraHiddenParams)) {
                foreach ($extraHiddenParams as $key => $value) {
                    echo $this->Form->hidden($key, array('value' => $value));
                }
            }
            ?>

            <md-input-container layout="column">
                <?php
                echo $this->Form->input('search', [
                    'value' => $this->_View->safeForAngular($search),
                    'label' => false
                ]);
                ?>

                <md-button type="submit" class="md-raised">
                    <?php /* @translators: search form button in List of lists pages (verb) */ ?>
                    <?= __x('button', 'Search') ?>
                </md-button>
            </md-input-container>

            <?php
            echo $this->Form->end();
            ?>
        </div>
        <?php
    }

    public function displayListsLinks()
    {
        ?>
        <md-list class="annexe-menu md-whiteframe-1dp" ng-cloak>
            <?php /* @translators: header text in sidebar on pages related to sentences lists */ ?>
            <md-subheader><?= __('Lists') ?></md-subheader>

            <?php
            $url = $this->Url->build([
                'controller' => 'sentences_lists',
                'action' => 'index'
            ]);
            ?>
            <md-list-item href="<?= $url ?>">
                <md-icon>keyboard_arrow_right</md-icon>
                <p><?= __('All public lists') ?></p>
            </md-list-item>

            <?php
            $url = $this->Url->build([
                'controller' => 'sentences_lists',
                'action' => 'collaborative'
            ]);
            ?>
            <md-list-item href="<?= $url ?>">
                <md-icon>keyboard_arrow_right</md-icon>
                <p><?= __('Collaborative lists') ?></p>
            </md-list-item>

            <?php
            if (CurrentUser::isMember()) {
                $url = $this->Url->build([
                    'controller' => 'sentences_lists',
                    'action' => 'of_user',
                    CurrentUser::get('username')
                ]);
                ?>
                <md-list-item href="<?= $url ?>">
                    <md-icon>keyboard_arrow_right</md-icon>
                    <p><?= __('My lists') ?></p>
                </md-list-item>
                <?php
            }
            ?>
        </md-list>
        <?php
    }

    /**
     * Returns the selectable lists for the current user
     */
    public function listsAsSelectable($lists)
    {
        /* @translators: dropdown option when no list is selected (used to filter by list in advanced search) */
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
