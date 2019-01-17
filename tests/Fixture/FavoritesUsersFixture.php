<?php
/* FavoritesUser Fixture generated on: 2014-09-14 16:11:49 : 1410711109 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class FavoritesUsersFixture extends TestFixture {
	public $name = 'FavoritesUser';

	public $fields = array(
		'favorite_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'user_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['favorite_id']], 'favorite_id' => ['type' => 'unique', 'columns' => ['favorite_id', 'user_id']]],
		'_options' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM']
	);

	public $records = array(
		array(
			'favorite_id' => '4',
			'user_id' => '7',
		)
	);
}
