<?php
namespace App\Mailer;

use App\Model\CurrentUser;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use Cake\Core\Configure;

/**
 * Mailer for User-related emails
 */
class UserMailer extends Mailer {

    public function blocked_or_suspended_user($user, $isSuspended) {
        $this->setTo(Configure::read('Tatoeba.communityModeratorEmail'))
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

    public function content_report($report) {
        $this
            ->setTo(Configure::read('Tatoeba.communityModeratorEmail'))
            ->setSubject($report->getTitle())
            ->setEmailFormat('html')
            ->setViewVars(compact('report'));
    }

    public function comment_with_outbound_links($comment, $author) {
        $this
            ->setTo(Configure::read('Tatoeba.communityModeratorEmail'))
            ->setSubject(
                $comment->isNew() ?
                "Outbound links in new sentence comment #{$comment->id}" :
                "Outbound links in edited sentence comment #{$comment->id}"
            )
            ->setEmailFormat('html')
            ->setViewVars(compact('comment', 'author'));
    }
}
