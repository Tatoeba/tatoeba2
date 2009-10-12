<?
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
?>
<div id="logsLegend">
<span class="sentenceAdded"><?php __('sentence added') ?></span>
<span class="linkAdded"><?php __('link added') ?></span>
<span class="sentenceModified"><?php __('sentence modified') ?></span>
<?php 
//<span class="correctionSuggested"> __('correction suggested') </span> 
// <span class="sentenceDeleted"> __('sentence deleted') </span>
// <span class="linkDeleted"> __('link deleted') </span>
?>
</div>

<table id="logs">
<?php
foreach ($contributions as $contribution){
	$logs->entry($contribution['Contribution'], $contribution['User']);
	//$logs->entry($contribution['Contribution']);
}
?>
</table>
