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
