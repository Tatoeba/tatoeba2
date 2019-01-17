<?php
namespace App\Test\TestCase\Controller;

use App\Controller\ActivitiesController;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

class ActivitiesControllerTest extends IntegrationTestCase {

    public $fixtures = array(
        'app.sentences',
        'app.users',
        'app.users_languages'
    );

    public function testPaginateRedirectsPageOutOfBoundsToLastPage() {
        $user = 'kazuki';
        $userId = 7;
        $lastPage = 2;

        $this->get("/eng/activities/translate_sentences_of/$user?page=9999999");

        $this->assertRedirect("/eng/activities/translate_sentences_of/$user?page=$lastPage");
    }
}
