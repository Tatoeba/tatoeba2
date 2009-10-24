<div id="footer">
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
?>
<?php
if (isset($this->params['lang'])) { 
	Configure::write('Config.language',  $this->params['lang']); 
}

echo $html->link(__('Contact me', true), array("controller" => 'pages', "action" => 'contact'));
echo ' | ';
echo $html->link(__('Tatoeba Blog',true), 'http://blog.tatoeba.org');
echo ' | ';
echo $html->link(__('Downloads',true), array("controller" => 'pages', "action" => 'download-tatoeba-example-sentences'));
echo ' | ';
echo $html->link(__('Team & Credits', true), array("controller" => 'pages', "action" => 'tatoeba-team-and-credits'));

?>
</div>
