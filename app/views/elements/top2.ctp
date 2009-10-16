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
if (isset($this->params['lang'])) {
	Configure::write('Config.language',  $this->params['lang']);
}
?>

<div id="top2">
	<a href="/" class="tatoeba_title"><strong>TATOEBA</strong> <em>Project</em></a>

	<cake:nocache>
	<div id="UserMenu">
	<?php
	if($session->read('Auth.User.id')){
		// Welcome message
		echo '<div>';
		__('Welcome');
		echo ' '.$session->read('Auth.User.username');
		echo '</div>';

		echo '<div>';
		echo $html->link(
			__('Messages', true),
			array("controller" => "privatemessages", "action" => "index")
		);
		echo '</div>';

		echo '<div>';
		echo $html->link(
			__('My profile',true),
			array("controller" => "users", "action" => "show/".$session->read('Auth.User.id' ))
		);
		echo ' | ';
		echo $html->link(
			__('My settings',true),
			array("controller" => "users", "action" => "settings")
		);
		echo '</div>';
	}else{
		echo '<span>';
		echo $html->link(
			__('Log in',true),
			array(
				"controller" => "users",
				"action" => "login"
			));
		echo '</span>';

		echo '<span>';
		echo $html->link(
			__('Register',true),
			array(
				"controller" => "users",
				"action" => "register"
			));
		echo '</span>';
	}
	?>
	</div>
	</cake:nocache>
</div>
