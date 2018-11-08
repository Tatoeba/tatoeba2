<?php
/* ReindexFlag Fixture generated on: 2015-11-27 23:54:27 : 1448668467 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ReindexFlagsFixture extends TestFixture {
	public $name = 'ReindexFlag';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'sentence_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'lang_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 3],
		'_indexes' => ['idx_sentence_id' => ['unique' => 0, 'columns' => 'sentence_id']],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
	);
}
