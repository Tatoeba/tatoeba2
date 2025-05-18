<?php
/* Tag Fixture generated on: 2014-09-14 16:11:59 : 1410711119 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TagsFixture extends TestFixture {
	public $name = 'Tag';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null],
		'internal_name' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'description' => ['type' => 'string', 'null' => true, 'default' => null, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'user_id' => ['type' => 'integer', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'nbrOfSentences' => ['type' => 'integer', 'null' => false, 'default' => '0'],
		'category_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
		'_indexes' => [
			'user_id' => ['type' => 'index', 'columns' => ['user_id'], 'length' => []],
			'nbr_sentences_idx' => ['type' => 'index', 'columns' => ['nbrOfSentences'], 'length' => []],
		],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM']
	);

	public $records = array(
		array(
			'id' => '1',
			'internal_name' => '@needs_native_check',
			'name' => '@needs native check',
			'description' => 'Sentences we wish to be checked by a native.',
			'user_id' => '2',
			'created' => '2013-04-14 13:10:02',
			'nbrOfSentences' => '1',
			'category_id' => null,
		),
		array(
			'id' => '2',
			'internal_name' => 'OK',
			'name' => 'OK',
			'description' => 'Sentences that are okay according to the author of the tag.',
			'user_id' => '1',
			'created' => '2014-02-13 10:46:09',
			'nbrOfSentences' => '1',
			'category_id' => null,
		),
		array(
			'id' => '3',
			'internal_name' => 'regional',
			'name' => 'regional',
			'description' => '',
			'user_id' => '1',
			'created' => '2014-02-13 10:46:09',
			'nbrOfSentences' => '1',
			'category_id' => null,
		),
	);
}
