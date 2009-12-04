<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

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

$this->pageTitle = __('Browse languages related tools provided by Tatoeba',true);
?>
<div id="annexe_content" >
    <div class="module" >
        <h2><?php __("Need a tool?")?></h2>

        <p>
            <?php
            echo sprintf(__("Well, Tatoeba's developpers don't have imagination to code every
            possibl usefull tools for language learners, so if there's a must-have
            tools which is missing or you have yourself code something you think can
            help others (as long as you can provide it under a GPL compatible licence)
            don't hesitate to talk about it<a href=%s> here</a>, we're always looking for new sutffs.", true),
            "/wall/index"); 
            ?>
        </p>

    </div>
</div>

<div id="main_content">
	<div class="module">
	    <h2> <?php __("Tools index:");?> </h2>

        <ul>
            <li><a href="kakasi" >Kakasi:</a> <?php __("convert japanese to romaji or furigana")  ?></li>
            <li><a href="search_hanzi_kanji" >Sinogram Search:</a> <?php __("search all chinese characters / kanjis by all possible ways") ?></li> 
        </ul>
	</div>
</div>
