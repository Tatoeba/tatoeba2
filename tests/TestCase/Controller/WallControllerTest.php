<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

class WallControllerTest extends IntegrationTestCase {

    public $fixtures = [
        'app.walls',
        'app.wall_threads',
        'app.users',
        'app.users_languages'
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
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
