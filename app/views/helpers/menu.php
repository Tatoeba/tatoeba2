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
        'Form'
    );
    
    
    /** 
     * Display button to add a translation.
     *
     * @param int $sentenceId Id of the original sentence.
     *
     * @return void
     */
    public function translateButton($sentenceId)
    {
        $this->Javascript->link('sentences.add_translation.js', false);
        ?>
        <li class="option translateLink"
            id="translate_<?php echo $sentenceId; ?>"><a>
        
        <script type='text/javascript'>
        $(document).ready(function() {
            $('#translate_<?php echo $sentenceId; ?>').data(
                'sentenceId',
                <?php echo $sentenceId; ?>
            );
        });
        </script>
        
        <?php
        echo $this->Html->image(
            'translate.png', 
            array(
                'alt'=>__('Translate', true), 
                'title'=>__('Translate', true)
            )
        );
        ?>
        </a></li>
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
        <li class="option simplified"><a>
        <?php
        echo $this->Html->image(
            'simplified_chinese.png', 
            array(
                'alt'=>__('This sentence is in simplified Chinese.', true), 
                'title'=>__('This sentence is in simplified Chinese.', true)
            )
        );
        ?>
        </a></li>
        <?php
    }

    /** 
     * Display button to notify the chinese sentence is in
     * traditional script
     *
     * @return void
     */
    public function traditionalButton()
    {
        ?>
        <li class="option traditional"><a>
        <?php
        echo $this->Html->image(
            'traditional_chinese.png', 
            array(
                'alt'=>__('This sentence is in traditional Chinese.', true), 
                'title'=>__('This sentence is in traditional Chinese.', true)
            )
        );
        ?>
        </a></li>
        <?php
    }



    /** 
     * Display button to adopt a sentence.
     *
     * @param int  $sentenceId Id of the sentence on which this button
     *                         is displayed
     * @param bool $isAdopted  Indicates whether the sentence is adopted by current
     *                         user or not.
     *
     * @return void
     */
    public function adoptButton($sentenceId, $isAdopted)
    {
        $this->Javascript->link('sentences.adopt.js', false);
        
        if ($isAdopted) {
            $cssClass = 'remove';
            $image = 'let_go.png';
            $tooltip = __('Let go', true);
        } else {
            $cssClass = 'add';
            $image = 'adopt.png';
            $tooltip = __('Adopt', true);
        }
        ?>
        
        <li class="option adopt <?php echo $cssClass; ?>" 
            id="adopt_<?php echo $sentenceId; ?>"><a>
            
        <script type='text/javascript'>
        $(document).ready(function() {
            $('#adopt_<?php echo $sentenceId; ?>').data(
                'sentenceId',
                <?php echo $sentenceId; ?>
            );
        });
        </script>
        <?php
        echo $this->Html->image(
            $image,
            array(
                'alt'=> $tooltip, 
                'title'=> $tooltip
            )
        );
        ?>
        </a></li>
        
        <?php
    }
    
    
    /** 
     * Display button to add to favorites.
     *
     * @param int  $sentenceId  Id of the sentence on which this button
     *                          is displayed
     * @param bool $isFavorited Indicates whether the sentence is favorited by
     *                          current user or not.
     *
     * @return void
     */
    public function favoriteButton($sentenceId, $isFavorited)
    {
        $this->Javascript->link('favorites.add.js', false);
        
        if ($isFavorited) {
            $cssClass = 'remove';
            $image = 'unfavorite.png';
            $tooltip = __('Remove from favorites', true);
        } else {
            $cssClass = 'add';
            $image = 'favorite.png';
            $tooltip = __('Add to favorites', true);
        }
        ?>
        
        
        <li class="option favorite <?php echo $cssClass; ?>" 
            id="favorite_<?php echo $sentenceId; ?>">
        
        <script type='text/javascript'>
        $(document).ready(function() {
            $('#favorite_<?php echo $sentenceId; ?>').data(
                'sentenceId',
                <?php echo $sentenceId; ?>
            );
        });
        </script>
        
        <a>
        <?php
        echo $this->Html->image(
            $image,
            array(
                'alt'=> $tooltip, 
                'title'=> $tooltip
            )
        );
        ?>
        </a>
        </li>
        
        <?php
    }
    
    
    /** 
     * Display button to add a sentence to a list.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return void
     */
    public function addToListButton($sentenceId)
    {
        ?>
        <li class="option addToList"
            id="addToListButton<?php echo $sentenceId; ?>"><a>
        <?php
        echo $this->Html->Image(
            'add_to_list.png',
            array(
                'alt'=>__('Add to list', true), 
                'title'=>__('Add to list', true)
            )
        );
        ?>
        </a>
        
        <script type='text/javascript'>
        $(document).ready(function() {
            $('#addToListButton<?php echo $sentenceId; ?>').data(
                'sentenceId',
                <?php echo $sentenceId; ?>
            );
        });
        </script>
        
        </li>
        
        
        <?php
        $this->Javascript->link('sentences_lists.menu.js', false);
        $this->Javascript->link('jquery.impromptu.js', false);
        
        // TODO Remove requestAction someday
        $lists = $this->requestAction('/sentences_lists/choices'); 
        
        $privateLists = __('Add to your lists', true);
        $publicLists = __('Add to public list', true);
        $selectItems[$privateLists] = $lists['Private'];
        $selectItems[$publicLists] = $lists['Public'];
        ?>
        
        <li style="display:none" id="addToList<?php echo $sentenceId; ?>">
        <?php
        echo $this->Form->select(
            'listSelection'.$sentenceId,
            $selectItems,
            null,
            array(
                "class" => "listOfLists"
            ),
            false
        );
        
        // ok button
        echo $this->Form->button(
            null,
            array(
                'type' => 'button',
                'class' => 'validateButton',
                'value' => 'ok'
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
    public function deleteButton($sentenceId)
    {
        ?>
        <li class="option delete">
        <?php
        echo $this->Html->link(
            $this->Html->image(
                'delete.png',
                array(
                    'alt'=>__('Delete', true), 
                    'title'=>__('Delete', true)
                )
            ),
            array(
                "controller" => "sentences",
                "action" => "delete",
                $sentenceId
            ), 
            array('escape' => false), 
            'Are you sure?'
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
        echo '<li class="belongsTo" id="belongsTo_'.$sentenceId.'">';
        $userLink = $this->Html->link(
            $ownerName,
            array(
                "controller" => "user",
                "action" => "profile",
                $ownerName
            )
        );
        echo sprintf(__('belongs to %s', true), $userLink);
        echo '</li>';
    }
    
    
    /**
     * Display menu for the main sentence.
     *
     * @param int    $sentenceId    Id of the sentence.
     * @param int    $ownerName     Username of the owner of the sentence.
     * @param bool   $isFavorited   Indicates if the sentence is favorited by the 
     *                              current user.
     * @param string $chineseScript For chinese only, 'traditional' or 'simplified'
     *
     * @return void
     */
    public function displayMenu(
        $sentenceId, $ownerName = null, $chineseScript = null
    ) {
        ?>
        <ul class="menu">
        
        <?php
        if (CurrentUser::isMember()) {
            $this->translateButton($sentenceId);
            
            $isAdopted = (CurrentUser::get('username') == $ownerName);
            $canAdopt = ($ownerName == null) || $isAdopted;
            if ($canAdopt) {
                $this->adoptButton($sentenceId, $isAdopted);
            }
            
            $isFavorited = CurrentUser::hasFavorited($sentenceId);
            $this->favoriteButton($sentenceId, $isFavorited);
            
            $this->addToListButton($sentenceId);
        }
        
        if (CurrentUser::isAdmin()) {
            $this->deleteButton($sentenceId);
        }
        
        if ($chineseScript === 'simplified') {
            $this->simplifiedButton(); 
        } else if ($chineseScript === 'traditional') {
            $this->traditionalButton(); 
        }
        
        $this->belongsTo($sentenceId, $ownerName);
        ?>
        
        <li>
        <?php
        echo $this->Html->image(
            'loading-small.gif', 
            array(
                "id"=>"_".$sentenceId."_in_process", 
                "style"=>"display:none",
                "width" => 16,
                "height" => 16
            )
        );
        echo $this->Html->image(
            'valid_16x16.png', 
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
        // to play audio
        // TODO Put it somewhere else. In search results and several random, it
        // will cause a conflict of ids.
        $this->Javascript->link('sentences.play_audio.js', false);
        echo '<div id="audioPlayer"></div>';
    }
}
?>