<?php
namespace App\Test\TestCase\Controller;

use App\Controller\ActivitiesController;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;

class ActivitiesControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

    public $fixtures = array(
        'app.aros',
        'app.acos',
        'app.aros_acos',
        'app.audios',
        'app.favorites_users',
        'app.links',
        'app.private_messages',
        'app.sentences',
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.transcriptions',
        'app.users',
        'app.users_languages'
    );

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/activities/adopt_sentences', null, true ],
            [ '/eng/activities/adopt_sentences', 'contributor', true ],
            [ '/eng/activities/adopt_sentences/jav', null, true ],
            [ '/eng/activities/adopt_sentences/jav', 'contributor', true ],
            [ '/eng/activities/translate_sentences', null, true ],
            [ '/eng/activities/translate_sentences', 'contributor', true ],
            [ '/eng/activities/translate_sentences_of/admin', null, true ],
            [ '/eng/activities/translate_sentences_of/admin', 'contributor', true ],
            [ '/eng/activities/translate_sentences_of/admin/fra', null, true ],
            [ '/eng/activities/translate_sentences_of/admin/fra', 'contributor', true ],
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
        $lastPage = 2;

        $this->get("/eng/activities/translate_sentences_of/$user?page=9999999");

        $this->assertRedirect("/eng/activities/translate_sentences_of/$user?page=$lastPage");
    }
}
