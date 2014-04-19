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

App::import('Core', 'Sanitize');
 
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
        'Acl',
        'Auth',
        'Permissions',
        'RememberMe',
        'Cookie',
        'RequestHandler'
    );

    public $helpers = array(
        'Sentences',
        'Comments',
        'Date',
        'Html',
        'Form',
        'Logs',
        'Javascript',
        'Languages'
    );
    /**
     * to know who can do what
     *
     * @return void
     */
    public function beforeFilter() 
    {
        $blockedIps = Configure::read('Tatoeba.blockedIP');


        App::import('Model', 'CurrentUser');
//        if (in_array( CurrentUser::getIp(), $blockedIp )) {
//          $this->redirect($redirectPage, 404);
//       }
        $ip = CurrentUser::getIp();
        foreach( $blockedIps as $blockedIp) {
            if (strpos($ip, $blockedIp, 0) ===0) {
                sleep(60);
                $this->redirect($redirectPage, 404);
                return; 
            }
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        #if(strpos($user_agent, ".NET CLR 3.5.30729") !== false) {
        #    $this->redirect($redirectPage, 404);
        #} 
        Security::setHash('md5');
        $this->Cookie->domain = TATOEBA_DOMAIN;
        // this line will call views/elements/session_expired.ctp
        // when one try to do an ajax action after is session expired
        // the action will return the content of this file instead of
        // the whole pages
        $this->Auth->ajaxLogin = 'session_expired'; 
        $this->Auth->allow('display');
        $this->Auth->authorize = 'actions';
        $this->Auth->authError = __('You need to be logged in.', true);
        // very important for the "remember me" to work
        $this->Auth->autoRedirect = false; 
        $this->RememberMe->check();
        
        // So that we can access the current users info from models.
        CurrentUser::store($this->Auth->user());

        
        // TODO
        // We're passing the value from the cookie to the session because it is
        // needed for the translation form (in helpers/sentences.php), but we
        // cannot access the Cookie component from a view.
        // This is not optimized, but I'm too lazy to do otherwise.
        $preSelectedLang = $this->Cookie->read('contribute_lang');
        $this->Session->write('contribute_lang', $preSelectedLang);
        
        
        // Language of interface:
        // - By default we use the language set in the browser (or English, if the
        //   language of the browser is not supported).
        // - If the user has a cookie, we use the language set in the cookie.
        // - If no cookie, we use the language set in the URL.
        $lang = $this->getSupportedLanguage();
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
     * Called after the controller action is run, i
     * but before the view is rendered.
     *
     * @return void
     */
    public function beforeRender()
    {
        // without this 3 lines, html send by ajax will have the whole layout
        if ($this->RequestHandler->isAjax()) {
            $this->layout = null;
        }
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
     * Redirect to a given url, and precise the interface language
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
        // if the developper has used "redirect" method without
        // precising the lang params, then we add it
        if (isset($this->params['lang']) && is_array($url)) {
            $url['lang'] = $this->params['lang'];
        }
        return parent::redirect($url, $status, $exit);
    }

    /**
     * Rebuild the Acl based on the current controllers in the application
     * To be removed in production mode.
     *
     * @return void
     */
    protected function _buildAcl()
    {
        $log = array();

        $aco =& $this->Acl->Aco;
        $root = $aco->node('controllers');
        if (!$root) {

            $aco->create(
                array(
                    'parent_id' => null,
                    'model' => null,
                    'alias' => 'controllers'
                )
            );
            $root = $aco->save();
            $root['Aco']['id'] = $aco->id;
            $log[] = 'Created Aco node for controllers';
            
        } else {

            $root = $root[0];
        }

        App::import('Core', 'File');
        $Controllers = Configure::listObjects('controller');
        $appIndex = array_search('App', $Controllers);
        if ($appIndex !== false ) {
            unset($Controllers[$appIndex]);
        }
        $baseMethods = get_class_methods('Controller');
        $baseMethods[] = '_buildAcl';

        // look at each controller in app/controllers
        foreach ($Controllers as $ctrlName) {
            App::import('Controller', $ctrlName);
            $ctrlclass = $ctrlName . 'Controller';
            $methods = get_class_methods($ctrlclass);

            // find / make controller node
            $controllerNode = $aco->node('controllers/'.$ctrlName);

            if (!$controllerNode) {

                $aco->create(
                    array(
                        'parent_id' => $root['Aco']['id'],
                        'model' => null,
                        'alias' => $ctrlName
                    )
                );
                $controllerNode = $aco->save();
                $controllerNode['Aco']['id'] = $aco->id;
                $log[] = 'Created Aco node for '.$ctrlName;

            } else {

                $controllerNode = $controllerNode[0];

            }

            //clean the methods. to remove those in Controller and private actions.
            foreach ($methods as $k => $method) {
                if (strpos($method, '_', 0) === 0) {
                    unset($methods[$k]);
                    continue;
                }
                if (in_array($method, $baseMethods)) {
                    unset($methods[$k]);
                    continue;
                }
                $methodNode = $aco->node('controllers/'.$ctrlName.'/'.$method);
                
                if (!$methodNode) {

                    $aco->create(
                        array(
                            'parent_id' => $controllerNode['Aco']['id'],
                            'model' => null,
                            'alias' => $method
                        )
                    );
                    $methodNode = $aco->save();
                    $log[] = 'Created Aco node for '. $method;

                }
            }
        }
        debug($log);
    }

    /**
     * Returns the ISO code of the language in which we should set the interface, 
     * considering the languages of the user's browser.
     *
     * @return string
     */
    public function getSupportedLanguage()
    {
        $supportedLanguages = array(
            'en'    => 'eng',
            'ja'    => 'jpn',
            'fr'    => 'fre',
            'es'    => 'spa',
            'de'    => 'deu',
            'zh'    => 'chi',
            'it'    => 'ita',
            'pl'    => 'pol',
            'pt-BR' => 'pt_BR',
            'ru'    => 'rus',
            'tr'    => 'tur',
            'el'    => 'gre',

        );
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
