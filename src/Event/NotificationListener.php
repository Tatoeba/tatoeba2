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
use Cake\ORM\TableRegistry;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Utility\Hash;


class NotificationListener implements EventListenerInterface {
    public function implementedEvents() {
        return array(
            'Model.PrivateMessage.messageSent' => 'sendPmNotification',
            'Model.SentenceComment.commentPosted' => 'sendSentenceCommentNotification',
            'Model.Wall.replyPosted' => 'sendWallReplyNotification',
        );
    }

    public function __construct($Email = null) {
        $this->Email = $Email ? $Email : new Email();
    }

    private function _getMessageForMail($parentMessageId)
    {
        $Wall = TableRegistry::getTableLocator()->get('Wall');
        return $Wall->find()
            ->where([
                'Wall.id' => $parentMessageId
            ])
            ->order(['Wall.id'])
            ->select(['Wall.id'])
            ->contain([
                'Users' => [
                    'fields' => [
                        'username',
                        'id',
                        'email',
                        'send_notifications',
                    ]
                ]
            ])
            ->first();
    }

    public function sendWallReplyNotification($event) {
        $post = $event->getData('post'); // $post

        $parentMessage = $this->_getMessageForMail($post['parent_id']);
        if (!$parentMessage->user->send_notifications
            || $parentMessage->user->id == $post['owner']) {
            return;
        }

        $recipient = $parentMessage->user->email;
        $User = TableRegistry::getTableLocator()->get('Users');
        $author = $User->getUsernameFromId($post['owner']);
        $subject = 'Tatoeba - ' . $author . ' has replied to you on the Wall';

        $this->Email
             ->setTo($recipient)
             ->setSubject($subject)
             ->setTemplate('wall_reply')
             ->setViewVars(array(
                 'author' => $author,
                 'postId' => $post['id'],
                 'messageContent' => $post['content']
             ))
             ->send();
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
            ->setTo($recipientEmail)
            ->setSubject('Tatoeba PM - ' . $title)
            ->setTemplate('new_private_message')
            ->setViewVars(array(
              'sender' => $sender,
              'title' => $title,
              'message' => $content,
              'messageId' => $message['id'],
            ))
            ->send();
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
        $comment = $event->getData('comment'); // $comment
        $sentenceId = $comment['sentence_id'];

        $Sentence = TableRegistry::getTableLocator()->get('Sentences');
        $SentenceComment = TableRegistry::getTableLocator()->get('SentenceComments');

        try {
            $sentence = $Sentence->get($sentenceId, ['user_id', 'text']);
        } catch (RecordNotFoundException $e) {
            $sentence = null;
        }
        
        $comments = $SentenceComment->findAllBySentenceId($sentenceId, [
            'fields' => ['user_id']
        ])->toList();
        $userIds = array_merge(array($sentence), $comments);
        $userIds = Hash::extract($userIds, '{n}.user_id');

        $usernames = $this->_getMentionedUsernames($comment['text']);
        $orCondition = ['id IN' => $userIds];
        if ($usernames) {
            $orCondition['username IN'] = $usernames;
        }

        $User =  TableRegistry::getTableLocator()->get('Users');
        $toNotify = $User->find()
            ->where([
                'OR' => $orCondition,
                'NOT' => ['id' => $comment['user_id']],
                'send_notifications' => true,
            ])
            ->select(['email'])
            ->toList();
        $toNotify = Hash::extract($toNotify, '{n}.email');

        $comment['sentence_text'] = $sentence ? $sentence->text : false;
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
        $commentText = $comment['text'];

        $this->Email
            ->setTo($recipient)
            ->setSubject($subject)
            ->setTemplate('comment_on_sentence')
            ->setViewVars(array(
              'author' => $comment['author'],
              'commentText' => $commentText,
              'sentenceIsDeleted' => $sentenceIsDeleted,
              'sentenceText' => $sentenceText,
              'sentenceId' => $sentenceId,
            ))
            ->send();
    }
}
