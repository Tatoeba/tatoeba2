<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  Allan SIMON (allan.simon@supinfo.com)

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
$this->pageTitle = __('Search Sinograms',true);
$javascript->link("sinograms.search.js",false);

?>

<div id="annexe_content" >
    <div class="module"  >
        <h2><?php __('Browse Radicals') ?></h2>
        <div id="numberList" >
        <?php 
            for ($i = 1 ; $i < 10 ; $i++){
                echo '<a class="radicalStrokesNumber" >';
                    echo $i;
                echo "</a>\n";
            }
                echo '<a class="radicalStrokesNumber" >';
                    echo "10+";
                echo '</a>';
        ?>
        </div>
        <div id="radicalsList" >
            <h3>1 Stroke</h3>
            <a class="radical">一</a>
            <a class="radical">丨</a>
            <a class="radical">丶</a>
            <a class="radical">丿</a>
            <a class="radical">乀</a>
            <a class="radical">乁</a>
            <a class="radical">乙</a>

            <a class="radical">乚</a>
            <a class="radical">乛</a>
            <a class="radical">亅</a>

        </div>

    </div>

    <div id="explode_part" class="module" >
        <h2><?php __('Explode A Character') ?></h2>
        <div id="explodeForm" >
        <?php
            echo $form->create("Sinogram", array("action" => "explode"  ) );
            echo $form->input("toExplode" ,array("label" => __("characters to explode",true) ));
            echo $form->end(__("Explode",true) );
        ?>
        </div>
        <div id="explosionResults">
        </div>
    </div>
    
    <div class="module" >
        <h2><?php __("Still beta:") ?></h2>

        <div>
            <p id="stillBetaText" >
            <?php
            echo sprintf( __('this works is based on <a href="%s" >this project </a>',true)
            ,"http://commons.wikimedia.org/wiki/Commons:Chinese_characters_decomposition");
            __('Please note this tool is still not complete yet and may contains errors
            or incomplete (though it will be accurate for most of search), so don\'t hesitate to
            report missing characters or any improvement suggestions.');

            ?>
            </p>
        </div>
    </div> 
</div>

<div id="main_content"  >
    <div id="introduction" class="module"  >
        <h2><?php __('Hanzis - Kanjis research engine');?></h2>
        
        <p class="introduction" >
            <?php
            __('This tool allows you to find informations about kanjis/hanzis. ');
            __('Especially when you don\'t know how to input them directly with IMEs. ');
            __('The main way to use this, is by submitting subglyph of the character.');
        ?>
        </p>
        <p class="introduction" >
            <?php
            __('For example 蝴, you can enter 月虫 as subglyphs (you don\'t need to know every subglyph).');
           ?> 
        </p>
        <p class="introduction" >
            <?php
            __('If you don\'t know how to input sublyph either, but you know a character that also contains this subglyph, then
            you can use the explode form.');
        
            ?> 
        </p>
        <p class="introduction" >
            <?php

            __('For example if you want to search 瞧, but you don\'t know how to input 隹 in order to make the research more accurate, if you know 推,
            you can just explode it, clicking on a subglyph will add it to the research form.');
            
           ?> 
        </p>
        <p class="introduction" >
            <?php
            __('On the right you also have the most common radicals grouped by strokes, in case you don\'t have anyway to type hankis/kanjis.');  

            ?>
        </p>
        

        <div id="search_part">
            <div id="search_form">
                <?php
                __ ("Search a character by describing it"); 
                echo $form->create("Sinogram", array("action" => "search"  ) );
                echo $form->input("subglyphs", array("label" => __("Sublgyphs: ",true) ));
                echo $form->end( array( "label" => __("Search",true))  );
                ?>
            </div> 
        </div>


        <div id="information_part">
        </div>
    </div>
</div>
