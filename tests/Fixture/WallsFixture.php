<?php
/* Wall Fixture generated on: 2014-04-15 16:50:14 : 1397573414 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class WallsFixture extends TestFixture {
	public $name = 'Wall';
	public $table = 'wall';

	public $records = array(
		array(
			'id' => '1',
			'owner' => '7',
			'parent_id' => NULL,
			'date' => '2014-04-15 16:37:11',
			'title' => '',
			'content' => 'When will the next version of Tatoeba be released?',
			'lft' => '1',
			'rght' => '4',
			'hidden' => 0,
			'modified' => '2014-04-15 16:37:12'
		),
		array(
			'id' => '2',
			'owner' => '1',
			'parent_id' => '1',
			'date' => '2014-04-15 16:38:36',
			'title' => '',
			'content' => 'When it’s done.',
			'lft' => '2',
			'rght' => '3',
			'hidden' => 0,
			'modified' => '2014-04-15 16:38:36'
		),
		array(
			'id' => '3',
			'owner' => '7',
			'parent_id' => NULL,
			'date' => '2025-06-19 12:33:44',
			'title' => '',
			'content' => 'Standalone post',
			'lft' => '5',
			'rght' => '6',
			'hidden' => 0,
			'modified' => '2025-06-19 12:33:44'
		),
		array(
			'id' => '4',
			'owner' => '9',
			'parent_id' => NULL,
			'date' => '2025-07-08 09:10:11',
			'title' => '',
			'content' => 'Standalone post by new member',
			'lft' => '7',
			'rght' => '8',
			'hidden' => 0,
			'modified' => '2025-07-08 09:10:11'
		),
		array(
			'id' => '5',
			'owner' => '9',
			'parent_id' => NULL,
			'date' => '2025-08-09 10:11:12',
			'title' => '',
			'content' => 'Standalone hidden post by new member',
			'lft' => '9',
			'rght' => '10',
			'hidden' => 1,
			'modified' => '2025-08-09 10:11:12'
		),
	);
}
