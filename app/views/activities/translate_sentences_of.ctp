<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2011  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
$username = Sanitize::paranoid($username, array("_"));

if ($results == null) {
    $title = sprintf(
        __("This user doesn't exist: %s", true),
        $username
    );
} else if (!empty($lang)) {
    $title = sprintf(
        __('Translate %1$s sentences that belong to %2$s', true),
        $languages->codeToName($lang),
        $username
    );
} else {
    $title = sprintf(
        __('Translate sentences that belong to %s', true),
        $username
    );
}
$this->set('title_for_layout', $title);
?>

<div id="annexe_content">    
    <?php     
    echo $this->element(
        'users_menu', 
        array('username' => $username)
    );
    
    $commonModules->createFilterByLangMod(2); 
    ?> 
</div>

<div id="main_content">    
    
    <div class="module">
    <h2>
    <?php 
    echo $title; 
    echo ' ';
    echo $paginator->counter(
        array(
            'format' => __('(%count% results)', true)
        )
    ); 
    ?>
    </h2>
    
    <?php
    if ($results != null) {
        $paginationUrl = array(
            $username,
            $lang
        );
        $pagination->display($paginationUrl);
        
        foreach ($results as $sentence) {
            $sentences->displaySentencesGroup(
                $sentence['Sentence'], 
                $sentence['Translations'], 
                $sentence['User'],
                $sentence['IndirectTranslations']
            );
        }
        
        $pagination->display($paginationUrl);
    }
    ?>
    </div>
</div>