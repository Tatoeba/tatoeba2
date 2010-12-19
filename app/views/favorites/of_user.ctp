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

$title = sprintf(__("%s's favorite sentences", true), $user['username']); 
$this->pageTitle = $title;
$numberOfSentences = count($favorites);
?>

<div id="annexe_content">
    <div class="module">
    <h2><?php __('Navigation links'); ?></h2>
    <p>
    <?php
    echo $html->link(
        sprintf(__("%s's contributions page", true), $user['username']),
        array(
            'controller' => 'users',
            'action' => 'show',
            $user['id']
        )
    );
    ?>
    </p>
    <p>
    <?php
    echo $html->link(
        sprintf(__("%s's profile", true), $user['username']),
        array(
            'controller' => 'user',
            'action' => 'profile',
            $user['username']
        )
    );
    ?>
    </p>
    </div>
</div>

<div id="main_content">
    <div class="module">
    
    <h2><?php echo $title . ' ('. $numberOfSentences.')'; ?></h2>
    
    <?php
    
    if ($numberOfSentences > 0) {
        $type = 'mainSentence';
        $parentId = null;
        $withAudio = false;
        $ownerName = null;
        foreach ($favorites as $sentence) {
            $sentences->displayGenericSentence(
                $sentence,
                $ownerName,
                $type,
                $parentId,
                $withAudio
            );
        }
    } else {
        __('This user does not have any favorites.');
    }
    ?>
    </div>
</div>
