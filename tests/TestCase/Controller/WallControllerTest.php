<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

class WallControllerTest extends IntegrationTestCase {
    use EmailTrait;
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.private_messages',
        'app.walls',
        'app.wall_threads',
        'app.users',
        'app.users_languages',
        'app.wiki_articles',
    ];

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
            [ '/en/wall/edit/9999999999', 'contributor', 'https://example.net/referer' ],
            [ '/en/wall/delete_message/1', null, '/en/users/login?redirect=%2Fen%2Fwall%2Fdelete_message%2F1' ],
            [ '/en/wall/delete_message/1', 'contributor', 'https://example.net/referer' ],
            [ '/en/wall/delete_message/1', 'admin', 'https://example.net/referer' ],
            [ '/en/wall/delete_message/999999999', 'contributor', 'https://example.net/referer' ],
            [ '/en/wall/show_message/1', null, true ],
            [ '/en/wall/show_message/1', 'contributor', true ],
            [ '/en/wall/show_message/999999999', null, '/en/wall/index' ],
            [ '/en/wall/messages_of_user/admin', null, true ],
            [ '/en/wall/messages_of_user/admin', 'contributor', true ],
            [ '/en/wall/hide_message/1', null, '/en/users/login?redirect=%2Fen%2Fwall%2Fhide_message%2F1' ],
            [ '/en/wall/hide_message/1', 'contributor', '/' ],
            [ '/en/wall/hide_message/1', 'advanced_contributor', '/' ],
            [ '/en/wall/hide_message/1', 'corpus_maintainer', '/' ],
            [ '/en/wall/hide_message/1', 'admin', 'https://example.net/referer' ],
            [ '/en/wall/unhide_message/1', null, '/en/users/login?redirect=%2Fen%2Fwall%2Funhide_message%2F1' ],
            [ '/en/wall/unhide_message/1', 'contributor', '/' ],
            [ '/en/wall/unhide_message/1', 'advanced_contributor', '/' ],
            [ '/en/wall/unhide_message/1', 'corpus_maintainer', '/' ],
            [ '/en/wall/unhide_message/1', 'admin', 'https://example.net/referer' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->addHeader('Referer', 'https://example.net/referer');
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function testSave_asGuest() {
        $this->enableCsrfToken();
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
        Configure::write('App.fullBaseUrl', 'https://example.net');
        $this->logInAs('contributor');
        $this->ajaxPost('/en/wall/save_inside', [
            'content' => 'Hello admin!',
            'replyTo' => '2',
        ]);
        $this->assertMailContainsHtml('https://example.net/wall/show_message/5#message_5');
    }

    private function assertFlashMessageContains($expected, $message = '') {
        $this->assertContains($expected, $this->_requestSession->read('Flash.flash.0.message'), $message);
    }

    public function postsWithLinksProvider() {
        return [
            // post data, comment should be saved, number of emails sent
            'inbound link, no confirmation' => [
                ['content' => 'Check this out https://example.net'], true, 0
            ],
            'outbound link, needs confirmation' => [
                ['content' => 'Check this out https://example.com'], false, 0
            ],
            'outbound link, confirmed' => [
                [
                    'content' => 'Check this out https://example.com',
                    'outboundLinksConfirmed' => '1',
                ],
                true,
                1,
            ],
            'confirmed but no links' => [
                [
                    'content' => 'Check this out',
                    'outboundLinksConfirmed' => '1',
                ],
                true,
                0,
            ],
        ];
    }

    /**
     * @dataProvider postsWithLinksProvider()
     */
    public function testSave_postWithLinksByNewMember($postData, $shouldSave, $nbEmails) {
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
        $this->assertMailCount($nbEmails);
    }

    /**
     * @dataProvider postsWithLinksProvider()
     */
    public function testEdit_postWithLinksByNewMember($postData, $shouldSave, $nbEmails) {
        $this->enableRetainFlashMessages();
        $this->logInAs('new_member');

        $this->put('https://example.net/en/wall/edit/4', $postData);

        if ($shouldSave) {
            $this->assertFlashMessageContains('Message saved');
        } else {
            $this->assertFlashMessageContains('Your message was not posted');
        }
        $this->assertMailCount($nbEmails);
    }

    /**
     * @dataProvider postsWithLinksProvider()
     */
    public function testSaveInside_postWithLinksByNewMember($postData, $shouldSave, $nbEmails) {
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
        $this->assertMailCount($nbEmails);
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
        $wall = TableRegistry::get('Wall');
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
