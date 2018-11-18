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
use Cake\Event\Event;
use App\Event\NotificationListener;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;


class SentenceCommentsTable extends Table
{
    public $actsAs = array('Containable');
    public $belongsTo = array('Sentence', 'User');


    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('text', 'notBlank', [
                'rule' => 'notBlank',
                'message' => __('Comments cannot be empty.')
            ]);

        return $validator;
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
        return $this->find(
            'count',
            array(
                'conditions' => array( 'SentenceComment.user_id' => $userId),
             )
        );

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
        return $this->find(
            'count',
            array(
                'conditions' => array('Sentence.user_id' => $userId),
                'contain' => array(
                    'Sentence' => array(
                    )
                )
            )
        );

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
        return $this->find(
            'all',
            array(
                'conditions' => array('SentenceComment.sentence_id' => $sentenceId),
                'order' => 'SentenceComment.created',
                'contain' => array(
                    'User' => array(
                        'fields' => array(
                            'id',
                            'username',
                            'image'
                        )
                    )
                )
            )
        );
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
        $conditions = array();
        $conditions['hidden'] = 0;
        $conditions = $this->getQueryConditionWithExcludedUsers($conditions);

        return $this->find(
            'all',
            array(
                'order' => 'SentenceComment.created DESC',
                'limit' => $limit,
                'conditions' => $conditions,
                'contain' => array(
                    'User' => array(
                        'fields' => array(
                            'id',
                            'username',
                            'image'
                        )
                    ),
                    'Sentence' => array(
                        'User' => array('username'),
                        'fields' => array('id', 'text', 'lang')
                    )
                )
            )
        );
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
        $result = $this->find(
            "first",
            array(
                'fields' => array('SentenceComment.user_id'),
                'conditions' => array('SentenceComment.id' => $commentId),
            )
        );

        return $result['SentenceComment']['user_id'];
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

    /**
     *
     */
    function getQueryConditionWithExcludedUsers($conditions = null)
    {
        $botsIds = Configure::read('Bots.userIds');

        if (!isset($conditions)) {
            $conditions = array();
        }
        if (!empty($botsIds)) {
            if (count($botsIds) > 1) {
                $conditions["SentenceComment.user_id NOT"] = $botsIds;
            } else {
                $conditions["SentenceComment.user_id !="] = $botsIds[0];
            }

        }

        return $conditions;
    }
}
