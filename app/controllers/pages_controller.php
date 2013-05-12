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
        $this->_redirect_for_old_url();
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
        if ($this->Auth->user()) {
            $this->redirect(array('action' => 'home'));
        }
        
        /*Some numbers part*/
        $this->loadModel('Contribution'); 
        $nbrContributions = $this->Contribution->getTodayContributions(); 
       
        
        $this->loadModel('User');
        $nbrActiveMembers = $this->User->getNumberOfActiveMembers();
       
        $this->set('nbrActiveMembers', $nbrActiveMembers); 
        $this->set('nbrContributions', $nbrContributions);
        
        // Random sentence part
        $this->_random_sentence();
    }
 
    /**
     * Hackish function create because I haven't succed to create
     * rewriterule with mod_rewrite, in order to redirect for old
     * /pages/xxxx to new url 
     *
     * @return void
     */
    
    private function _redirect_for_old_url()
    {
    
        // TODO it's an hack
        $urlArray = explode("/", $this->params['url']['url']);
        if (empty($urlArray[1]) || $urlArray[1] != "pages") {
            return;
        }
        
        $action = $urlArray[2];

        switch ($action) {
            case "tatoeba-team-and-credits":
                $action = "tatoeba_team_and_credits";
                break;

            case "how-to-contribute":
                $action = "how_to_contribute";
                break;

            case "download-tatoeba-example-sentences":
                $action = "download_tatoeba_example_sentences";
                break;

            case "terms-of-use":
                $action = "terms_of_use";
                break;

            case "whats-new":
                $action = "whats_new";
                break;
        }
         
        $this->redirect(
            array(
                "controller" => "pages",
                "action" => $action
            ),
            301
        );

    }
    /**
     * Display the "home" page which is the default page for
     * logged user
     *
     * @return void
     */ 
    public function home()
    {
        $this->helpers[] = 'Wall';
        $this->helpers[] = 'Sentences';
        
        $userId = $this->Auth->user('id');
        $groupId = $this->Auth->user('group_id');
        $isLogged = !empty($userId);
        /*latest comments part */
        $this->loadModel('SentenceComment');
        $latestComments = $this->SentenceComment->getLatestComments(5);


        $commentsPermissions = $this->Permissions->getCommentsOptions(
            $latestComments,
            $userId,
            $groupId
        );


        $this->set('sentenceComments', $latestComments);
        $this->set('commentsPermissions', $commentsPermissions);  
        
        
        /*latest messages part */
        $this->loadModel('Wall');
        $latestMessages = $this->Wall->getLastMessages(5);

        $this->set('isLogged', $isLogged); 
        $this->set('latestMessages', $latestMessages); 
        
        // Random sentence part
        $this->_random_sentence();
    }
    
    
    /**
     * Random sentence on homepage.
     *
     * NOTE: It's pretty much a copy-paste from SentencesController::random().
     * Not sure what's the good way to call an action from another controller...
     *
     * @TODO then move it to "commentSentence" component when a method is share
     * by several controller it's the way to do
     *
     * @return void
     */
    private function _random_sentence() {
        $this->loadModel('Sentence');
        $lang = $this->Session->read('random_lang_selected');
        $randomId = $this->Sentence->getRandomId($lang);
        $randomSentence = $this->Sentence->getSentenceWithId($randomId);
        $alltranslations = $this->Sentence->getTranslationsOf($randomId);
        $translations = $alltranslations['Translation'];
        $indirectTranslations = $alltranslations['IndirectTranslation'];
        
        $this->set('random', $randomSentence);
        $this->set('translations', $translations);
        $this->set('indirectTranslations', $indirectTranslations);
        
        if (isset($randomSentence['Sentence']['script'])) {
            $this->set('sentenceScript', $randomSentence['Sentence']['script']);
        }
    }
    
    
    /**
     * Contribute page. Non registered users are redirected to "How to contribute".
     *
     * @return void
     */
    public function contribute()
    {
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
        $this->helpers[] = 'Members';

        $this->loadModel('User');
        $peoples = $this->User->getMembersForTeamAndCredits();

        $this->set("padawans", $peoples['Padawan']);
        $this->set("cores", $peoples['Core']);
        $this->set("exmembers", $peoples['Ex']);
        $this->set("translators", $peoples['Translator']);
        $this->set("specialThanks", $peoples['Special']);
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
    
    
    /**
     *
     *
     */
    public function faq()
    {
    }
    
    
    /**
     *
     *
     */
    public function maintenance()
    {
        $this->layout = null;
    }
}
?>
