<?php
/* Tag Fixture generated on: 2014-09-14 16:11:59 : 1410711119 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TagFixture extends CakeTestFixture {
	public $name = 'Tag';

	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'internal_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'nbrOfSentences' => array('type' => 'integer', 'null' => false, 'default' => '0', 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_id' => array('column' => 'user_id', 'unique' => 0), 'nbr_sentences_idx' => array('column' => 'nbrOfSentences', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
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
		),
		array(
			'id' => '2',
			'internal_name' => 'OK',
			'name' => 'OK',
			'description' => 'Sentences that are okay according to the author of the tag.',
			'user_id' => '1',
			'created' => '2014-02-13 10:46:09',
			'nbrOfSentences' => '1',
		),
		array(
			'id' => '3',
			'internal_name' => 'regional',
			'name' => 'regional',
			'description' => '',
			'user_id' => '1',
			'created' => '2014-02-13 10:46:09',
			'nbrOfSentences' => '1',
		),
	);
}
