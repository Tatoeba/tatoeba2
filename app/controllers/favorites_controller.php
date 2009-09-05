<?php
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
	        $user_id =$this->Auth->user('id');
		if ( $user_id != NULL ){
	
			$result = $this->Favorite->query("SELECT id FROM sentences WHERE ID= $sentence_id");

			if ( $result != NULL ){ 
				
				$this->Favorite->habtmAdd ('Favorite' , $sentence_id , $this->Auth->user('id') );
				
				$this->set('user_id' , $user_id );
				$this->set('sentence_id' , $result[0]["sentences"]["id"] ) ;

			}
		}
	}

	function remove_favorite ($sentence_id){
	        	
	        $user_id =$this->Auth->user('id');

		if ( $user_id != NULL ){

			$result = $this->Favorite->query("SELECT id FROM sentences WHERE ID= $sentence_id");

			if ( $result != NULL ){ 
				$this->Favorite->habtmDelete ('Favorite' , $sentence_id , $this->Auth->user('id') );
				
				$this->set('user_id' , $user_id );
				$this->set('sentence_id' , $result[0]["sentences"]["id"] ) ;

			}
		}
	}


}
?>
