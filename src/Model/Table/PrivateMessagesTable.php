<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009 DEPARIS Étienne <etienne.deparis@umaneti.net>
 * Copyright (C) 2010 SIMON   Allan   <allan.simon@supinfo.com>
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
 */
namespace App\Model\Table;

use Cake\Database\Schema\TableSchema;
use Cake\ORM\Table;
use App\Model\CurrentUser;
use Cake\I18n\Time;
use Cake\Event\Event;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use Cake\Datasource\Exception\RecordNotFoundException;


class PrivateMessagesTable extends Table
{
    public $name = 'PrivateMessage';

    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('date', 'string');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->belongsTo('Users');
        $this->belongsTo('Recipients', [
            'className' => 'Users',
            'foreignKey' => 'recpt'
        ]);
        $this->belongsTo('Authors', [
            'className' => 'Users',
            'foreignKey' => 'sender'
        ]);
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->addCreate(function($message) {
            return $message->type == 'machine' || $this->canSendMessage($message->sender);
        }, 'limitExceeded');

        $rules->addUpdate(function($message) {
            return $message->user_id == CurrentUser::get('id');
        }, 'isAllowedToUpdate');

        $rules->addDelete(function($message) {
            return $message->user_id == CurrentUser::get('id');
        }, 'isAllowedToDelete');
        
        return $rules;
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence('content')
            ->add('content', 'notBlank', [
                'rule' => [$this, 'notBlankExceptDraft'],
                'message' =>  __('You cannot send an empty message.')
            ]);

        $validator
            ->add('recipients', 'notBlank', [
                'rule' => [$this, 'notBlankExceptDraft'],
                'message' => __('You must fill the "To" field.')
            ]);
        
        return $validator;
    }

    function notBlankExceptDraft($value, $provider)
    {
        $data = $provider['data'];
        if (isset($data['folder']) && $data['folder'] == 'Drafts') {
            return true;
        } else {
            return !empty(trim($value));
        }
    }

    public function beforeFind($event, $query, $options, $primary)
    {
        // Making sure users can read only their own PM's.
        $query->where(['user_id' => CurrentUser::get('id')]);
    }

    /**
     * Return query for paginated messages in specified folder.
     *
     * @param  int $userId    ID for current user.
     * @param  string $folder Folder to get messages for.
     * @param  string $status Type of messages to get: 'read' or 'unread'
     *
     * @return array
     */
    public function getPaginatedMessages($userId, $folder, $status)
    {
        $conditions = array('folder' => $folder);

        if ($folder == 'Inbox') {
            $conditions['recpt'] = $userId;
        } else if ($folder == 'Sent' || $folder == 'Drafts') {
            $conditions['sender'] = $userId;
        } else if ($folder == 'Trash') {
            $conditions['user_id'] = $userId;
        }

        if ($status == 'read') {
            $conditions['isnonread'] = 0;
        } else if ($status == 'unread') {
            $conditions['isnonread'] = 1;
        }

        return [
            'conditions' => $conditions,
            'contain' => [
                'Authors' => [
                    'fields' => [
                        'id',
                        'username',
                        'image'
                    ]
                ],
                'Recipients' => [
                    'fields' => [
                        'id',
                        'username',
                        'image',
                    ]
                ]
            ],
            'order' => ['date' => 'DESC'],
            'limit' => 20
        ];
    }

    /**
     * Get unread message count for user.
     *
     * @param int $userId ID for user.
     *
     * @return int
     */
    public function numberOfUnreadMessages($userId)
    {
        return $this->find()
            ->where([
                'recpt' => $userId,
                'folder' => 'Inbox',
                'isnonread' => 1
            ])
            ->count();
    }

    /**
     * Return count of messages sent by user in the last 24 hours.
     *
     * @param  int $userId ID for user.
     *
     * @return int
     */
    public function todaysMessageCount($userId)
    {
        $yesterday = new Time('-24 hours');

        return $this->find()
            ->where([
                'sender' => $userId,
                'folder IN' => ['Sent', 'Trash'],
                'date >=' => $yesterday->i18nFormat('yyyy-MM-dd HH:mm:ss')
            ])
            ->count();
    }

    /**
     * Build message to send.
     *
     * @param  array  $data          Private message data.
     * @param  int    $currentUserId ID of current user.
     * @param  string $now           Current timestamp.
     *
     * @return array
     */
    private function buildMessage($data, $currentUserId, $now)
    {
        $message = array(
            'sender'    => $currentUserId,
            'date'      => $now,
            'folder'    => 'Inbox',
            'title'     => $data['title'],
            'content'   => $data['content'],
            'isnonread' => 1,
        );

        return $message;
    }

    /**
     * Save a draft message.
     *
     * @param  int      $currentUserId ID for current user.
     * @param  array    $data          Form data from controller.
     *
     * @return array                   Draft.
     */
    public function saveDraft($currentUserId, $now, $data)
    {
        $draft = array(
            'user_id'       => $currentUserId,
            'sender'        => $currentUserId,
            'draft_recpts'  => $data['recipients'],
            'date'          => $now,
            'folder'        => 'Drafts',
            'title'         => $data['title'],
            'content'       => $data['content'],
            'isnonread'     => 1,
            'sent'          => 0,
        );

        if ($data['messageId']) {
            $draft['id'] = $data['messageId'];
        }

        $entity = $this->newEntity($draft);
        return $this->save($entity);
    }

    /**
     * Save message to recipients inbox.
     *
     * @param  array $message Message to send.
     * @param  int   $recptId User id for recipient.
     *
     * @return array
     */
    private function saveToInbox($message, $recptId)
    {
        $message = array_merge($message, array(
            'recpt' => $recptId,
            'user_id' => $recptId,
            'draft_recpts' => '',
            'sent' => 1
        ));
        $message = $this->newEntity($message);
        return $this->save($message);
    }

    /**
     * Save message to senders outbox.
     *
     * @param  array $messageToSave Message to save to outbox.
     * @param  int   $recptId       User id for recipient.
     * @param  int   $currentUserId User id for current user.
     *
     * @return array
     */
    private function saveToOutbox($messageToSave, $recptId, $currentUserId)
    {
        $message = array_merge($messageToSave, array(
            'user_id'   => $currentUserId,
            'folder'    => 'Sent',
            'isnonread' => 0,
            'recpt' => $recptId,
            'draft_recpts' => '',
            'sent' => 1,
            'id' => null
        ));

        $message = $this->newEntity($message);
        return $this->save($message);
    }

    public function notify($recptId, $now, $message)
    {
        $toSend = $this->buildMessage($message, 0, $now);
        return $this->saveToInbox($toSend, $recptId);
    }

    public function send($currentUserId, $now, $data)
    {
        if (empty($data['recipients'])) {
            return [];
        }

        $recipients = $this->_buildRecipientsArray($data['recipients']);
        $messages = [];
        foreach ($recipients as $recpt) {
            $recptId = $this->Users->getIdFromUsername($recpt);
            $data = $this->buildMessage($data, $currentUserId, $now);
            if ($recptId) {
                $msg = $this->saveToInbox($data, $recptId);
                if ($msg) {
                    $event = new Event('Model.PrivateMessage.messageSent', $this, array(
                        'message' => $msg,
                    ));
                    $this->getEventManager()->dispatch($event);

                    $this->saveToOutbox($data, $recptId, $currentUserId);
                }                
            } else {
                $msg = $this->newEntity($data);
                $msg->setError('recicipients', format(
                    __('The user {username} to whom you want to send this '.
                        'message does not exist. Please try with another username.'),
                    ['username' => $recpt]
                ));
            }
            $messages[] = $msg;
        }
        return $messages;
    }

    /**
     * Build array of unique recipients from recipents string.
     *
     * @param  array $recpt Recipents
     * @return array
     */
    private function _buildRecipientsArray($recpt)
    {
        $recptArray = explode(',', $recpt);
        $recptArray = array_map('trim', $recptArray);
        $recptArray = array_filter($recptArray);

        return array_unique($recptArray, SORT_REGULAR);
    }

    /**
     * Return true if user can send message. New users can only send 5/24 hours.
     *
     * @param  int  $messagesToday Number of messages sent today.
     *
     * @return bool
     */
    public function canSendMessage($userId)
    {
        $user = $this->Users->get($userId, ['fields' => 'since']);
        $sentToday = $this->todaysMessageCount($userId);
        $since = new Time($user->since);
        $isNewUser = $since->wasWithinLast('2 weeks');

        return !$isNewUser || $sentToday < 5;
    }

    /**
     * Mark a private message as read.
     *
     * @param  array $message Private message.
     *
     * @return array
     */
    public function markAsRead($message)
    {
        if ($message->isnonread == 1) {
            $message->isnonread = 0;
            return $this->save($message);
        }

        return $message;
    }

    /**
     * Toggle message isnonread column.
     *
     * @param  array $message Private message.
     *
     * @return array
     */
    public function toggleUnread($id)
    {
        try {
            $message = $this->get($id);
        } catch (RecordNotFoundException $e) {
            return null;
        }
        
        $status = !! $message->isnonread;
        $message->isnonread = !$status;

        return $this->save($message);
    }

    public function deleteMessage($id)
    {
        try {
            $message = $this->get($id);
        } catch (RecordNotFoundException $e) {
            return false;
        }
        
        if ($message->folder == 'Trash') {
            return $this->delete($message);
        } else {
            $message->folder = 'Trash';
            return $this->save($message);
        }
    }
    
    public function restoreMessage($id)
    {
        try {
            $message = $this->get($id);
        } catch (RecordNotFoundException $e) {
            return false;
        }

        $message->folder = $message->origin;
        return $this->save($message);
    }

    public function readMessage($id)
    {
        try {
            $message = $this->get($id, [
                'contain' => [
                    'Authors' => [
                        'fields' => ['username', 'image']
                    ]
                ]   
            ]);
        } catch (RecordNotFoundException $e) {
            return null;
        }
        
        $message = $this->markAsRead($message);

        return $message;
    }
}
