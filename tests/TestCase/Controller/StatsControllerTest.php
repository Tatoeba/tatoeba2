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
        'app.sentences',
        'app.users_languages',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/stats/sentences_by_language', null, true ],
            [ '/eng/stats/sentences_by_language', 'contributor', true ],
            [ '/eng/stats/users_languages', null, true ],
            [ '/eng/stats/users_languages', 'contributor', true ],
            [ '/eng/stats/native_speakers', null, true ],
            [ '/eng/stats/native_speakers', 'contributor', true ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }
}
