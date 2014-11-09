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
 * Controller for contributions.
 *
 * @category App
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

App::import('Core', 'Sanitize');
App::import('Model', 'CurrentUser');

class AppController extends Controller
{
    public $components = array(
        'Acl',
        'Auth',
        'Permissions',
        'RememberMe',
        'Cookie',
        'RequestHandler',
        'Session'
    );

    public $helpers = array(
        'Sentences',
        'Comments',
        'Date',
        'Html',
        'Form',
        'Logs',
        'Javascript',
        'Languages',
        'Pages',
        'Session'
    );
    
    
    /**
     * 
     *
     * @return void
     */
    public function beforeFilter() 
    {
        // blocked IP's
        $blockedIps = Configure::read('Tatoeba.blockedIP');
        $ip = CurrentUser::getIp();
        foreach( $blockedIps as $blockedIp) {
            if (strpos($ip, $blockedIp, 0) ===0) {
                sleep(60);
                $this->redirect($redirectPage, 404);
                return; 
            }
        }
        
        Security::setHash('md5');
        $this->Cookie->domain = TATOEBA_DOMAIN;
        // This line will call views/elements/session_expired.ctp.
        // When one tries to do an AJAX action after the session is expired,
        // the action will return the content of this file instead of
        // the whole page.
        $this->Auth->ajaxLogin = 'session_expired'; 
        $this->Auth->allow('display');
        $this->Auth->authorize = 'actions';
        $this->Auth->authError = __('You need to be logged in.', true);
        // very important for the "remember me" to work
        $this->Auth->autoRedirect = false; 
        $this->RememberMe->check();

        // So that we can access the current users info from models.
        // Important: needs to be done after RememberMe->check().
        CurrentUser::store($this->Auth->user());
        
        // Language of interface:
        // - By default we use the language set in the browser (or English, if the
        //   language of the browser is not supported).
        // - If the user has a cookie, we use the language set in the cookie.
        // - If no cookie, we use the language set in the URL.
        if (empty($lang)) {
            $lang = $this->getSupportedLanguage();
        }
        $langInCookie = $this->Cookie->read('interfaceLanguage');
        $langInURL = null;
        if (isset($this->params['lang'])) {
            $langInURL = $this->params['lang'];
        }
        if ($langInCookie) {
            $lang = $langInCookie;
        } else if (!empty($langInURL)) {
            $lang = $langInURL;
            $this->Cookie->write('interfaceLanguage', $lang, false, "+1 month");
        }
        Configure::write('Config.language', $lang);

        // Forcing the URL to have the (correct) language in it.
        $url = $_SERVER["REQUEST_URI"];
        if (!empty($langInURL) && $langInCookie && $langInURL != $langInCookie) {
            // We're are now going to remove the language from the URL and set
            // $langURL to null so that we get the the correct URL through
            // redirection (below).
            $url = preg_replace("/^\/$langInURL(\/|$)/", '/', $url);
            $langInURL = null;
        }
        if (empty($langInURL)) {
            $redirectPage = "/".$lang.$url;
            $this->redirect($redirectPage, 301);
        }
    }

    /**
     * Called after the controller action is run,
     * but before the view is rendered.
     *
     * @return void
     */
    public function beforeRender()
    {
        // without these 3 lines, html sent by AJAX will have the whole layout
        if ($this->RequestHandler->isAjax()) {
            $this->layout = null;
        }
        
        // TODO
        // We're passing the value from the cookie to the session because it is
        // needed for the translation form (in helpers/sentences.php), but we
        // cannot access the Cookie component from a view.
        // This is not optimized, but I'm too lazy to do otherwise.
        $preSelectedLang = $this->Cookie->read('contribute_lang');
        $this->Session->write('contribute_lang', $preSelectedLang);
        
        // Same for these cookies, used in show_all_in.
        $lang = $this->Cookie->read('browse_sentences_in_lang');
        $this->Session->write('browse_sentences_in_lang', $lang);
        
        $translationLang = $this->Cookie->read('show_translations_into_lang');
        $this->Session->write('show_translations_into_lang', $translationLang);
        
        $notTranslatedInto = $this->Cookie->read('not_translated_into_lang');
        $this->Session->write('not_translated_into_lang', $notTranslatedInto);
        
        $filterAudioOnly = $this->Cookie->read('filter_audio_only');
        $this->Session->write('filter_audio_only', $filterAudioOnly);

        // Use this when displaying the list to which a sentence should be assigned.
        // See views/helpers/menu.php, controllers/sentences_list_controller.php.
        $mostRecentList = $this->Cookie->read('most_recent_list');
        $this->Session->write('most_recent_list', $mostRecentList);

        // This controls whether to use the most_recent_list cookie, or simply
        // to choose the first list in alphabetical order.
        $useMostRecentList = $this->Cookie->read('use_most_recent_list');
        $this->Session->write('use_most_recent_list', $useMostRecentList);

        $jqueryChosen = $this->Cookie->read('jquery_chosen');
        $this->Session->write('jquery_chosen', $jqueryChosen);
    }
    

    /**
     * TODO This method smells
     *
     * @return void
     */
    public function flash($msg,$to)
    {
        $this->Session->setFlash($msg);
        $this->redirect('/'.$this->params['lang'].$to);
        exit;
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
        if (isset($this->params['lang']) && is_array($url)) {
            $url['lang'] = $this->params['lang'];
        }
        return parent::redirect($url, $status, $exit);
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
            if ($langs[1] != null) {
                $supportedLanguages[$langs[1]] = $langs[0];
            }
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
}
?>
