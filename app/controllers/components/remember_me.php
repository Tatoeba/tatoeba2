<?php
/*
	This file is part of NeutrinoCMS.

	NeutrinoCMS is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	NeutrinoCMS is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with NeutrinoCMS.  If not, see <http://www.gnu.org/licenses/>.
*/

class RememberMeComponent extends Object
{
	var $components = array('Auth', 'Cookie');
	var $controller = null;

	/**
	 * Cookie retention period.
	 *
	 * @var string
	 */
	var $period = '+2 weeks';
	var $cookieName = 'User';

	function startup(&$controller)
	{
		$this->controller =& $controller;
	}

	function remember($username, $password)
	{
		$cookie = array();
		$cookie[$this->Auth->fields['username']] = $username;
		$cookie[$this->Auth->fields['password']] = $password;
		$this->Cookie->write($this->cookieName, $cookie, true, $this->period);
	}

	function check()
	{
		$cookie = $this->Cookie->read($this->cookieName);

		if (!is_array($cookie) || $this->Auth->user())
			return;

		if ($this->Auth->login($cookie))
		{
			$this->Cookie->write($this->cookieName, $cookie, true, $this->period);
		}
		else
		{
			$this->delete();
		}
	}

	function delete()
	{
		$this->Cookie->del($this->cookieName);
	}
}

?>