<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class StatsControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.private_messages',
        'app.users',
        'app.languages',
        'app.users_languages',
        'app.wiki_articles',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/stats/sentences_by_language', null, true ],
            [ '/en/stats/sentences_by_language', 'contributor', true ],
            [ '/en/stats/users_languages', null, true ],
            [ '/en/stats/users_languages', 'contributor', true ],
            [ '/en/stats/native_speakers', null, true ],
            [ '/en/stats/native_speakers', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }
}
