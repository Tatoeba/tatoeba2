<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

class WallControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.PrivateMessages',
        'app.Walls',
        'app.WallThreads',
        'app.Users',
        'app.UsersLanguages',
        'app.WikiArticles',
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
