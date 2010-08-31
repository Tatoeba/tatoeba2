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
        "User"
    );    
    public $helpers = array(
        'Comments',
        'Sentences',
        'Languages',
        'Navigation',
        'Html',
        'CommonModules',
        'Pagination'
    );
    public $components = array ('GoogleLanguageApi', 'Permissions', 'Mailer');
    public $paginate = array(
        'limit' => 100,
        "order" => "SentenceComment.created DESC",
        'fields' => array(
            'id',
            'user_id',
            'text',
            'created',
            'sentence_id',
        ),
        "contain" => array(
            'User' => array(
                'fields' => array(
                    'id',
                    'username',
                    'image',
                )
            ),
            'Sentence' => array(
                'fields' => "text"
            )
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
            "*"
        );
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
        $permissions = array();
        
        if ($langFilter != 'und') {
            $this->paginate['conditions'] = array(
                "Sentence.lang" => $langFilter
            );
        }
        
        $latestComments = $this->paginate();

        $permissions = $this->Permissions->getCommentsOptions(
            $latestComments,
            $this->Auth->user('id'),
            $this->Auth->user('group_id')
        );

        $this->set('sentenceComments', $latestComments);
        $this->set('commentsPermissions', $permissions);
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
        $userName = $this->Auth->user('username');
        $userEmail = $this->Auth->user('email');


        if (!empty($this->data['SentenceComment']['text'])) {
            
            $this->data['SentenceComment']['user_id'] = $userId;
            
            if ($this->SentenceComment->save($this->data)) {
                $sentenceId = $this->data['SentenceComment']['sentence_id'];
                $participants = $this->SentenceComment->getEmailsFromComments(
                    $sentenceId
                );
                $sentenceOwner = $this->Sentence->getEmailFromSentence(
                    $sentenceId
                );
                
                if ($sentenceOwner != null 
                    && !in_array($sentenceOwner, $participants)
                ) {
                    $participants[] = $sentenceOwner;
                }
                
                // send message to the other participants of the thread
                foreach ($participants as $participant) {
                    if ($participant != $userEmail) {
                        // prepare message
                        $subject = 'Tatoeba - Comment on sentence : ' 
                            . $this->data['SentenceComment']['sentence_text'];
                        if ($participant == $sentenceOwner) {
                            $msgStart = sprintf(
                                '%s has posted a comment on one of your sentences.', 
                                $userName
                            );
                        } else {
                            $msgStart = sprintf(
                                '%s has posted a comment on a sentence where you also
                                posted a comment.',
                                $userName
                            );
                        }
                        $message = $msgStart
                            . "\n"
                            . 'http://'.$_SERVER['HTTP_HOST'] 
                            . '/sentence_comments/show/'
                            . $this->data['SentenceComment']['sentence_id']
                            .'#comments'
                            . "\n\n- - - - - - - - - - - - - - - - -\n\n" 
                            . $this->data['SentenceComment']['text']
                            . "\n\n- - - - - - - - - - - - - - - - -\n\n";
                            
                        // send notification
                        $this->Mailer->to = $participant;
                        $this->Mailer->toName = '';
                        $this->Mailer->subject = $subject;
                        $this->Mailer->message = $message;
                        $this->Mailer->send();
                    }
                }
                
                $this->flash(
                    __('Your comment has been saved.', true), 
                    '/sentence_comments/show/'
                    .$this->data['SentenceComment']['sentence_id']
                );
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
            null,
            $commentOwnerId,
            $this->Auth->user('id'),
            $this->Auth->user('group_id')
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

        $this->paginate = array(
            'SentenceComment' => array(
                'fields' => array(
                    'id',
                    'user_id',
                    'text',
                    'created',
                    'sentence_id',
                ),
                'conditions' => array('SentenceComment.user_id' => $userId),
                'contain' => array(
                    'User' => array(
                        'fields' => array(
                            'id',
                            'username',
                            'image',
                        )
                    ),
                    'Sentence' => array(
                        'fields' => "text"
                    )
                ),
                'limit' => 50,
                'order' => 'created DESC',
            )
        ); 
        
        $userComments = $this->paginate(
            'SentenceComment'
        );


        $permissions = $this->Permissions->getCommentsOptions(
            $userComments,
            $this->Auth->user('id'),
            $this->Auth->user('group_id')
        );

        $this->set('userComments', $userComments);
        $this->set('userName', $userName);
        $this->set('commentsPermissions', $permissions);
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
        $this->paginate = array(
            'SentenceComment' => array(
                'fields' => array(
                    'id',
                    'user_id',
                    'text',
                    'created',
                    'sentence_id',
                ),
                'conditions' => array(
                    'Sentence.user_id' => $userId
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
                        'fields' => "text"
                    )
                ),
                'limit' => 50,
                'order' => 'created DESC',
            )
        ); 
        
        $userComments = $this->paginate(
            'SentenceComment'
        );

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


        $permissions = $this->Permissions->getCommentsOptions(
            $userComments,
            $this->Auth->user('id'),
            $this->Auth->user('group_id')
        );
        $this->set('userExists', true);
        $this->set('noComment', false);
        $this->set('userComments', $userComments);
        $this->set('userName', $userName);
        $this->set('commentsPermissions', $permissions);
    }


}
?>
