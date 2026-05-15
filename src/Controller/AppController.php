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
use App\Model\Entity\User;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;

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

    private function blackhole($type) {
      var_dump("Blackholed: $type");
    }

    public function initialize(): void
    {
        $this->loadComponent('Flash');
        $this->loadComponent('Permissions');
        $this->loadComponent('Security');
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'passwordHasher' => ['className' => 'Versioned'],
                    'finder' => 'userToLogin',
                ],
            ]
        ]);
        $this->loadComponent('RememberMe');
    }

    /**
     * This function is imported from CakePHP's 3.x Router.php
     * It provides legacy support for named parameters on incoming URLs.
     *
     * Checks the passed parameters for elements containing `$options['separator']`
     * Those parameters are split and parsed as if they were old style named parameters.
     *
     * The parsed parameters will be moved from params['pass'] to params['named'].
     *
     * ### Options
     *
     * - `separator` The string to use as a separator. Defaults to `:`.
     *
     * @param \Cake\Http\ServerRequest $request The request object to modify.
     * @param array $options The array of options.
     * @return \Cake\Http\ServerRequest The modified request
     * @deprecated 3.3.0 Named parameter backwards compatibility will be removed in 4.0.
     */
    private function parseNamedParams(ServerRequest $request, array $options = [])
    {
        $options += ['separator' => ':'];
        if (!$request->getParam('pass')) {
            return $request->withParam('named', []);
        }
        $named = [];
        $pass = $request->getParam('pass');
        foreach ((array)$pass as $key => $value) {
            if (strpos($value, $options['separator']) === false) {
                continue;
            }
            unset($pass[$key]);
            list($key, $value) = explode($options['separator'], $value, 2);

            if (preg_match_all('/\[([A-Za-z0-9_-]+)?\]/', $key, $matches, PREG_SET_ORDER)) {
                $matches = array_reverse($matches);
                $parts = explode('[', $key);
                $key = array_shift($parts);
                $arr = $value;
                foreach ($matches as $match) {
                    if (empty($match[1])) {
                        $arr = [$arr];
                    } else {
                        $arr = [
                            $match[1] => $arr,
                        ];
                    }
                }
                $value = $arr;
            }
            $named = array_merge_recursive($named, [$key => $value]);
        }

        return $request
            ->withParam('pass', $pass)
            ->withParam('named', $named);
    }

    /**
     *
     *
     * @return void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        // only prevent CSRF for logins and registration in the users controller
        $this->Security->csrfCheck = false;
        $this->Security->blackHoleCallback = 'blackhole';

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

        // Get logged-in user so that we can access it info from models.
        // Important: needs to be done after RememberMe->check().
        $logged_in_user = $this->Auth->user();
        if ($logged_in_user) {
            $user = $this->fetchTable('Users')
                ->getInformationOfCurrentUser($logged_in_user['id'])
                ->toArray();

            // Immediately logout if status was downgraded to one that cannot login
            if (in_array($user['role'], [User::ROLE_INACTIVE, User::ROLE_SPAMMER])) {
                $this->RememberMe->delete();
                $this->Auth->logout();
                if ($user['role'] == User::ROLE_SPAMMER) {
                    $this->Flash->set(__('Your account has been suspended.'));
                } elseif ($user['role'] == User::ROLE_INACTIVE) {
                    $this->Flash->set(__('Your account has been deactivated.'));
                }
                $user = false;
            } else {
                // Check if auth info needs to be updated
                // May happen if an admin just changed the user's role or username
                $updated_user_auth = array_intersect_key($user, $logged_in_user);
                if ($updated_user_auth != $logged_in_user) {
                    $this->Auth->setUser($updated_user_auth);
                    // AuthComponent::setUser() renews the session id, which makes
                    // the Security component blackhole the request if it's a post
                    // so disable it just this time
                    $this->Security->setConfig(['validatePost' => false]);
                }
            }
        } else {
            $user = false;
        }
        CurrentUser::store($user);

        // Restore named parameters removed in CakePHP 3
        $this->request = $this->parseNamedParams($this->request);

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
    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        // without these 3 lines, html sent by AJAX will have the whole layout
        if ($this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('ajax');
        }

        // Make some cookie values accessible to views
        // used in translation form and new sentence form
        $preSelectedLang = $this->request->getCookie('contribute_lang');
        $this->set('contribute_lang', $preSelectedLang);

        // used in show_all_in
        $lang = $this->request->getCookie('browse_sentences_in_lang');
        $this->set('browse_sentences_in_lang', $lang);

        // Use this when displaying the list to which a sentence should be assigned.
        // See views/helpers/menu.php, controllers/sentences_list_controller.php.
        $mostRecentList = $this->request->getCookie('most_recent_list');
        $this->set('most_recent_list', $mostRecentList);

        $wikiLinkLocalizer = $this->fetchTable('WikiArticles')->wikiLinkLocalizer();
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
     *
     * @return mixed
     */
    public function redirect($url, int $status = 302): ?\Cake\Http\Response
    {
        // if the developer has used "redirect" method without
        // specifying the lang param, then we add it
        if ($this->request->getParam('lang') !== false && is_array($url)) {
            $url['lang'] = $this->request->getParam('lang');
        }
        return parent::redirect($url, $status);
    }

    protected function redirectPaginationToLastPage()
    {
        $paging = $this->request->getAttribute('paging');
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
