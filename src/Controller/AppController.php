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
namespace App\Controller;

use App\Lib\LanguagesLib;
use App\Model\CurrentUser;
use App\Utility\Sanitize;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;
use Cake\Routing\Router;
use Cake\I18n\I18n;
use Locale;

/**
 * Controller for contributions.
 *
 * @category App
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */



class AppController extends Controller
{
    public $components = array(
        'Acl' => array(
            'className' => 'Acl.Acl',
        ),
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

    private function remapOldLangAlias($lang)
    {
        $uiLangSettings = Configure::read('UI.languages');
        foreach ($uiLangSettings as $setting) {
            if (isset($setting[3]) && is_array($setting[3])
                && in_array($lang, $setting[3])) {
                return $setting[0];
            }
        }
        return $lang;
    }

    private function blackhole($type) {
      var_dump("Blackholed: $type");
    }

    public function initialize()
    {
        $this->loadComponent('Cookie', [
            'key' => Configure::read('Security.cookieSalt', Configure::read('Security.salt')),
        ]);

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
        // This line will call views/elements/session_expired.ctp.
        // When one tries to do an AJAX action after the session is expired,
        // the action will return the content of this file instead of
        // the whole page.
        $this->Auth->ajaxLogin = 'session_expired';
        $this->Auth->allow('display');
        $this->Auth->authorize = 'Actions';
        $this->Auth->authError = __('You need to be logged in.', true);
        // very important for the "remember me" to work
        $this->Auth->autoRedirect = false;
        $this->RememberMe->check();

        // So that we can access the current users info from models.
        // Important: needs to be done after RememberMe->check().
        CurrentUser::store($this->Auth->user());

        // Set view variables for the search bar
        $this->set('query', '');

        // Language of interface:
        // - By default we use the language set in the browser (or English, if the
        //   language of the browser is not supported).
        // - If the user has a cookie, we use the language set in the cookie.
        // - If no cookie, we use the language set in the URL.
        $lang = $this->getSupportedLanguage();
        $langInCookie = $this->Cookie->read('CakeCookie.interfaceLanguage');
        $langInURL = $this->request->getParam('lang', null);

        $langInURLAlias = $this->remapOldLangAlias($langInURL);
        if ($langInURLAlias != $langInURL) {
            $lang = $langInURLAlias;
        } else if ($langInCookie) {
            $langInCookieAlias = $this->remapOldLangAlias($langInCookie);
            if ($langInCookieAlias != $langInCookie && !empty($langInURL)) {
                $this->Cookie->write('CakeCookie.interfaceLanguage', $langInCookieAlias, false, "+1 month");
                $langInCookie = $langInCookieAlias;
            }
            $lang = $langInCookie;
        } else if (!empty($langInURL)) {
            $lang = $langInURL;
            $this->Cookie->write('CakeCookie.interfaceLanguage', $lang, false, "+1 month");
        }
        Configure::write('Config.language', $lang);
        $locale = Locale::parseLocale($lang);
        I18N::setLocale($locale['language']);

        // If the Router did not parse the URL, we don't know if the URL
        // contains a language, we so cannot perform any kind of language
        // redirection
        $routerDidParseURL = !empty($this->request->getParam('controller'));
        if ($routerDidParseURL) {

            // Forcing the URL to have the (correct) language in it.
            $url = $this->request->getRequestTarget();
            if (!empty($langInURL) && (
                  ($langInCookie && $langInURL != $langInCookie) ||
                  ($langInURLAlias != $langInURL)
               )) {
                // We're are now going to remove the language from the URL and set
                // $langURL to null so that we get the the correct URL through
                // redirection (below).
                $url = preg_replace("/^\/$langInURL(\/|$)/", '/', $url);
                $langInURL = null;
            }
            if (empty($langInURL)
                && !$this->request->is('post')   // Avoid throwing away POST or
                && !$this->request->is('put')) { // PUT data by redirecting
                $redirectPage = "/".$lang.$url;
                // Redirection of Ajax requests will be handled internally and all in
                // one request thanks to RequestHandlerComponent::beforeRedirect().
                // However, this function sets the HTTP return code and we don't want
                // that. Instead, we want to hide the fact a redirection happened and
                // let the sub-request return its own return code.
                $redirectCode = $this->request->is('ajax') ? null : 301;
                return $this->redirect($redirectPage, $redirectCode);
            }
        }

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

        $translationLang = $this->Cookie->read('show_translations_into_lang');
        $session->write('show_translations_into_lang', $translationLang);

        $notTranslatedInto = $this->Cookie->read('not_translated_into_lang');
        $session->write('not_translated_into_lang', $notTranslatedInto);

        $filterAudioOnly = $this->Cookie->read('filter_audio_only');
        $session->write('filter_audio_only', $filterAudioOnly);

        // Use this when displaying the list to which a sentence should be assigned.
        // See views/helpers/menu.php, controllers/sentences_list_controller.php.
        $mostRecentList = $this->Cookie->read('most_recent_list');
        $session->write('most_recent_list', $mostRecentList);
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

    private function redirectPaginationToLastPage($object, $settings)
    {
        $paging = $this->request->getParam('paging');
        $lastPage = reset($paging)['page'];
        $queryParams = $this->request->params['?'];
        $queryParams['page'] = $lastPage;
        $url = Router::url(array_merge(
            [
                'controller' => $this->request->params['controller'],
                'action' => $this->request->params['action'],
                '?' => $queryParams
            ],
            $this->request->params['pass']
        ));
        $this->redirect($url);
    }

    public function paginate($object = NULL, array $settings = array())
    {
        try {
            return parent::paginate($object, $settings);
        } catch (NotFoundException $e) {
            $this->redirectPaginationToLastPage($object, $settings);
            return array();
        }
    }

    /**
     * Returns the ISO code of the language in which we should set the interface,
     * considering the languages of the user's browser.
     *
     * @return string
     */
    public function getSupportedLanguage()
    {
        $configUiLanguages = Configure::read('UI.languages');
        $supportedLanguages = array();
        foreach ($configUiLanguages as $langs) {
            $browserCompatibleCode = LanguagesLib::languageTag($langs[0]);
            $supportedLanguages[$browserCompatibleCode] = $langs[0];
        }

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {

            $browserLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

            foreach ($browserLanguages as $browserLang) {
                $browserLangArray = explode(';', $browserLang);
                $lang = $browserLangArray[0];
                if (isset($supportedLanguages[$lang])) {
                    return $supportedLanguages[$lang];
                }
            }

        }
        return 'eng';
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
