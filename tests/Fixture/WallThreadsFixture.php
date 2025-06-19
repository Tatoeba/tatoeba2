<?php
/* WallThreadsLastMessage Fixture generated on: 2014-04-15 17:08:46 : 1397574526 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class WallThreadsFixture extends TestFixture {
	public $name = 'WallThread';
	public $table = 'wall_threads_last_message';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'last_message_date' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
	);

	public $records = array(
		array(
			'id' => '1',
			'last_message_date' => '2014-04-15 16:38:36'
		),
		array(
			'id' => '3',
			'last_message_date' => '2025-06-19 12:33:44'
		),
	);
}
