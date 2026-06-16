<?php
/* SentencesList Fixture generated on: 2014-04-15 02:32:40 : 1397521960 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SentencesListsFixture extends TestFixture {
	public $name = 'SentencesList';

	public $records = array(
		array(
			'id' => '1',
			'name' => 'Interesting French sentences',
			'user_id' => 7,
			'numberOfSentences' => 3,
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
		array(
			'id' => '5',
			'name' => 'Collaborative list',
			'user_id' => 1,
			'numberOfSentences' => 0,
			'created' => '2018-10-10 10:10:01',
			'modified' => '2018-10-10 10:10:09',
			'visibility' => 'public',
			'editable_by' => 'anyone'
		),
		array(
			'id' => '6',
			'name' => 'Inactive list',
			'user_id' => 7,
			'numberOfSentences' => 1,
			'created' => '2018-10-10 10:10:01',
			'modified' => '2018-10-10 10:10:09',
			'visibility' => 'public',
			'editable_by' => 'no_one'
		),
		array(
			'id' => '7',
			'name' => 'Public list that belongs to a deleted user',
			'user_id' => 10,
			'numberOfSentences' => 0,
			'created' => '2020-10-12 10:12:12',
			'modified' => '2020-10-12 10:12:12',
			'visibility' => 'public',
			'editable_by' => 'no_one'
		)
	);
}
