<?php
/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

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
	var $helpers = array('Navigation');

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
			
			/*
			$result = $this->Favorite->query("SELECT id FROM sentences WHERE ID= $sentence_id");

			if ( $result != NULL ){ 
				
				$this->Favorite->habtmAdd ('User' , $sentence_id , $this->Auth->user('id') );
				
				$this->set('user_id' , $user_id );
				$this->set('sentence_id' , $result[0]["sentences"]["id"] ) ;

			}
			*/
			
			if($this->Favorite->habtmAdd ('User' , $sentence_id , $this->Auth->user('id') )){
				$this->set('saved' , true );
			}
		}
	}

	function remove_favorite ($sentence_id){
	    Configure::write('debug',0);
    	
		$user_id =$this->Auth->user('id');
		
		if ( $user_id != NULL ){
			
			/*
			$result = $this->Favorite->query("SELECT id FROM sentences WHERE ID= $sentence_id");
			
			if ( $result != NULL ){ 
				$this->Favorite->habtmDelete ('User' , $sentence_id , $this->Auth->user('id') );
				
				$this->set('user_id' , $user_id );
				$this->set('sentence_id' , $result[0]["sentences"]["id"] ) ;

			}
			*/
			
			if($this->Favorite->habtmDelete ('User' , $sentence_id , $this->Auth->user('id') )){
				$this->set('saved' , true );
			}
		}
	}


}
?>
