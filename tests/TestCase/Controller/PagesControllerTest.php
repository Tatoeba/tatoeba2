<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

class PagesControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $autoFixtures = false;

    public $fixtures = [
        'app.Audios',
        'app.Contributions',
        'app.FavoritesUsers',
        'app.Languages',
        'app.LastContributions',
        'app.Links',
        'app.PrivateMessages',
        'app.Sentences',
        'app.SentenceComments',
        'app.SentencesLists',
        'app.SentencesSentencesLists',
        'app.Transcriptions',
        'app.Users',
        'app.UsersLanguages',
        'app.Walls'
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/terms-of-use', null, '/eng/terms_of_use' ],
            [ '/eng/terms-of-use', 'contributor', '/eng/terms_of_use' ],
            [ '/eng/terms_of_use', null, true ],
            [ '/eng/terms_of_use', 'contributor', true ],
            [ '/eng/tatoeba-team-and-credits', null, '/eng/tatoeba_team_and_credits' ],
            [ '/eng/tatoeba-team-and-credits', 'contributor', '/eng/tatoeba_team_and_credits' ],
            [ '/eng/tatoeba_team_and_credits', null, '/eng/home' ],
            [ '/eng/tatoeba_team_and_credits', 'contributor', '/eng/home' ],
            [ '/eng/download-tatoeba-example-sentences', null, '/eng/downloads' ],
            [ '/eng/download-tatoeba-example-sentences', 'contributor', '/eng/downloads' ],
            [ '/eng/downloads', null, true ],
            [ '/eng/downloads', 'contributor', true ],
            [ '/eng/home', null, '/eng' ],
            [ '/eng/home', 'contributor', '/eng' ],
            [ '/eng/about', null, true ],
            [ '/eng/about', 'contributor', true ],
            [ '/eng/contact', null, true ],
            [ '/eng/contact', 'contributor', true ],
            [ '/eng/help', null, true ],
            [ '/eng/help', 'contributor', true ],
            [ '/eng/faq', null, 'http://wiki.tatoeba.org/articles/show/faq' ],
            [ '/eng/faq', 'contributor', 'http://wiki.tatoeba.org/articles/show/faq' ],
            [ '/eng/donate', null, true ],
            [ '/eng/donate', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->loadFixtures('PrivateMessages', 'Users', 'UsersLanguages');
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function testHomeAccess() {
        $this->loadFixtures(); // load all $this->fixtures
        $this->assertAccessUrlAs('/eng', null, true);
        $this->assertAccessUrlAs('/eng', 'contributor', true);
    }
}
