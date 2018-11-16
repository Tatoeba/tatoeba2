<?php
/* ReindexFlag Fixture generated on: 2015-11-27 23:54:27 : 1448668467 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ReindexFlagsFixture extends TestFixture {
	public $name = 'ReindexFlag';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'sentence_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'lang' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 4],
		'_indexes' => [
			'idx_sentence_id' => ['type' => 'index', 'columns' => ['sentence_id'], 'length' => []],
		],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
	);
}
