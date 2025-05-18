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
        'app.UsersSentences',
        'app.Walls',
        'app.WikiArticles',
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Tatowiki.baseHost', 'wiki.example.com');
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/terms-of-use', null, '/en/terms_of_use' ],
            [ '/en/terms-of-use', 'contributor', '/en/terms_of_use' ],
            [ '/en/terms_of_use', null, true ],
            [ '/en/terms_of_use', 'contributor', true ],
            [ '/en/tatoeba-team-and-credits', null, '/en/tatoeba_team_and_credits' ],
            [ '/en/tatoeba-team-and-credits', 'contributor', '/en/tatoeba_team_and_credits' ],
            [ '/en/tatoeba_team_and_credits', null, '/en/home' ],
            [ '/en/tatoeba_team_and_credits', 'contributor', '/en/home' ],
            [ '/en/download-tatoeba-example-sentences', null, '/en/downloads' ],
            [ '/en/download-tatoeba-example-sentences', 'contributor', '/en/downloads' ],
            [ '/en/downloads', null, true ],
            [ '/en/downloads', 'contributor', true ],
            [ '/en/home', null, '/en' ],
            [ '/en/home', 'contributor', '/en' ],
            [ '/en/about', null, true ],
            [ '/en/about', 'contributor', true ],
            [ '/en/contact', null, true ],
            [ '/en/contact', 'contributor', true ],
            [ '/en/help', null, true ],
            [ '/en/help', 'contributor', true ],
            [ '/en/faq', null, 'http://en.wiki.example.com/articles/show/faq' ],
            [ '/en/faq', 'contributor', 'http://en.wiki.example.com/articles/show/faq' ],
            [ '/en/donate', null, true ],
            [ '/en/donate', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->loadFixtures('PrivateMessages', 'Users', 'UsersLanguages', 'WikiArticles');
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function testHomeAccess() {
        $this->loadFixtures(); // load all $this->fixtures
        $this->assertAccessUrlAs('/en', null, true);
        $this->assertAccessUrlAs('/en', 'contributor', true);
    }
}
