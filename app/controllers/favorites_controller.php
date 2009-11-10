<?php
/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  Allan SIMON <allan.simon@supinfo.com>,
	HO Ngoc Phuong Trang <tranglich@gmail.com>

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
class FavoritesController extends AppController{

	var $name = 'Favorites' ;
	var $paginate = array('limit' => 50); 
	var $helpers = array('Navigation', 'Html');

	function beforeFilter() {
	    parent::beforeFilter();
		
		// setting actions that are available to everyone, even guests
		$this->Auth->allowedActions = array('of_user');
	}
	
	function of_user($user_id){
		$u = new User();
		$u->id = $user_id;
		$u->hasAndBelongsToMany['Favorite']['limit'] = null;
		$user = $u->read();
		$this->set('user', $user);
	}

	function add_favorite ($sentence_id){
		Configure::write('debug',0);
		
	    $user_id =$this->Auth->user('id');
		
		if ( $user_id != NULL ){
			
			if($this->Favorite->habtmAdd ('User' , $sentence_id , $this->Auth->user('id') )){
				$this->set('saved' , true );
			}
		}
	}

	function remove_favorite ($sentence_id){
	    Configure::write('debug',0);
    	
		$user_id =$this->Auth->user('id');
		
		if ( $user_id != NULL ){
			
			if($this->Favorite->habtmDelete ('User' , $sentence_id , $this->Auth->user('id') )){
				$this->set('saved' , true );
			}
		}
	}


}
?>
