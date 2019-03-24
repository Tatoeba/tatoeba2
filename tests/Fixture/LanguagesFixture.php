<?php
/* Language Fixture generated on: 2015-05-12 10:28:59 : 1431426539 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class LanguagesFixture extends TestFixture {
	public $name = 'Language';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 3],
		'code' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 4, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'sentences' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10],
		'audio' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10],
		'group_1' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 2],
		'group_2' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 3],
		'group_3' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4],
		'group_4' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10],
		'level_0' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10],
		'level_1' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10],
		'level_2' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10],
		'level_3' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10],
		'level_4' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10],
		'level_5' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10],
		'level_unknown' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 10],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']], 'lang' => ['type' => 'unique', 'columns' => 'code']],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM']
	);

	public $records = array(
		array(
			'id' => '1',
			'code' => 'eng',
			'sentences' => '2',
			'audio' => '0',
			'group_1' => '0',
			'group_2' => '0',
			'group_3' => '0',
			'group_4' => '0',
			'level_0' => '0',
			'level_1' => '0',
			'level_2' => '0',
			'level_3' => '0',
			'level_4' => '0',
			'level_5' => '0',
			'level_unknown' => '0'
		),
		array(
			'id' => '2',
			'code' => 'cmn',
			'sentences' => '1',
			'audio' => '0',
			'group_1' => '0',
			'group_2' => '0',
			'group_3' => '0',
			'group_4' => '0',
			'level_0' => '0',
			'level_1' => '0',
			'level_2' => '0',
			'level_3' => '0',
			'level_4' => '0',
			'level_5' => '0',
			'level_unknown' => '0'
		),
		array(
			'id' => '3',
			'code' => 'spa',
			'sentences' => '1',
			'audio' => '1',
			'group_1' => '0',
			'group_2' => '0',
			'group_3' => '0',
			'group_4' => '0',
			'level_0' => '0',
			'level_1' => '0',
			'level_2' => '0',
			'level_3' => '0',
			'level_4' => '0',
			'level_5' => '0',
			'level_unknown' => '0'
		),
		array(
			'id' => '4',
			'code' => 'fra',
			'sentences' => '2',
			'audio' => '2',
			'group_1' => '0',
			'group_2' => '0',
			'group_3' => '0',
			'group_4' => '0',
			'level_0' => '0',
			'level_1' => '0',
			'level_2' => '0',
			'level_3' => '0',
			'level_4' => '0',
			'level_5' => '0',
			'level_unknown' => '0'
		),
		array(
			'id' => '5',
			'code' => 'deu',
			'sentences' => '1',
			'audio' => '0',
			'group_1' => '0',
			'group_2' => '0',
			'group_3' => '0',
			'group_4' => '0',
			'level_0' => '0',
			'level_1' => '0',
			'level_2' => '0',
			'level_3' => '0',
			'level_4' => '0',
			'level_5' => '0',
			'level_unknown' => '0'
		),
		array(
			'id' => '6',
			'code' => 'jpn',
			'sentences' => '1',
			'audio' => '0',
			'group_1' => '0',
			'group_2' => '0',
			'group_3' => '0',
			'group_4' => '0',
			'level_0' => '0',
			'level_1' => '0',
			'level_2' => '0',
			'level_3' => '0',
			'level_4' => '0',
			'level_5' => '0',
			'level_unknown' => '0'
		),
		array(
			'id' => '7',
			'code' => 'yue',
			'sentences' => '1',
			'audio' => '0',
			'group_1' => '0',
			'group_2' => '0',
			'group_3' => '0',
			'group_4' => '0',
			'level_0' => '0',
			'level_1' => '0',
			'level_2' => '0',
			'level_3' => '0',
			'level_4' => '0',
			'level_5' => '0',
			'level_unknown' => '0'
		),
	);
}
