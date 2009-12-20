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

<?php
$lang = 'eng';
if (isset($this->params['lang'])) {
	Configure::write('Config.language',  $this->params['lang']);
	$lang = $this->params['lang'];
}

$languages = array(
	  'eng' => 'English'
	, 'fre' => 'Français'
	, 'chi' => '中文'
	, 'spa' => 'Español'
	//, 'jpn' => '日本語'
	, 'deu' => 'Deutsch'
	, 'ita' => 'Italiano'
);
$path = $this->params['controller'].'/';
if($this->params['action'] != 'display'){
	$path .= $this->params['action'].'/';
}
foreach($this->params['pass'] as $extraParam){
	$path .= $extraParam.'/';
}

echo $form->select('languageSelection', $languages, $lang, array("onchange" => "$(location).attr('href', '/' + this.value+ '/' + '".$path."');"));
?>