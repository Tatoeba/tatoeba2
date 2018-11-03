<?php
/* WallThreadsLastMessage Fixture generated on: 2014-04-15 17:08:46 : 1397574526 */
namespace App\Test\Fixture;

class WallThreadFixture extends CakeTestFixture {
	public $name = 'WallThread';
	public $table = 'wall_threads_last_message';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'last_message_date' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
	);

	public $records = array(
		array(
			'id' => '1',
			'last_message_date' => '2014-04-15 16:38:36'
		),
	);
}
