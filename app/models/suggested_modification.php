<?php
class SuggestedModification extends AppModel {

	var $name = 'SuggestedModification';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Sentence' => array('className' => 'Sentence',
								'foreignKey' => 'sentence_id',
								'conditions' => '',
								'fields' => '',
								'order' => ''
			)
	);

}
?>