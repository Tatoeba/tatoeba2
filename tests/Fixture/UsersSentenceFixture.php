<?php
/**
 * UsersSentence Fixture
 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersSentenceFixture extends TestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'correctness' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 1, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'dirty' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'user_sentence' => array('column' => array('user_id', 'sentence_id'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'sentence_id' => 2,
			'correctness' => 1,
			'created' => '2018-10-24 00:00:00',
			'modified' => '2018-10-24 00:00:00',
			'dirty' => false
		)
	);

}
