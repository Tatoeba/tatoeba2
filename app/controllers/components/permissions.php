<?php
class PermissionsComponent extends Object{
	var $components = array('Auth', 'Acl');
	
	/**
	 * Check which options user can access to and returns
	 * an associative array with boolean value for each
	 * of the options.
	 * The options are : canComment, canEdit and canDelete.
	 */

	function getSentencesOptions($sentence, $current_user_id){
	//function getSentencesOptions($sentence_owner_id, $current_user_id){
		$sentence_owner_id = $sentence['Sentence']['user_id'];

		$specialOptions = array(
			  '5canComment' => false
			, 'canEdit' => false
			, 'canDelete' => false
			, 'canAdopt' => false
			, 'canLetGo' => false
			, 'canTranslate' => false
			, 'canFavorite' => false	
			, 'canUnFavorite' => false	
		);
		
		if($this->Auth->user('id')){
			$specialOptions['canComment'] = true;
			$specialOptions['canTranslate'] = true;
	
			$specialOptions['canFavorite'] = true;
			// if we have already favorite it then we just can
			// unfavorite it
			// is_array is here to avoid a warning when favorites_users is an empty array
			if (is_array($sentence['Favorites_users'])){
				foreach(   $sentence['Favorites_users'] as  $favoritingUser ){			 
					if ($favoritingUser['user_id'] == $this->Auth->user('id')){
						$specialOptions['canUnFavorite'] = true;
						$specialOptions['canFavorite'] = false;
						}
				}
			}
			
			if($this->Auth->user('group_id') < 3){
				
				$specialOptions['canEdit'] = true;
				
			}
			if($sentence_owner_id == $current_user_id){
			
				$specialOptions['canEdit'] = true;
				$specialOptions['canLetGo'] = true;
				
			}
			
			$specialOptions['canDelete'] = ($this->Auth->user('group_id') < 2);
			
		}
		if($sentence_owner_id == NULL OR $sentence_owner_id = 0){
			
			$specialOptions['canAdopt'] = true;
			
		}
		
		return $specialOptions;
	}
}
?>
