<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestCase;

class AudioControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.audios',
        'app.languages',
        'app.private_messages',
        'app.sentences',
        'app.transcriptions',
        'app.users',
        'app.users_languages',
        'app.wiki_articles',
        'app.queued_jobs',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/audio/import', null, '/en/users/login?redirect=%2Fen%2Faudio%2Fimport' ],
            [ '/en/audio/import', 'spammer', '/' ],
            [ '/en/audio/import', 'inactive', '/' ],
            [ '/en/audio/import', 'contributor', '/' ],
            [ '/en/audio/import', 'advanced_contributor', '/' ],
            [ '/en/audio/import', 'corpus_maintainer', '/' ],
            [ '/en/audio/import', 'admin', true ],
            [ '/en/audio/index', null, true ],
            [ '/en/audio/index', 'contributor', true ],
            [ '/en/audio/index/fra', null, true ],
            [ '/en/audio/index/fra', 'contributor', true ],
            [ '/en/audio/of/contributor', null, true ],
            [ '/en/audio/of/contributor', 'contributor', true ],
            [ '/en/audio/save_settings', null, '/en/users/login?redirect=%2Fen%2Faudio%2Fsave_settings' ],
            [ '/en/audio/save_settings', 'contributor', '/en/audio/of/contributor' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testAudioControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }
}
