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
$navigation->displayUsersNavigation($user['User']['id'], $user['User']['username']);

echo '<h3>';
__('User following');
echo '</h3>';

var_dump($following);
/*
if(count($user['Follower']) > 0){
	foreach($user['Follower'] as $follower){
		var_dump($follower);
	}
}else{
	__('This user does not follow any users.');
}*/
?>
