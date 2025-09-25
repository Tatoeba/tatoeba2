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
 */
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\Event\Event;
use App\Event\NotificationListener;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use App\Model\CurrentUser;
use Cake\Datasource\Exception\RecordNotFoundException;

class SentenceCommentsTable extends Table
{
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('text', 'text');
        return $schema;
    }

    public function initialize(array $config)
    {
        $this->belongsTo('Sentences');
        $this->belongsTo('Users');

        $this->addBehavior('Timestamp');
        $this->addBehavior('LimitResults');
    }

    public function buildRules(RulesChecker $rules)
    {
        $rules->addDelete(function($message) {
            return $message->user_id == CurrentUser::get('id') || CurrentUser::isAdmin();
        }, 'isAllowedToDelete');
        
        return $rules;
    }

    public function validationSkipOutboundLinksCheck(Validator $validator)
    {
        return $this
            ->validationDefault($validator)
            ->remove('text', 'outboundLinks');
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('text', 'notBlank', [
                'rule' => 'notBlank',
                'message' => __('Comments cannot be empty.')
            ])
            ->add('text', 'outboundLinks', [
                'rule' => function ($data, $provider) {
                    return CurrentUser::hasOutboundLinkPermission() || !$this->Users->containsOutboundLink($data);
                },
            ]);

        $validator->dateTime('created');

        $validator->dateTime('modified');

        return $validator;
    }

    public function beforeSave($event, $entity, $options = array()) {
        if ($entity->isDirty('hidden')) {
            $entity->modified = $entity->getOriginal('modified');
        }
    }

    public function afterSave($event, $entity, $options)
    {
        if ($entity->isNew()) {
            $event = new Event('Model.SentenceComment.commentPosted', $this, array(
                'comment' => $entity,
            ));
            $this->getEventManager()->dispatch($event);
        }
    }

    /**
     * Get number of sentences owned by a user.
     *
     * @param int $userId Id of the user.
     *
     * @return array
     */
    public function numberOfCommentsOwnedBy($userId)
    {
        return $this->find()
            ->where(['user_id' => $userId])
            ->count();
    }

    /**
     * get number of comments posted on all the sentences owned by
     * a specified user
     *
     * @param int $userId Id of the user.
     *
     * @return int
     */

    public function numberOfCommentsOnSentencesOf($userId)
    {
        return $this->find()
            ->contain(['Sentences'])
            ->where(['Sentences.user_id' => $userId])
            ->count();
    }

    /**
     * Return comments for given sentence.
     *
     * @param int $sentenceId Id of the sentence.
     *
     * @return array
     */
    public function getCommentsForSentence($sentenceId)
    {
        return $this->find()
            ->where(['sentence_id' => $sentenceId])
            ->order('SentenceComments.created')
            ->contain(['Users' => function ($q) {
                    return $q->select(['id', 'username', 'image']);
            }])
            ->all();
    }

    /**
     * Return latest comments.
     *
     * @param int $limit Number of comments to be retrieved.
     *
     * @return array
     */
    public function getLatestComments($limit)
    {
        $query = $this->find()
            ->limit($limit)
            ->where(['hidden' => 0])
            ->orderDesc('SentenceComments.created')
            ->contain([
                'Users' => [
                    'fields' => ['id', 'username', 'image']
                ],
                'Sentences' => [
                    'Users' => [
                        'fields' => ['id', 'username']
                    ]
                ]
            ]);
        $query = $this->excludeBots($query);
        return $query->toList();
    }

    /**
     * Retrieve the id of one comment's owner
     *
     * @param int $commentId Id of the comment
     *
     * @return int The owner id
     */

    public function getOwnerIdOfComment($commentId)
    {
        $result = $this->find()
            ->select(['user_id'])
            ->where(['id' => $commentId])
            ->first();

        return $result->user_id;
    }


    /**
     * Overridden paginateCount method, for optimization purpose.
     *
     * @param array $conditions
     * @param int   $recursive
     * @param array $extra
     *
     * @return int
     */
    function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
        $contain = array();
        foreach ($conditions as $key => $value) {
            if (strpos($key, "SentenceComment") === false) {
                $tmp = explode('.', $key);
                $model = $tmp[0];
                $contain[] = $model;
            }
        }

        $result = $this->find(
            'count',
            array(
                'contain' => $contain,
                'conditions' => $conditions
            )
        );

        return $result;
    }

    public function excludeBots($query)
    {
        $botsIds = Configure::read('Bots.userIds');

        if (!empty($botsIds)) {
            return $query->where(['SentenceComments.user_id NOT IN' => $botsIds]);
        }

        return $query;
    }

    public function deleteComment($id)
    {
        try {
            $comment = $this->get($id);
        } catch (RecordNotFoundException $e) {
            return false;
        }
        
        return $this->delete($comment);
    }
}
