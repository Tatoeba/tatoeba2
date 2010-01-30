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

    public $helpers = array('Date', 'Html');
    
    /** 
     * Display a contribution.
     *
     * @param array $contribution Contribution to display.
     * @param array $user         User who contributed.
     * @param array $sentence     Sentence related.
     *
     * @return void
     */
    public function entry($contribution, $user = null , $sentence = null)
    {
        $type = '';
        $status = '';
        
        if ($contribution['translation_id'] == '') {
            $type = 'sentence';
        } else {
            $type = 'link';
        }
        
        switch ($contribution['action']) {
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
            if ($sentence['lang'] == '') {
                echo '?';
            } else {
                echo $this->Html->image(
                    $sentence['lang'].".png", 
                    array("alt" => $sentence['lang'], "class" => "flag")
                );
                // TODO should be replace by the real name
            }
        }
        echo '</td>';
        
        // sentence text
        echo '<td class="text">';
        echo $this->Html->link(
            $contribution['text'],
            array(
                "controller" => "sentences",
                "action" => "show",
                $contribution['sentence_id']
            )
        );
        echo '</td>';
        
        // contributor
        echo '<td class="username">';
        echo $this->Html->link(
            $user['username'], 
            array("controller" => "users", "action" => "show", $user['id'])
        );
        echo '</td>';
        
        // date of contribution
        echo '<td class="date">';
        echo $this->Date->ago($contribution['datetime']);
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
        $type = '';
        $status = '';
        
        if ($contribution['translation_id'] == null 
            OR $contribution['translation_id'] == ''
        ) {
            $type = 'sentence';
        } else {
            $type = 'link';
        }
        
        switch ($contribution['action']) {
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
        if (isset($user['username'])) {
            echo $this->Html->link(
                $user['username'], 
                array("controller" => "users", "action" => "show", $user['id'])
            );
            echo ' - ';
        }
        echo $this->Date->ago($contribution['datetime']);
        echo '</div>';
        
        echo '<div>';
        if ($type == 'link') {
            __('linked to');
            echo ' &raquo; ';
            
            echo $this->Html->link(
                $contribution['translation_id'],
                array(
                    "controller" => "sentences",
                    "action" => "show",
                    $contribution['translation_id']
                )
            );
            
        } else {
            echo ' <span class="text">';
            echo Sanitize::html($contribution['text']);
            echo '</span>';
        }
        echo '</div>';
        
        echo '</div>';
    }
}
?>
