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
		$specialOptions = array('canComment' => false, 'canEdit' => false, 'canDelete' => false);
		if($this->Auth->user('id')){
			$specialOptions['canComment'] = true;
			$specialOptions['canEdit'] = ($this->Auth->user('group_id') < 3 OR ($this->Acl->check(array('Group'=>$this->Auth->user('group_id')), 'controllers/Sentences/edit') AND $sentence_owner_id == $current_user_id));
			$specialOptions['canDelete'] = $this->Acl->check(array('Group'=>$this->Auth->user('group_id')), 'controllers/Sentences/delete');
		}
		return $specialOptions;
	}
}
?>