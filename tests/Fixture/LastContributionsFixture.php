<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class LastContributionsFixture extends TestFixture {

	public $fields = array(
		'sentence_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'sentence_lang' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'translation_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false],
		'translation_lang' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'script' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'text' => ['type' => 'text', 'null' => false, 'default' => null, 'length' => 1500],
		'action' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 6, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'user_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false],
		'datetime' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'ip' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 15, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'],
		'type' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 8, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true],
		'_indexes' => [
			'sentence_id' => ['type' => 'index', 'columns' => ['sentence_id'], 'length' => []],
			'datetime' => ['type' => 'index', 'columns' => ['datetime'], 'length' => []],
			'user_id' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
			'sentence_lang' => ['type' => 'index', 'columns' => ['sentence_lang', 'type'], 'length' => []],
			'translation_id_idx' => ['type' => 'index', 'columns' => ['translation_id'], 'length' => []],
		],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'id_desc' => ['type' => 'unique', 'columns' => 'id']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
	);
}
