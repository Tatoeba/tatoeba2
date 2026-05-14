<?php
/**
 * UsersSentence Fixture
 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersSentencesFixture extends TestFixture {
/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'sentence_id' => 2,
			'correctness' => 1,
			'created' => '2018-10-24 00:00:00',
			'modified' => '2018-10-24 00:00:00',
			'dirty' => false
		)
	);

}
