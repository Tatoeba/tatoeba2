<?php
/* Wall Fixture generated on: 2014-04-15 16:50:14 : 1397573414 */
class WallFixture extends CakeTestFixture {
	public $name = 'Wall';
	public $table = 'wall';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'owner' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'date' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'content' => array('type' => 'binary', 'null' => false, 'default' => NULL),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'hidden' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
	);

	public $records = array(
		array(
			'id' => '1',
			'owner' => '7',
			'parent_id' => NULL,
			'date' => '2014-04-15 16:37:11',
			'title' => '',
			'content' => 'When will the next version of Tatoeba be released?',
			'lft' => '1',
			'rght' => '4',
			'hidden' => 0,
			'modified' => '2014-04-15 16:37:12'
		),
		array(
			'id' => '2',
			'owner' => '1',
			'parent_id' => '1',
			'date' => '2014-04-15 16:38:36',
			'title' => '',
			'content' => 'When it’s done.',
			'lft' => '2',
			'rght' => '3',
			'hidden' => 0,
			'modified' => '2014-04-15 16:38:36'
		),
	);
}
