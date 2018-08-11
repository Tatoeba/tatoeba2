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
 * @link     http://tatoeba.org
 */

App::uses('CakeEmail', 'Network/Email');

/**
 * Component for mails.
 *
 * @category Default
 * @package  Components
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class MailerComponent extends Component
{
    public function sendBlockedOrSuspendedUserNotif($username, $isSuspended) {
        $this->Email = new CakeEmail();
        $this->Email
            ->to('community-admins@tatoeba.org')
            ->subject('( ! ) ' . $username)
            ->template('blocked_or_suspended_user');

        $User = ClassRegistry::init('User');
        $Contribution = ClassRegistry::init('Contribution');
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


    public function sendPmNotification($pm, $id)
    {
        $User = ClassRegistry::init('User');
        $recipientEmail = $User->getEmailFromId($pm['recpt']);
        $sender = $User->getUsernameFromId($pm['sender']);
        $title = $pm['title'];
        $content = $pm['content'];

        $this->Email = new CakeEmail();
        $this->Email
            ->to($recipientEmail)
            ->subject('Tatoeba PM - ' . $title)
            ->template('new_private_message')
            ->viewVars(array(
              'sender' => $sender,
              'title' => $title,
              'message' => $content,
              'messageId' => $id
            ));

        $this->_send();
    }


    public function sendSentenceCommentNotification($recipient, $comment, $sentenceOwner) {
        if (empty($recipient)) {
            return;
        }
        $author = CurrentUser::get('username');
        $sentenceText = $comment['sentence_text'];
        $sentenceIsDeleted = $sentenceText === false;
        $sentenceId = $comment['sentence_id'];
        if ($sentenceIsDeleted) {
            $subject = 'Tatoeba - Comment on deleted sentence #' . $sentenceId;
        } else {
            $subject = 'Tatoeba - Comment on sentence : ' . $sentenceText;
        }
        $linkToSentence = 'https://'.$_SERVER['HTTP_HOST']
            . '/sentence_comments/show/'
            . $comment['sentence_id']
            . '#comments';
        $recipientIsOwner = ($recipient == $sentenceOwner);
        $commentText = $comment['text'];

        $this->Email = new CakeEmail();
        $this->Email
            ->to($recipient)
            ->subject($subject)
            ->template('comment_on_sentence')
            ->viewVars(array(
              'author' => $author,
              'linkToSentence' => $linkToSentence,
              'commentText' => $commentText,
              'recipientIsOwner' => $recipientIsOwner,
              'sentenceIsDeleted' => $sentenceIsDeleted,
              'sentenceText' => $sentenceText,
              'sentenceId' => $sentenceId,
            ));

        $this->_send();
    }


    public function sendWallReplyNotification($recipient, $message)
    {
        $author = $message['User']['username'];
        $subject = 'Tatoeba - ' . $author . ' has replied to you on the Wall';
        $linkToMessage = 'https://'.$_SERVER['HTTP_HOST']
            . '/wall/show_message/'
            . $message['Wall']['id']
            . '#message_'.$message['Wall']['id'];
        $messageContent = $message['Wall']['content'];

        $this->Email = new CakeEmail();
        $this->Email
            ->to($recipient)
            ->subject($subject)
            ->template('wall_reply')
            ->viewVars(array(
              'author' => $author,
              'linkToMessage' => $linkToMessage,
              'messageContent' => $messageContent
            ));

        $this->_send();
    }


    public function sendNewPassword($recipient, $username, $newPassword)
    {
        $this->Email = new CakeEmail();
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

        $this->Email->config(array(
            'port' => '465',
            'timeout' => '45',
            'host' => 'ssl://smtp.gmail.com',
            'username' => Configure::read('Mailer.username'),
            'password' => Configure::read('Mailer.password'),
            'transport' => $this->getTransport(),
        ));
        $this->Email->emailFormat('html');
        $this->Email->from(array(Configure::read('Mailer.username') => 'noreply'));
        $this->Email->send();
    }

    public function getTransport()
    {
        return 'Smtp';
    }
}
