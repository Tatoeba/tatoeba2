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
        'Languages'
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
     * @return void
     */
    public function index()
    {
        $this->helpers[] = 'Sentences';
        $this->helpers[] = 'Members';
        $this->helpers[] = 'Logs';

        $userId = $this->Auth->user('id');
        $isLogged = !empty($userId);

        // Random sentence part
        $hideRandomSentence = CurrentUser::getSetting('hide_random_sentence');
        
        $this->set('hideRandomSentence', $hideRandomSentence);

        if (!$hideRandomSentence) {
            $this->_random_sentence();
        }

        // Stats
        $stats = ClassRegistry::init('Language')->getSentencesStatistics(5);
        $numSentences = ClassRegistry::init('Sentence')->getTotalNumberOfSentences();

        $this->set('stats', $stats);
        $this->set('numSentences', $numSentences);

        if ($isLogged) {
            $this->_homepageForMembers();
        } else {
            $this->_homepageForGuests();
        }
    }


    private function _homepageForGuests() {
        $contribToday = ClassRegistry::init('Contribution')->getTodayContributions();

        $this->set('contribToday', $contribToday);
        $this->render('index_for_guests');
    }

    private function _homepageForMembers() {
        $this->helpers[] = 'Wall';
        $this->helpers[] = 'Messages';

        /*latest comments part */
        $this->loadModel('SentenceComment');
        $latestComments = $this->SentenceComment->getLatestComments(5);
        $commentsPermissions = $this->Permissions->getCommentsOptions(
            $latestComments
        );

        $this->set('sentenceComments', $latestComments);
        $this->set('commentsPermissions', $commentsPermissions);


        /*latest messages part */
        $this->loadModel('Wall');
        $latestMessages = $this->Wall->getLastMessages(5);

        $this->set('latestMessages', $latestMessages);
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
        $action = $this->params['action'];

        switch ($action) {
            case "tatoeba-team-and-credits":
                $action = "tatoeba_team_and_credits";
                break;

            case "download-tatoeba-example-sentences":
                $action = "downloads";
                break;

            case "terms-of-use":
                $action = "terms_of_use";
                break;

            default:
                return;
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
        $this->redirect(
            array(
                "controller" => "pages",
                "action" => "index"
            ),
            301
        );
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
        if (is_null($randomId)) {
            $this->set('searchProblem', true);
        } else {
            $randomSentence = $this->Sentence->getSentenceWithId($randomId);
            $this->set('random', $randomSentence);
        }

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
    public function tatoeba_team_and_credits()
    {
        $this->redirect(
            array(
                "controller" => "pages",
                "action" => "home"
            ),
            301
        );
    }


    /**
     *
     *
     */
    public function download_tatoeba_example_sentences()
    {
        $this->redirect(
            array(
                "controller" => "pages",
                "action" => "downloads"
            ),
            301
        );
    }

    /**
     *
     *
     */
    public function downloads()
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
    public function faq()
    {
        $this->redirect(
            "http://wiki.tatoeba.org/articles/show/faq",
            301
        );
    }
    
    
    /**
     *
     *
     */
    public function donate()
    {
    }
}
?>
