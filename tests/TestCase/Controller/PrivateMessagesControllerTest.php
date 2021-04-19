<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class PrivateMessagesControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.private_messages',
        'app.sentences',
        'app.users',
        'app.users_languages',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/private_messages/index', null, '/en/users/login?redirect=%2Fen%2Fprivate_messages%2Findex' ],
            [ '/en/private_messages/index', 'contributor', '/en/private_messages/folder/Inbox' ],
            [ '/en/private_messages/folder/Inbox', 'contributor', true ],
            [ '/en/private_messages/folder/Inbox/all', 'contributor', true ],
            [ '/en/private_messages/folder/Inbox/read', 'contributor', true ],
            [ '/en/private_messages/folder/Inbox/unread', 'contributor', true ],
            [ '/en/private_messages/folder/Drafts', 'contributor', true ],
            [ '/en/private_messages/folder/Drafts/all', 'contributor', true ],
            [ '/en/private_messages/folder/Drafts/read', 'contributor', true ],
            [ '/en/private_messages/folder/Drafts/unread', 'contributor', true ],
            [ '/en/private_messages/folder/Trash', 'contributor', true ],
            [ '/en/private_messages/folder/Trash/all', 'contributor', true ],
            [ '/en/private_messages/folder/Trash/read', 'contributor', true ],
            [ '/en/private_messages/folder/Trash/unread', 'contributor', true ],
            [ '/en/private_messages/show/1', null, '/en/users/login?redirect=%2Fen%2Fprivate_messages%2Fshow%2F1' ],
            [ '/en/private_messages/show/1', 'contributor', '/en/private_messages/folder/Inbox' ],
            [ '/en/private_messages/show/1', 'advanced_contributor', true ],
            [ '/en/private_messages/show/1', 'admin', '/en/private_messages/folder/Inbox' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function testSendAsGuest() {
        $this->enableCsrfToken();
        $this->post('/en/private_messages/send');
        $this->assertRedirect('/en/users/login');
    }

    public function testSendDraft() {
        $this->logInAs('contributor');
        $this->post('/en/private_messages/send', [
            'submitType' => 'saveDraft',
            'recipients' => 'admin',
            'title' => 'Hello',
            'content' => 'Hello world!',
        ]);
        $this->assertRedirect('/en/private_messages/folder/Drafts');
    }

    public function testSendMessage() {
        $this->logInAs('contributor');
        $this->post('/en/private_messages/send', [
            'submitType' => 'send',
            'recipients' => 'admin',
            'title' => 'Hello',
            'content' => 'Hello world!',
        ]);
        $this->assertRedirect('/en/private_messages/folder/Sent');
    }
}
