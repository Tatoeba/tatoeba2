<?php
/* Aro Fixture generated on: 2015-02-04 02:32:22 : 1423017142 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class AroFixture extends CakeTestFixture {
	public $name = 'Aro';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'index', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL, 'key' => 'index', 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'idx_aros_lft_rght' => array('column' => array('lft', 'rght'), 'unique' => 0), 'idx_aros_alias' => array('column' => 'alias', 'unique' => 0), 'idx_aros_model_foreign_key' => array('column' => array('model', 'foreign_key'), 'unique' => 0)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	public $records = array(
		array(
			'id' => '1',
			'parent_id' => NULL,
			'model' => 'Group',
			'foreign_key' => '1',
			'alias' => 'group_admin',
			'lft' => '1',
			'rght' => '4'
		),
		array(
			'id' => '2',
			'parent_id' => NULL,
			'model' => 'Group',
			'foreign_key' => '2',
			'alias' => 'group_moderator',
			'lft' => '5',
			'rght' => '8'
		),
		array(
			'id' => '3',
			'parent_id' => NULL,
			'model' => 'Group',
			'foreign_key' => '3',
			'alias' => 'group_trusted_user',
			'lft' => '9',
			'rght' => '12'
		),
		array(
			'id' => '4',
			'parent_id' => NULL,
			'model' => 'Group',
			'foreign_key' => '4',
			'alias' => 'group_user',
			'lft' => '13',
			'rght' => '18'
		),
		array(
			'id' => '5',
			'parent_id' => NULL,
			'model' => 'Group',
			'foreign_key' => '5',
			'alias' => 'group_inactive',
			'lft' => '19',
			'rght' => '22'
		),
		array(
			'id' => '6',
			'parent_id' => NULL,
			'model' => 'Group',
			'foreign_key' => '6',
			'alias' => 'group_spammer',
			'lft' => '23',
			'rght' => '26'
		),
		array(
			'id' => '7',
			'parent_id' => '1',
			'model' => 'User',
			'foreign_key' => '1',
			'alias' => 'user_admin',
			'lft' => '2',
			'rght' => '3'
		),
		array(
			'id' => '8',
			'parent_id' => '2',
			'model' => 'User',
			'foreign_key' => '2',
			'alias' => 'user_moderator',
			'lft' => '6',
			'rght' => '7'
		),
		array(
			'id' => '9',
			'parent_id' => '3',
			'model' => 'User',
			'foreign_key' => '3',
			'alias' => 'user_trusted_user',
			'lft' => '10',
			'rght' => '11'
		),
		array(
			'id' => '10',
			'parent_id' => '4',
			'model' => 'User',
			'foreign_key' => '4',
			'alias' => 'user_user',
			'lft' => '14',
			'rght' => '15'
		),
		array(
			'id' => '11',
			'parent_id' => '5',
			'model' => 'User',
			'foreign_key' => '5',
			'alias' => 'user_inactive',
			'lft' => '20',
			'rght' => '21'
		),
		array(
			'id' => '12',
			'parent_id' => '6',
			'model' => 'User',
			'foreign_key' => '6',
			'alias' => 'user_spammer',
			'lft' => '24',
			'rght' => '25'
		),
		array(
			'id' => '13',
			'parent_id' => '4',
			'model' => 'User',
			'foreign_key' => '7',
			'alias' => NULL,
			'lft' => '16',
			'rght' => '17'
		),
	);
}
