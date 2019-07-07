<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class SControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.audios',
        'app.favorites_users',
        'app.links',
        'app.sentences',
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.transcriptions',
        'app.users',
        'app.users_languages',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/s/s/1', null, true ],
            [ '/eng/s/s/1', 'contributor', true ],
            [ '/eng/s/s/99999999999999', null, 404 ],
            [ '/eng/s/s/99999999999999', 'contributor', 404 ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }
}
