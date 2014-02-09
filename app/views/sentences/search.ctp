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

$query = Sanitize::html($query);

$this->pageTitle = sprintf(__('Sentences with: %s', true), $query);
?>

<div id="annexe_content">
    <?php
    $attentionPlease->tatoebaNeedsYou();
    
    echo $this->element('search_features');
    ?>
</div>


<div id="main_content">
<?php
if (!empty($results)) {
    
    ?>
    <div class="module">
        <h2>
        <?php 
        echo sprintf(__('Search: %s', true), $query);
        echo ' ';
        echo $paginator->counter(
            array(
                'format' => __('(%count% results)', true)
            )
        ); 
        ?>
        </h2>
        
        <?php
        $pagination->display();
        
        foreach ($results as $sentence) {
            $sentences->displaySentencesGroup(
                $sentence['Sentence'], 
                $sentence['Translations'], 
                $sentence['User'],
                $sentence['IndirectTranslations']
            );
        }
        
        $pagination->display();
        ?>
    </div>
    <?php
    
} else {
    
    echo $this->element('search_with_no_result');
    
}
?>  
</div>