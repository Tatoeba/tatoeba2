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
 * Display latest contributions.
 *
 * @category Contributions
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

$this->pageTitle = 'Tatoeba - ' . __('Contributors statistics', true);
?>

<div id="annexe_content">
    <div class="module">
    <h2><?php __('Number of contributions'); ?></h2>
    <?php 
    __(
        'The number of contributions represents the number of sentences added '.
        '+ the number of translations added + the number of sentences modified.'
    ); 
    ?>
    </div>

    <div class="module">
    <h2><?php __('About the colors'); ?></h2>
    <p>
    <?php
    __(
        'In January 2009, a new version of Tatoeba was released and all the '.
        'accounts have been disactivated. Users in grey are users who have not '.
        'reactivated their account (yet?).'
    ); 
    ?>
    </p>
    <p>
    <?php
    __(
        'They are however displayed here because they still deserve some '.
        'acknowledgement for their dedication.'
    );
    ?>
    </p>
    <p><?php __('Users in red are admins (and there is only one so far).'); ?></p>
    </div>
</div>

<div id="main_content">
    <div class="module">
    <h2><?php __('Contributions statistics'); ?></h2>
    <?php
    echo '<table id="usersStatistics">';
        echo '<tr>';
        echo '<th>' . __('rank', true) . '</th>';
        echo '<th>' . __('username', true) . '</th>';
        echo '<th>' . __('member since', true) . '</th>';
        echo '<th>' . __('number of contributions', true) . '</th>';
        echo '</tr>';

    $i = 1;
    foreach ($stats as $stat) {
        $css = 'class=';
        if ($stat['User']['group_id'] == 1) {
            $css .= '"admin"';
        }
        if ($stat['User']['group_id'] == 4) {
            $css .= '"normal"';
        }
        if ($stat['User']['group_id'] == 5) {
            $css .= '"pending"';
        }

        echo '<tr '.$css.'><td>';
        echo $i; $i++;
        echo '</td><td>';
        echo $html->link(
            $stat['User']['username'], 
            array(
                "controller"=>"user", 
                "action"=>"profile", 
                $stat['User']['username']
            )
        );
        echo '</td><td>';
        echo $date->ago($stat['User']['since']);
        echo '</td><td>';
        echo $stat['0']['total'];
        echo '</td></tr>';
    }
    echo '</table>';
    ?>
    </div>
</div>