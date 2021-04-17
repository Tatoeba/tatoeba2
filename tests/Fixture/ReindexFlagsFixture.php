<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ReindexFlagsFixture extends TestFixture {
	public $name = 'ReindexFlag';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'sentence_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'lang' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 4],
		'indexed' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'type' => ['type' => 'string', 'null' => false],
		'_indexes' => [
			'idx_sentence_id' => ['type' => 'index', 'columns' => ['sentence_id'], 'length' => []],
		],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
	);
}
