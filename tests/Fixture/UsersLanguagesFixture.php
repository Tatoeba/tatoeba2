<?php
/* UsersLanguage Fixture generated on: 2015-05-02 14:16:52 : 1430576212 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersLanguagesFixture extends TestFixture {
	public $name = 'UsersLanguage';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'of_user_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'by_user_id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'language_code' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'level' => ['type' => 'integer', 'null' => true, 'default' => '0', 'length' => 2],
		'details' => ['type' => 'binary', 'null' => false, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'user_lang' => ['type' => 'unique', 'columns' => ['of_user_id', 'by_user_id', 'language_code']]],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
		array(
			'id' => 1,
			'of_user_id' => 4,
			'by_user_id' => 4,
			'language_code' => 'jpn',
			'level' => 1,
			'details' => '',
			'created' => '2018-10-31 00:00:00',
			'modified' => '2018-10-31 00:00:00'
		),
		array(
			'id' => 2,
			'of_user_id' => 4,
			'by_user_id' => 4,
			'language_code' => 'fra',
			'level' => 5,
			'details' => '',
			'created' => '2018-10-31 00:00:00',
			'modified' => '2018-10-31 00:00:00'
		),
		array(
			'id' => 3,
			'of_user_id' => 7,
			'by_user_id' => 7,
			'language_code' => 'jpn',
			'level' => 5,
			'details' => '',
			'created' => '2018-10-31 00:00:00',
			'modified' => '2018-10-31 00:00:00'
		),
		array(
			'id' => 4,
			'of_user_id' => 3,
			'by_user_id' => 3,
			'language_code' => 'fra',
			'level' => 5,
			'details' => '',
			'created' => '2018-10-31 00:00:00',
			'modified' => '2018-10-31 00:00:00'
		),
	);
}
