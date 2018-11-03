<?php
/* SentenceComment Fixture generated on: 2014-09-14 16:11:54 : 1410711114 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SentenceCommentFixture extends CakeTestFixture {
	public $name = 'SentenceComment';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'lang' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'text' => array('type' => 'binary', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'hidden' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'sentence_id_idx' => array('column' => 'sentence_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
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
	);
}
