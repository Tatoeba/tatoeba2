<?php
/* Transcription Fixture generated on: 2014-10-26 15:19:49 : 1414336789 */
class TranscriptionFixture extends CakeTestFixture {
	var $name = 'Transcription';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'script' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'text' => array('type' => 'text', 'null' => false, 'default' => NULL, 'length' => 10000),
		'dirty' => array('type' => 'boolean', 'null' => false, 'default' => NULL),
		'user_modified' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'unique_transcriptions' => array('column' => array('sentence_id', 'script'), 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'sentence_id' => 6,
			'parent_id' => '',
			'script' => 'Hrkt',
			'text' => 'その [問題|もんだい] の [根本|こんぽん] [原因|げんいん] は 、 [現代|げんだい] の [世界|せかい] において 、 [賢明|けんめい] な [人々|ひとびと] が [猜疑心|さいぎしん] に [満|み]ちている [一方|いっぽう] で、[愚|おろ]か な [人々|ひとびと] が [自信|じしん] [過剰|かじょう] で ある という こと で ある 。',
			'dirty' => 0,
			'user_modified' => 0,
			'created' => '2014-10-18 17:43:32',
			'modified' => '2014-10-18 17:43:32'
		),
		array(
			'id' => 2,
			'sentence_id' => 6,
			'parent_id' => 1,
			'script' => 'Latn',
			'text' => 'sono mondai no konpon gen\'in wa, gendai no sekai nioite, kenmei na hitobito ga saigishin ni michiteiru ippō de, oroka na hitobito ga jishin kajō de aru toiu koto de aru.',
			'dirty' => 0,
			'user_modified' => 0,
			'created' => '2014-10-18 17:43:32',
			'modified' => '2014-10-18 17:43:32'
		),
		array(
			'id' => 3,
			'sentence_id' => 10,
			'parent_id' => '',
			'script' => 'Hrkt',
			'text' => 'ちょっと [待|ま]って 。',
			'dirty' => 0,
			'user_modified' => 0,
			'created' => '2014-10-18 17:43:32',
			'modified' => '2014-10-18 17:43:32'
		),
	);
}
