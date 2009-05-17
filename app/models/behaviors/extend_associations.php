<?php
/**
 * Extend Associations Behavior
 * Extends some basic add/delete function to the HABTM relationship
 * in CakePHP.  Also includes an unbindAll($exceptions=array()) for 
 * unbinding ALL associations on the fly.
 * 
 * This code is loosely based on the concepts from:
 * http://rossoft.wordpress.com/2006/08/23/working-with-habtm-associations/
 * 
 * @author Brandon Parise <brandon@parisemedia.com>
 * @package CakePHP Behaviors
 *
 */
class ExtendAssociationsBehavior extends ModelBehavior {
	/**
	 * Model-specific settings
	 * @var array
	 */
	var $settings = array();
	
	/**
	 * Setup
	 * Noething sp
	 *
	 * @param unknown_type $model
	 * @param unknown_type $settings
	 */
	function setup(&$model, $settings = array()) {
		// no special setup required
		$this->settings[$model->name] = $settings;
	}
	
	/**
	 * Add an HABTM association
	 *
	 * @param Model $model
	 * @param string $assoc
	 * @param int $id
	 * @param mixed $assoc_ids
	 * @return boolean
	 */
	function habtmAdd(&$model, $assoc, $id, $assoc_ids) {
		if(!is_array($assoc_ids)) {
			$assoc_ids = array($assoc_ids);
		}
		
		// make sure the association exists
		if(isset($model->hasAndBelongsToMany[$assoc])) {
			$data = $this->__habtmFind($model, $assoc, $id);
			
			// no data to update
			if(empty($data)) {
				return false;
			}
			
			// important to use array_unique() since merging will add 
			// non-unique values to the array.
			$data[$assoc][$assoc] = array_unique(am($data[$assoc][$assoc], $assoc_ids));
			return $model->save($data);
		}
		
		// association doesn't exist, return false
		return false;
	}
	
	/**
	 * Delete an HABTM Association
	 *
	 * @param Model $model
	 * @param string $assoc
	 * @param int $id
	 * @param mixed $assoc_ids
	 * @return boolean
	 */
	function habtmDelete(&$model, $assoc, $id, $assoc_ids) {
		if(!is_array($assoc_ids)) {
			$assoc_ids = array($assoc_ids);
		}
		
		// make sure the association exists
		if(isset($model->hasAndBelongsToMany[$assoc])) {
			$data = $this->__habtmFind($model, $assoc, $id);
			
			// no data to update
			if(empty($data)) {
				return false;
			}
						
			// if the * (all) is set then we want to delete all
			if($assoc_ids[0] == '*') {
				$data[$assoc][$assoc] = array();
			} else {
				// use array_diff to see what values we DONT want to delete
				// which is the ones we want to re-save.
				$data[$assoc][$assoc] = array_diff($data[$assoc][$assoc], $assoc_ids);
			}
			return $model->save($data);
		}
		
		// association doesn't exist, return false		
		return false;
	}
		
	/**
	 * Delete All HABTM Associations
	 * Just a nicer way to do easily delete all.
	 *
	 * @param Model $model
	 * @param string $assoc
	 * @param int $id
	 * @return boolean
	 */
	function habtmDeleteAll(&$model, $assoc, $id) {
		return $this->habtmDelete($model, $assoc, $id, '*');
	}
	
	/**
	 * Find 
	 * This method allows cake to do the dirty work to 
	 * fetch the current HABTM association.
	 *
	 * @param Model $model
	 * @param string $assoc
	 * @param int $id
	 * @return array
	 */	
	function __habtmFind(&$model, $assoc, $id) {
		// temp holder for model-sensitive params
		$tmp_recursive = $model->recursive;
		$tmp_cacheQueries = $model->cacheQueries;
		
		$model->recursive = 1;
		$model->cacheQueries = false;
		
		// unbind all models except the habtm association
		$this->unbindAll($model, array('hasAndBelongsToMany' => array($assoc)));
		$data = $model->find(array($model->name.'.'.$model->primaryKey => $id));
			
		$model->recursive = $tmp_recursive;
		$model->cacheQueries = $tmp_cacheQueries;
		
		if(!empty($data)) {
			// use Set::extract to extract the id's ONLY of the $assoc
			$data[$assoc] = array($assoc => Set::extract($data, $assoc.'.{n}.'.$model->primaryKey));
		}
		
		return $data;
	}
	
	/**
	 * UnbindAll with Exceptions
	 * Allows you to quickly unbindAll of a model's 
	 * associations with the exception of param 2.
	 *
	 * Usage:
	 *   $this->Model->unbindAll(); // unbinds ALL
	 *   $this->Model->unbindAll(array('hasMany' => array('Model2')) // unbind All except hasMany-Model2
	 * 
	 * @param Model $model
	 * @param array $exceptions
	 */
	function unbindAll(&$model, $exceptions = array()) {
		$unbind = array();
		foreach($model->__associations as $type) {
			foreach($model->{$type} as $assoc=>$assocData) {
				// if the assoc is NOT in the exceptions list then
				// add it to the list of models to be unbound.
				if(@!in_array($assoc, $exceptions[$type])) {
					$unbind[$type][] = $assoc;
				}
			}
		}
		// if we actually have models to unbind
		if(count($unbind) > 0) {
			$model->unbindModel($unbind);
		}
	}
}
?>