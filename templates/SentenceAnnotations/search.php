<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>

<div id="annexe_content" ng-non-bindable>
    <?php
    $this->SentenceAnnotations->displayGoToBox();
    
    $this->SentenceAnnotations->displaySearchBox();
    
    $this->SentenceAnnotations->displayReplaceBox($query);
    ?>
    
    
</div>

<div id="main_content" ng-non-bindable>
    <div class="module">
    <?php
    $numberOfResults = count($annotations);
    echo '<h2>';
    echo 'Search : ' . $query . ' (' . $numberOfResults . ' results)';
    echo '</h2>';
    
    if($numberOfResults > 0){
        foreach($annotations as $annotation){
            // sentence
            echo '<p>';
            echo $this->Html->link(
                $annotation->sentence->text,
                ['action' => 'show', $annotation->sentence_id]
            );
            echo '</p>';
            
            
            // annotation
            echo '<p class="annotation">';
            echo $annotation->text;
            echo '</p>';
            
            
            echo '<hr/>';
        }
    }
    ?>
    </div>
</div>
