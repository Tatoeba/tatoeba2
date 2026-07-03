<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class StatsControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use TatoebaControllerTestTrait;

    public array $fixtures = [
        'app.PrivateMessages',
        'app.Users',
        'app.Languages',
        'app.UsersLanguages',
        'app.WikiArticles',
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
