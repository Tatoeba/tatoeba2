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

use App\View\Helper\AppHelper;
use App\Model\CurrentUser;
use App\Model\Entity\User;
use Cake\Controller\Component\AuthComponent;
use Cake\ORM\TableRegistry;

/**
 * Helper to display buttons in sentences menu.
 *
 * @category Sentences
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class MenuHelper extends AppHelper
{

    public $helpers = array(
        'Html',
        'Form',
        'Pages',
        'Images'
    );


    /**
     * Display button to add a translation.
     *
     * @param int    $sentenceId Id of the original sentence.
     * @param bool   $isLogged   True if user is logged in, false otherwise.
     * @param bool   $enabled    True if sentence can be translated .Only sentences
     *                           that are not "unapproved" can be translated.
     *
     * @return void
     */
    public function translateButton($sentenceId, $isLogged, $enabled)
    {
        $translateButton = $this->Images->svgIcon(
            'translate',
            array(
                'width' => 16,
                'height' => 16
            )
        );

        echo $this->Html->tag('li', null, array(
            'class' => 'option translateLink',
            'data-sentence-id' => $sentenceId,
            'title' => __('Translate')
        ));

        if (!$enabled) {

            echo '<a class="disabled">';
            echo $this->Images->svgIcon(
                'translate',
                array(
                    'alt'=>__('Translate'),
                    'title'=>__(
                        'This sentence is currently marked as unapproved. '.
                        'Unapproved sentences cannot be translated.',
                        true
                    ),
                    'width' => 16,
                    'height' => 16
                )
            );
            echo '</a>';

        } else if ($isLogged) {
            ?>
            <a><?php echo $translateButton;?></a>
           <?php
        } else {
            echo $this->Html->link(
                $translateButton,
                array(
                    "controller" => "users",
                    "action" => "login",
                    "?" => array(AuthComponent::QUERY_STRING_REDIRECT => $this->Pages->currentPageUrl()),
                ),
                array(
                    "escape" => false
                ),
                null
            );
        }

        echo $this->Html->tag('/li');
    }

    /**
     * Display button to adopt a sentence.
     *
     * @param int    $sentenceId Id of the sentence on which this button
     *                           is displayed
     * @param string $ownerName  Indicates whether the sentence is adopted by current
     *                           user or not.
     * @param bool   $isNative   'true' if the owner is a native speaker in the
     *                           language of the sentence.
     *
     * @return void
     */
    public function adoptButton($sentenceId, $owner)
    {
        $ownerName = $owner ? $owner['username'] : null;
        $isNative = isset($owner['is_native']) ? $owner['is_native'] : false;
        $isAdopted = !empty($ownerName);
        $userAccountDeactivated = isset($owner['role']) ?
            in_array($owner['role'], [User::ROLE_SPAMMER, User::ROLE_INACTIVE]) : false;
        $currentUserIsAdvanced = CurrentUser::isTrusted();
        $isAdoptable = !$isAdopted || ($userAccountDeactivated
                && $currentUserIsAdvanced);
        $currentUserName = CurrentUser::get('username');
        $isOwnedByCurrentUser = $isAdopted && $ownerName == $currentUserName;

        $tooltip = null;
        $action = '';
        if ($isAdoptable) {
            $image = 'unadopted';
            $tooltip = __('Click to adopt');
            $action = ' add';
        } else {
            $image = 'adopted';
        }

        if ($isOwnedByCurrentUser) {
            $tooltip = __('Click to unadopt');
            $action = ' remove';
        }


        $svgIconOptions = array(
            'width' => 26,
            'height' => 16,
            'class' => 'option',
        );

        if (empty($action)) {
            $svgIconOptions['class'] .= ' adopt-item uneditable';
            $contents = $this->Images->svgIcon($image, $svgIconOptions);
        } else {
            $contents = $this->Images->svgIcon($image, $svgIconOptions);
            $contents = '<a class="adopt-item adopt-button">'.$contents.'</a>';
        }
        if ($isAdopted) {
            $contents .= $this->belongsTo($ownerName, $isNative);
        }
        echo $this->Html->tag('li', $contents, array(
            'class' => 'adopt'.$action,
            'data-sentence-id' => $sentenceId,
            'title'=> $tooltip,
        ));
    }


    /**
     * Display button to add to favorites.
     *
     * @param int  $sentenceId  Id of the sentence on which this button
     *                          is displayed
     * @param bool $isFavorited Indicates whether the sentence is favorited by
     *                          current user or not.
     * @param bool $isLogged    True if user is logged in, false otherwise.
     *
     * @return void
     */
    public function favoriteButton($sentenceId, $isFavorited, $isLogged, $withRemoveAndUndo = false)
    {
        if ($isFavorited) {
            $type = 'remove';
            if ($withRemoveAndUndo){
                $image = 'clear';
            } else {
                $image = 'favorite-remove';
            }
            $tooltip = __('Remove from favorites');
        } else {
            $type = 'add';
            if($withRemoveAndUndo){
                $image = 'undo';
                /* @translators: button after removing a favorite from the My favorites page (verb) */
                $tooltip = __('Undo');
            } else {
                $image = 'favorite-add';
                $tooltip = __('Add to favorites');
            }

        }

        $favoriteImage = $this->Images->svgIcon(
            $image,
            array(
                'width' => 16,
                'height' => 16
            )
        );

        $cssClass = array('option', 'favorite', $type);

        echo $this->Html->tag('li', null, array(
            'class' => join(' ', $cssClass),
            'data-sentence-id' => $sentenceId,
            'title' => $tooltip
        ));

        if ($isLogged) {
            ?>
            <a><?php echo $favoriteImage;?></a>
            <?php
        } else {
            echo $this->Html->link(
                $favoriteImage,
                array(
                    "controller" => "users",
                    "action" => "login",
                    "?" => array(AuthComponent::QUERY_STRING_REDIRECT => $this->Pages->currentPageUrl()),
                ),
                array(
                    "escape" => false
                ),
                null
            );
        }

        echo $this->Html->tag('/li');
    }

    public function linkToSentenceButton($sentenceId, $langFilter = 'und') {
        $langFilter = json_encode($langFilter);
        $image = $this->Images->svgIcon(
            'link',
            array(
                'width' => 16,
                'height' => 16
            )
        );

        $linkToSentenceButton = $this->Html->tag('a', $image,
            array(
                'title' => __('Link to another sentence'),
                'class' => 'linkTo',
                'onClick' => "linkToSentence($sentenceId, $langFilter)",
                'onDrop' => "linkToSentenceByDrop(event, $sentenceId, $langFilter)",
            )
        );
        ?>
        <li class="option"><?php echo $linkToSentenceButton; ?></li>

        <li style="display:none" id="linkTo<?php echo $sentenceId; ?>">
        <?php
        echo $this->Form->control('linkToSentence'.$sentenceId, array(
            'id' => 'linkToSentence'.$sentenceId,
            'label' => false,
            'placeholder' => __('Sentence number'),
            'class' => 'sentenceId'
        ));

        echo $this->Form->button(
            /* @translators: button to link translations (verb) */
            __('Link'),
            array(
                'type' => 'button',
                'id' => 'linkToSubmitButton'.$sentenceId,
                'class' => 'validateButton',
            )
        );

        ?>
        </li>
        <?php
    }

    /**
     * Display button to add a sentence to a list.
     *
     * @param int  $sentenceId Id of the sentence.
     * @param bool $isLogged   True if user is logged in, false otherwise.
     *
     * @return void
     */
    public function addToListButton($sentenceId, $isLogged)
    {
        $useMostRecentList = CurrentUser::get('settings.use_most_recent_list');
        if ($useMostRecentList != null && $useMostRecentList) {
            $mostRecentList = $this->request->getSession()->read('most_recent_list');
        } else {
            $mostRecentList = null;
        }
        $addToListButton = $this->Images->svgIcon(
            'list',
            array(
                'width' => 20,
                'height' => 16
            )
        );

        echo $this->Html->tag('li', null, array(
            'class' => 'option addToList',
            'data-sentence-id' => $sentenceId,
            'title' => __('Add to list')
        ));

        if ($isLogged) {
            ?>
            <a><?php echo $addToListButton; ?></a>
            <?php
        } else {
            echo $this->Html->link(
                $addToListButton,
                array(
                    "controller" => "users",
                    "action" => "login",
                    "?" => array(AuthComponent::QUERY_STRING_REDIRECT => $this->Pages->currentPageUrl()),
                ),
                array(
                    "escape" => false
                ),
                null
            );
        }

        echo $this->Html->tag('/li');

        if (!$isLogged) {
            return;
        }

        $SentencesLists = TableRegistry::getTableLocator()->get('SentencesLists');
        $lists = $SentencesLists->getUserChoices(
            CurrentUser::get('id'), $sentenceId
        );

        if (empty($lists['OfUser']) && empty($lists['Collaborative'])) {
            $selectItems[__('Sentence already in all possible lists')] = [];
        } else {
            $listsOfCurrentUser = __('Add to one of your lists');
            $listsEditableByAnyone = __('Add to a collaborative list');
            $selectItems[$listsOfCurrentUser] = $lists['OfUser'];
            $selectItems[$listsEditableByAnyone] = $lists['Collaborative'];
        }
        ?>

        <li style="display:none" id="addToList<?php echo $sentenceId; ?>">


        <?php
        echo $this->Form->select(
            'list',
            $selectItems,
            array(
                'id' => 'listSelection'.$sentenceId,
                "value" => $mostRecentList,
                "class" => "listOfLists",
                "empty" => false,
                "ng-non-bindable" => "",
            )
        );

        // ok button
        echo $this->Form->button(
            /* @translators: submit button to add a sentence to a list from the sentence block */
            __('OK'),
            array(
                'type' => 'button',
                'class' => 'validateButton',
                'data-sentence-id' => $sentenceId
            )
        );
        ?>
        </li>
    <?php
    }

    /**
     * Display button to delete.
     *
     * @param int $sentenceId Id of the sentence on which this button
     *                        is displayed
     *
     * @return void
     */
    public function deleteButton($sentenceId, $hasAudio)
    {
        $deleteImage = $this->Images->svgIcon(
            'delete',
            array(
                'width' => 20,
                'height' => 16
            )
        );

        if ($hasAudio) {

            $title = __(
                'You cannot delete this sentence because it has audio.', true
            );
            $liContent = $this->Html->tag(
                'a', $deleteImage, array('class' => 'disabled')
            );

        } else {

            /* @translators: delete button on sentence menu (verb) */
            $title = __('Delete');
            $liContent = $this->Html->link(
                $deleteImage,
                array(
                    "controller" => "sentences",
                    "action" => "delete",
                    $sentenceId
                ),
                array(
                    "escape" => false,
                    'alt'=> __('Delete'),
                    'title'=> $title,
                    'confirm' => __('Are you sure?')
                )
            );

        }

        echo $this->Html->tag('li', $liContent,
            array(
                'class' => 'option delete',
                'title'=> $title,
            )
        );
    }


    /**
     * Display button to edit a sentence.
     *
     * @param bool $hasAudio true if sentence has associated audio
     *
     * @return void
     */
    public function editButton($hasAudio)
    {
        $editImage = $this->Images->svgIcon(
            'edit',
            array(
                'width' => 16,
                'height' => 16
            )
        );

        if ($hasAudio) {
            $title = __('You cannot edit this sentence because it has audio.');
            $liContent = $this->Html->tag(
                'a', $editImage, array('class' => 'disabled')
            );
        } else {
            /* @translators: edit button on sentence menu (verb) */
            $title = __('Edit');
            $liContent = $editImage;
        }

        echo $this->Html->tag('li', $liContent,
            array(
                'class' => 'option edit',
                'title'=> $title,
            )
        );
    }

    private function transcribeButtons() {
        /* Buttons will be loaded using Javascript */
        echo $this->Html->tag('ul', '', array(
            'class' => 'transcribe-buttons',
        ));
    }

    /**
     * Return a link to owner's profile
     *
     * @param string $ownerName  The owner's name.
     * @param bool   $isNative   'true' if the owner is a native speaker in the
     *                           language of the sentence.
     *
     * @return string
     */
    private function belongsTo($ownerName, $isNative)
    {
        $belongsToTitle = format(
            __('belongs to {user}'),
            array('user' => $ownerName)
        );

        $username = $this->Html->link(
            $ownerName,
            array(
                "controller" => "user",
                "action" => "profile",
                $ownerName
            ),
            array(
                'title' => $belongsToTitle,
                'class' => 'adopt-item',
            )
        );

        $belongsToContent = array($username);
        if ($isNative) {
            $belongsToContent[] = $this->Html->tag('span',
                __('(native)'),
                array('class' => 'adopt-item is-native')
            );
        }

        return join('', $belongsToContent);
    }


    public function correctnessButton($sentenceId)
    {
        $userCorrectness = CurrentUser::correctnessForSentence($sentenceId);

        $icons = array(
            1 => array(
                'class' => 'ok',
                'icon' => 'ok',
                'tooltip' => __('Mark as "OK"')
            ),
            0 => array(
                'class' => 'unsure',
                'icon' => 'unsure',
                'tooltip' => __('Mark as "unsure"')
            ),
            -1 => array(
                'class' => 'not-ok',
                'icon' => 'not-ok',
                'tooltip' => __('Mark as "not OK"')
            )
        );

        echo '<ul class="correctness">';
        foreach($icons as $correctness => $icon) {
            $svgIcon = $this->Images->svgIcon(
                $icon['icon'], array('width' => 20, 'height' => 16)
            );
            $cssClass = array('option', 'add-to-corpus', $icon['class']);
            if ($correctness == $userCorrectness) {
                $cssClass[] = 'selected';
                $tooltip = __('Unmark sentence');
            } else {
                $tooltip = $icon['tooltip'];
            }
            echo $this->Html->tag('li',
                $this->Html->tag('a', $svgIcon),
                array(
                    'class' => join(' ', $cssClass),
                    'data-sentence-id' => $sentenceId,
                    'data-sentence-correctness' => $correctness,
                    'title' => $tooltip
                )
            );
        }
        echo '</ul>';
    }


    /**
     * Display menu for the main sentence.
     *
     * @param int    $sentenceId    Id of the sentence.
     * @param int    $ownerName     Owner of the sentence.
     * @param string $chineseScript For chinese only, 'traditional' or 'simplified'
     * @param array  $canTranslate  True if user can translate the sentence.
     *                              False otherwise.
     * @param array  $langFilter    Language filter of translations.
     * @param bool   $hasAudio      'true' if sentence has audio, 'false' otherwise.
     * @param bool   $isNative      'true' if the owner is a native speaker in the
     *                              language of the sentence.
     *
     * @return void
     */
    public function displayMenu(
        $sentenceId,
        $owner = null,
        $chineseScript = null,
        $canTranslate,
        $langFilter = 'und',
        $hasAudio = true,
        $isFavorited
    ) {
        ?>
        <ul class="menu">

        <?php
        $isLogged = CurrentUser::isMember();
        $ownerName = $owner ? $owner['username'] : null;

        // Adopt
        $this->adoptButton($sentenceId, $owner);

        // Translate
        $this->translateButton($sentenceId, $isLogged, $canTranslate);

        // Edit
        if (CurrentUser::canEditSentenceOfUser($ownerName)) {
           $this->editButton($hasAudio);
        }

        // Favorite
        $this->favoriteButton($sentenceId, $isFavorited, $isLogged);

        // Add to list
        $this->addToListButton($sentenceId, $isLogged);

        if (CurrentUser::isTrusted()) {
            $this->linkToSentenceButton($sentenceId, $langFilter);
        }

        if (CurrentUser::canRemoveSentence($sentenceId, null, $ownerName)) {
            // Delete
            $this->deleteButton($sentenceId, $hasAudio);
        }

        if ($isLogged && CurrentUser::get('settings.users_collections_ratings')) {
            $this->correctnessButton($sentenceId);
        }

        $this->transcribeButtons();
        ?>

        <li>
        <?php
        echo $this->Html->div('loader-small loader', '', array(
            'id' => '_'.$sentenceId.'_in_process',
            'style' => 'display:none',
        ));
        echo $this->Html->image(
            IMG_PATH . 'valid_16x16.png',
            array(
                "id" => "sentence".$sentenceId."_saved_in_list",
                "style" =>"display:none",
                "width" => 16,
                "height" => 16
            )
        );
        ?>
        </li>

        </ul>
        <div id="linkWarning<?= $sentenceId ?>" style="padding: 10px; border: 1px solid red; display: none;">
            <?= __('Tatoeba can automatically enter the sentence ID when you have copied a sentence URL. To enable this, please allow your browser to read from the clipboard.'); ?>
        </div>
    <?php
    }
}
?>
