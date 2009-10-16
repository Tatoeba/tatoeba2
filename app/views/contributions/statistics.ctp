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
//pr($stats);
?>

<div id="second_modules">
	<div class="module">
		<h2>Mon espace</h2>
		<?php
			if(!$session->read('Auth.User.id')){
				echo $this->element('login'); 
			} else {
				echo $this->element('space'); 
			}
		?>
	</div>

</div>

<div id="main_modules">
	<div class="module">
<?php
echo '<table id="usersStatistics">';
	echo '<tr>';
	echo '<th>' . __('rank', true) . '</th>';
	echo '<th>' . __('username', true) . '</th>';
	echo '<th>' . __('member since', true) . '</th>';
	echo '<th>' . __('number of contributions', true) . '</th>';
	echo '</tr>';
	
$i = 0;	
foreach($stats as $stat){
	$css = 'class=';
	if($stat['User']['group_id'] == 1){
		$css .= '"admin"';
	}
	if($stat['User']['group_id'] == 4){
		$css .= '"normal"';
	}
	if($stat['User']['group_id'] == 5){
		$css .= '"pending"';
	}
	
	echo '<tr '.$css.'><td>';
	echo $i; $i++;
	echo '</td><td>';
	echo $html->link($stat['User']['username'], array("controller"=>"users", "action"=>"show", $stat['User']['id']));
	echo '</td><td>';
	echo $date->ago($stat['User']['since']);
	echo '</td><td>';
	echo $stat['0']['total'];
	echo '</td></tr>';
}
echo '</table>';
?>
	</div>
</div>

