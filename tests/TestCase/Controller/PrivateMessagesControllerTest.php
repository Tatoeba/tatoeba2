<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

class PrivateMessagesControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.aros',
        'app.acos',
        'app.aros_acos',
        'app.private_messages',
        'app.sentences',
        'app.users',
        'app.users_languages',
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/private_messages/index', null, '/eng/users/login?redirect=%2Feng%2Fprivate_messages%2Findex' ],
            [ '/eng/private_messages/index', 'contributor', '/eng/private_messages/folder/Inbox' ],
            [ '/eng/private_messages/folder/Inbox', 'contributor', true ],
            [ '/eng/private_messages/folder/Inbox/all', 'contributor', true ],
            [ '/eng/private_messages/folder/Inbox/read', 'contributor', true ],
            [ '/eng/private_messages/folder/Inbox/unread', 'contributor', true ],
            [ '/eng/private_messages/folder/Drafts', 'contributor', true ],
            [ '/eng/private_messages/folder/Drafts/all', 'contributor', true ],
            [ '/eng/private_messages/folder/Drafts/read', 'contributor', true ],
            [ '/eng/private_messages/folder/Drafts/unread', 'contributor', true ],
            [ '/eng/private_messages/folder/Trash', 'contributor', true ],
            [ '/eng/private_messages/folder/Trash/all', 'contributor', true ],
            [ '/eng/private_messages/folder/Trash/read', 'contributor', true ],
            [ '/eng/private_messages/folder/Trash/unread', 'contributor', true ],
            [ '/eng/private_messages/show/1', null, '/eng/users/login?redirect=%2Feng%2Fprivate_messages%2Fshow%2F1' ],
            [ '/eng/private_messages/show/1', 'contributor', '/eng/private_messages/folder/Inbox' ],
            [ '/eng/private_messages/show/1', 'advanced_contributor', true ],
            [ '/eng/private_messages/show/1', 'admin', '/eng/private_messages/folder/Inbox' ],
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
        $this->post('/eng/private_messages/send');
        $this->assertRedirect('/eng/users/login');
    }

    public function testSendDraft() {
        $this->logInAs('contributor');
        $this->post('/eng/private_messages/send', [
            'submitType' => 'saveDraft',
            'recipients' => 'admin',
            'title' => 'Hello',
            'content' => 'Hello world!',
        ]);
        $this->assertRedirect('/eng/private_messages/folder/Drafts');
    }

    public function testSendMessage() {
        $this->logInAs('contributor');
        $this->post('/eng/private_messages/send', [
            'submitType' => 'send',
            'recipients' => 'admin',
            'title' => 'Hello',
            'content' => 'Hello world!',
        ]);
        $this->assertRedirect('/eng/private_messages/folder/Sent');
    }
}
