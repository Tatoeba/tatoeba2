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

use Cake\Datasource\ModelAwareTrait;
use Cake\Event\EventListenerInterface;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Utility\Hash;


class NotificationListener implements EventListenerInterface {

    use MailerAwareTrait;
    use ModelAwareTrait;

    public function implementedEvents() {
        return [
            'Model.PrivateMessage.messageSent' => 'sendPmNotification',
            'Model.SentenceComment.commentPosted' => 'sendSentenceCommentNotification',
            'Model.Wall.replyPosted' => 'sendWallReplyNotification',
            'Model.Wall.newThread' => 'sendNewThreadNotification',
        ];
    }

    public function __construct() {
        $this->Mailer = $this->getMailer('Messaging');
        $this->Users = $this->loadModel('Users');
    }

    private function _getMessageForMail($parentMessageId)
    {
        return $this->loadModel('Wall')->find()
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

    private function _getUsersEmailsToNotify($query) {
        $toNotify =
            $query
            ->where(['send_notifications' => true])
            ->select(['email'])
            ->toList();
        $toNotify = Hash::extract($toNotify, '{n}.email');
        $toNotify = array_filter($toNotify);
        return $toNotify;
    }

    public function sendWallReplyNotification($event) {
        $post = $event->getData('post'); // $post

        $parentMessage = $this->_getMessageForMail($post->parent_id);
        $author = $this->Users->getUsernameFromId($post->owner);
        $toMention = $this->Users->find();

        if ($parentMessage->user->send_notifications
            && $parentMessage->user->id != $post->owner) {
            $recipient = $parentMessage->user->email;
            $this->Mailer->send(
                'wall_reply',
                [$recipient, $author, $post]
            );
            $toMention->where(['NOT' => ['id' => $parentMessage->user->id]]);
        }

        $this->_sendWallMentionNotification($post, $author, $toMention);
    }

    public function sendPmNotification($event) {
        $message = $event->getData('message'); // $message
        $userSettings = $this->Users->getSettings($message->recpt);

        if (!$userSettings['send_notifications']) {
            return;
        }

        $recipientEmail = $this->Users->getEmailFromId($message->recpt);
        $sender = $this->Users->getUsernameFromId($message->sender);

        $this->Mailer->send(
            'new_private_message',
            [$recipientEmail, $sender, $message]
        );
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
        $sentenceId = $comment->sentence_id;

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
        $userIds = array_merge([$sentence], $comments);
        $userIds = Hash::extract($userIds, '{n}.user_id');

        $usernames = $this->_getMentionedUsernames($comment->text);
        $orCondition = ['id IN' => $userIds];
        if ($usernames) {
            $orCondition['username IN'] = $usernames;
        }

        $baseQuery = $this->Users->find()->where([
            'OR' => $orCondition,
            'NOT' => ['id' => $comment->user_id],
        ]);

        $author = $this->Users->getUsernameFromId($comment->user_id);
        foreach ($this->_getUsersEmailsToNotify($baseQuery) as $recipient) {
            $this->Mailer->send(
                'comment_on_sentence',
                [$recipient, $author, $comment, $sentence]
            );
        }
    }

    public function sendNewThreadNotification($event) {
        $post = $event->getData('post');
        $author = $this->Users->getUsernameFromId($post->owner);
        $this->_sendWallMentionNotification($post, $author, $this->Users->find());
    }

    private function _sendWallMentionNotification($post, $author, $baseQuery) {
        $usernames = $this->_getMentionedUsernames($post->content);
        if (empty($usernames)) {
            return;
        }

        $baseQuery->where([
            'username IN' => $usernames,
            'NOT' => ['id' => $post->owner],
        ]);
        foreach ($this->_getUsersEmailsToNotify($baseQuery) as $recipient) {
            $this->Mailer->send(
                'wall_mention',
                [$recipient, $author, $post]
            );
        }
    }
}
