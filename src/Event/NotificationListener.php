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
namespace App\Event;

use Cake\Event\EventListenerInterface;
use Cake\Mailer\Email;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;


class NotificationListener implements EventListenerInterface {
    public function implementedEvents() {
        return array(
            'Model.PrivateMessage.messageSent' => 'sendPmNotification',
            'Model.SentenceComment.commentPosted' => 'sendSentenceCommentNotification',
            'Model.Wall.postPosted' => 'sendWallReplyNotification',
        );
    }

    public function __construct($Email = null) {
        $this->Email = $Email ? $Email : new Email();
    }

    private function _getMessageForMail($parentMessageId)
    {
        $Wall = ClassRegistry::init('Wall');
        return $Wall->find(
            'first',
            array(
                'order' => 'Wall.id',
                'fields'=> array('Wall.id'),
                'conditions' => array('Wall.id' => $parentMessageId),
                'contain'    => array(
                    'User' => array (
                        'fields' => array(
                            'User.username',
                            'User.id',
                            'User.email',
                            'User.send_notifications',
                        )
                    )
                )
            )
        );
    }

    public function sendWallReplyNotification($event) {
        $post = $event->getData('post'); // $post
        if (!$post['parent_id']) {
            return;
        }

        $parentMessage = $this->_getMessageForMail($post['parent_id']);
        if (!$parentMessage['User']['send_notifications']
            || $parentMessage['User']['id'] == $post['owner']) {
            return;
        }

        $recipient = $parentMessage['User']['email'];
        $User = ClassRegistry::init('User');
        $author = $User->getUsernameFromId($post['owner']);
        $subject = 'Tatoeba - ' . $author . ' has replied to you on the Wall';
        $url = array(
            'controller' => 'wall',
            'action' => 'show_message',
            $post['id'],
            '#' => 'message_'.$post['id']
        );
        $linkToMessage = Router::url($url, true);

        $this->Email
             ->to($recipient)
             ->subject($subject)
             ->template('wall_reply')
             ->viewVars(array(
                 'author' => $author,
                 'linkToMessage' => $linkToMessage,
                 'messageContent' => $post['content']
             ));

        $this->_send();
    }

    public function sendPmNotification($event) {
        $message = $event->getData('message'); // $message
        $User = TableRegistry::getTableLocator()->get('Users');
        $userSettings = $User->getSettings($message['recpt']);

        if (!$userSettings['send_notifications']) {
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

    private function _getMentionedUsernames($comment)
    {
        preg_match_all(
            "/@([a-zA-Z0-9_]+)/",
            $comment,
            $matches,
            PREG_PATTERN_ORDER
        );
        return $matches[1];
    }

    public function sendSentenceCommentNotification($event) {
        extract($event->data); // $comment
        $sentenceId = $comment['sentence_id'];

        $Sentence = ClassRegistry::init('Sentence');
        $SentenceComment = ClassRegistry::init('SentenceComment');

        $sentence = $Sentence->findById($sentenceId, array('user_id', 'text'));
        $comments = $SentenceComment->findAllBySentenceId($sentenceId, 'user_id');
        $userIds = array_merge(array($sentence), $comments);
        $userIds = Hash::extract($userIds, '{n}.{s}.user_id');

        $usernames = $this->_getMentionedUsernames($comment['text']);

        $User = ClassRegistry::init('User');
        $toNotify = $User->find('all', array(
            'fields' => array('email'),
            'conditions' => array(
                'OR' => array(
                    array('id' => $userIds),
                    array('username' => $usernames),
                ),
                'NOT' => array('id' => $comment['user_id']),
                'send_notifications' => true,
            ),
        ));
        $toNotify = Hash::extract($toNotify, '{n}.User.email');

        $comment['sentence_text'] = $sentence ? $sentence['Sentence']['text'] : false;
        $comment['author'] = $User->getUsernameFromId($comment['user_id']);
        foreach ($toNotify as $email) {
            $this->_sendSentenceCommentNotification($email, $comment);
        }
    }

    private function _sendSentenceCommentNotification($recipient, $comment) {
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
