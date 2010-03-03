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
 * @link     http://tatoeba.org
 */

/**
 * Controller for static content
 * used to render the files in views/pages/*
 *
 * @category Contributions
 * @package  Controllers
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
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
        $path = func_get_args();

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
        /*Some numbers part*/
        $Contribution = ClassRegistry::init('Contribution'); 
        $nbrContributions = $Contribution->getTodayContributions(); 
       
        
        $User = ClassRegistry::init('User');
        $nbrActiveMembers = $User->getNumberOfActiveMembers();
       
        $this->set('nbrActiveMembers', $nbrActiveMembers); 
        $this->set('nbrContributions', $nbrContributions);
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
