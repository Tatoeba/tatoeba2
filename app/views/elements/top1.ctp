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
$onlineVisitors = $this->requestAction('/visitors/online');
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}
?>

<div id="top1">
	<div class="logout">
	<?php
	// Log out link
	if($session->read('Auth.User.id')){
		echo $html->link(
			__('Log out',true),
			array(
				"controller" => "users",
				"action" => "logout"
			)
		);
	}
	?>
	</div>

	<div class="online_visitors">
	<?php
	__('Visitor(s) : ');
	echo $onlineVisitors;
	?>
	</div>
	
	<div class="language_choice">
	Language : 
	<?php
	$languages = array(
		  'eng' => 'English' 
		, 'fre' => 'Français'
		, 'chi' => '中文'
		, 'spa' => 'Español'
		//, 'jpn' => '日本語'
		//, 'deu' => 'Deutsch'
		//, 'ita' => 'Italiano'
	);
	
	foreach($languages as $code => $language){
		$path  = '/'.$code.'/';	
		$path .= $this->params['controller'].'/';
		
		if($this->params['action'] != 'display'){
			$path .= $this->params['action'].'/';
		}
		
		foreach($this->params['pass'] as $extraParam){
			$path .= $extraParam.'/';
		}
	
		// probably not the best way to do it but never mind
		
		echo $html->link($language, $path);
		echo ' | ';
	}
	?>
	</div>
</div>
