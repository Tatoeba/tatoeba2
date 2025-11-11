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
 * @link     https://tatoeba.org
 */
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;


/**
 * Component for permissions.
 *
 * @category Default
 * @package  Components
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class RememberMeComponent extends Component
{
    public $components = array('Auth', 'Cookie');

    /**
     * Cookie retention period.
     *
     * @var string
     */
    private $_period = '+2 weeks';
    private $_cookieName = 'User';

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
        $validCookie = is_array($cookie) &&
                       isset($cookie['username']) &&
                       isset($cookie['password']);

        if (!$validCookie || $this->Auth->user()) {
            return;
        }

        $model = TableRegistry::get('Users');
        $user = $model->find('userToLogin')
            ->where([
                'username' => $cookie['username'],
                'password' => $cookie['password'],
            ])->first();

        if ($user) {
            $this->Auth->setUser($user->toArray());
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
