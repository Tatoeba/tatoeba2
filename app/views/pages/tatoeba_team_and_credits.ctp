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
        $people = array(
            'HO Ngoc Phuong Trang' => 'trang',
            'SIMON Allan' => 'sysko'
        );
        
        foreach ($people as $realName => $username) {
            $members->creditsToUser($realName, $username);
        }
        ?>
        </table>
        
        
        <h3><?php __('Padawan members'); ?></h3>
        <table>
        <?php
        $people = array(
            'BEN YAALA Salem' => 'socom',
            'DEPARIS Étienne' => 'milouse',
            'PARIS Robin' => 'fendtwick',
            'DUCATEL Baptiste' => 'biptaste'
        );
        
        foreach ($people as $realName => $username) {
            $members->creditsToUser($realName, $username);
        }
        ?>
        </table>

        <h3><?php __('Ex-members'); ?></h3>
        <table>
        <?php
        $people = array(
            'TAN Kevin' => 'keklesurvivant',
            'ERNALSTEEN Jonathan' => '9h0ost'
        );
        
        foreach ($people as $realName => $username) {
            $members->creditsToUser($realName, $username);
        }
        ?>
        </table>
    
    </div>
    
    
    
    <div class="module">
        <h2><?php __('Translations of the interface'); ?></h2>
        
        <?php
        $launchpadUrl = 'https://launchpad.net/tatoeba/+topcontributors';
        $launchpadDescription = sprintf(
            __(
                'Thank you to everyone who translated on '.
                '<a href="%s">Launchpad</a>.', true
            ), $launchpadUrl
        );
        
        $people = array(
            array(__('Chinese', true), 'FU Congcong 傅琮琮', 'fucongcong'),
            array(__('Spanish', true), 'JIMÉNEZ Gabriel', 'kylecito'),
            array(__('Spanish', true), 'sirgazil', 'sirgazil'),
            array(__('Italian', true), 'Pharamp', 'pharamp'),
            array(__('Portuguese', true), 'brauliobezerra', 'brauliobezerra'),
            array(__('Japanese', true), 'BLAY Paul', 'blay_paul'),
            array(__('Polish', true), 'zipangu', 'zipangu'),
            array($launchpadDescription, 'Launchpad translators', null),
        );
        
        foreach ($people as $memberInfo) {
            $description = $memberInfo[0];
            $realName = $memberInfo[1];
            $username = $memberInfo[2];
            
            $members->creditsToUser($realName, $username, $description);
        }
        ?>
    </div>
    
    <div class="module">
    
    <h2><?php __('Special thanks'); ?></h2
    <?php
    $people = array(
        array('Coded search engine a while ago.', 'BOUCHER François', 'kentril'),
        array('Hosted Tatoeba for a few years.', 'Masa', 'masa'),
        array('Free Software Foundation. Currently hosting Tatoeba.', 'FSF (France)', null)
    );
    foreach ($people as $memberInfo) {
        $description = $memberInfo[0];
        $realName = $memberInfo[1];
        $username = $memberInfo[2];
        
        $members->creditsToUser($realName, $username, $description);
    }
    ?>
    </div>
    
</div>