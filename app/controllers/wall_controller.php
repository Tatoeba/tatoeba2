<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
controller for the wall
should contains all the method related to send a new message on the wall
replying and so 
*/

class WallController extends Appcontroller{
    var $name = 'Wall' ;
    var $paginate = array('limit' => 50);
    var $helpers = array('Wall');

    function beforeFilter(){
        parent::beforeFilter();
        $this->Auth->allowedActions = array('*');
           
        // TODO complete this method
    }

    function index(){
       $messages = $this->Wall->find('all',
                                      array( "order" => "Wall.date DESC" )
                                    );
       $this->set('messages' , $messages) ;

    }

    function save(){
        //TODO
        if(!empty($this->data['Wall']['content'] )){
            $this->data['Wall']['owner'] = $this->Auth->user('id');
            $this->data['Wall']['date'] = date("Y-m-d H:i:s");
            
            // now save to database 
            if ($this->Wall->save($this->data)){

            }


        }

        $this->redirect(array('action'=>'index'));

    }

    



}


?>
