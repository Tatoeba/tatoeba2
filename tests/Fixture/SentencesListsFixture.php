<?php
/* SentencesList Fixture generated on: 2014-04-15 02:32:40 : 1397521960 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SentencesListsFixture extends TestFixture {
	public $name = 'SentencesList';

	public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 450, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
		'user_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false],
		'numberOfSentences' => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 8, 'unsigned' => true],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'visibility' => ['type' => 'string', 'null' => false, 'default' => 'unlisted', 'length' => 10, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
		'editable_by' => ['type' => 'string', 'null' => false, 'default' => 'creator', 'length' => 10, 'collate' => 'utf8_unicode_ci', 'charset' => 'utf8'],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
	);

	public $records = array(
		array(
			'id' => '1',
			'name' => 'Interesting French sentences',
			'user_id' => 7,
			'numberOfSentences' => 2,
			'created' => '2014-04-15 00:54:01',
			'modified' => '2014-04-15 00:54:12',
			'visibility' => 'unlisted',
			'editable_by' => 'creator'
		),
		array(
			'id' => '2',
			'name' => 'Public list',
			'user_id' => 7,
			'numberOfSentences' => 1,
			'created' => '2018-04-15 00:54:01',
			'modified' => '2018-04-15 00:54:12',
			'visibility' => 'public',
			'editable_by' => 'creator'
		),
		array(
			'id' => '3',
			'name' => 'Private list',
			'user_id' => 7,
			'numberOfSentences' => 1,
			'created' => '2018-04-15 00:54:01',
			'modified' => '2018-04-15 00:54:12',
			'visibility' => 'private',
			'editable_by' => 'creator'
		),
		array(
			'id' => '4',
			'name' => 'Sentences to switch to CC0',
			'user_id' => 4,
			'numberOfSentences' => 1,
			'created' => '2018-10-10 10:10:01',
			'modified' => '2018-10-10 10:10:09',
			'visibility' => 'private',
			'editable_by' => 'creator'
		),
	);
}
