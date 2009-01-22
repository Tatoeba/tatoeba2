<?php
class PermissionsComponent extends Object{
	var $components = array('Auth', 'Acl');
	
	/**
	 * Check which options user can access to and returns
	 * an associative array with boolean value for each
	 * of the options.
	 * The options are : canComment, canEdit and canDelete.
	 */
	function getSentencesOptions($sentence_owner_id, $current_user_id){
		$specialOptions = array(
			  'canComment' => false
			, 'canEdit' => false
			, 'canDelete' => false
			, 'canAdopt' => false
			, 'canLetGo' => false
		);
		
		if($this->Auth->user('id')){
			$specialOptions['canComment'] = true;
			
			if($this->Auth->user('group_id') < 3){
				
				$specialOptions['canEdit'] = true;
				
			}else if($this->Acl->check(array('Group'=>$this->Auth->user('group_id')), 'controllers/Sentences/edit')){
				// people who have Sentence/edit permission can only edit sentences that they own...
				// I don't know how to do that with ACL without having each sentence being an ACO
				// (in which case it would be not optimal at all)
				if($sentence_owner_id == $current_user_id){
					
					$specialOptions['canEdit'] = true;
					$specialOptions['canLetGo'] = true;
					
				}else if($sentence_owner_id == NULL OR $sentence_owner_id = 0){
					
					$specialOptions['canAdopt'] = true;
					
				}
				
			}
			
			
			$specialOptions['canDelete'] = $this->Acl->check(array('Group'=>$this->Auth->user('group_id')), 'controllers/Sentences/delete');
		}
		return $specialOptions;
	}
}
?>