<?php
namespace App\Mailer;

use App\Model\CurrentUser;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;

/**
 * Mailer for User-related emails
 */
class UserMailer extends Mailer {

    public function blocked_or_suspended_user($user, $isSuspended) {
        $this->setTo('tatoeba-community-admins@googlegroups.com')
            ->setSubject("( ! ) {$user->username}")
            ->setEmailFormat('html')
            ->setViewVars([
                'admin' => CurrentUser::get('username'),
                'user' => $user->username,
                'userId' => $user->id,
                'isSuspended' => $isSuspended,
            ]);
    }

    public function new_password($user, $newPassword)
    {
        $this
            ->setTo($user->email)
            ->setSubject(__('Tatoeba, new password'))
            ->setEmailFormat('html')
            ->setViewVars([
                'username' => $user->username,
                'newPassword' => $newPassword,
            ]);
    }
}
