<?php 
class AppController extends Controller { 
    var $components = array('Acl','Auth','Permissions');
	var $helpers = array('Sentences');
	
    function beforeFilter() { 
		Security::setHash('md5');
		
        if (isset($this->params['lang'])) { 
            Configure::write('Config.language',  $this->params['lang']); 
        } 
		
		//Configure AuthComponent
		$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
		$this->Auth->logoutRedirect = array('controller' => 'pages', 'action' => 'display', 'home');
		$this->Auth->allow('display');
		$this->Auth->authorize = 'actions';
		$this->Auth->authError = __('You need to be logged in.',true);
		
		// to remove in production mode
		//$this->buildAcl();
    }

	function flash($msg,$to){
		$this->Session->setFlash($msg);
		$this->redirect('/'.$this->params['lang'].$to);
		exit;
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