<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
 * Helper for members.
 *
 * @category Utilities
 * @package  Helpers
 * @author   SIMON Allan <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class MembersHelper extends AppHelper
{
    public $helpers = array('Html');
    
    /**
     * To display the names of the people listed in Team & Credits. 
     *
     * @param string $username    Username of the member.
     * @param string $realName    Real name of the member.
     * @param string $description Description of the member's participation.
     *
     * @return void
     */
    public function creditsToUser($realName, $username = null, $description = null)
    {
        $realName = Sanitize::html($realName);
        ?>
        <div class="person">
            <div class="realName">
            <?php
            echo $realName;
            ?>
            </div>
            
            <div class="description">
            <?php echo $description; ?>
            </div>
            
            <?php
            if (!empty($username)) {
                ?>
                <div class="username">
                <?php
                $profileIcon = $this->Html->image(
                    'profile.png',
                    array(
                        "alt" => __('Profile', true),
                        "width" => 14,
                        "height" => 14
                    )
                );
                echo $this->Html->link(
                    $profileIcon.' '.$username,
                    array(
                        'controller' => 'user',
                        'action' => 'profile',
                        $username
                    ),
                    array(
                        'escape' => false
                    )
                );
                ?>
                </div>
                <?php
            }
            ?>
        
        </div>
        <?php
    }
    
}
?>