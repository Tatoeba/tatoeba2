<?php
namespace App\Test\TestCase\Controller;

use App\Controller\ActivitiesController;
use Cake\TestSuite\IntegrationTestCase;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;

class ActivitiesControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

    public $fixtures = array(
        'app.audios',
        'app.favorites_users',
        'app.links',
        'app.private_messages',
        'app.sentences',
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.transcriptions',
        'app.users',
        'app.users_languages',
        'app.users_sentences',
        'app.wiki_articles',
    );

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/activities/adopt_sentences', null, '/en/users/login?redirect=%2Fen%2Factivities%2Fadopt_sentences' ],
            [ '/en/activities/adopt_sentences', 'contributor', true ],
            [ '/en/activities/adopt_sentences/jav', null, '/en/users/login?redirect=%2Fen%2Factivities%2Fadopt_sentences%2Fjav' ],
            [ '/en/activities/adopt_sentences/jav', 'contributor', true ],
            [ '/en/activities/translate_sentences', null, '/en/users/login?redirect=%2Fen%2Factivities%2Ftranslate_sentences' ],
            [ '/en/activities/translate_sentences', 'contributor', true ],
            [ '/en/activities/translate_sentences_of/admin', null, true ],
            [ '/en/activities/translate_sentences_of/admin', 'contributor', true ],
            [ '/en/activities/translate_sentences_of/admin/fra', null, true ],
            [ '/en/activities/translate_sentences_of/admin/fra', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testActivitiesControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function testPaginateRedirectsPageOutOfBoundsToLastPage() {
        $user = 'kazuki';
        $userId = 7;
        $lastPage = 3;

        $this->get("/en/activities/translate_sentences_of/$user?page=9999999");

        $this->assertRedirect("/en/activities/translate_sentences_of/$user?page=$lastPage");
    }
}
