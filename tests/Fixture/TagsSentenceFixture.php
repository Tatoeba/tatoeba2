<?php
/* TagsSentence Fixture generated on: 2014-09-14 16:12:00 : 1410711120 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TagsSentenceFixture extends TestFixture {
	public $name = 'TagsSentence';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'tag_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'user_id' => ['type' => 'integer', 'null' => true, 'default' => null],
		'sentence_id' => ['type' => 'integer', 'null' => true, 'default' => null],
		'added_time' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'_indexes' => ['user_id' => ['unique' => 0, 'columns' => 'user_id'], 'tag_id' => ['unique' => 0, 'columns' => 'tag_id'], 'sentence_id' => ['unique' => 0, 'columns' => 'sentence_id']],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM']
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
