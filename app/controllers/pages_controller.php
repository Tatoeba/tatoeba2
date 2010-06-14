<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 Allan SIMON <allan.simon@supinfo.com>
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
 * Controller for static content
 * used to render the files in views/pages/*
 *
 * @category Contributions
 * @package  Controllers
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

class PagesController extends AppController
{
    /**
     * Controller name
     *
     * @var string
     * @access public
     */
    public $name = 'Pages';
    /**
     * Default helper
     *
     * @var array
     * @access public
     */
    public $helpers = array(
        'Html',
        'Languages',
        'AttentionPlease',
        'Wall'
    );
    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array();

    public $components = array('Permissions');

    
    /**
     * Before filter.
     * 
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        
        // setting actions that are available to everyone, even guests
        $this->Auth->allowedActions = array("*");
    }

    /**
     * use to retrive data needed to display all the index module
     * data are sent to pages/index.ctp
     *
     * @return void
     */

    public function index()
    {
        /*Some numbers part*/
        $Contribution = ClassRegistry::init('Contribution'); 
        $nbrContributions = $Contribution->getTodayContributions(); 
       
        
        $User = ClassRegistry::init('User');
        $nbrActiveMembers = $User->getNumberOfActiveMembers();
       
        $this->set('nbrActiveMembers', $nbrActiveMembers); 
        $this->set('nbrContributions', $nbrContributions);
    }
 
     /**
     * use to retrive data needed to display all the home module
     * data are sent to pages/home.ctp
     *
     * @return void
     */   
 
    public function home()
    {
        $userId = $this->Auth->user('id');
        $groupId = $this->Auth->user('group_id');
        $isLogged = !empty($userId);
        /*latest comments part */
        $SentenceComment = ClassRegistry::init('SentenceComment');
        $latestComments = $SentenceComment->getLatestComments(5);


        $commentsPermissions = $this->Permissions->getCommentsOptions(
            $latestComments,
            $userId,
            $groupId
        );


        $this->set('sentenceComments', $latestComments);
        $this->set('commentsPermissions', $commentsPermissions);  
       
        /*Uknown language's sentences part */
        if ($isLogged) {  
            $Sentence = ClassRegistry::init('Sentence');
            $nbrUnknownSentences = $Sentence->numberOfUnknownLanguageForUser(
                $userId
            );
            $this->set('nbrUnknownSentences', $nbrUnknownSentences);
        }
        
        /*latest messages part */
        $Wall = ClassRegistry::init('Wall');
        $latestMessages = $Wall->getLastMessages(5);

        $this->set('isLogged', $isLogged); 
        $this->set('latestMessages', $latestMessages); 
    }
    
    
    /**
     * Contribute page. Non registered users are redirected to "How to contribute".
     *
     * @return void
     */
    public function contribute()
    {
        if (!$this->Auth->user('id')) {
            $this->redirect(
                array(
                    'controller' => 'pages',
                    'action' => 'how_to_contribute'
                )
            );
        }
    }

    /**
     *
     *
     */
    public function about()
    {
    }

    /**
     *
     *
     */
   
    public function search()
    {
        //TODO should be moved in "search" controller
    }

    /**
     *
     *
     */

    public function contact()
    {
    }

    /**
     *
     *
     */

    public function help()
    {
    }
    
    /**
     *
     *
     */
    public function how_to_contribute()
    {
    }

    /**
     *
     *
     */
    public function tatoeba_team_and_credits()
    {
    }

    /**
     *
     *
     */
    public function download_tatoeba_example_sentences()
    {
    }

    /**
     *
     *
     */
    public function terms_of_use()
    {

    }

   
    /**
     *
     *
     */
    public function whats_new()
    {
    }
}
?>
