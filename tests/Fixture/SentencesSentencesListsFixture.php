<?php
/* SentencesSentencesList Fixture generated on: 2014-09-14 16:11:58 : 1410711118 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SentencesSentencesListsFixture extends TestFixture {
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
		array(
			'id' => '3',
			'sentences_list_id' => '3',
			'sentence_id' => '50',
			'created' => '2018-04-08 02:08:51',
		),
		array(
			'id' => '4',
			'sentences_list_id' => '4',
			'sentence_id' => '48',
			'created' => '2018-10-10 10:10:09',
		),
		array(
			'id' => '5',
			'sentences_list_id' => '4',
			'sentence_id' => '53',
			'created' => '2018-10-10 10:10:09',
		),
		array(
			'id' => '6',
			'sentences_list_id' => '1',
			'sentence_id' => '55',
			'created' => '2018-10-13 00:43:42',
		),
		array(
			'id' => '7',
			'sentences_list_id' => '6',
			'sentence_id' => '20',
			'created' => '2018-10-13 00:43:42',
		),
		array( # Ghost link inserted on purpose
			'id' => '8',
			'sentences_list_id' => '999999',
			'sentence_id' => '20',
			'created' => '2018-10-13 01:23:45',
		)
	);
}
