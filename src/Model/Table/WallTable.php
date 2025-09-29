<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
 */
namespace App\Model\Table;

use Cake\Database\Schema\TableSchema;
use Cake\ORM\Table;
use Cake\Event\Event;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use App\Model\CurrentUser;
use Cake\Datasource\Exception\RecordNotFoundException;

class WallTable extends Table
{
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('content', 'text');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->hasOne('WallThreads', ['foreignKey' => 'id'])
            ->setDependent(true);
        $this->belongsTo('Users', [
            'foreignKey' => 'owner'
        ]);

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'date' => 'new',
                    'modified' => 'always',
                ]
            ]
        ]);
        $this->addBehavior('Tree');
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->addDelete(function($message) {
            $messageHasReplies = $this->hasMessageReplies($message->id);
            $userHasPermission = $message->owner == CurrentUser::get('id') || CurrentUser::isAdmin();
            return !$messageHasReplies && $userHasPermission;
        }, 'isAllowedToDelete');
        
        return $rules;
    }

    public function validationSkipOutboundLinksCheck(Validator $validator)
    {
        return $this
            ->validationDefault($validator)
            ->remove('content', 'outboundLinks');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('content', 'notBlank', [
                'rule' => 'notBlank',
                'message'    => __('You cannot save an empty message.'),
            ])
            ->add('content', 'outboundLinks', [
                'rule' => 'isLinkPermitted',
                'provider' => 'appvalidation',
                'message' => __(
                    'Your message was not posted because it contains outbound links. '.
                    'Please confirm the links are legitimate by ticking the checkbox below, '.
                    'and re-submit your message.'
                ),
            ]);

        $validator->dateTime('date');

        $validator->dateTime('modified');

        return $validator;
    }

    public function beforeSave($event, $entity, $options = array()) {
        if ($entity->isDirty('hidden')) {
            $entity->modified = $entity->getOriginal('modified');
        }
    }

    /**
     * used after a save is made in the database
     *
     * @param bool $created if succeed or not
     *
     * @return void
     */

    public function afterSave($event, $entity, $options = array())
    {
        if ($entity->isNew() && $entity->date) {
            $root = $this->getRootMessageOfReply($entity->id);
            $newThreadData = $this->WallThreads->newEntity([
                'id' => $root->id,
                'last_message_date' => $entity->date,
            ]);
            $this->WallThreads->save($newThreadData);
        }

        if ($entity->hidden) {
            $root = $this->getRootMessageOfReply($entity->id);
            $this->recalculateThreadDateIgnoringHiddenPosts($root);
        }

        if ($entity->isNew() && !$entity->parent_id) {
            $event = new Event('Model.Wall.newThread', $this, [
                'post' => $entity,
            ]);
            $this->getEventManager()->dispatch($event);
        }
    }

    private function recalculateThreadDateIgnoringHiddenPosts($root)
    {
        $result = $this->find()
            ->select(['latest_date' => 'MAX(date)'])
            ->where([
                'lft >=' => $root->lft,
                'rght <=' => $root->rght,
                'hidden IS' => false,
            ])
            ->first();

        if ($result->latest_date) {
            $thread = $this->WallThreads->get($root->id);
            $thread->last_message_date = $result->latest_date;
            $this->WallThreads->save($thread);
        }
    }


    /**
     * retrieve all the thread of the given root message
     *
     * @param ResultSet $rootMessages Set of messages with 'lft' and 'rght' field
     *                            take a look at how to store hierarchical data
     *                            with mysql for more informations
     *
     * @return array A nested array of array of array ... nested in a logical way
     *                children are nested in their parent etc...
     */
    public function getMessagesThreaded($rootMessages)
    {
        if ($rootMessages->isEmpty()) {
            return [];
        }

        // execute the request
        $result = $this->find('threaded')
            ->order([
                'WallThreads.last_message_date' => 'DESC',
                'Wall.date' => 'ASC'
            ])
            ->where(function($exp) use ($rootMessages) {
                $or = $exp->or_([]);
                foreach ($rootMessages as $rootMessage) {
                    $or->between(
                        'Wall.lft', 
                        $rootMessage->lft, 
                        $rootMessage->rght
                    );
                }
                return $exp->add($or);
            })
            ->contain([
                'Users' => [
                    'fields' => ['id', 'image', 'username']
                ],
                'WallThreads' => [
                    'fields' => ['last_message_date']
                ]
            ])
            ->toList();

        return $result;
    }

    /**
     * get the X last messages posted
     *
     * @param int $numberOfLastMessages number of messages wanted
     *
     * @return array of these messages
     */

    public function getLastMessages($numberOfLastMessages)
    {
        return $this->find()
            ->orderDesc('date')
            ->limit($numberOfLastMessages)
            ->where(['hidden' => 0])
            ->contain(['Users' => function ($q) {
                return $q->select(['id', 'username']);
            }])
            ->all();
    }


    /**
     * Return of the id of the first of the thread of the given
     * message
     *
     * @param int $replyId The id of the message we want the root.
     *
     * @return int Return the root id.
     */

    public function getRootMessageOfReply($replyId)
    {
        try {
            $replyLftRght = $this->get($replyId, ['fields' => ['lft', 'rght']]);
        } catch (RecordNotFoundException $e) {
            return null;
        }

        $replyLft = $replyLftRght->lft;
        $replyRght = $replyLftRght->rght;
        $result = $this->find()
            ->where([
                'parent_id IS NULL',
                'lft <=' => $replyLft,
                'rght >=' => $replyRght
            ])
            ->select(['id', 'lft', 'rght'])
            ->first();

        return $result;
    }

    /**
     * Retrieve a nested array with all the messages of the thread
     * which contain the message id given as parameter.
     *
     * @param int $messageId Id of the message.
     *
     * @return array the whole thread
     */
    public function getWholeThreadContaining($messageId)
    {
        $rootMsg = $this->getRootMessageOfReply($messageId); 
        if (!$rootMsg) {
            return [];
        }

        // execute the request
        $result = $this->find('threaded')
            ->order(['Wall.date'])
            ->where(function($q) use ($rootMsg) {
                return $q->between('Wall.lft', $rootMsg->lft, $rootMsg->rght);
            })
            ->contain(['Users' => function ($q) {
                return $q->select(['id', 'image', 'username']);
            }])
            ->toList();
        
        return $result;
    }

    /**
     * Tell if the current Message Id has replies
     *
     * @param int $messageId The message id.
     *
     * @return bool True if the message has replies, False otherwise
     */
    public function hasMessageReplies($messageId)
    {
        $replyLftRght = $this->find()
            ->select(['lft', 'rght'])
            ->where(['id' => $messageId])
            ->first();
        return $replyLftRght->lft != ($replyLftRght->rght - 1);
    }

    public function deleteMessage($id)
    {
        try {
            $message = $this->get($id);
        } catch (RecordNotFoundException $e) {
            return false;
        }
        
        return $this->delete($message);
    }

    public function saveReply($parentId, $content, $userId)
    {
        if (!$parentId) {
            return null;
        }
        
        $data = $this->newEntity([
            'content'   => $content,
            'owner'     => $userId,
            'parent_id' => $parentId,
        ]);

        $savedMessage = $this->save($data);

        if ($savedMessage) {
            $event = new Event('Model.Wall.replyPosted', $this, [
                'post' => $savedMessage
            ]);
            $this->getEventManager()->dispatch($event);

            return $this->getMessage($savedMessage->id);
        } else {
            return null;
        }
    }

    public function getMessage($id) {
        return $this->get($id, [
            'contain' => [
                'Users' => [
                    'fields' => ['id', 'username', 'image']
                ]
            ]
        ]);
    }
}
