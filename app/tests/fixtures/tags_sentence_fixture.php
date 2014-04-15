<?php
/* TagsSentence Fixture generated on: 2014-04-15 18:03:49 : 1397577829 */
class TagsSentenceFixture extends CakeTestFixture {
	var $name = 'TagsSentence';

	var $fields = array(
		'tag_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'sentence_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'added_time' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('user_id' => array('column' => 'user_id', 'unique' => 0), 'tag_id' => array('column' => 'tag_id', 'unique' => 0), 'sentence_id' => array('column' => 'sentence_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'tag_id' => '1',
			'user_id' => '1',
			'sentence_id' => '8',
			'added_time' => '2014-04-16 07:24:37',
		),
	);
}
