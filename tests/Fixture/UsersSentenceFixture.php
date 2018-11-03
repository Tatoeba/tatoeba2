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
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'user_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'sentence_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'correctness' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 1, 'unsigned' => false],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'dirty' => ['type' => 'boolean', 'null' => true, 'default' => '0'],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'user_sentence' => ['type' => 'unique', 'columns' => ['user_id', 'sentence_id']]],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
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
