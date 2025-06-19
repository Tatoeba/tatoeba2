<?php
/* Wall Fixture generated on: 2014-04-15 16:50:14 : 1397573414 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class WallsFixture extends TestFixture {
	public $name = 'Wall';
	public $table = 'wall';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'owner' => ['type' => 'integer', 'null' => false, 'default' => null],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null],
		'date' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'title' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'content' => ['type' => 'binary', 'null' => false, 'default' => null],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null],
		'hidden' => ['type' => 'boolean', 'null' => false, 'default' => '0'],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]]
	);

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
	);
}
