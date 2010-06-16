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

$this->pageTitle = __('Tatoeba team and credits', true);
?>

<div id="main_content">
    
    <div class="module">
        <h2><?php __('The dev team'); ?></h2>
        
        <h3><?php __('Core members'); ?></h3>
        <table>
        <?php
        
        foreach ($cores as $people) {
            $realName = $people[0];
            $username = $people[1];
            $members->creditsToUser($realName, $username);
        }
        ?>
        </table>
        
        
        <h3><?php __('Padawan members'); ?></h3>
        <table>
        <?php
        
        foreach ($padawans as $people) {
            $realName = $people[0];
            $username = $people[1];
            $members->creditsToUser($realName, $username);
        }
        ?>
        </table>

        <h3><?php __('Ex-members'); ?></h3>
        <table>
        <?php
            
        foreach ($exmembers as $people) {
            $realName = $people[0];
            $username = $people[1];
            $members->creditsToUser($realName, $username);
        }
        ?>
        </table>
    
    </div>
    
    
    
    <div class="module">
        <h2><?php __('Translations of the interface'); ?></h2>
        
        <?php

        
        foreach ($translators as $people) {
            $description = $people[2];
            $realName = $people[0];
            $username = $people[1];
            
            $members->creditsToUser($realName, $username, $description);
        }
        ?>
    </div>
    
    <div class="module">
    
    <h2><?php __('Special thanks'); ?></h2>
    <?php
    
    foreach ($specialThanks as $people) {
        $description = $people[2];
        $realName = $people[0];
        $username = $people[1];
        
        $members->creditsToUser($realName, $username, $description);
    }
    ?>
    </div>
    
</div>
