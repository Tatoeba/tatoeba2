<?php
/* SentenceAnnotation Fixture generated on: 2014-09-14 16:11:53 : 1410711113 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SentenceAnnotationFixture extends CakeTestFixture {
	public $name = 'SentenceAnnotation';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'sentence_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'meaning_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'text' => array('type' => 'text', 'null' => false, 'default' => NULL, 'length' => 2000),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'sentence_id' => array('column' => 'sentence_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	public $records = array(
		array(
			'id' => 1,
			'sentence_id' => 6,
			'meaning_id' => 1,
			'text' => 'その問題の根本原因は、現代の世界において、賢明な人々が猜疑心に満ちている一方で、愚かな人々が自信過剰であるということである。',
			'modified' => '2018-10-24 00:00:00',
			'user_id' => 1
		)
	);
}
