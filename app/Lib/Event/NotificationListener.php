<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2018  Gilles Bedel

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

App::uses('CakeEmail', 'Network/Email');
App::uses('CakeEventListener', 'Event');
App::uses('Router', 'Routing');

class NotificationListener implements CakeEventListener {
    public function implementedEvents() {
        return array(
            'Model.PrivateMessage.messageSent' => 'sendPmNotification',
            'Model.SentenceComment.commentPosted' => 'sendSentenceCommentNotification',
        );
    }

    public function __construct($Email = null) {
        $this->Email = $Email ? $Email : new CakeEmail();
    }

    public function sendPmNotification($event) {
        extract($event->data); // $message
        $User = ClassRegistry::init('User');
        $userSettings = $User->getSettings($message['recpt']);

        if (!$userSettings['User']['send_notifications']) {
            return;
        }

        $recipientEmail = $User->getEmailFromId($message['recpt']);
        $sender = $User->getUsernameFromId($message['sender']);
        $title = $message['title'];
        $content = $message['content'];

        $this->Email
            ->to($recipientEmail)
            ->subject('Tatoeba PM - ' . $title)
            ->template('new_private_message')
            ->viewVars(array(
              'sender' => $sender,
              'title' => $title,
              'message' => $content,
              'messageId' => $message['id'],
            ));

        $this->_send();
    }

    private function _getMentionedEmails($comment)
    {
        $User = ClassRegistry::init('User');
        preg_match_all(
            "/@[a-zA-Z0-9_]+/",
            $comment['text'],
            $usernames
        );
        $emails = array();
        foreach ($usernames[0] as $string) {
            $username = substr($string, 1);
            $user = $User->findByUsername($username);
            $sendNotif = !empty($user) && $user['User']['send_notifications'] == 1;
            if ($sendNotif) {
                $emails[] = $user['User']['email'];
            }
        }

        return $emails;
    }

    public function sendSentenceCommentNotification($event) {
        extract($event->data); // $comment
        $sentenceId = $comment['sentence_id'];

        $SentenceComment = ClassRegistry::init('SentenceComment');
        $participants = $SentenceComment->getEmailsFromComments(
            $sentenceId
        );

        $Sentence = ClassRegistry::init('Sentence');
        $sentenceOwner = $Sentence->getEmailFromSentence(
            $sentenceId
        );

        if ($sentenceOwner) {
            $participants[] = $sentenceOwner;
        }

        $Sentence->id = $sentenceId;
        $sentenceText = $Sentence->field('text');

        $commentId = $SentenceComment->id;
        $mentionEmails = $this->_getMentionedEmails($comment);
        foreach($mentionEmails as $email) {
            $participants[] = $email;
        }
        $participants = array_unique($participants);

        $User = ClassRegistry::init('User');
        $User->id = $comment['user_id'];
        $userEmail = $User->field('email');

        // send message to the other participants of the thread
        $comment['sentence_text'] = $sentenceText;
        $comment['author'] = $User->getUsernameFromId($comment['user_id']);
        foreach ($participants as $participant) {
            if ($participant != $userEmail) {
                $this->_sendSentenceCommentNotification(
                    $participant,
                    $comment
                );
            }
        }
    }

    public function _sendSentenceCommentNotification($recipient, $comment) {
        if (empty($recipient)) {
            return;
        }
        $sentenceText = $comment['sentence_text'];
        $sentenceIsDeleted = $sentenceText === false;
        $sentenceId = $comment['sentence_id'];
        if ($sentenceIsDeleted) {
            $subject = 'Tatoeba - Comment on deleted sentence #' . $sentenceId;
        } else {
            $subject = 'Tatoeba - Comment on sentence : ' . $sentenceText;
        }
        $url = array(
            'controller' => 'sentence_comments',
            'action' => 'show',
            $comment['sentence_id'],
            '#' => 'comments'
        );
        $linkToSentence = Router::url($url, true);
        $commentText = $comment['text'];

        $this->Email
            ->to($recipient)
            ->subject($subject)
            ->template('comment_on_sentence')
            ->viewVars(array(
              'author' => $comment['author'],
              'linkToSentence' => $linkToSentence,
              'commentText' => $commentText,
              'sentenceIsDeleted' => $sentenceIsDeleted,
              'sentenceText' => $sentenceText,
              'sentenceId' => $sentenceId,
            ));

        $this->_send();
    }

    private function _send() {
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
