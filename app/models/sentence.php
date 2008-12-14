<?php
class Sentence extends AppModel{
	var $name = 'Sentence';
	
	const MAX_CORRECTNESS = 6;
	
	var $validate = array(
		'lang' => array(
			'rule' => array('inList', array('de', 'en', 'es', 'fr', 'he', 'it', 'jp', 'ko', 'nl', 'ru', 'vn', 'zh'))
		),
		'text' => array(
			'rule' => array('minLength', '1')
		)
	);
	
	var $hasMany = array('SuggestedModification', 'SentenceLog', 'TranslationLog', 'Contribution', 'SentenceComment');
	
	var $hasAndBelongsToMany = array(
		'Translation' => array(
			'className' => 'Translation',
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
		),
		'InverseTranslation' => array(
			'className' => 'InverseTranslation',
			'joinTable' => 'sentences_translations',
			'foreignKey' => 'sentence_id',
			'associationForeignKey' => 'translation_id',
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