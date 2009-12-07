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
echo '<p><em>'.__('Note : the list is not complete yet...',true) .'</em></p>';

echo '<h2>'. __('The team', true) . '</h2>';
echo '<ul>';
	echo '<li>' . $html->link('HO Ngoc Phuong Trang', array(
									'controller' => 'user',
									'action' => 'profile',
									'trang'));
	echo ' (aka. <span class="aka" title="' . __('Contact me', true) . '">';
	echo $html->link('trang', array(
									'controller' => 'privateMessages',
									'action' => 'write',
									'trang')) . '</span>)</li>';
	echo '<li>' . $html->link('SIMON Allan', array(
									'controller' => 'user',
									'action' => 'profile',
									'sysko'));
	echo ' (aka. <span class="aka" title="' . __('Contact me', true) . '">';
	echo $html->link('sysko', array(
									'controller' => 'privateMessages',
									'action' => 'write',
									'sysko')) . '</span>)</li>';

	//echo '<li>TAN Kévin (aka. keklesurvivant)</li>';

	//echo '<li>BEN YAALA Salem (aka. socom)</li>';

	//echo '<li>DEPARIS Étienne (aka. milouse)</li>';
echo '</ul>';

echo '<h2>'. __('Credits', true) . '</h2>';
echo '<ul class="credits">';
	echo '<li>';
	echo '<strong>' . __('Chinese translations', true) . '</strong> - ';
	echo $html->link('FU Congcong 傅琮琮', array(
							'controller' => 'user',
							'action' => 'profile',
							'fucongcong'));
	echo ' (aka. <span class="aka" title="' . __('Contact me', true) . '">';
	echo $html->link('fucongcong', array(
									'controller' => 'privateMessages',
									'action' => 'write',
									'fucongcong')) . '</span>)';

	echo '<li>';
	echo '<strong>' . __('Spanish translations', true) . '</strong> - ';
	echo $html->link('JIMÉNEZ Gabriel', array(
							'controller' => 'user',
							'action' => 'profile',
							'kylecito'));
	echo ' (aka. <span class="aka" title="' . __('Contact me', true) . '">';
	echo $html->link('kylecito', array(
									'controller' => 'privateMessages',
									'action' => 'write',
									'kylecito')) . '</span>)';
	echo '</li>';
echo '</ul>';

echo '<h2>'. __('Special thanks', true) . '</h2>';
echo '<ul>';
echo '<li><em>' . __("Lots of people...",true).'</em></li>';
echo '</ul>';
?>
