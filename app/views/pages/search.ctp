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

$this->pageTitle = 'Tatoeba - ' . __('Examples of search', true);
?>

<div id="annexe_content">
    <div class="module">
        <h2><?php __('Search features'); ?></h2>
        <p>
        <?php
        __('The search is powered by <a href="%s">Sphinx</a>.');
        ?>
        </p>
        
        <p>
        Until we have time to write more about the search features, you can always
        take a look at the documentation 
        <a href="http://sphinxsearch.com/docs/current.html#boolean-syntax">here</a>.
        
        </p>
    </div>
</div>
    
<div id="main_content">
    <div class="module">
        <p><?php __('Please enter a non empty search'); ?></p>
    </div>
</div>


