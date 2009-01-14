<?php 
class AppController extends Controller { 
    var $components = array('Acl','Auth','Permissions','Cookie');
	var $helpers = array('Sentences','Date', 'Html', 'Form', 'Logs', 'Tooltip');
	
    function beforeFilter() { 
		Security::setHash('md5');
		
        if (isset($this->params['lang'])) { 
            Configure::write('Config.language',  $this->params['lang']); 
        } 
		
		//Configure AuthComponent
		$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
		if(isset($this->params['lang'])){
			$this->Auth->loginAction['lang'] = $this->params['lang'];
		}
		$this->Auth->logoutRedirect = array('controller' => 'pages', 'action' => 'display', 'home');
		$this->Auth->allow('display');
		$this->Auth->authorize = 'actions';
		$this->Auth->authError = __('You need to be logged in.',true);
		
		
		$cookie = $this->Cookie->read('Auth.User');
		if (!is_null($cookie)) {
			if ($this->Auth->login($cookie)) {
				//  Clear auth message, just in case we use it.
				$this->Session->del('Message.auth');
				//$this->redirect($this->Auth->redirect());
			} else { // Delete invalid Cookie
				$this->Cookie->del('Auth.User');
			}
		}
	
		
		// to remove in production mode
		//$this->buildAcl();
    }

	function flash($msg,$to){
		$this->Session->setFlash($msg);
		$this->redirect('/'.$this->params['lang'].$to);
		exit;
	}
	
	function redirect($url = null, $full = false) { 
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
    function buildAcl() {
        $log = array();
 
        $aco =& $this->Acl->Aco;
        $root = $aco->node('controllers');
        if (!$root) {
            $aco->create(array('parent_id' => null, 'model' => null, 'alias' => 'controllers'));
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
        $baseMethods[] = 'buildAcl';
 
        // look at each controller in app/controllers
        foreach ($Controllers as $ctrlName) {
            App::import('Controller', $ctrlName);
            $ctrlclass = $ctrlName . 'Controller';
            $methods = get_class_methods($ctrlclass);
 
            // find / make controller node
            $controllerNode = $aco->node('controllers/'.$ctrlName);
            if (!$controllerNode) {
                $aco->create(array('parent_id' => $root['Aco']['id'], 'model' => null, 'alias' => $ctrlName));
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
                    $aco->create(array('parent_id' => $controllerNode['Aco']['id'], 'model' => null, 'alias' => $method));
                    $methodNode = $aco->save();
                    $log[] = 'Created Aco node for '. $method;
                }
            }
        }
        debug($log);
    }
} 
?>