<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *                                1785 E. Sahara Avenue, Suite 490-204
 *                                Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright        Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link                http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package            cake
 * @subpackage        cake.cake.libs.controller
 * @since            CakePHP(tm) v 0.2.9
 * @version            $Revision: 7118 $
 * @modifiedby        $LastChangedBy: gwoo $
 * @lastmodified    $Date: 2008-06-04 13:49:29 -0700 (Wed, 04 Jun 2008) $
 * @license            http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package        cake
 * @subpackage    cake.cake.libs.controller
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
    public $helpers = array('Html');
    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array();

    public $components = array('Permissions');
    /**
     * Displays a view
     *
     * @param mixed $path What page to display
     *
     * @return void
     */
    public function display($path)
    {

        if (!count($path)) {
            $this->redirect('/');
        }
        $count = count($path);
        $page = $subpage = $title = null;

        if (!empty($path[0])) {
            $page = $path[0];

            if ($page == 'index') { // IF INDEX PAGE
                if ($this->Auth->user('id')) {
                    $this->redirect(
                        array(
                            "action" => "display",
                            "home"
                        )
                    );
                }
                $this->_index();     

            } else if ($page == 'home') { // IF HOME PAGE
                $this->_home();     
            }

        }

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        if (!empty($path[$count - 1])) {
            $title = Inflector::humanize($path[$count - 1]);
        }
        $this->set(compact('page', 'subpage', 'title'));
        $this->render(join('/', $path));
    }

    /**
     * use to retrive data needed to display all the index module
     * data are sent to pages/index.ctp
     *
     * @return void
     */

    private function _index()
    {
        $userId = $this->Auth->user('id');
        $groupId = $this->Auth->user('group_id');

        
        /*Some numbers part*/
        $Contribution = ClassRegistry::init('Contribution'); 
        $nbrContributions = $Contribution->getDailyContributions(); 
       
        
        $User = ClassRegistry::init('User'); // Add Post Class
        $nombreDeMembresActifs = $User->getNumberOfActiveMembers();
       
        $this->set('nombreDeMembresActifs', $nombreDeMembresActifs); 
        $this->set('nombreDeContribution', $nbrContributions);
    }
 
     /**
     * use to retrive data needed to display all the home module
     * data are sent to pages/home.ctp
     *
     * @return void
     */   
 
    private function _home()
    {
        $userId = $this->Auth->user('id');
        $groupId = $this->Auth->user('group_id');

        /*latest comments part */
        $SentenceComment = ClassRegistry::init('SentenceComment');
        $latestComments = $SentenceComment->getLatestComments(5);

        $commentsPermissions = $this->Permissions->getCommentsOptions(
            $latestComments,
            $userId,
            $groupId
        );


        $this->set('sentenceComments', $latestComments);
        $this->set('commentsPermissions', $commentsPermissions);  
            
    }

}


?>
