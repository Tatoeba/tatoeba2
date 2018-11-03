<?php
/* SentencesSentencesList Fixture generated on: 2014-09-14 16:11:58 : 1410711118 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SentencesSentencesListFixture extends TestFixture {
	public $name = 'SentencesSentencesLists';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'sentences_list_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'index'),
		'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'list_id' => array('column' => array('sentences_list_id', 'sentence_id'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
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
