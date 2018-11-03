<?php
/* SentencesSentencesList Fixture generated on: 2014-09-14 16:11:58 : 1410711118 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SentencesSentencesListFixture extends TestFixture {
	public $name = 'SentencesSentencesLists';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'sentences_list_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'sentence_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'list_id' => ['type' => 'unique', 'columns' => ['sentences_list_id', 'sentence_id']]],
		'_options' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
		array(
			'id' => '1',
			'sentences_list_id' => '1',
			'sentence_id' => '4',
			'created' => '2018-03-14 12:15:13',
		),
		array(
			'id' => '2',
			'sentences_list_id' => '1',
			'sentence_id' => '8',
			'created' => '2018-03-14 12:15:18',
		),
	);
}
