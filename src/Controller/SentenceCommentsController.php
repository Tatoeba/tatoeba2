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
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use App\Event\NotificationListener;
use Cake\Event\Event;
use Cake\Core\Configure;
use App\Model\CurrentUser;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Controller for sentence comments.
 *
 * @category SentenceComments
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class SentenceCommentsController extends AppController
{
    public $name = 'SentenceComments';
    public $uses = array(
        "SentenceComment",
        "User",
    );
    public $helpers = array(
        'Comments',
        'CommonModules',
        'Pagination',
    );
    public $components = array ('Flash', 'Permissions');

    public $paginate = [
        'contain' => [
            'Users' => [
                'fields' => [
                    'id',
                    'username',
                    'image',
                ]
            ],
            'Sentences' => [
                'Users' => [
                    'fields' => ['id', 'username']
                ]
            ]
        ],
        'limit' => 50,
        'order' => ['SentenceComments.id' => 'DESC'],
    ];

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        // setting actions that are available to everyone, even guests
        $this->Auth->allowedActions = array(
            'index',
            'show',
            'of_user',
            'on_sentences_of_user'
        );

        $eventManager = $this->SentenceComments->getEventManager();
        $eventManager->attach(new NotificationListener());

        // disable Form Tampering Protection for actions where it's no big deal
        // (it was only protecting against changing the sentence_id)
        $this->Security->setConfig('unlockedActions', [
            'save',
            'edit',
        ]);

        return parent::beforeFilter($event);
    }

    /**
     * Display latest comments. Can be filtered by the language of sentences.
     *
     * @param string $langFilter To filter comments by the language of sentences
     *
     * @return void
     */
    public function index($langFilter = 'und')
    {
        $this->helpers[] = 'Messages';
        $this->helpers[] = 'Members';

        $options = [
            'maxResults' => $this::PAGINATION_DEFAULT_TOTAL_LIMIT,
        ];
        $botsIds = Configure::read('Bots.userIds');
        if (!empty($botsIds)) {
            $options['conditions']['SentenceComments.user_id NOT IN'] = $botsIds;
        }
        if ($langFilter != 'und') {
            $options['contain'] = 'Sentences';
            $options['conditions']['Sentences.lang'] = $langFilter;
        }

        $finder = ['latest' => $options];
        $latestComments = $this->paginateOrRedirect($this->SentenceComments, compact('finder'));

        $commentsPermissions = $this->Permissions->getCommentsOptions($latestComments);

        $this->set('sentenceComments', $latestComments);
        $this->set('commentsPermissions', $commentsPermissions);
        $this->set('langFilter', $langFilter);

    }


    /**
     * Display comments for given sentence.
     *
     * @param int $sentenceId Id of sentence.
     *
     * @return void
     */
    public function show($sentenceId)
    {
        // redirect to sentences/show
        // we don't remove the method to be compatible with previous google indexing
        $this->redirect(
            array(
                "controller" => "sentences",
                "action" => "show",
                $sentenceId
            ),
            301
        );
    }

    /**
     * Save new comment.
     *
     * @return void
     */
    public function save()
    {
        $data = $this->request->getData();
        $commentText = $data['text'];
        $sentenceId = $data['sentence_id'];
        // to avoid spammer and repost
        $lastCom = $this->Cookie->read('hash_last_com');
        $thisCom = md5($commentText . $sentenceId);
        if ($lastCom == $thisCom) {
            return $this->redirect('/');
        }

        $this->Cookie->write(
            'hash_last_com',
            $thisCom,
            false,
            '+1 month'
        );

        $comment = $this->SentenceComments->newEntity([
            'sentence_id' => $sentenceId,
            'text' => $commentText,
            'user_id' => $this->Auth->user('id')
        ]);
        $savedComment = $this->SentenceComments->save($comment);
        if ($savedComment) {
            $this->Flash->set(__('Your comment has been saved.'));
        } else if ($comment->getErrors()) {
            foreach($comment->getErrors() as $error) {
                $firstValidationErrorMessage = reset($error);
                $this->Flash->set($firstValidationErrorMessage);
            }
        }
        return $this->redirect([
            'controller' => 'sentences',
            'action' => 'show',
            $sentenceId
        ]);
    }


    /**
     * Edit comment
     *
     * @param string $commentId The comment id.
     *
     * @return void
     */
    public function edit($commentId)
    {
        try {
            $comment = $this->SentenceComments->get($commentId, [
                'contain' => [
                    'Sentences' => ['Transcriptions'],
                    'Users',
                ]
            ]);
        } catch (RecordNotFoundException $e) {
            return $this->redirect($this->referer());
        }
        
        $sentenceId = $comment->sentence_id;
        $authorId = $comment->user_id;

        $canEdit = $authorId === CurrentUser::get('id') || CurrentUser::isAdmin();
        if (!$canEdit) {
            $no_permission = __(
                "You do not have permission to edit this comment. ",
                true
            );
            $wrongly = format(__(
                "If you have received this message in error, ".
                "please contact administrators at {email}.", true
            ), array('email' => 'team@tatoeba.org'));
            $this->Flash->set($no_permission.$wrongly);
            return $this->redirect(
                array(
                    'controller' => "sentences",
                    'action'=> 'show',
                    $sentenceId
                )
            );
        } 

        if ($this->request->is('put')) {
            $data = $this->request->getData();
            $this->SentenceComments->patchEntity($comment, [
                'text' => $data['text']
            ]);
            $savedComment = $this->SentenceComments->save($comment);
            if ($savedComment) {
                $this->Flash->set(
                    __("Changes to your comment have been saved.")
                );
                return $this->redirect([
                    'controller' => 'sentences',
                    'action'=> 'show',
                    $sentenceId,
                    '#' => 'comment-'.$commentId
                ]);
            } else if ($comment->getErrors()) {
                foreach($comment->getErrors() as $error) {
                    $firstValidationErrorMessage = reset($error);
                    $this->Flash->set($firstValidationErrorMessage);
                }
                return $this->redirect(['action' => 'edit', $commentId]);
            }
        } else {
            $this->set('sentenceComment', $comment);
        }        
    }

    /**
     * Delete requested comment.
     *
     * @param int $commentId id of the comment
     *
     * @return void
     */

    public function delete_comment($commentId)
    {
        $this->SentenceComments->deleteComment($commentId);
        $this->redirect($this->referer());
    }

    /**
     * show all the comments of a specified user
     *
     * @param int $userName name of the user we want comments of
     *
     * @return void
     */

    public function of_user($userName)
    {
        $this->set('userName', $userName);
        $this->loadModel('Users');
        $userId = $this->Users->getIdFromUsername($userName);
        $this->set('userExists', !empty($userId));
        // if there's no such user no need to do more computation
        if (empty($userId)) {
            return;
        }

        $this->paginate['conditions'] = array(
            'SentenceComments.user_id' => $userId
        );

        $userComments = $this->paginate();

        $commentsPermissions = $this->Permissions->getCommentsOptions($userComments);

        $this->set('userComments', $userComments);
        $this->set('commentsPermissions', $commentsPermissions);
    }


    /**
     * show all the comments on sentences of a specified user
     *
     * @param string $userName Name of the user we want comments on his sentences
     *
     * @return void
     */

    public function on_sentences_of_user($userName)
    {
        $this->set('userName', $userName);
        $this->loadModel('Users');
        $userId = $this->Users->getIdFromUsername($userName);
        $this->set('userExists', !empty($userId));

        if (empty($userId)) {
            return;
        }

        $botsIds = Configure::read('Bots.userIds');
        $conditions = ['Sentences.user_id' => $userId];
        if (!empty($botsIds)) {
            $conditions['SentenceComments.user_id NOT IN'] = $botsIds;
        }
        $this->paginate['conditions'] = $conditions;
        $userComments = $this->paginate();

        $commentsPermissions = $this->Permissions->getCommentsOptions($userComments);

        $this->set('userComments', $userComments);
        $this->set('commentsPermissions', $commentsPermissions);
    }


    /**
     * Hides a given comment. The message is still going to be there
     * but only visible to the admins and the author of the message.
     *
     * @param int $messageId Id of the comment to hide
     *
     * @return void
     */
    public function hide_message($messageId)
    {
        $this->_setHiding($messageId, true);
    }


    /**
     * Display back a given comment that was hidden.
     *
     * @param int $messageId Id of the message to display again
     *
     * @return void
     */
    public function unhide_message($messageId)
    {
        $this->_setHiding($messageId, false);
    }

    private function _setHiding($messageId, $hiding)
    {
        if (CurrentUser::isAdmin()) {
            $comment = $this->SentenceComments->get($messageId);
            $comment->hidden = $hiding;
            $this->SentenceComments->save($comment);

            // redirect to previous page
            $this->redirect($this->referer());
        }

    }
}
