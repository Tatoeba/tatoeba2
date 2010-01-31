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

class AppController extends Controller
{
    public $components = array('Acl','Auth','Permissions','RememberMe', 'Cookie');
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
        // When Tatoeba is being under maintenance, and website needs
        // to be blocked temporarily, uncomment the line below:
        // $this->layout = 'maintenance';
        
        Security::setHash('md5');
        // seems to be important so that the browser displays properly 
        //login info in header
        $this->disableCache(); 
        //Configure AuthComponent
        $this->Auth->loginAction = array(
            'controller' => 'users',
            'action' => 'login'
        );
        $this->Auth->logoutRedirect = array(
            'controller' => 'pages',
            'action' => 'display',
            'home'
        );
        $this->Auth->allow('display');
        $this->Auth->authorize = 'actions';
        $this->Auth->authError = __('You need to be logged in.', true);
        // very important for the "remember me" to work
        $this->Auth->autoRedirect = false; 
        $this->RememberMe->check();

        // to remove in production mode
        //$this->_buildAcl();
    }

    /**
     * Called after the controller action is run, i
     * but before the view is rendered.
     *
     * @return void
     */
    public function beforeRender()
    {
        // Language of interface
        if (isset($this->params['lang'])) {

            Configure::write(
                'Config.language',
                $this->params['lang']
            );
        
            $this->Cookie->write(
                'interfaceLanguage',
                $this->params['lang'],
                false,
                '+2 weeks'
            );

        } elseif ($this->Cookie->read('interfaceLanguage')) {

            $interfaceLanguage = $this->Cookie->read('interfaceLanguage');
            Configure::write('Config.language', $interfaceLanguage);
            $this->params['lang'] = $interfaceLanguage;
        
        } else {
            $interfaceLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            switch($interfaceLanguage){
                
            case 'fr':
                $lang = 'fre';
                break;
            case 'zh':
                $lang = 'chi';
                break;
            case 'es':
                $lang = 'spa';
                break;
            case 'it':
                $lang = 'ita';
                break;
            default  :
                $lang = 'eng';
            }
            Configure::write('Config.language', $lang);
            $this->Cookie->write('interfaceLanguage', $lang, false, '+2 weeks');
            $this->params['lang'] = $lang;
        }
    }

    /**
     * TODO
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
     * TODO
     *
     *
     */

    public function redirect($url = null, $full = false)
    {
        if (isset($this->params['lang']) && is_array($url)) {
            $url['lang'] = $this->params['lang'];
        }
        return parent::redirect($url, $full);
    }

    /**
     * Rebuild the Acl based on the current controllers in the application
     * To be removed in production mode.
     *
     * @return void
     */
    private function _buildAcl()
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
     * Change the language of the interface
     *
     * @return void
     */
    public function changeLanguage()
    {


    }
}
?>
