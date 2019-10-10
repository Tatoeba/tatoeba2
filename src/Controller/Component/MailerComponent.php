<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use App\Model\CurrentUser;


/**
 * Component for mails.
 *
 * @category Default
 * @package  Components
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class MailerComponent extends Component
{
    public function sendBlockedOrSuspendedUserNotif($username, $isSuspended) {
        $this->Email = new Email();
        $this->Email
            ->to('tatoeba-community-admins@googlegroups.com')
            ->subject('( ! ) ' . $username)
            ->template('blocked_or_suspended_user');

        $User = TableRegistry::get('Users');
        $Contribution = TableRegistry::get('Contributions');
        $userId = $User->getIdFromUsername($username);
        $ips = $Contribution->getLastContributionOf($userId);

        $this->Email->viewVars(array(
          'admin' => CurrentUser::get('username'),
          'user' => $username,
          'userId' => $userId,
          'isSuspended' => $isSuspended,
          'ips' => $ips
        ));

        $this->_send();
    }

    public function sendNewPassword($recipient, $username, $newPassword)
    {
        $this->Email = new Email();
        $this->Email
            ->to($recipient)
            ->subject(__('Tatoeba, new password'))
            ->template('new_password')
            ->viewVars(array(
              'username' => $username,
              'newPassword' => $newPassword
            ));

        $this->_send();
    }

    private function _send()
    {
        if (Configure::read('Mailer.enabled') == false) {
            return;
        }

        $this->Email->transport('gmail')
            ->emailFormat('html')
            ->setFrom([Configure::read('Mailer.username') => 'noreply'])
            ->send();
    }

    public function getTransport()
    {
        return 'Smtp';
    }
}
