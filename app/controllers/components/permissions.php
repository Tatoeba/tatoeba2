<?php
class PermissionsComponent extends Object{
	var $components = array('Auth', 'Acl');
	
	/**
	 * Check which options user can access to and returns
	 * an associative array with boolean value for each
	 * of the options.
	 * The options are : canComment, canEdit and canDelete.
	 */
	function getSentencesOptions(){
		$specialOptions = array('canComment' => false, 'canEdit' => false, 'canDelete' => false);
		if($this->Auth->user('id')){
			$specialOptions['canComment'] = true;
			$specialOptions['canEdit'] = $this->Acl->check(array('Group'=>$this->Auth->user('group_id')), 'controllers/Sentences/edit');
			$specialOptions['canDelete'] = $this->Acl->check(array('Group'=>$this->Auth->user('group_id')), 'controllers/Sentences/delete');
		}
		return $specialOptions;
	}
}
?>