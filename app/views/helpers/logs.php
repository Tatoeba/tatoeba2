<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * Helper for contribution logs.
 *
 * @category Contributions
 * @package  Helpers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class LogsHelper extends AppHelper
{

    public $helpers = array('Date', 'Html', 'Languages');
    
    /** 
     * Display a contribution.
     *
     * @param array $contribution Contribution to display.
     * @param array $user         User who contributed.
     *
     * @return void
     */
    public function entry($contribution, $user = null)
    {
        $type = 'link';
        $status = '';
        
        if (isset($user)) {
            $username = Sanitize::paranoid($user['username']);
            $userId = Sanitize::paranoid($user['id']);
        }
        
        $contributionText = $contribution['text']; // No sanitize here, we use
            // the value in Html::link() which already already sanitizes.
        $contributionId = Sanitize::paranoid($contribution['sentence_id']);
        if (isset($contribution['translation_id'])) {
            $translationId = Sanitize::paranoid($contribution['translation_id']); 
        }
        $action = Sanitize::paranoid($contribution['action']);
        $contributionDate = $contribution['datetime'];
        $lang = Sanitize::paranoid($contribution['sentence_lang']);
        
        if (empty($translationId)) {
            $type = 'sentence';
        }
        
        switch ($action) {
            case 'suggest' : 
                $type = 'correction';
                $status = 'Suggested'; 
                break;
            
            case 'insert' :
                $status = 'Added';
                break;
            
            case 'update' :
                $status = 'Modified';
                break;
            
            case 'delete' :
                $status = 'Deleted';
                break;
        }
        
        echo '<tr class="'.$type.$status.'">';
        
        // language flag
        echo '<td class="lang">';
        if ($type == 'link') {
            echo '&raquo;';
        } else {
            if ($lang == '') {
                echo '?';
            } else {
                echo $this->Languages->icon(
                    $lang, 
                    array(
                        "class" => "flag",
                        "width" => 30,
                        "height" => 20
                    )
                );
            }
        }
        echo '</td>';
        
        $dir = $this->Languages->getLanguageDirection($lang);
        // sentence text
        echo '<td class="text">';
        echo $this->Html->link(
            $contributionText,
            array(
                "controller" => "sentences",
                "action" => "show",
                $contributionId
            ),
            array(
                'dir' => $dir
            )
        );
        echo '</td>';
        
        // contributor
        echo '<td class="username">';
        echo $this->_displayLinkToUserContributions($username, $userId);
        echo '</td>';
        
        // date of contribution
        echo '<td class="date">';
        echo $this->Date->ago($contributionDate);
        echo '</td>';
        
        echo '</tr>';
    }
    
    /** 
     * Display a contribution in annexe module.
     *
     * @param array $contribution Contribution to display.
     * @param array $user         User who contributed.
     *
     * @return void
     */
    public function annexeEntry($contribution, $user = null)
    {
        $type = 'link';
        $status = '';
        
        if (isset($user)) {
            $username = Sanitize::html($user['username']);
            $userId = Sanitize::paranoid($user['id']);
        }
        
        $contributionText = Sanitize::html($contribution['text']);
        $lang = null;
        if (!empty($contribution['sentence_lang'])) {
            $lang = Sanitize::paranoid($contribution['sentence_lang']);
        }
        $translationId = Sanitize::paranoid($contribution['translation_id']); 
        $action = Sanitize::paranoid($contribution['action']);
        $contributionDate = $contribution['datetime'];

        if (empty($translationId)) {
            $type = 'sentence';
        } 
        
        switch ($action) {
            case 'suggest' : 
                $type = 'correction';
                $status = 'Suggested'; 
                break;
            case 'insert' :
                $status = 'Added';
                break;
            case 'update' :
                $status = 'Modified';
                break;
            case 'delete' :
                $status = 'Deleted';
                break;
        }
        
        echo '<div class="annexeLogEntry '.$type.$status.'">';
        
        echo '<div>';
        if (isset($username)) {
            echo $this->_displayLinkToUserContributions($username);
            echo ' - ';
        }
        echo $this->Date->ago($contributionDate);
        echo '</div>';
        
        echo '<div>';
        if ($type === 'link') {
            
            $linkToTranslation = $this->Html->link(
                $translationId,
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $translationId
                )
            );
            
            if ($action == 'insert') {
                echo sprintf(
                    __('linked to %s', true), $linkToTranslation
                );
            } else {
                echo sprintf(
                    __('unlinked from %s', true), $linkToTranslation
                );
            }
            
        } else {
            $dir = $this->Languages->getLanguageDirection($lang);
            echo ' <span class="text" dir="'.$dir.'" >';
            echo $contributionText;
            echo '</span>';
        }
        echo '</div>';
        
        echo '</div>';
    }
    
    
    /**
     * Create the html link to the profile of a given user
     * 
     * @param string $userName The user name
     *
     * @return string The html link.
     */
    private function _displayLinkToUserProfile($username) 
    {
        return $this->Html->link(
            $username, 
            array(
                "controller" => "user",
                "action" => "profile",
                $username
            )
        );
    }
    
    
    /**
     * Create the html link to the profile of a given user.
     *
     * @param string $userName Username.
     *
     * @return string
     */
    private function _displayLinkToUserContributions($username) 
    {
        return $this->Html->link(
            $username, 
            array(
                "controller" => "contributions",
                "action" => "of_user",
                $username
            )
        );
    }
    
    
    /**
     * Display log entry of 'sentence' type.
     *
     * @param int    $sentenceId   Id of the sentence.
     * @param string $sentenceText Text of the sentence.
     * @param string $sentenceLang Language of the sentence.
     * @param string $username     Username of the contributor.
     * @param string $datetime     Datetime, format YYYY-MM-DD HH:mm:ss.
     * @param string $action       { 'insert', 'update', 'delete' }
     *
     * @return void
     */
    public function displaySentenceEntry(
        $sentenceId, $sentenceText, $sentenceLang, $username, $datetime, $action
    ) {
        $dir = $this->Languages->getLanguageDirection($sentenceLang);
        ?>
        <tr class="<?php echo $this->_getCssClassName('sentence', $action); ?>">
            <td class="lang">
            <?php 
            echo $this->Languages->icon(
                $sentenceLang, 
                array(
                    "class" => "flag",
                    "width" => 30,
                    "height" => 20
                )
            );
            ?>
            </td>
            
            <td class="text">
            <?php
            echo $this->Html->link(
                $sentenceText,
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $sentenceId
                ),
                array(
                    'dir' => $dir
                )
            );
            ?>
            </td>
            
            <td class="username">
            <?php echo $this->_displayLinkToUserProfile($username); ?>
            </td>
            
            <td class="date">
            <?php echo $this->Date->ago($datetime); ?>
            </td>  
        </tr>
        <?php
    }
    
    
    /**
     * Display log entry of 'link' type.
     *
     * @param int    $sentenceId    Id of the sentence.
     * @param int    $translationId Id of the translation.
     * @param string $username      Username of the contributor.
     * @param string $datetime      Datetime.
     * @param string $action        { 'insert', 'delete' }
     *
     * @return void
     */
    public function displayLinkEntry(
        $sentenceId, $translationId, $username, $datetime, $action
    ) {
        ?>
        <tr class="<?php echo $this->_getCssClassName('link', $action); ?>">
            <td></td>
            
            <td class="linkInfo">
            <?php 
            echo $this->Html->link(
                $sentenceId,
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $sentenceId
                )
            );
            echo ' « » ';
            echo $this->Html->link(
                $translationId,
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $translationId
                )
            );
            ?>
            </td>
            
            <td class="username">
            <?php echo $this->_displayLinkToUserProfile($username); ?>
            </td>
            
            <td class="date">
            <?php echo $this->Date->ago($datetime); ?>
            </td>        
        </tr>
        <?php
    }
    
    
    /**
     * Returns the CSS class for a log entry, given its type and action.
     *
     * @param string $type   { 'link', 'sentence' }
     * @param string $action { 'insert', 'update', 'delete' }
     *
     * @return string
     */
    private function _getCssClassName($type, $action)
    {
        switch ($action) {
            case 'insert' :
                $type .= 'Added';
                break;
            case 'update' :
                $type .= 'Modified';
                break;
            case 'delete' :
                $type .= 'Deleted';
                break;
        }
        
        return $type;
    }
}
?>
