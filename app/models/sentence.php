<?php
class Sentence extends AppModel{
	var $name = 'Sentence';
	
	const MAX_CORRECTNESS = 6;
	
	var $validate = array(
		'lang' => array(
			'rule' => array('inList', array('ar', 'de', 'en', 'es', 'fr', 'he', 'it', 'id', 'jp', 'ko', 'nl', 'pt', 'ru', 'vn', 'zh', null))
		),
		'text' => array(
			'rule' => array('minLength', '1')
		)
	);
	
	var $hasMany = array('Contribution', 'SentenceComment');
	
	var $belongsTo = array('User');
	
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
	
	function afterSave($created){
		if(isset($this->data['Sentence']['text'])){
			$whoWhenWhere = array(
				  'user_id' => $this->data['Sentence']['user_id']
				, 'datetime' => date("Y-m-d H:i:s")
				, 'ip' => $_SERVER['REMOTE_ADDR']
			);
			
			$data['Contribution'] = $whoWhenWhere;
			$data['Contribution']['sentence_id'] = $this->id;
			$data['Contribution']['sentence_lang'] = $this->data['Sentence']['lang'];
			$data['Contribution']['text'] = $this->data['Sentence']['text'];
			$data['Contribution']['type'] = 'sentence';
			
			if($created){
				$data['Contribution']['action'] = 'insert';
				
				if(isset($this->data['Translation'])){
					// Translation logs
					$data2['Contribution'] = $whoWhenWhere;
					$data2['Contribution']['sentence_id'] = $this->data['Translation']['Translation'][0];
					$data2['Contribution']['sentence_lang'] = $this->data['Sentence']['sentence_lang'];
					$data2['Contribution']['translation_id'] = $this->id;
					$data2['Contribution']['translation_lang'] = $this->data['Sentence']['lang'];
					$data2['Contribution']['action'] = 'insert';
					$data2['Contribution']['type'] = 'link';
					$contributions[] = $data2;
				}
				if(isset($this->data['InverseTranslation'])){
					// Inverse translation logs
					$data2['Contribution'] = $whoWhenWhere;
					$data2['Contribution']['sentence_id'] = $this->id;
					$data2['Contribution']['sentence_lang'] = $this->data['Sentence']['lang'];
					$data2['Contribution']['translation_id'] = $this->data['Translation']['Translation'][0];
					$data2['Contribution']['translation_lang'] = $this->data['Sentence']['sentence_lang'];
					$data2['Contribution']['action'] = 'insert';
					$data2['Contribution']['type'] = 'link';
					$contributions[] = $data2;
				}
				if(isset($contributions)){
					$this->Contribution->saveAll($contributions);
				}
				
			}else{
				$data['Contribution']['action'] = 'update';
			}
			$this->Contribution->save($data);
		}
	}
	
	function afterDelete(){
		$data['Contribution']['sentence_id'] = $this->data['Sentence']['id'];
		$data['Contribution']['sentence_lang'] = $this->data['Sentence']['lang'];
		$data['Contribution']['text'] = $this->data['Sentence']['text'];
		$data['Contribution']['action'] = 'delete';
		$data['Contribution']['user_id'] = $this->data['User']['id'];
		$data['Contribution']['datetime'] = date("Y-m-d H:i:s");
		$data['Contribution']['ip'] = $_SERVER['REMOTE_ADDR'];
		$data['Contribution']['type'] = 'sentence';
		$this->Contribution->save($data);
		
		foreach($this->data['Translation'] as $translation){
			$data2['Contribution']['sentence_id'] = $this->data['Sentence']['id'];
			$data2['Contribution']['sentence_lang'] = $this->data['Sentence']['lang'];
			$data2['Contribution']['translation_id'] = $translation['id'];
			$data2['Contribution']['translation_lang'] = $translation['lang'];
			$data2['Contribution']['action'] = 'delete';
			$data2['Contribution']['user_id'] = $this->data['User']['id'];
			$data2['Contribution']['datetime'] = date("Y-m-d H:i:s");
			$data2['Contribution']['ip'] = $_SERVER['REMOTE_ADDR'];
			$data2['Contribution']['type'] = 'link';
			$contributions[] = $data2;
			
			$data2['Contribution']['sentence_id'] = $translation['id'];
			$data2['Contribution']['sentence_lang'] = $translation['lang'];
			$data2['Contribution']['translation_id'] = $this->data['Sentence']['id'];
			$data2['Contribution']['translation_lang'] = $this->data['Sentence']['lang'];
			$data2['Contribution']['action'] = 'delete';
			$data2['Contribution']['user_id'] = $this->data['User']['id'];
			$data2['Contribution']['datetime'] = date("Y-m-d H:i:s");
			$data2['Contribution']['ip'] = $_SERVER['REMOTE_ADDR'];
			$data2['Contribution']['type'] = 'link';
			$contributions[] = $data2;
		}
		if(isset($contributions)){
			$this->Contribution->saveAll($contributions);
		}
	}
}
?>