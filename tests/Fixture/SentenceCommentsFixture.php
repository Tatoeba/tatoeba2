<?php
/* SentenceComment Fixture generated on: 2014-09-14 16:11:54 : 1410711114 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SentenceCommentsFixture extends TestFixture {
	public $name = 'SentenceComment';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'sentence_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'lang' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'text' => ['type' => 'binary', 'null' => false, 'default' => null],
		'user_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'hidden' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'_indexes' => [
			'sentence_id_idx' => ['type' => 'index', 'columns' => ['sentence_id'], 'length' => []],
			'user_id' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
			'created' => ['type' => 'index', 'columns' => ['created'], 'length' => []],
		],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM']
	);

	public $records = array(
		array(
			'id' => '1',
			'sentence_id' => '4',
			'lang' => NULL,
			'text' => 'Voilà une phrase pleine de bon sens.',
			'user_id' => '7',
			'created' => '2014-04-15 02:07:10',
			'modified' => '2014-04-15 02:07:10',
			'hidden' => 0
		),
		array(
			'id' => '2',
			'sentence_id' => '13',
			'lang' => NULL,
			'text' => 'Someone should delete this sentence.',
			'user_id' => '3',
			'created' => '2014-09-02 05:29:12',
			'modified' => '2014-09-02 05:29:12',
			'hidden' => 0
		),
		array(
			'id' => '3',
			'sentence_id' => '14',
			'lang' => NULL,
			'text' => 'Please, someone adopt this sentence!',
			'user_id' => '2',
			'created' => '2015-08-17 22:12:02',
			'modified' => '2015-08-17 22:12:02',
			'hidden' => 0
		),
		array(
			'id' => '4',
			'sentence_id' => '17',
			'lang' => NULL,
			'text' => 'A comment posted by the sentence owner.',
			'user_id' => '3',
			'created' => '2011-06-07 08:52:02',
			'modified' => '2011-06-07 08:52:02',
			'hidden' => 0
		),
		array(
			'id' => '5',
			'sentence_id' => '19',
			'lang' => NULL,
			'text' => 'Who knows? :)',
			'user_id' => '1',
			'created' => '2015-08-18 14:59:02',
			'modified' => '2015-08-18 15:00:30',
			'hidden' => 0
		),
		array(
			'id' => '6',
			'sentence_id' => '15',
			'lang' => NULL,
			'text' => 'A comment by a new member on an orphan sentence.',
			'user_id' => '9',
			'created' => '2020-02-20 02:20:02',
			'modified' => '2020-02-20 02:20:02',
			'hidden' => 0
		),
	);
}
