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
        'Session',
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
            'title' => __('Translate', true)
        ));

        if (!$enabled) {

            echo '<a class="disabled">';
            echo $this->Images->svgIcon(
                'translate',
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
            <a><?php echo $translateButton;?></a>
           <?php
        } else {
            echo $this->Html->link(
                $translateButton,
                array(
                    "controller" => "users",
                    "action" => "login",
                    "?" => array("redirectTo" => Router::reverse($this->params)),
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
     * @return void
     */
    public function adoptButton($sentenceId, $ownerName)
    {
        $isAdopted = !empty($ownerName);
        $currentUserName = CurrentUser::get('username');
        $isOwnedByCurrentUser = $isAdopted && $ownerName == $currentUserName;

        $tooltip = null;
        $action = '';
        if ($isAdopted) {
            $image = 'adopted';
            if ($isOwnedByCurrentUser) {
                $tooltip = __('Click to unadopt', true);
                $action = ' remove';
            }
        } else {
            $image = 'unadopted';
            if (!$isAdopted) {
                $tooltip = __('Click to adopt', true);
                $action = ' add';
            }
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
            $this->Javascript->link('sentences.adopt.js', false);
            $contents = $this->Images->svgIcon($image, $svgIconOptions);
            $contents = '<a class="adopt-item adopt-button">'.$contents.'</a>';
        }
        if ($isAdopted) {
            $contents .= $this->belongsTo($ownerName);
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
    public function favoriteButton($sentenceId, $isFavorited, $isLogged)
    {
        if ($isFavorited) {
            $type = 'remove';
            $image = 'favorite-remove';
            $tooltip = __('Remove from favorites', true);
        } else {
            $type = 'add';
            $image = 'favorite-add';
            $tooltip = __('Add to favorites', true);
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
            $this->Javascript->link('favorites.add.js', false);
            ?>
            <a><?php echo $favoriteImage;?></a>
            <?php
        } else {
            echo $this->Html->link(
                $favoriteImage,
                array(
                    "controller" => "users",
                    "action" => "login",
                    "?" => array("redirectTo" => Router::reverse($this->params)),
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
                'title' => __('Link to another sentence', true),
                'class' => 'linkTo',
                'onClick' => "linkToSentence($sentenceId, $langFilter)",
                'onDrop' => "linkToSentenceByDrop(event, $sentenceId, $langFilter)",
            )
        );
        ?>
        <li class="option"><?php echo $linkToSentenceButton; ?></li>

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
        $useMostRecentList = CurrentUser::get('settings.use_most_recent_list');
        if ($useMostRecentList != null && $useMostRecentList) {
            $mostRecentList = $this->Session->read('most_recent_list');
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
            'title' => __('Add to list', true)
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
                    "?" => array("redirectTo" => Router::reverse($this->params)),
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

            $title = __('Delete', true);
            $liContent = $this->Html->link(
                $deleteImage,
                array(
                    "controller" => "sentences",
                    "action" => "delete",
                    $sentenceId
                ),
                array(
                    "escape" => false,
                    'alt'=> __('Delete', true),
                    'title'=> $title,
                ),
                __('Are you sure?', true)
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
            $title = __('You cannot edit this sentence because it has audio.', true);
            $liContent = $this->Html->tag(
                'a', $editImage, array('class' => 'disabled')
            );
        } else {
            $title = __('Edit', true);
            $liContent = $editImage;
        }

        echo $this->Html->tag('li', $liContent,
            array(
                'class' => 'option edit',
                'title'=> $title,
            )
        );
    }


    /**
     * Return a link to owner's profile
     *
     * @param int    $sentenceId The sentence's id.
     * @param string $ownerName  The owner's name.
     *
     * @return string
     */
    private function belongsTo($ownerName)
    {
        $belongsToTitle = format(
            __('belongs to {user}', true),
            array('user' => $ownerName)
        );
        return $this->Html->link(
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
    }


    public function correctnessButton($sentenceId)
    {
        $this->Javascript->link('collections.add_remove.js', false);

        $userCorrectness = CurrentUser::correctnessForSentence($sentenceId);

        $icons = array(
            1 => array(
                'class' => 'ok',
                'icon' => 'ok',
                'tooltip' => __('Mark as "OK"', true)
            ),
            0 => array(
                'class' => 'unsure',
                'icon' => 'unsure',
                'tooltip' => __('Mark as "unsure"', true)
            ),
            -1 => array(
                'class' => 'not-ok',
                'icon' => 'not-ok',
                'tooltip' => __('Mark as "not OK"', true)
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
                $tooltip = __('Unmark sentence', true);
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

        // Adopt
        $this->adoptButton($sentenceId, $ownerName);

        // Translate
        $this->translateButton($sentenceId, $isLogged, $canTranslate);

        // Edit
        if (CurrentUser::canEditSentenceOfUser($ownerName)) {
           $this->editButton($hasAudio);
        }

        // Favorite
        $isFavorited = CurrentUser::hasFavorited($sentenceId);
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

        if ($chineseScript == 'Hans') {
            $this->simplifiedButton();
        } else if ($chineseScript == 'Hant') {
            $this->traditionalButton();
        }
        ?>

        <li>
        <?php
        echo $this->Images->svgIcon(
            'loading',
            array(
                'id' => '_'.$sentenceId.'_in_process',
                'class' => 'loading',
                'width' => 16,
                'height' => 16
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
