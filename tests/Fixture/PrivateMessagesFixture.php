<?php
/**
 * PrivateMessage Fixture
 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class PrivateMessagesFixture extends TestFixture {

/**
 * Fields
 *
 * @var array
 */
    public $fields = array(
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'recpt' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'sender' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false],
		'user_id' => ['type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false],
		'date' => ['type' => 'datetime', 'null' => false, 'default' => null],
		'folder' => ['type' => 'string', 'null' => false, 'default' => 'Inbox', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'title' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'content' => ['type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'isnonread' => ['type' => 'integer', 'null' => false, 'default' => '1', 'length' => 4, 'unsigned' => false],
		'draft_recpts' => ['type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'sent' => ['type' => 'integer', 'null' => false, 'default' => '1', 'length' => 4, 'unsigned' => false],
		'_indexes' => ['idx_recpt' => ['unique' => 0, 'columns' => 'recpt']],
		'_constraints' => ['primary' => ['type' => 'primary', 'columns' => ['id']]],
		'_options' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB']
	);

    public $records = array(
        # PM sent and not yet read
        array(
            'id' => '1',
            'recpt' => '3',
            'sender' => '4',
            'user_id' => '3',
            'date' => '2010-10-13 01:27:38',
            'folder' => 'Inbox',
            'title' => 'Sentence #123',
            'content' => 'There is a problem with sentence #123.',
            'isnonread' => '1',
            'draft_recpts' => '',
            'sent' => '1'
        ),
        array(
            'id' => '2',
            'recpt' => '3',
            'sender' => '4',
            'user_id' => '4',
            'date' => '2010-10-13 01:27:38',
            'folder' => 'Sent',
            'title' => 'Sentence #123',
            'content' => 'There is a problem with sentence #123.',
            'isnonread' => '0',
            'draft_recpts' => '',
            'sent' => '1'
        ),
        # PM sent, read and trashed
        array(
            'id' => '3',
            'recpt' => '1',
            'sender' => '4',
            'user_id' => '1',
            'date' => '2015-04-02 16:41:01',
            'folder' => 'Inbox',
            'title' => 'The admin is evil!',
            'content' => 'blah blah blah',
            'isnonread' => '0',
            'draft_recpts' => '',
            'sent' => '1'
        ),
        array(
            'id' => '4',
            'recpt' => '1',
            'sender' => '4',
            'user_id' => '4',
            'date' => '2015-04-02 16:41:01',
            'folder' => 'Trash',
            'title' => 'The admin is evil!',
            'content' => 'blah blah blah',
            'isnonread' => '0',
            'draft_recpts' => '',
            'sent' => '1'
        ),
        # A draft
        array(
            'id' => '5',
            'recpt' => '0',
            'sender' => '4',
            'user_id' => '4',
            'date' => '2017-10-13 01:07:10',
            'folder' => 'Drafts',
            'title' => 'Feelings',
            'content' => 'I\'m worrying about Tom.',
            'isnonread' => '1',
            'draft_recpts' => 'advanced_contributor',
            'sent' => '0'
        ),
        # A system notification
        array(
            'id' => '6',
            'recpt' => '1',
            'sender' => '0',
            'user_id' => '1',
            'date' => '2018-09-10 17:41:59',
            'folder' => 'Inbox',
            'title' => 'Result of license switch to CC0 1.0',
            'content' => 'Changed the license of 29 sentences.',
            'isnonread' => '1',
            'draft_recpts' => '',
            'sent' => '1'
        ),
    );

}
