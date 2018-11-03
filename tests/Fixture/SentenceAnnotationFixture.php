<?php
/* SentenceAnnotation Fixture generated on: 2014-09-14 16:11:53 : 1410711113 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SentenceAnnotationFixture extends TestFixture {
	public $name = 'SentenceAnnotation';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
		'sentence_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'meaning_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'text' => ['type' => 'text', 'null' => false, 'default' => null, 'length' => 2000],
		'modified' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'user_id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10],
		'_indexes' => ['sentence_id' => ['unique' => 0, 'columns' => 'sentence_id']],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
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
