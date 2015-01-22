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
 * Helper to display buttons in sentences menu.
 *
 * @category Sentences
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class MenuHelper extends AppHelper
{

    public $helpers = array(
        'Html',
        'Javascript',
        'Form',
        'Session'
    );


    /**
     * Display button to add a translation.
     *
     * @param int    $sentenceId Id of the original sentence.
     * @param string $ownerName  Username of the owner of the main sentence.
     * @param bool   $isLogged   True if user is logged in, false otherwise.
     * @param bool   $enabled    True if sentence can be translated .Only sentences
     *                           that are not "unapproved" can be translated.
     *
     * @return void
     */
    public function translateButton($sentenceId, $ownerName, $isLogged, $enabled)
    {
        $translateButton = $this->Html->image(
            IMG_PATH . 'translate.svg',
            array(
                'alt'=>__('Translate', true),
                'title'=>__('Translate', true),
                'width' => 16,
                'height' => 16
            )
        );
        ?>

        <li class="option translateLink"
            id="translate_<?php echo $sentenceId; ?>">

        <?php
        if (!$enabled) {
            
            echo '<a class="disabled">';
            echo $this->Html->image(
                IMG_PATH . 'translate.svg',
                array(
                    'alt'=>__('Translate', true),
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
            $this->Javascript->link('sentences.add_translation.js', false);
            ?>

            <script type='text/javascript'>
            $(document).ready(function() {
                $('#translate_<?php echo $sentenceId; ?>').data(
                    'sentenceId',
                    <?php echo $sentenceId; ?>
                );
                $('#translate_<?php echo $sentenceId; ?>').data(
                    'parentOwnerName',
                    '<?php echo $ownerName; ?>'
                );
            });
            </script>
            <a><?php echo $translateButton;?></a>
           <?php
        } else {
            echo $this->Html->link(
                $translateButton,
                array(
                    "controller" => "users",
                    "action" => "login",
                ),
                array(
                    "escape" => false
                ),
                null
            );
        }
        ?>
        </li>
    <?php
    }


    /**
     * Display button to notify the chinese sentence is in
     * simplified script
     *
     * @return void
     */
    public function simplifiedButton()
    {
        ?>
        <li class="option simplified">
        <?php
        echo $this->Html->image(
            IMG_PATH . 'simplified_chinese.png',
            array(
                'alt'=>__('This sentence is in simplified Chinese.', true),
                'title'=>__('This sentence is in simplified Chinese.', true),
                'width' => 18,
                'height' => 16
            )
        );
        ?>
        </li>
    <?php
    }

    /** 
     * Display button to indicate that the Chinese sentence is in
     * traditional script
     *
     * @return void
     */
    public function traditionalButton()
    {
        ?>
        <li class="option traditional">
        <?php
        echo $this->Html->image(
            IMG_PATH . 'traditional_chinese.png',
            array(
                'alt'=>__('This sentence is in traditional Chinese.', true),
                'title'=>__('This sentence is in traditional Chinese.', true),
                'width' => 18,
                'height' => 16
            )
        );
        ?>
        </li>
    <?php
    }



    /**
     * Display button to adopt a sentence.
     *
     * @param int    $sentenceId Id of the sentence on which this button
     *                           is displayed
     * @param string $ownerName  Indicates whether the sentence is adopted by current
     *                           user or not.
     * @param bool   $isLogged   True if user is logged in, false otherwise.
     *
     * @return void
     */
    public function adoptButton($sentenceId, $ownerName, $isLogged)
    {
        $isAdopted = !empty($ownerName);
        $currentUserName = CurrentUser::get('username');
        $isOwnedByCurrentUser = $isAdopted && $ownerName == $currentUserName;

        $tooltip = null;
        if ($isAdopted) {
            $cssClass = 'remove';
            $image = 'adopted.svg';
            if ($isLogged && $isOwnedByCurrentUser) {
                $tooltip = __('Click to unadopt', true);
            }
        } else {
            $cssClass = 'add';
            $image = 'unadopted.svg';
            if ($isLogged) {
                $tooltip = __('Click to adopt', true);
            }
        }

        $adoptImage = $this->Html->image(
            IMG_PATH . $image,
            array(
                'alt'=> $tooltip,
                'title'=> $tooltip,
                'width' => 26,
                'height' => 16
            )
        );
        if ($isLogged && ($isOwnedByCurrentUser || !$isAdopted)) {

            ?>
            <li class="option adopt <?php echo $cssClass; ?>"
                id="adopt_<?php echo $sentenceId; ?>">
            <?php
            $this->Javascript->link('sentences.adopt.js', false);
            ?>

            <script type='text/javascript'>
            $(document).ready(function() {
                $('#adopt_<?php echo $sentenceId; ?>').data(
                    'sentenceId',
                    <?php echo $sentenceId; ?>
                );
            });
            </script>

            <a><?php echo $adoptImage; ?></a>
            </li>
            <?php

        } else if ($isAdopted) {

            echo '<li class="adopt uneditable">';
            echo $adoptImage;
            echo '</li>';

        }
        ?>
        
    <?php
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
    public function favoriteButton($sentenceId, $isFavorited, $isLogged)
    {
        if ($isFavorited) {
            $cssClass = 'remove';
            $image = 'favorite-remove.svg';
            $tooltip = __('Remove from favorites', true);
        } else {
            $cssClass = 'add';
            $image = 'favorite-add.svg';
            $tooltip = __('Add to favorites', true);
        }

        $favoriteImage = $this->Html->image(
            IMG_PATH . $image,
            array(
                'alt'=> $tooltip,
                'title'=> $tooltip,
                'width' => 16,
                'height' => 16
            )
        );
        ?>

        <li class="option favorite <?php echo $cssClass; ?>"
            id="favorite_<?php echo $sentenceId; ?>">

        <?php
        if ($isLogged) {
            $this->Javascript->link('favorites.add.js', false);
            ?>

            <script type='text/javascript'>
            $(document).ready(function() {
                $('#favorite_<?php echo $sentenceId; ?>').data(
                    'sentenceId',
                    <?php echo $sentenceId; ?>
                );
            });
            </script>

            <a><?php echo $favoriteImage;?></a>

            <?php
        } else {
            echo $this->Html->link(
                $favoriteImage,
                array(
                    "controller" => "users",
                    "action" => "login"
                ),
                array(
                    "escape" => false
                ),
                null
            );
        }
        ?>
        </li>
    <?php
    }

    public function linkToSentenceButton($sentenceId, $langFilter = 'und') {
        $langFilter = json_encode($langFilter);
        $linkToSentenceButton = $this->Html->Image(
            IMG_PATH . 'link.svg',
            array(
                'alt'=>__('Link to another sentence', true),
                'title'=>__('Link to another sentence', true),
                'width' => 16,
                'height' => 16,
                'class' => 'linkTo',
                'onClick' => "linkToSentence($sentenceId, $langFilter)",
                'onDrop' => "linkToSentenceByDrop(event, $sentenceId, $langFilter)",
            )
        );
        ?>
        <li><a><?php echo $linkToSentenceButton; ?></a></li>

        <li style="display:none" id="linkTo<?php echo $sentenceId; ?>">
        <?php
        echo $this->Form->input('linkToSentence'.$sentenceId, array(
            'label' => false,
            'placeholder' => __('Sentence number', true),
            'class' => 'sentenceId'
        ));

        echo $this->Form->button(
            __('Link', true),
            array(
                'type' => 'button',
                'id' => 'linkToSubmitButton'.$sentenceId,
                'class' => 'validateButton',
            )
        );

        ?>
        </li>
        <?php
        $this->Javascript->link('sentences.link.js', false);
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
        $useMostRecentList = $this->Session->read('use_most_recent_list');
        if ($useMostRecentList != null && $useMostRecentList)
        {
            $mostRecentList = $this->Session->read('most_recent_list');
        }
        else
        {
            $mostRecentList = null;
        }
        $addToListButton = $this->Html->Image(
            IMG_PATH . 'list.svg',
            array(
                'alt'=>__('Add to list', true),
                'title'=>__('Add to list', true),
                'width' => 20,
                'height' => 16
            )
        );
        ?>

        <li class="option addToList"
            id="addToListButton<?php echo $sentenceId; ?>">

        <?php
        if ($isLogged) {
            ?>

            <script type='text/javascript'>
            $(document).ready(function() {
                $('#addToListButton<?php echo $sentenceId; ?>').data(
                    'sentenceId',
                    <?php echo $sentenceId; ?>
                );
            });
            </script>

            <a><?php echo $addToListButton; ?></a>
            <?php
        } else {
            echo $this->Html->link(
                $addToListButton,
                array(
                    "controller" => "users",
                    "action" => "login"
                ),
                array(
                    "escape" => false
                ),
                null
            );
        }
        ?>

        </li>

        <?php
        $this->Javascript->link('sentences_lists.menu.js', false);

        $lists = ClassRegistry::init('SentencesList')->getUserChoices(
            CurrentUser::get('id')
        );
        
        $privateLists = __('Add to one of your lists', true);
        $publicLists = __('Add to a collaborative list', true);
        $selectItems[$privateLists] = $lists['Private'];
        $selectItems[$publicLists] = $lists['Public'];
        ?>

        <li style="display:none" id="addToList<?php echo $sentenceId; ?>">
        
         
        <?php
        echo $this->Form->select(
            'listSelection'.$sentenceId,
            $selectItems,
            $mostRecentList,
            array(
                "class" => "listOfLists",
                "empty" => false
            )
        );

        // ok button
        echo $this->Form->button(
            __('OK', true),
            array(
                'type' => 'button',
                'class' => 'validateButton',
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
        $title = __('Delete', true);
        if ($hasAudio) {
            $title = __('You cannot delete this sentence because it has audio.', true);
        }

        $deleteImage = $this->Html->image(
            IMG_PATH . 'delete.svg',
            array(
                'alt'=> __('Delete', true),
                'title'=> $title,
                'width' => 20,
                'height' => 16
            )
        );

        echo '<li class="option delete">';

        if ($hasAudio) {
            
            echo '<a class="disabled">';
            echo $deleteImage;
            echo '</a>';

        } else {

            echo $this->Html->link(
                $deleteImage,
                array(
                    "controller" => "sentences",
                    "action" => "delete",
                    $sentenceId
                ),
                array("escape" => false),
                'Are you sure?'
            );

        }

        echo '</li>';
    }


    /**
     * Display button to delete.
     *
     * @param int $sentenceId Id of the sentence on which this button
     *                        is displayed
     *
     * @return void
     */
    public function editButton($sentenceId)
    {
        ?>
        <li class="option edit">
        <?php
        echo $this->Html->link(
            $this->Html->image(
                IMG_PATH . 'edit.svg',
                array(
                    'alt'=>__('Edit', true),
                    'title'=>__('Edit', true)
                )
            ),
            array(
                "controller" => "sentences",
                "action" => "edit",
                $sentenceId
            ),
            array("escape" => false)
        );
        ?>
        </li>
    <?php
    }


    /**
     * Display a <li></li> with the current owner name
     * and a link to owner's profile
     *
     * @param int    $sentenceId The sentence's id.
     * @param string $ownerName  The owner's name.
     *
     * @return void
     */
    public function belongsTo($sentenceId,$ownerName)
    {
        if (empty($ownerName)) {
            return;
        }

        // the id is used by sentence.adopt.js
        $belongsToTitle = format(
            __('belongs to {user}', true), 
            array('user' => $ownerName)
        );
        echo '<li class="belongsTo" id="belongsTo_'.$sentenceId.'" 
              title="'.$belongsToTitle.'">';
        $userLink = $this->Html->link(
            $ownerName,
            array(
                "controller" => "user",
                "action" => "profile",
                $ownerName
            )
        );
        echo $userLink;
        echo '</li>';
    }


    /**
     * Display menu for the main sentence.
     *
     * @param int    $sentenceId    Id of the sentence.
     * @param int    $ownerName     Username of the owner of the sentence.
     * @param string $chineseScript For chinese only, 'traditional' or 'simplified'
     * @param array  $canTranslate  True if user can translate the sentence.
     *                              False otherwise.
     * @param array  $langFilter    Language filter of translations.
     * @param bool   $hasAudio      'true' if sentence has audio, 'false' otherwise.
     * 
     * @return void
     */
    public function displayMenu(
        $sentenceId, 
        $ownerName = null, 
        $chineseScript = null, 
        $canTranslate, 
        $langFilter = 'und',
        $hasAudio = true
    ) {
        ?>
        <ul class="menu">

        <?php
        $isLogged = CurrentUser::isMember();

        // Username of the owner
        $this->belongsTo($sentenceId, $ownerName);

        // Adopt
        $this->adoptButton($sentenceId, $ownerName, $isLogged);

        // Translate
        $this->translateButton($sentenceId, $ownerName, $isLogged, $canTranslate);

        // Edit
        // TODO
        // $this->editButton($sentenceId);

        // Favorite
        $isFavorited = CurrentUser::hasFavorited($sentenceId);
        $this->favoriteButton($sentenceId, $isFavorited, $isLogged);

        // Add to list
        $this->addToListButton($sentenceId, $isLogged);

        if (CurrentUser::isTrusted()) {
            $this->linkToSentenceButton($sentenceId, $langFilter);
        }

        if (CurrentUser::isModerator()) {
            // Delete
            $this->deleteButton($sentenceId, $hasAudio);
        }

        if ($chineseScript == 'simplified_script') {
            $this->simplifiedButton();
        } else if ($chineseScript == 'traditional_script') {
            $this->traditionalButton();
        }
        ?>

        <li>
        <?php
        echo $this->Html->image(
            IMG_PATH . 'loading-small.gif',
            array(
                "id"=>"_".$sentenceId."_in_process",
                "style"=>"display:none",
                "width" => 16,
                "height" => 16
            )
        );
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
    <?php
    }
}
?>
