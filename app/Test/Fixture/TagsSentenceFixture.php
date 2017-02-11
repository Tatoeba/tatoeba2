<?php
/* TagsSentence Fixture generated on: 2014-09-14 16:12:00 : 1410711120 */
class TagsSentenceFixture extends CakeTestFixture {
	public $name = 'TagsSentence';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'tag_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'sentence_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'added_time' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_id' => array('column' => 'user_id', 'unique' => 0), 'tag_id' => array('column' => 'tag_id', 'unique' => 0), 'sentence_id' => array('column' => 'sentence_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'tag_id' => '1',
			'user_id' => '1',
			'sentence_id' => '8',
			'added_time' => '2014-04-16 07:24:37',
		),
		array(
			'tag_id' => '2',
			'user_id' => '3',
			'sentence_id' => '2',
			'added_time' => '2014-02-13 10:46:09',
		),
		array(
			'tag_id' => '3',
			'user_id' => '1',
			'sentence_id' => '8',
			'added_time' => '2014-02-13 10:46:09',
		),
	);
}
