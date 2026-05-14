<?php
/**
 * PrivateMessage Fixture
 */
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class PrivateMessagesFixture extends TestFixture {
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
        # A deleted draft
        array(
            'id' => '7',
            'recpt' => '1',
            'sender' => '4',
            'user_id' => '4',
            'date' => '2018-10-12 01:07:10',
            'folder' => 'Trash',
            'title' => 'Draft',
            'content' => 'Deleted.',
            'isnonread' => '1',
            'draft_recpts' => 'advanced_contributor',
            'sent' => '0'
        ),
        # A deleted message from Inbox
        array(
            'id' => '8',
            'recpt' => '4',
            'sender' => '1',
            'user_id' => '4',
            'date' => '2018-10-13 01:07:10',
            'folder' => 'Trash',
            'title' => 'Hello',
            'content' => 'How are you?',
            'isnonread' => '0',
            'draft_recpts' => '',
            'sent' => '1'
        ),
    );

}
