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

class WallTable extends Table
{
    public $name = 'Wall';
    public $useTable = 'wall';
    public $actsAs = array('Tree','Containable');

    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('content', 'text');
        $schema->setColumnType('modified', 'string');
        $schema->setColumnType('date', 'string');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->hasOne('WallThreads')
             ->setDependent(true);
        $this->belongsTo('Users', [
            'foreignKey' => 'owner'
        ]);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Tree');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('content', 'notBlank', [
                'rule' => 'notBlank',
                'message'    => __('You cannot save an empty message.'),
            ]);

        return $validator;
    }

    public function beforeSave($event, $entity, $options = array()) {
        if ($entity->date && !$entity->isDirty('hidden')) {
            $entity->modified = $entity->date;
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
            $rootId = $this->getRootMessageIdOfReply($entity->id);
            $newThreadData = $this->WallThreads->newEntity([
                'id' => $rootId,
                'last_message_date' => $entity->date,
            ]);
            $this->WallThreads->save($newThreadData);
        }

        if ($entity->isNew()) {
            $event = new Event('Model.Wall.postPosted', $this, array(
                'post' => $entity,
            ));
            $this->getEventManager()->dispatch($event);
        }
    }

    /**
     * Get all the message which start a thread
     *
     * @return array of all messages which start a thread
     */

    public function getFirstMessages()
    {
        return  $this->find()
            ->orderDesc('date')
            ->where(['parent_id' => 0])
            ->contain(['Users' => function ($q) {
                return $q->select(['id', 'username', 'image']);
            }])
            ->all();
    }

    /**
     * get all Messages
     *
     * @return array of all messages
     */

    public function getMessages()
    {
        return $this->find()
            ->order('id')
            ->contain(['Users' => function ($q) {
                return $q->select(['id', 'username', 'image']);
            }])
            ->all();
    }

    /**
     * retrieve all the thread of the given root message
     *
     * @param array $rootMessages Array of array with 'lft' and 'rght' field
     *                            take a look at how to store hierarchical data
     *                            with mysql for more informations
     *
     * @return array A nested array of array of array ... nested in a logical way
     *                children are nested in their parent etc...
     */

    public function getMessagesThreaded($rootMessages)
    {
        if (empty($rootMessages)) {
            return array();
        }

        // generate the condition array as it's a bit complicated
        $orArray = array();
        foreach ($rootMessages as $rootMessage) {
            $lftRghtArray = array($rootMessage['lft'], $rootMessage['rght']);
            $betweenArray = array (
                'Wall.lft BETWEEN ? AND ?' => $lftRghtArray
            );

            array_push($orArray, $betweenArray);
        }
        // execute the request
        $result = $this->find('threaded')
            ->orderDesc('WallThreads.last_message_date')
            ->orderAsc('Wall.date')
            ->where(['OR' => $orArray])
            ->contain([
                'Users' => function ($q) {
                    return $q->select(['id', 'image', 'username']);
                },
                'WallThreads' => function ($q) {
                    return $q->select(['last_message_date']);
                }
            ])
            ->all();

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
     * Retrieve the id of one message's owner
     *
     * @param int $messageId Id of the message
     *
     * @return int The owner id
     */

    public function getOwnerIdOfMessage($messageId)
    {
        $result = $this->find()
            ->select(['owner'])
            ->where(['id' => $messageId])
            ->first();

        return $result->owner;
    }

    /**
     * Return of the id of the first of the thread of the given
     * message
     *
     * @param int $replyId The id of the message we want the root.
     *
     * @return int Return the root id.
     */

    public function getRootMessageIdOfReply($replyId)
    {
        $replyLftRght = $this->_getLftRghtOfMessage($replyId);
        $replyLft = $replyLftRght->lft;
        $replyRght = $replyLftRght->rght;
        $result = $this->find()
            ->where([
                'parent_id IS NULL',
                'lft <=' => $replyLft,
                'rght >=' => $replyRght
            ])
            ->select('id')
            ->first();

        return $result->id;
    }





    /**
     * retriev a nested array with all the messages of the thread
     * which contain the message id given as parameter
     *
     * @param int $messageId Id of the message.
     *
     * @return array the whole thread
     */
    public function getWholeThreadContaining($messageId)
    {
        $rootId = $this->getRootMessageIdOfReply($messageId);
        $rootLftRght = $this->_getLftRghtOfMessage($rootId);
        $lftRghtArray = array(
            $rootLftRght['lft'],
            $rootLftRght['rght']
        );

        // execute the request
        $result = $this->find('threaded')
            ->orderAsc('Wall.date')
            ->where(['Wall.lft BETWEEN ? AND ?' => $lftRghtArray])
            ->contain(['Users' => function ($q) {
                return $q->select(['id', 'image', 'username']);
            }])
            ->all();
        return $result;

    }

    /**
     * retrive the left and right field (use for hierarchical data in mysql)
     * of a message
     *
     * @param int $messageId The message id.
     *
     * @return array Array with 'lft'  and 'rght' fields.
     */
    private function _getLftRghtOfMessage($messageId)
    {
        $replyLftRght = $this->get($messageId, ['fields' => ['lft', 'rght']]);

        return $replyLftRght;
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
}
