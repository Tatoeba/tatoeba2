<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009 Etienne Deparis <etienne.deparis@umaneti.net>

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

class PrivateMessage extends AppModel{
	var $name = 'PrivateMessage';

	var $belongsTo = array('User' => array('className' => 'User'));

	function get_messages($folderId, $userId){
		return $this->find(
			'all',
			array(
				'conditions' => array('PrivateMessage.user_id' => $userId,
										'PrivateMessage.folder' => $folderId),
				'limit'=> 10,
				'order' => 'PrivateMessage.date DESC'
			)
		);
	}

	function format_reply_message($content,$login){
		$messNextRegExp = preg_replace("#\r?\n#iU", " ", $content);
		$messNextRegExp = preg_replace("#\r?\n#iU", "\n > ", wordwrap($messNextRegExp, 50));
		return "\n" . sprintf( __('%s wrote:', true) , $login ) . "\n > " . $messNextRegExp;
	}
}
?>
