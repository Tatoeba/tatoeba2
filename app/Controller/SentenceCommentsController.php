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
 * @link     http://tatoeba.org
 */

App::uses('AppController', 'Controller');
App::uses('NotificationListener', 'Lib/Event');

/**
 * Controller for sentence comments.
 *
 * @category SentenceComments
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class SentenceCommentsController extends AppController
{

    public $persistentModel = true;
    public $name = 'SentenceComments';
    public $uses = array(
        "SentenceComment",
        "Sentence",
        "User",
        "PrivateMessage"
    );
    public $helpers = array(
        'Comments',
        'Sentences',
        'Languages',
        'Navigation',
        'Html',
        'Form',
        'CommonModules',
        'Pagination',
        'Tags'
    );
    public $components = array ('Flash', 'LanguageDetection', 'Permissions', 'Mailer');

    public $paginate = array(
        'SentenceComment' => array(
            'fields' => array(
                'id',
                'user_id',
                'text',
                'created',
                'modified',
                'sentence_id',
                'hidden'
            ),
            'contain' => array(
                'User' => array(
                    'fields' => array(
                        'id',
                        'username',
                        'image',
                    )
                ),
                'Sentence' => array(
                    'User' => array('username'),
                    'fields' => array('id', 'text', 'lang', 'correctness')
                )
            ),
            'limit' => 50,
            'order' => 'created DESC',
        )
    );

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();

        // setting actions that are available to everyone, even guests
        $this->Auth->allowedActions = array(
            'index',
            'show',
            'of_user',
            'on_sentences_of_user'
        );

        $eventManager = $this->SentenceComment->getEventManager();
        $eventManager->attach(new NotificationListener());
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

        $conditions = $this->SentenceComment->getQueryConditionWithExcludedUsers();
        if ($langFilter != 'und') {
            $conditions["Sentence.lang"] = $langFilter;
        }

        $this->paginate['SentenceComment']['conditions'] = $conditions;

        $latestComments = $this->paginate();

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

        $sentenceId = Sanitize::paranoid($sentenceId);
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
        $userId = $this->Auth->user('id');
        $userEmail = $this->Auth->user('email');

        $sentenceId = $this->request->data['SentenceComment']['sentence_id'];
        $commentText = $this->request->data['SentenceComment']['text'];

        if (empty($sentenceId)) {
            $this->redirect('/');
        }

        // to avoid spammer and repost
        $lastCom = $this->Cookie->read('hash_last_com');
        $thisCom = md5($commentText . $sentenceId);
        if ($lastCom == $thisCom) {
            $this->redirect('/');
        }

        $this->Cookie->write(
            'hash_last_com',
            $thisCom,
            false,
            "+1 month"
        );

        $allowedFields = array('sentence_id', 'text');
        $comment = $this->filterKeys($this->request->data['SentenceComment'], $allowedFields);
        $comment['user_id'] = $userId;

        if ($this->SentenceComment->save($comment)) {
            $this->Flash->set(__('Your comment has been saved.'));
        } else {
            $firstValidationErrorMessage = reset($this->SentenceComment->validationErrors)[0];
            $this->Flash->set($firstValidationErrorMessage);
        }
        $this->redirect(array(
            'controller' => 'sentences',
            'action' => 'show',
            $sentenceId
        ));
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
        $commentId = Sanitize::paranoid($commentId);
        $this->SentenceComment->id = $commentId;

        //get permissions
        if (empty($this->request->data)) {
            $sentenceComment = $this->SentenceComment->find('first', array(
                'conditions' => array('SentenceComment.id' => $commentId),
                'contain' => array(
                    'Sentence' => array('Transcription'),
                    'User',
                )
            ));
            $sentenceId = $sentenceComment['SentenceComment']['sentence_id'];
            $authorId = $sentenceComment['SentenceComment']['user_id'];
        } else {
            $sentenceComment = $this->request->data['SentenceComment'];
            $sentenceId = $this->request->data['SentenceComment']['sentence_id'];
            $authorId = $this->SentenceComment->getOwnerIdOfComment(
                $this->request->data['SentenceComment']['id']
            );
        }

        //check permissions now
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
            $this->redirect(
                array(
                    'controller' => "sentences",
                    'action'=> 'show',
                    $sentenceId
                )
            );
        } else {
            //user has permissions so either display form or save comment
            if (empty($this->request->data)) {
                $this->helpers[] = "Messages";
                $this->request->data = $sentenceComment;
                $this->set('sentenceComment', $sentenceComment);
            } else {
                $commentId = $this->request->data['SentenceComment']['id'];
                //save comment
                $commentUpdate = array(
                    'id'   => $commentId,
                    'text' => $this->request->data['SentenceComment']['text'],
                );
                if ($this->SentenceComment->save($commentUpdate)) {
                    $this->Flash->set(
                        __("Changes to your comment have been saved.")
                    );
                    $this->redirect(
                        array(
                            'controller' => "sentences",
                            'action'=> 'show',
                            $sentenceId,
                            "#" => "comment-".$commentId
                        )
                    );
                } else {
                    $firstValidationErrorMessage = reset($this->SentenceComment->validationErrors)[0];
                    $this->Flash->set($firstValidationErrorMessage);
                    $this->redirect(
                        array(
                            'controller' => "sentence_comments",
                            'action'=> 'edit',
                            $commentId,
                        )
                    );
                }
            }
        }

    }

    /**
     * delete requested comment
     * NOTE: delete is a php5 keyword
     *
     * @param int $commentId id of the comment
     *
     * @return void
     */

    public function delete_comment($commentId)
    {
        $commentId = Sanitize::paranoid($commentId);

        $commentOwnerId = $this->SentenceComment->getOwnerIdOfComment($commentId);

        //we check a second time even if it has been checked while displaying
        // or not the delete icon, but one can try to directly call delete_comment
        // so we need to recheck
        $commentPermissions = $this->Permissions->getCommentOptions(
            $commentOwnerId
        );
        if ($commentPermissions['canDelete']) {
            $this->SentenceComment->delete($commentId);
        }
        // redirect to previous page
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
        $userId = $this->User->getIdfromUsername($userName);
        $backLink = $this->referer(array('action'=>'index'), true);
        // if there's no such user no need to do more computation
        if (empty($userId)) {

            $this->set('backLink', $backLink);
            $this->set('userName', $userName);
            $this->set("userExists", false);
            $this->set("noComment", true);
            return;
        }

        $this->helpers[] = 'Messages';
        $this->helpers[] = 'Members';

        // in the same idea, we do not need to do extra request if the user
        // has no comment
        $numberOfComments = $this->SentenceComment->numberOfCommentsOwnedBy($userId);
        if ($numberOfComments === 0) {

            $this->set('backLink', $backLink);
            $this->set('userName', $userName);
            $this->set("userExists", true);
            $this->set("noComment", true);
            return;
        }

        $this->paginate['SentenceComment']['conditions'] = array(
            'SentenceComment.user_id' => $userId
        );

        $userComments = $this->paginate(
            'SentenceComment'
        );

        $commentsPermissions = $this->Permissions->getCommentsOptions($userComments);

        $this->set('userComments', $userComments);
        $this->set('userName', $userName);
        $this->set('commentsPermissions', $commentsPermissions);
        $this->set("noComment", false);
        $this->set("userExists", true);
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
        $userId = $this->User->getIdfromUsername($userName);

        $conditions = array(
            'Sentence.user_id' => $userId
        );
        $conditions = $this->SentenceComment->getQueryConditionWithExcludedUsers(
            $conditions
        );
        $this->paginate['SentenceComment']['conditions'] = $conditions;
        $userComments = $this->paginate('SentenceComment');

        $userId = $this->User->getIdfromUsername($userName);
        $backLink = $this->referer(array('action'=>'index'), true);
        // if there's no such user no need to do more computation
        if (empty($userId)) {

            $this->set('backLink', $backLink);
            $this->set('userName', $userName);
            $this->set("userExists", false);
            $this->set("noComment", false);
            return;
        }

        // in the same idea, we do not need to do extra request if the user
        // has no comment
        $numberOfComments = $this->SentenceComment->numberOfCommentsOnSentencesOf(
            $userId
        );
        if ($numberOfComments === 0) {
            $this->set('backLink', $backLink);
            $this->set('userName', $userName);
            $this->set("userExists", true);
            $this->set("noComment", true);
            return;
        }

        $this->helpers[] = 'Messages';
        $this->helpers[] = 'Members';

        $commentsPermissions = $this->Permissions->getCommentsOptions($userComments);

        $this->set('userExists', true);
        $this->set('noComment', false);
        $this->set('userComments', $userComments);
        $this->set('userName', $userName);
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
        if (CurrentUser::isAdmin()) {
            $messageId = Sanitize::paranoid($messageId);

            $this->SentenceComment->id = $messageId;
            $this->SentenceComment->saveField('hidden', true);

            // redirect to previous page
            $this->redirect($this->referer());
        }

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
        if (CurrentUser::isAdmin()) {
            $messageId = Sanitize::paranoid($messageId);

            $this->SentenceComment->id = $messageId;
            $this->SentenceComment->saveField('hidden', false);

            // redirect to previous page
            $this->redirect($this->referer());
        }

    }
}
