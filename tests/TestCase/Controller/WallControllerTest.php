<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\IntegrationTestCase;

class WallControllerTest extends IntegrationTestCase {
    use EmailTrait;
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.PrivateMessages',
        'app.Walls',
        'app.WallThreads',
        'app.Users',
        'app.UsersLanguages',
        'app.WikiArticles',
    ];

    public function setUp(): void {
        parent::setUp();
        Configure::write('Tatoeba.minOutboundLinksTriggeringAutoban', 100);
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/wall/index', null, true ],
            [ '/en/wall/index', 'contributor', true ],
            [ '/en/wall/edit/1', null, '/en/users/login?redirect=%2Fen%2Fwall%2Fedit%2F1' ],
            [ '/en/wall/edit/1', 'contributor', '/en/wall/index' ],
            [ '/en/wall/edit/1', 'kazuki', true ], // author of message #1
            [ '/en/wall/edit/1', 'advanced_contributor', '/en/wall/index' ],
            [ '/en/wall/edit/1', 'corpus_maintainer', '/en/wall/index' ],
            [ '/en/wall/edit/1', 'admin', true ],
            [ '/en/wall/edit/9999999999', 'contributor', 'http://localhost/referer' ],
            [ '/en/wall/delete_message/1', null, '/en/users/login?redirect=%2Fen%2Fwall%2Fdelete_message%2F1' ],
            [ '/en/wall/delete_message/1', 'contributor', 'http://localhost/referer' ],
            [ '/en/wall/delete_message/1', 'admin', 'http://localhost/referer' ],
            [ '/en/wall/delete_message/999999999', 'contributor', 'http://localhost/referer' ],
            [ '/en/wall/show_message/1', null, true ],
            [ '/en/wall/show_message/1', 'contributor', true ],
            [ '/en/wall/show_message/999999999', null, '/en/wall/index' ],
            [ '/en/wall/show_message/invalid', null, '/en/wall/index' ],
            [ '/en/wall/messages_of_user/admin', null, true ],
            [ '/en/wall/messages_of_user/admin', 'contributor', true ],
            [ '/en/wall/hide_message/1', null, '/en/users/login?redirect=%2Fen%2Fwall%2Fhide_message%2F1' ],
            [ '/en/wall/hide_message/1', 'contributor', 'http://localhost/referer' ],
            [ '/en/wall/hide_message/1', 'advanced_contributor', 'http://localhost/referer' ],
            [ '/en/wall/hide_message/1', 'corpus_maintainer', 'http://localhost/referer' ],
            [ '/en/wall/hide_message/1', 'admin', 'http://localhost/referer' ],
            [ '/en/wall/unhide_message/1', null, '/en/users/login?redirect=%2Fen%2Fwall%2Funhide_message%2F1' ],
            [ '/en/wall/unhide_message/1', 'contributor', 'http://localhost/referer' ],
            [ '/en/wall/unhide_message/1', 'advanced_contributor', 'http://localhost/referer' ],
            [ '/en/wall/unhide_message/1', 'corpus_maintainer', 'http://localhost/referer' ],
            [ '/en/wall/unhide_message/1', 'admin', 'http://localhost/referer' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->addHeader('Referer', 'http://localhost/referer');
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function testSave_asGuest() {
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/en/wall/save', [
            'replyTo' => '',
            'content' => 'How about more butterflies on the home page?',
        ]);
        $this->assertRedirect('/en/users/login');
    }

    public function testSave_asMember() {
        $this->logInAs('contributor');
        $this->post('/en/wall/save', [
            'replyTo' => '',
            'content' => 'How about more butterflies on the home page?',
        ]);
        $this->assertRedirect('/en/wall/index');
    }

    public function roleChangeProvider() {
        return [
            // new role, redirect link, flash message match
            ['spammer',  'http://localhost/en/users/login?redirect=%2Fprevious_page', 'suspended'],
            ['inactive', 'http://localhost/en/users/login?redirect=%2Fprevious_page', 'deactivated'],
            ['contributor',       '/en/wall/index'],
            ['corpus_maintainer', '/en/wall/index'],
            ['admin',             '/en/wall/index'],
        ];
    }

    /**
     * @dataProvider roleChangeProvider()
     */
    public function testSave_asMember_roleJustChanged($newRole, $exceptedRedirect, $flashMsg = null) {
        $this->enableRetainFlashMessages();
        $this->logInAs('advanced_contributor');
        $users = $this->fetchTable('Users');
        $advcontributor = $users->get(3);
        $advcontributor->role = $newRole;
        $users->save($advcontributor);

        $this->configRequest([
            'headers' => ['Referer' => 'http://localhost/previous_page']
        ]);
        $this->post('/en/wall/save', [
            'replyTo' => '',
            'content' => 'Am I having my new role yet?',
        ]);

        $this->assertRedirect($exceptedRedirect);
        if ($flashMsg) {
            $this->assertFlashMessageContains($flashMsg);
        }
    }

    public function testSaveInside_asGuest() {
        $this->enableCsrfToken();
        $this->ajaxPost('/en/wall/save_inside', [
            'content' => 'Just have a little faith!',
            'replyTo' => '1',
        ]);
        $this->assertResponseError();
    }

    public function testSaveInside_asMember() {
        $this->logInAs('contributor');
        $this->ajaxPost('/en/wall/save_inside', [
            'content' => 'Just have a little faith!',
            'replyTo' => '1',
        ]);
        $this->assertResponseOk();
    }

    public function testSaveInside_notificationEmailLink() {
        $this->logInAs('contributor');
        $this->ajaxPost('/en/wall/save_inside', [
            'content' => 'Hello admin!',
            'replyTo' => '2',
        ]);
        $this->assertMailContainsHtml('http://localhost/wall/show_message/6#message_6');
    }

    private function assertFlashMessageContains($expected, $message = '') {
        $this->assertStringContainsString($expected, $this->_requestSession->read('Flash.flash.0.message'), $message);
    }

    public function postsWithLinksProvider() {
        return [
            // post data, comment should be saved, one email sent containing
            'inbound link, no confirmation' => [
                ['content' => 'Check this out https://example.net'], true, null
            ],
            'outbound link, needs confirmation' => [
                ['content' => 'Check this out https://example.com'], false, null
            ],
            'outbound link, confirmed' => [
                [
                    'content' => 'Check this out https://example.com',
                    'outboundLinksConfirmed' => '1',
                ],
                true,
                'wall post containing one or more outbound links',
            ],
            'confirmed but no links' => [
                [
                    'content' => 'Check this out',
                    'outboundLinksConfirmed' => '1',
                ],
                true,
                null,
            ],
            'too many outbound links, confirmed' => [
                [
                    'content' => 'Check this out'.str_repeat(' https://example.com', 100),
                    'outboundLinksConfirmed' => '1',
                ],
                true,
                'was automatically banned',
            ],
        ];
    }

    /**
     * @dataProvider postsWithLinksProvider()
     */
    public function testSave_postWithLinksByNewMember($postData, $shouldSave, $email) {
        $this->enableRetainFlashMessages();
        $this->logInAs('new_member');

        $this->post(
            'https://example.net/en/wall/save',
            ['replyTo' => ''] + $postData
        );

        if ($shouldSave) {
            $this->assertFlashMessageContains('Your message has been posted on the wall');
        } else {
            $this->assertFlashMessageContains('Your message was not posted');
        }
        if ($email) {
            $this->assertMailCount(1);
            $this->assertMailContains($email);
        } else {
            $this->assertMailCount(0);
        }
    }

    /**
     * @dataProvider postsWithLinksProvider()
     */
    public function testEdit_postWithLinksByNewMember($postData, $shouldSave, $email) {
        $this->enableRetainFlashMessages();
        $this->logInAs('new_member');

        $this->put('https://example.net/en/wall/edit/4', $postData);

        if ($shouldSave) {
            $this->assertFlashMessageContains('Message saved');
        } else {
            $this->assertFlashMessageContains('Your message was not posted');
        }
        if ($email) {
            $this->assertMailCount(1);
            $this->assertMailContains($email);
        } else {
            $this->assertMailCount(0);
        }
    }

    /**
     * @dataProvider postsWithLinksProvider()
     */
    public function testEdit_hiddenPostWithLinksByNewMember($postData, $shouldSave, $email) {
        $this->enableRetainFlashMessages();
        $this->logInAs('new_member');

        $this->put('https://example.net/en/wall/edit/5', $postData);

        if ($shouldSave) {
            $this->assertFlashMessageContains('Message saved');
        } else {
            $this->assertFlashMessageContains('Your message was not posted');
        }
        $this->assertMailCount(0);
    }

    /**
     * @dataProvider postsWithLinksProvider()
     */
    public function testSaveInside_postWithLinksByNewMember($postData, $shouldSave, $email) {
        $this->logInAs('new_member');

        $this->ajaxPost(
             'https://example.net/en/wall/save_inside',
            ['replyTo' => '4'] + $postData
        );

        $response = json_decode($this->_response->getBody());
        if ($shouldSave) {
            $this->assertResponseOk();
            $this->assertObjectHasAttribute('content', $response);
            $this->assertEquals($postData['content'], $response->content);
        } else {
            $this->assertResponseError();
            $this->assertObjectHasAttribute('content', $response);
            $this->assertObjectHasAttribute('outboundLinks', $response->content);
        }
        if ($email) {
            $this->assertMailCount(1);
            $this->assertMailContains($email);
        } else {
            $this->assertMailCount(0);
        }
    }

    private function postNewPosts($n) {
        $userId = 1;
        $initialDate = new \DateTime('2018-01-01');
        $posts = [];
        for ($i = 0; $i <= $n; $i++) {
            $date = $initialDate->format('Y-m-d H:i:s');
            $posts[] = [
                'content' => "Wall message $i",
                'date' => $date,
                'owner' => $userId,
            ];
            $initialDate->modify("+1 day");
        }
        $wall = $this->fetchTable('Wall');
        $wall->saveMany($wall->newEntities($posts));
    }

    public function testPaginateRedirectsPageOutOfBoundsToLastPage() {
        $lastPage = 2;

        $this->postNewPosts(15);

        $this->get("/en/wall/index?page=9999999");
        $this->assertRedirect("/en/wall/index?page=$lastPage");
    }
}
?>
