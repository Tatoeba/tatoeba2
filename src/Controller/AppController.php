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

use App\Lib\LanguagesLib;
use App\Model\CurrentUser;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Cake\I18n\I18n;
use Locale;

/**
 * Controller for contributions.
 *
 * @category App
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */



class AppController extends Controller
{
    use \AuthActions\Lib\AuthActionsTrait;

    const PAGINATION_DEFAULT_TOTAL_LIMIT = 1000;

    public $components = array(
        'Auth' => array(
		'authenticate' => array(
			'Form' => array(
                            'passwordHasher' => array('className' => 'Versioned'),
                        ),
		)
	),
        'Flash',
        'Permissions',
        'RememberMe',
        'Security',
    );

    public $helpers = array(
        'AssetCompress.AssetCompress',
        'Sentences',
        'Comments',
        'Date',
        'Html',
        'Form',
        'Logs',
        'Pages',
        'Search',
        'Security',
        'Images'
    );

    private function blackhole($type) {
      var_dump("Blackholed: $type");
    }

    public function initialize()
    {
        $this->loadComponent('Cookie');
        $this->loadComponent('Csrf');
    }

    /**
     *
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        // only prevent CSRF for logins and registration in the users controller
        $this->Security->csrfCheck = false;
        $this->Security->blackHoleCallback = 'blackhole';

        $this->Cookie->domain = TATOEBA_DOMAIN;
        $this->Cookie->configKey('CakeCookie', 'encryption', false);
        $this->Auth->allow('display');
        $this->Auth->setConfig([
            // This line will call views/elements/session_expired.ctp.
            // When one tries to do an AJAX action after the session is expired,
            // the action will return the content of this file instead of
            // the whole page.
            'ajaxLogin' => 'session_expired',
            'authError' => false,
            'authorize' => ['Controller'],
            'loginAction' => [ 'controller' => 'users', 'action' => 'login' ],
            'logoutRedirect' => [ 'controller' => 'users', 'action' => 'login' ],
            // namespace declaration of AuthUtilsComponent
            'AuthActions.AuthUtils',
        ]);
        $this->initAuthActions();
        $this->RememberMe->check();

        // So that we can access the current users info from models.
        // Important: needs to be done after RememberMe->check().
        $user = $this->Auth->user();
        if ($user) {
            // Keep the info up to date
            $this->loadModel('Users');
            $user = $this->Users->getInformationOfCurrentUser($user['id'])->toArray();
        }
        CurrentUser::store($user);

        // Restore named parameters removed in CakePHP 3
        $this->request = Router::parseNamedParams($this->request);

        // Parse named parameters (e.g. /page:123)
        // as if they were query params (e.g. ?page=123)
        $namedParams = $this->request->getParam('named');
        $newQueryParams = array_merge($this->request->getQueryParams(), $namedParams);
        $this->request = $this->request->withQueryParams($newQueryParams);
    }

    /**
     * Called after the controller action is run,
     * but before the view is rendered.
     *
     * @return void
     */
    public function beforeRender(Event $event)
    {
        $current_user = CurrentUser::get('User');
        $auth_user = $this->Auth->user();
        if ($auth_user && $current_user && $auth_user != $current_user) {
            // User data changed, tell the Auth component about it.
            $this->Auth->setUser($current_user);
        }

        // without these 3 lines, html sent by AJAX will have the whole layout
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }

        // TODO
        // We're passing the value from the cookie to the session because it is
        // needed for the translation form (in helpers/sentences.php), but we
        // cannot access the Cookie component from a view.
        // This is not optimized, but I'm too lazy to do otherwise.
        $session = $this->request->getSession();
        $preSelectedLang = $this->Cookie->read('contribute_lang');
        $session->write('contribute_lang', $preSelectedLang);

        // Same for these cookies, used in show_all_in.
        $lang = $this->Cookie->read('browse_sentences_in_lang');
        $session->write('browse_sentences_in_lang', $lang);

        $notTranslatedInto = $this->Cookie->read('not_translated_into_lang');
        $session->write('not_translated_into_lang', $notTranslatedInto);

        $filterAudioOnly = $this->Cookie->read('filter_audio_only');
        $session->write('filter_audio_only', $filterAudioOnly);

        // Use this when displaying the list to which a sentence should be assigned.
        // See views/helpers/menu.php, controllers/sentences_list_controller.php.
        $mostRecentList = $this->Cookie->read('most_recent_list');
        $session->write('most_recent_list', $mostRecentList);

        $this->loadModel('WikiArticles');
        $wikiLinkLocalizer = $this->WikiArticles->wikiLinkLocalizer();
        $this->set(compact('wikiLinkLocalizer'));
    }


    /**
     * TODO This method smells
     *
     * @return void
     */
    public function flash($msg, $to, $pause = 1, $layout = 'flash')
    {
        $this->Flash->set($msg);
        if (is_array($to)) {
            $to = array_merge(array('lang' => $this->request->getParam('lang')), $to);
        } else {
            $to = '/'.$this->request->getParam('lang').$to;
        }
        $this->redirect($to);
    }


    /**
     * Redirect to a given url, and specify the interface language
     *
     * @param mixed $url    The url to go to, can be a raw url (string)
     *                      or a cakephp array
     * @param int   $status HTTP status code to send
     * @param bool  $exit   If true, exit() will be called after the redirect
     *
     * @return mixed
     */
    public function redirect($url = null, $status = null, $exit = true)
    {
        // if the developer has used "redirect" method without
        // specifying the lang param, then we add it
        if ($this->request->getParam('lang') !== false && is_array($url)) {
            $url['lang'] = $this->request->getParam('lang');
        }
        return parent::redirect($url, $status, $exit);
    }

    protected function redirectPaginationToLastPage()
    {
        $paging = $this->request->getParam('paging');
        $lastPage = reset($paging)['page'];
        $queryParams = $this->request->getParam('?');
        $queryParams['page'] = $lastPage;
        $url = Router::url(array_merge(
            [
                'controller' => $this->request->getParam('controller'),
                'action' => $this->request->getParam('action'),
                '?' => $queryParams
            ],
            $this->request->getParam('pass')
        ));
        return $this->redirect($url);
    }

    public function paginateOrRedirect($object = null, array $settings = []) {
        try {
            return $this->paginate($object, $settings);
        } catch (\Cake\Http\Exception\NotFoundException $e) {
            return $this->redirectPaginationToLastPage();
        }
    }

    /**
     * Returns $array containing only $allowedKeys keys.
     *
     * @param array $array  An associative array
     * @param array $allowedKeys Allowed keys inside $array
     *
     * @return string Filtered array.
     */
    public function filterKeys($array, $allowedKeys)
    {
        return array_intersect_key($array, array_flip($allowedKeys));
    }

    /**
     * Adds a language to the list of last used languages.
     * This list is used to provide guests (non logged-in users)
     * with a 'preferred languages' list.
     */
    public function addLastUsedLang($code) {
        $session = $this->request->getSession();
        if (!CurrentUser::isMember() && LanguagesLib::languageExists($code)) {
            $current = (array)$session->read('last_used_lang');
            if (!in_array($code, $current)) {
                $current[] = $code;
                $session->write('last_used_lang', $current);
            }
        }
    }
}
