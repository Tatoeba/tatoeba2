<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

class WallControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.private_messages',
        'app.walls',
        'app.wall_threads',
        'app.users',
        'app.users_languages'
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/wall/index', null, true ],
            [ '/eng/wall/index', 'contributor', true ],
            [ '/eng/wall/edit/1', null, '/eng/users/login?redirect=%2Feng%2Fwall%2Fedit%2F1' ],
            [ '/eng/wall/edit/1', 'contributor', '/eng/wall/index' ],
            [ '/eng/wall/edit/1', 'kazuki', true ], // author of message #1
            [ '/eng/wall/edit/1', 'advanced_contributor', '/eng/wall/index' ],
            [ '/eng/wall/edit/1', 'corpus_maintainer', '/eng/wall/index' ],
            [ '/eng/wall/edit/1', 'admin', true ],
            [ '/eng/wall/edit/9999999999', 'contributor', 'https://example.net/referer' ],
            [ '/eng/wall/delete_message/1', null, '/eng/users/login?redirect=%2Feng%2Fwall%2Fdelete_message%2F1' ],
            [ '/eng/wall/delete_message/1', 'contributor', 'https://example.net/referer' ],
            [ '/eng/wall/delete_message/1', 'admin', 'https://example.net/referer' ],
            [ '/eng/wall/delete_message/999999999', 'contributor', 'https://example.net/referer' ],
            [ '/eng/wall/show_message/1', null, true ],
            [ '/eng/wall/show_message/1', 'contributor', true ],
            [ '/eng/wall/show_message/999999999', null, '/eng/wall/index' ],
            [ '/eng/wall/messages_of_user/admin', null, true ],
            [ '/eng/wall/messages_of_user/admin', 'contributor', true ],
            [ '/eng/wall/hide_message/1', null, '/eng/users/login?redirect=%2Feng%2Fwall%2Fhide_message%2F1' ],
            [ '/eng/wall/hide_message/1', 'contributor', '/' ],
            [ '/eng/wall/hide_message/1', 'advanced_contributor', '/' ],
            [ '/eng/wall/hide_message/1', 'corpus_maintainer', '/' ],
            [ '/eng/wall/hide_message/1', 'admin', 'https://example.net/referer' ],
            [ '/eng/wall/unhide_message/1', null, '/eng/users/login?redirect=%2Feng%2Fwall%2Funhide_message%2F1' ],
            [ '/eng/wall/unhide_message/1', 'contributor', '/' ],
            [ '/eng/wall/unhide_message/1', 'advanced_contributor', '/' ],
            [ '/eng/wall/unhide_message/1', 'corpus_maintainer', '/' ],
            [ '/eng/wall/unhide_message/1', 'admin', 'https://example.net/referer' ],
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
        $this->post('/eng/wall/save', [
            'replyTo' => '',
            'content' => 'How about more butterflies on the home page?',
        ]);
        $this->assertRedirect('/eng/users/login');
    }

    public function testSave_asMember() {
        $this->logInAs('contributor');
        $this->post('/eng/wall/save', [
            'replyTo' => '',
            'content' => 'How about more butterflies on the home page?',
        ]);
        $this->assertRedirect('/eng/wall/index');
    }

    public function testSaveInside_asGuest() {
        $this->enableCsrfToken();
        $this->ajaxPost('/eng/wall/save_inside', [
            'content' => 'Just have a little faith!',
            'replyTo' => '1',
        ]);
        $this->assertResponseError();
    }

    public function testSaveInside_asMember() {
        $this->logInAs('contributor');
        $this->ajaxPost('/eng/wall/save_inside', [
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

        $this->get("/eng/wall/index?page=9999999");
        $this->assertRedirect("/eng/wall/index?page=$lastPage");
    }
}
?>
