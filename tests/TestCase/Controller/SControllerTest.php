<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class SControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use TatoebaControllerTestTrait;

    public array $fixtures = [
        'app.Audios',
        'app.FavoritesUsers',
        'app.Links',
        'app.Sentences',
        'app.SentencesLists',
        'app.SentencesSentencesLists',
        'app.Transcriptions',
        'app.Users',
        'app.UsersLanguages',
        'app.UsersSentences',
        'app.WikiArticles',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/s/s/1', null, true ],
            [ '/en/s/s/1', 'contributor', true ],
            [ '/en/s/s/99999999999999', null, 404 ],
            [ '/en/s/s/99999999999999', 'contributor', 404 ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }
}
