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
    public $components = array('Email');


    public function sendBlockedOrSuspendedUserNotif(
        $username, $isSuspended
    ) {
        $this->Email->to = 'community-admins@tatoeba.org';
        $this->Email->subject = '( ! ) ' . $username;
        $this->Email->template = 'blocked_or_suspended_user';

        $User = ClassRegistry::init('User');
        $Contribution = ClassRegistry::init('Contribution');
        $userId = $User->getIdFromUsername($username);
        $suspendedUsers = $User->getUsersWithSamePassword($userId);
        $ips = $Contribution->getLastContributionOf($userId);

        $this->set('admin', CurrentUser::get('username'));
        $this->set('user', $username);
        $this->set('userId', $userId);
        $this->set('isSuspended', $isSuspended);
        $this->set('suspendedUsers', $suspendedUsers);
        $this->set('ips', $ips);

        $this->_send();
    }


    public function sendPmNotification($pm, $id)
    {
        $User = ClassRegistry::init('User');
        $recipientEmail = $User->getEmailFromId($pm['recpt']);
        $sender = $User->getUsernameFromId($pm['sender']);
        $title = $pm['title'];
        $content = $pm['content'];

        $this->Email->to = $recipientEmail;
        $this->Email->subject = 'Tatoeba PM - ' . $title;
        $this->Email->template = 'new_private_message';

        $this->set('sender', $sender);
        $this->set('title', $title);
        $this->set('message', $content);
        $this->set('messageId', $id);

        $this->_send();
    }


    public function sendSentenceCommentNotification(
        $recipient, $comment, $sentenceOwner
    ) {
        $author = CurrentUser::get('username');
        $subject = 'Tatoeba - Comment on sentence : ' . $comment['sentence_text'];
        $linkToSentence = 'https://'.$_SERVER['HTTP_HOST']
            . '/sentence_comments/show/'
            . $comment['sentence_id']
            . '#comments';
        $recipientIsOwner = ($recipient == $sentenceOwner);
        $commentText = $comment['text'];
        $sentenceText = $comment['sentence_text'];
        
        $this->Email->to = $recipient;
        $this->Email->subject = $subject;
        $this->Email->template = 'comment_on_sentence';

        $this->set('author', $author);
        $this->set('linkToSentence', $linkToSentence);
        $this->set('commentText', $commentText);
        $this->set('recipientIsOwner', $recipientIsOwner);
        $this->set('sentenceText', $sentenceText);
        
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

        $this->Email->to = $recipient;
        $this->Email->subject = $subject;
        $this->Email->template = 'wall_reply';

        $this->set('author', $author);
        $this->set('linkToMessage', $linkToMessage);
        $this->set('messageContent', $messageContent);

        $this->_send();
    }


    public function sendNewPassword($recipient, $username, $newPassword)
    {
        $this->Email->to = $recipient;
        $this->Email->subject = __('Tatoeba, new password');
        $this->Email->template = 'new_password';

        $this->set('username', $username);
        $this->set('newPassword', $newPassword);

        $this->_send();
    }


    private function set($key, $value)
    {
        $this->Email->Controller->set($key, $value);
    }


    private function _send()
    {
        if (Configure::read('Mailer.enabled') == false) {
            return;
        }
        
        $this->Email->smtpOptions = array(
            'port' => '465',
            'timeout' => '45',
            'host' => 'ssl://smtp.gmail.com',
            'username' => Configure::read('Mailer.username'),
            'password' => Configure::read('Mailer.password'),
        );
        $this->Email->delivery = 'smtp';
        $this->Email->sendAs = 'html';
        $this->Email->from = 'no-reply <'.Configure::read('Mailer.username').'>';
        $this->Email->send();
    }
}
?>
