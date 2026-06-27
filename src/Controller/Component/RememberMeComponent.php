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
use Cake\Datasource\FactoryLocator;
use Cake\Http\Cookie\Cookie;
use DateTime;


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
    public $components = array('TinyAuth.Authentication');

    /**
     * Cookie retention period.
     *
     * @var string
     */
    private $_period = '+2 weeks';
    private static $_cookieName = 'User';

    private function saveCookie($cookie)
    {
        $resp = $this->getController()->getResponse()->withCookie($cookie);
        $this->getController()->setResponse($resp);
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
        $cookie = Cookie::create(
            self::$_cookieName,
            compact('username', 'password'),
            [
                'expires' => new DateTime($this->_period),
                'httponly' => true,
            ]
        );
        $this->saveCookie($cookie);
    }

    /**
     * Check if user can be automatically logged in.
     *
     * @return void
     */
    public function check()
    {
        $cookie = $this->getController()->getRequest()->getCookie(self::$_cookieName);
        $validCookie = is_array($cookie) &&
                       isset($cookie['username']) &&
                       isset($cookie['password']);

        if (!$validCookie || $this->Authentication->getIdentity()) {
            return;
        }

        $model = FactoryLocator::get('Table')->get('Users');
        $user = $model->find('userToLogin')
            ->where([
                'username' => $cookie['username'],
                'password' => $cookie['password'],
            ])->first();

        if ($user) {
            $user = new \ArrayObject($user); // TODO remove after upgrading to cakephp/authentication >= 3.3.1
            $this->Authentication->setIdentity($user);
            // refresh cookie expiration date to $this->_period from now
            $this->remember($cookie['username'], $cookie['password']);
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
        $controller = $this->getController();
        $cookie = $controller->getRequest()->getCookie(self::$_cookieName);
        if ($cookie) {
            $resp = $controller->getResponse()->withExpiredCookie(new Cookie(self::$_cookieName));
            $controller->setResponse($resp);
        }
    }

    public static function getCookieName()
    {
        return self::$_cookieName;
    }
}
