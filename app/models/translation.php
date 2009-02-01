<?php
class Translation extends AppModel{
	var $name = 'Translation';
	var $useTable = 'sentences';
	
	var $hasAndBelongsToMany = array(
		'IndirectTranslation' => array(
			'className' => 'IndirectTranslation',
			'joinTable' => 'sentences_translations',
			'foreignKey' => 'translation_id',
			'associationForeignKey' => 'sentence_id',
			'conditions' => '',
			'order' => '',
			'limit' => '',
			'unique' => true,
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);
}
?>