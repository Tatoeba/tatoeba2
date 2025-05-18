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
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use App\Model\CurrentUser;
use Cake\Event\Event;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\I18n\I18n;
use App\Lib\LanguagesLib;

/**
 * Controller for static content
 * used to render the files in views/pages/*
 *
 * @category Contributions
 * @package  Controllers
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
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

    public $components = array('Permissions');


    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        if ($response = $this->_redirect_for_old_url()) {
            return $response;
        }
        return parent::beforeFilter($event);
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
        $this->loadModel('Languages');
        $numSentences = $this->Languages->getTotalSentencesNumber();
        $this->set('numSentences', $numSentences);

        $this->loadModel('Contributions');
        $contribToday = $this->Contributions->getTodayContributions();
        $this->set('contribToday', $contribToday);

        $numberOfLanguages = count(LanguagesLib::languagesInTatoeba());
        $this->set('numberOfLanguages', $numberOfLanguages);

        if ($isLogged) {
            $this->_homepageForMembers();
        } else {
            $this->_homepageForGuests();
        }
    }


    private function _homepageForGuests() {
        $this->render('index_for_guests');
    }

    private function _homepageForMembers() {
        $this->helpers[] = 'Wall';
        $this->helpers[] = 'Messages';

        // latest comments
        $this->loadModel('SentenceComments');
        $latestComments = $this->SentenceComments->getLatestComments(5);
        $commentsPermissions = $this->Permissions->getCommentsOptions(
            $latestComments
        );

        $this->set('sentenceComments', $latestComments);
        $this->set('commentsPermissions', $commentsPermissions);


        // latest messages
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
        $action = $this->request->getParam('action');

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

        return $this->redirect(
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
        $this->loadModel('Sentences');
        $lang = $this->request->getSession()->read('random_lang_selected');
        $randomId = $this->Sentences->getRandomId($lang);
        if (is_null($randomId)) {
            $this->set('searchProblem', true);
        } else {
            $randomSentence = $this->Sentences->getSentenceWith(
                $randomId,
                ['translations' => true]
            );
            $this->set('random', $randomSentence);
        }

        if (isset($randomSentence->script)) {
            $this->set('sentenceScript', $randomSentence->script);
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
        $lang = I18n::getLocale();
        $translated = true;
        $dir = new Folder(LOCALE . $lang);
        $file = new File($dir->pwd() . DS . 'terms-of-use.html');

        if (!$file->exists()) {
            $translated = false;
            $dir = new Folder(LOCALE . 'fr');
            $file = new File($dir->pwd() . DS . 'terms-of-use.html');
        }

        $content = $file->read();
        
        $this->set('content', $content);
        $this->set('translated', $translated);
    }


    /**
     *
     *
     */
    public function faq()
    {
        $proto = $this->getRequest()->getUri()->getScheme();
        $this->loadModel('WikiArticles');
        $this->redirect(
            $proto.':'.$this->WikiArticles->getWikiLink('faq'),
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
