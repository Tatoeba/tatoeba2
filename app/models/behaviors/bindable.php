<?php
/* SVN FILE: $Id: bindable.php 46 2008-05-09 13:39:22Z mgiglesias $ */

/**
 * Bindable Behavior class file.
 *
 * Go to the Bindable Behavior page at Cake Syrup to learn more about it:
 *
 * http://cake-syrup.sourceforge.net/ingredients/bindable-behavior/
 *
 * @filesource
 * @author Mariano Iglesias
 * @link http://cake-syrup.sourceforge.net/ingredients/bindable-behavior/
 * @version	$Revision: 46 $
 * @license	http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package app
 * @subpackage app.models.behaviors
 */

/**
 * Model behavior to support unbinding of models.
 *
 * @package app
 * @subpackage app.models.behaviors
 */
class BindableBehavior extends ModelBehavior {
	/**
	 * Types of relationships available for models
	 *
	 * @var array
	 * @access private
	 */
	var $__bindings = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');

	/**
	 * Initiate behavior for the model using specified settings. Available settings:
	 *
	 * - recursive: (boolean, optional) set to true to allow bindable to automatically
	 * 				determine the recursiveness level needed to fetch specified models,
	 * 				and set the model recursiveness to this level. setting it to false
	 * 				disables this feature. DEFAULTS TO: true
	 *
	 * - notices:	(boolean, optional) issues E_NOTICES for bindings referenced in a
	 * 				bindable call that are not valid. DEFAULTS TO: false
	 *
	 * - autoFields: (boolean, optional) auto-add needed fields to fetch requested
	 * 				bindings. DEFAULTS TO: true
	 *
	 * @param object $Model Model using the behavior
	 * @param array $settings Settings to override for model.
	 * @access public
	 */
	function setup(&$Model, $settings = array()) {
		$default = array('recursive' => true, 'notices' => false, 'autoFields' => true);

		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $default;
		}

		$this->settings[$Model->alias] = array_merge($this->settings[$Model->alias], ife(is_array($settings), $settings, array()));
	}

	/**
	 * Unbinds all relations from a model except the specified ones. Calling this function without
	 * parameters unbinds all related models.
	 *
	 * @param object $Model Model on which binding restriction is being applied
	 * @return mixed If direct call, integer with recommended value for recursive
	 * @access public
	 */
	function restrict(&$Model) {
		$innerCall = false;
		$reset = true;
		$recursive = null;
		$arguments = func_get_args();
		$totalArguments = count($arguments);

		// Get the model, and find out if we're being called directly

		$shift = 1;

		if ($totalArguments > 1 && is_bool($arguments[1])) {
			$reset = $arguments[1];
			$shift++;

			if ($totalArguments > 2 && is_bool($arguments[2])) {
				$innerCall = $arguments[2];
				$shift++;
			}
		}

		// Process arguments into a set of models to include

		$arguments = array_slice($arguments, $shift);
		foreach($arguments as $index => $argument) {
			if (is_array($argument)) {
				if (!empty($argument)) {
					$arguments = array_merge($arguments, $argument);
				}

				unset($arguments[$index]);
			}
		}

		$models = array();
		if (!$innerCall) {
			$models = $this->__models($Model, $arguments, $this->settings[$Model->alias]['notices'], $this->settings[$Model->alias]['autoFields']);
			$recursive = -1;

			if (!empty($models)) {
				$recursive = $this->__recursivity($models);
			}
		} else if (!empty($arguments)) {
			$models = $arguments;
		}

		// Go through all models and run bindable on inner models

		foreach($models as $name => $children) {
			if (isset($Model->$name)) {
				if (isset($children['__settings__'])) {
					foreach($this->__bindings as $relation) {
						if (isset($Model->{$relation}[$name])) {
							if (!$reset) {
								$this->__backupAssociations($Model);
							}

							$Model->bindModel(array($relation => array(
								$name => array_merge($Model->{$relation}[$name], $children['__settings__'])
							)), $reset);
						}
					}

					unset($children['__settings__']);
				}

				// Run bindable on inner model

				if (!isset($Model->__backInnerAssociation)) {
					$Model->__backInnerAssociation = array();
				}

				$Model->__backInnerAssociation[] = $name;
				$this->restrict($Model->$name, $reset, true, $children);
			}
		}

		// Setup mandatory fields

		if (!$innerCall && $this->settings[$Model->alias]['autoFields'] && isset($models['__settings__'])) {
			if (!empty($models['__settings__']['fields'])) {
				if (empty($this->__fields[$Model->alias])) {
					$this->__fields[$Model->alias] = array();
				}
				$this->__fields[$Model->alias] = $models['__settings__']['fields'];
			}
			unset($models['__settings__']);
		}

		// Unbind unneeded models

		$unbind = array();
		$models = array_keys($models);
		$bindings = $Model->getAssociated();

		foreach($bindings as $bindingName => $relation) {
			if (!in_array($bindingName, $models)) {
				$unbind[$relation][] = $bindingName;
			}
		}

   		if (!empty($unbind)) {
			if (!$reset) {
				$this->__backupAssociations($Model);
			}
			$Model->unbindModel($unbind, $reset);
		}

		// Keep a reference that this model is the originator of a chain-bindable call

		if (!$innerCall && $reset) {
			$this->__runResetBindable[$Model->alias] = true;
		}

		// If specified, set this model's recursiveness level

		if (!$innerCall && $this->settings[$Model->alias]['recursive'] === true && $recursive !== null) {
			$Model->__backRecursive = $Model->recursive;
			$Model->recursive = $recursive;
		}

		return $recursive;
	}

	/**
	 * Resets all relations and inner model relations after calling restrict()
	 *
	 * @param object $Model	Model using the behavior
	 * @param boolean $resetOriginal Force resetting original associations that may have been set to not reset
	 * @access public
	 */
	function resetBindable(&$Model, $resetOriginal = false) {
		$innerAssociations = array();

		if (isset($Model->__backInnerAssociation)) {
			$innerAssociations = $Model->__backInnerAssociation;
			unset($Model->__backInnerAssociation);
		}

		if ($resetOriginal && !empty($Model->__backOriginalAssociation)) {
			$Model->__backAssociation = array_pop($Model->__backOriginalAssociation);
			if (empty($Model->__backOriginalAssociation)) {
				unset($Model->__backOriginalAssociation);
			}
		}

		if (empty($innerAssociations) && !empty($Model->__backAssociation)) {
			$innerAssociations = array();
			foreach($this->__bindings as $relation) {
				if (!empty($Model->__backAssociation[$relation])) {
					$innerAssociations = array_merge($innerAssociations, array_keys($Model->__backAssociation[$relation]));
				}
			}
		}

		if (isset($Model->__backAssociation)) {
			$Model->__resetAssociations();
		}

		if (isset($Model->__backRecursive)) {
			$Model->recursive = $Model->__backRecursive;
			unset($Model->__backRecursive);
		}

		if (isset($this->__fields[$Model->alias])) {
			unset($this->__fields[$Model->alias]);
		}

		foreach($innerAssociations as $currentModel) {
			$this->resetBindable($Model->$currentModel, $resetOriginal);
		}
	}

	/**
	 * Runs before a find() operation. Used to allow 'restrict' setting
	 * as part of the find call, like this:
	 *
	 * Model->find('all', array('restrict' => array('Model1', 'Model2')));
	 *
	 * Model->find('all', array('restrict' => array(
	 * 	'Model1' => array('Model11', 'Model12'),
	 * 	'Model2',
	 * 	'Model3' => array(
	 * 		'Model31' => 'Model311',
	 * 		'Model32',
	 * 		'Model33' => array('Model331', 'Model332')
	 * )));
	 *
	 * @param object $Model	Model using the behavior
	 * @param array $query Query parameters as set by cake
	 * @access public
	 */
	function beforeFind(&$Model, $query) {
		if (isset($query['restrict'])) {
			$reset = true;
			if (is_bool(end($query['restrict']))) {
				$reset = array_pop($query['restrict']);
			}
			$query = array_merge(compact('reset'), $query);
			$this->restrict($Model, $query['reset'], false, $query['restrict']);
		}

		if ($Model->findQueryType != 'list' && is_array($query['fields']) && !empty($this->__fields[$Model->alias]) && !empty($query['fields'])) {
			$query['fields'] = array_unique(array_merge($query['fields'], $this->__fields[$Model->alias]));
		}
		return $query;
	}

	/**
	 * Runs after a find() operation.
	 *
	 * @param object $Model	Model using the behavior
	 * @param array $results Results of the find operation.
	 * @access public
	 */
	function afterFind(&$Model, $results) {
		if (isset($this->__runResetBindable[$Model->alias]) && $this->__runResetBindable[$Model->alias]) {
			$this->resetBindable($Model);
			unset($this->__runResetBindable[$Model->alias]);
		}
	}

	/**
	 * Backup associations for a model right before a non-resettable binding
	 * operation.
	 *
	 * @param object $Model Model being processed
	 * @access private
	 */
	function __backupAssociations(&$Model) {
		$bindings = array();
		foreach($this->__bindings as $relation) {
			$bindings[$relation] = $Model->{$relation};
		}
		if (empty($Model->__backOriginalAssociation)) {
			$Model->__backOriginalAssociation = array();
		}
		$Model->__backOriginalAssociation[] = $bindings;
	}

	/**
	 * Get a list of models in the form: Model1 => array(Model2, ...), converting
	 * dot-notation arguments (i.e: Model1.Model2.Model3) to their depth-notation
	 * equivalent (i.e: Model1 => Model2 => Model3). The convertion is used for
	 * backwards compatibility with previous versions of bindable (expects).
	 *
	 * @param object $Model Model being processed
	 * @param array $arguments Set of arguments to convert
	 * @param boolean $notices Set to true to throw a notice when a binding does not exist
	 * @param boolean $autoFields Discover and add fields needed to fetch requested bindings
	 * @param boolean $inner Set to true to indicate inner call, false otherwise
	 * @return array Converted arguments
	 * @access private
	 */
	function __models(&$Model, $arguments, $notices = false, $autoFields = true, $inner = false) {
		$models = array();
		$bindings = $Model->getAssociated();
		$settings = array('conditions', 'fields', 'limit', 'offset', 'order');

		foreach($arguments as $key => $children) {
			$name = null;
			$setting = null;
			$settingValue = array();

			if (is_numeric($key) && !is_array($children)) {
				$name = $children;
				$children = array();
			} else if (!is_numeric($key)) {
				$name = $key;
			}

			if (!empty($name) && is_string($name) && !in_array($name, $settings) && $Model->hasField($name) && (!isset($Model->$name) || !is_object($Model->$name)) && (!isset($children['fields']) || !in_array($name, $children['fields']))) {
				$setting = 'fields';
				$settingValue = array($name);
			} else if (!empty($name) && in_array($name, $settings)) {
				$setting = $name;
				$settingValue = $children;
				$children = array();
			}

			if (!empty($setting)) {
				if ($setting == 'fields') {
					if (!is_array($children)) {
						$children = array($setting => array());
					} else if (!isset($children[$setting])) {
						$children[$setting] = array();
					}
					$settingValue = array_merge($children[$setting], ife(!is_array($settingValue), array($settingValue), $settingValue));
				}
				$models = Set::merge($models, array('__settings__' => array($setting => $settingValue)));
			} else if (!empty($name)) {
				if (!is_array($children) && $children != $key) {
					$children = array($children => array());
				}

				// Handle dot notation and in place list of fields

				if (strpos($name, '.') !== false) {
					$chain = explode('.', $name);
					$name = array_shift($chain);
					$children = array(join('.', $chain) => $children);

					if (isset($models[$name])) {
						$children = array_merge($children, $models[$name]);
					}
				}

				$fields = null;
				if (preg_match('/^(\w+)\(([^\)]+)\)$/i', $name, $matches)) {
					$name = $matches[1];
					$fields = preg_split('/,\s*/', $matches[2]);
				}

				if ($name != '*' && !isset($models[$name])) {
					$models[$name] = array();
				}

				// Do a processing of children and assign

				if ($name == '*') {
					$children = array_flip(array_keys($bindings));
					array_walk($children, create_function('&$item', '$item = array();'));
					$models = Set::merge($models, $children);
				} else if (isset($Model->$name) && is_object($Model->$name)) {
					if (!empty($fields)) {
						if (!isset($children['fields'])) {
							$children['fields'] = array();
						}
						$children['fields'] = array_merge($children['fields'], $fields);
					}
					$models[$name] = array_merge($models[$name], $this->__models($Model->$name, $children, $notices, $autoFields, true));
				} else if ($notices) {
					trigger_error(sprintf(__('%s.%s is not a valid binding', true), $Model->alias, $name), E_USER_NOTICE);
				}
			}
		}

		if (!$inner && $autoFields) {
			$models = $this->__fields($Model, $models);
		}

		return $models;
	}

	/**
	 * Compute mandatory fields for fetching required bindings.
	 *
	 * @param object $Model Model to start from
	 * @param array $models Bindings for this model
	 * @param array $mandatory Include these mandatory fields
	 * @param bool $inner Set to true if on inner call, false otherwise
	 * @return array Modified bindings with mandatory fields included
	 * @access private
	 */
	function __fields(&$Model, $models, $mandatory = array(), $inner = false) {
		$bindings = $Model->getAssociated();

		$fields = (!empty($mandatory) ? $mandatory : array());
		foreach($models as $name => $data) {
			if ($name == '__settings__' || empty($bindings[$name])) {
				continue;
			}
			$mandatory = array();
			$relation = $bindings[$name];
			switch($relation) {
				case 'belongsTo':
					$mandatory[] = $Model->$name->alias . '.' . $Model->$name->primaryKey;
					$fields[] = $Model->alias . '.' . $Model->{$relation}[$name]['foreignKey'];
					break;
				case 'hasOne':
				case 'hasMany':
					$mandatory[] = $Model->$name->alias . '.' . $Model->{$relation}[$name]['foreignKey'];
					$fields[] = $Model->alias . '.' . $Model->primaryKey;
					break;
				case 'hasAndBelongsToMany':
					$mandatory[] = $Model->$name->alias . '.' . $Model->$name->primaryKey;
					$fields[] = $Model->alias . '.' . $Model->primaryKey;
					break;
			}
			if (is_object($Model->$name)) {
				$models[$name] = $this->__fields($Model->$name, $models[$name], $mandatory, true);
			}
		}

		if ((!$inner && !empty($fields)) || (!empty($fields) && isset($models['__settings__']) && isset($models['__settings__']['fields']))) {
			if (!$inner) {
				foreach($models as $name => $data) {
					if ($name != '__settings__' && $bindings[$name] == 'belongsTo' && !empty($data['__settings__']) && !empty($data['__settings__']['fields'])) {
						$innerFields = $data['__settings__']['fields'];
						foreach($innerFields as $index => $field) {
							if (strpos($field, '.') === false) {
								$innerFields[$index] = $Model->$name->alias . '.' . $field;
							}
						}
						$fields = array_merge($fields, $innerFields);
					}
				}
			}
			if (!isset($models['__settings__'])) {
				$models['__settings__'] = array();
			}
			if (!isset($models['__settings__']['fields'])) {
				$models['__settings__']['fields'] = array();
			}
			$models['__settings__']['fields'] = array_unique(array_merge($models['__settings__']['fields'], $fields));
		}

		return $models;
	}

	/**
	 * Calculate the minimum recursivity required for fetching the bindings
	 *
	 * @param array $models Bindings
	 * @param bool $inner Set to true if on inner call, false otherwise
	 * @return int Recursivity level
	 * @access private
	 */
	function __recursivity($models, $inner = false) {
		if ($inner) {
			if (isset($models['__settings__'])) {
				unset($models['__settings__']);
			}
			foreach($models as $key => $value) {
				$models[$key] = $this->__recursivity($models[$key], true);
			}
			return $models;
		}

		return Set::countDim($this->__recursivity($models, true), true);
	}
}

?>