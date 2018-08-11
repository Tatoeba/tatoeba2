<?php
/**
 * This file is part of NeutrinoCMS.
 *
 * NeutrinoCMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NeutrinoCMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NeutrinoCMS.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Component for permissions.
 *
 * @category Default
 * @package  Components
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class RememberMeComponent extends Component
{
    public $components = array('Auth', 'Cookie');
    public $controller = null;

    /**
     * Cookie retention period.
     *
     * @var string
     */
    private $_period = '+2 weeks';
    private $_cookieName = 'User';

    /**
     * ?
     *
     * @param unknown $controller ?
     *
     * @return void
     */
    public function startup(Controller $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Remember user so (s)he doesn't have to log in again.
     *
     * @param string $username Username.
     * @param string $password Password.
     *
     * @return void
     */
    public function remember($username, $password)
    {
        $cookie = array();
        $cookie['username'] = $username;
        $cookie['password'] = $password;
        $this->Cookie->write($this->_cookieName, $cookie, true, $this->_period);
    }

    /**
     * Check if user can be automatically logged in.
     *
     * @return void
     */
    public function check()
    {
        $cookie = $this->Cookie->read($this->_cookieName);

        if (!is_array($cookie) || $this->Auth->user()) {
            return;
        }

        $model = ClassRegistry::init('User');
        $user = $model->find('first', array(
            'conditions' => array(
                'username' => $cookie['username'],
                'password' => $cookie['password'],
            )
        ));

        if (!empty($user) && $this->Auth->login($user['User'])) {
            $this->Cookie->write($this->_cookieName, $cookie, true, $this->_period);
        } else {
            $this->delete();
        }
    }

    /**
     * Delete cookie.
     *
     * @return void
     */
    public function delete()
    {
        $this->Cookie->delete($this->_cookieName);
    }
}
