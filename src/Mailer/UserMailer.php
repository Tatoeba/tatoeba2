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

    public function setSubject($subject)
    {
        if (Configure::read('Tatoeba.devStylesheet')) {
            $subject = "[TEST]" . $subject;
        }
        return parent::setSubject($subject);
    }

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

    public function content_with_outbound_links($entity, $author) {
        if ($entity instanceOf \App\Model\Entity\SentenceComment) {
            $subject = $entity->isNew() ?
                "Outbound links in new sentence comment #{$entity->id}" :
                "Outbound links in edited sentence comment #{$entity->id}";
        } elseif ($entity instanceOf \App\Model\Entity\Wall) {
            $subject = $entity->isNew() ?
                "Outbound links in new wall post #{$entity->id}" :
                "Outbound links in edited wall post #{$entity->id}";
        }
        $this
            ->setTo(Configure::read('Tatoeba.communityModeratorEmail'))
            ->setSubject($subject)
            ->setEmailFormat('html')
            ->setViewVars(compact('entity', 'author'));
    }
}
