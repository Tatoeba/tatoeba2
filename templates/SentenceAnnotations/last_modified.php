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
 * @link     https://tatoeba.org
 */

/**
 * Display the last modified annotations.
 *
 * @category SentenceAnnotation
 * @package  Views
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */ 
?>

<div id="annexe_content" ng-non-bindable>
    <?php
    $this->SentenceAnnotations->displayGoToBox();
    
    $this->SentenceAnnotations->displaySearchBox();
    ?>
</div>

<div id="main_content" ng-non-bindable>
    
    <div class="module">
    <h2>
    <?php 
    echo $this->Paginator->counter(
        'Browse by last modified (total {{count}})'
    ); 
    ?>
    </h2>
    
    <?php
    $this->Pagination->display();
    ?>
    
    <table class="logs">
    <?php
    //pr($annotations);
    foreach ($annotations as $annotation) {
        $username = $annotation->user->username;
        if (empty($username)) {
            $username = 'unknown';
        }
        $this->SentenceAnnotations->displayLogEntry(
            $annotation->sentence_id,
            $annotation->text,
            $username,
            $annotation->modified
        );
    }
    ?>
    </table>
    </div>
    
    <?php
    $this->Pagination->display();
    ?>
</div>
