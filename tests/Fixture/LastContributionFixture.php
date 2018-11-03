<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class LastContributionFixture extends TestFixture {

	public $fields = array(
		'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'sentence_lang' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'translation_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'translation_lang' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'script' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'text' => array('type' => 'text', 'null' => false, 'default' => null, 'length' => 1500),
		'action' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 6, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'datetime' => array('type' => 'datetime', 'null' => false, 'default' => null, 'key' => 'index'),
		'ip' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 15, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 8, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true, 'key' => 'primary'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'id_desc' => array('column' => 'id', 'unique' => 1),
			'sentence_id' => array('column' => 'sentence_id', 'unique' => 0),
			'datetime' => array('column' => 'datetime', 'unique' => 0),
			'user_id' => array('column' => 'user_id', 'unique' => 0),
			'sentence_lang' => array('column' => array('sentence_lang', 'type'), 'unique' => 0),
			'translation_id_idx' => array('column' => 'translation_id', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
	);
}
