<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

class AudioControllerTest extends IntegrationTestCase
{
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.aros',
        'app.acos',
        'app.aros_acos',
        'app.audios',
        'app.languages',
        'app.transcriptions',
        'app.users',
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/eng/audio/import', null, '/eng/users/login?redirect=%2Feng%2Faudio%2Fimport' ],
            [ '/eng/audio/import', 'spammer', '/' ],
            [ '/eng/audio/import', 'inactive', '/' ],
            [ '/eng/audio/import', 'contributor', '/' ],
            [ '/eng/audio/import', 'advanced_contributor', '/' ],
            [ '/eng/audio/import', 'corpus_maintainer', '/' ],
            [ '/eng/audio/import', 'admin', true ],
            [ '/eng/audio/index', null, true ],
            [ '/eng/audio/index', 'contributor', true ],
            [ '/eng/audio/index/fra', null, true ],
            [ '/eng/audio/index/fra', 'contributor', true ],
            [ '/eng/audio/of/contributor', null, true ],
            [ '/eng/audio/of/contributor', 'contributor', true ],
            [ '/eng/audio/save_settings', null, '/eng/users/login?redirect=%2Feng%2Faudio%2Fsave_settings' ],
            [ '/eng/audio/save_settings', 'contributor', '/eng/audio/of/contributor' ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testAudioControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }
}
