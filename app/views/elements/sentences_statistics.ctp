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
$stats = $this->requestAction('/sentences/statistics');
if (isset($this->params['lang'])) {
	Configure::write('Config.language',  $this->params['lang']);
}

echo '<div id="sentencesStats">';
echo '<ul>';
for($i = 0; $i < 5; $i++){
	$stat = $stats[$i];
	echo '<li class="stat" title="'.$languages->codeToName($stat['Sentence']['lang']).'">';
	echo $html->image(($stat['Sentence']['lang']? $stat['Sentence']['lang']: "unknown_lang").'.png');
	echo '<span class="tooltip">'.$stat['Sentence']['lang'].' : </span>';
	echo $stat[0]['count'];
	echo '</li>';
}
echo '</ul>';

echo '<ul class="minorityLanguages" style="display:none">';
for($i = 5; $i < count($stats); $i++){
	$stat = $stats[$i];
	echo '<li class="stat" title="'.$languages->codeToName($stat['Sentence']['lang']).'">';
	echo $html->image(($stat['Sentence']['lang']? $stat['Sentence']['lang']: "unknown_lang").'.png');
	echo '<span class="tooltip">'.$stat['Sentence']['lang'].' : </span>';
	echo $stat[0]['count'];
	echo '</li>';
}
echo '</ul>';

echo '<a class="statsDisplay showStats">[+] '. __('show all', true) . '</a>';
echo '<a class="statsDisplay hideStats" style="display:none">[-] '. __('top 5 only', true) . '</a>';

echo '</div>';


?>
