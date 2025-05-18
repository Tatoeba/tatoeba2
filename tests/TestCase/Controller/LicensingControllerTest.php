<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;

class LicensingControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.users',
        'app.users_languages',
        'app.queued_jobs',
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.sentences',
        'app.private_messages',
        'app.wiki_articles',
    ];

    public function accessesProvider() {
        return [
            [ '/en/licensing/switch_my_sentences', null, '/en/users/login?redirect=%2Fen%2Flicensing%2Fswitch_my_sentences' ],
            [ '/en/licensing/switch_my_sentences', 'contributor', true ], // has can_switch_license
            [ '/en/licensing/switch_my_sentences', 'kazuki', '/en' ], // doesn't have can_switch_license
            [ '/en/licensing/switch_my_sentences', 'admin', '/en' ], // doesn't have can_switch_license
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testLicensingControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
    }

    public function ajaxAccessesProvider() {
        return [
            [ '/en/licensing/get_license_switch_list', null, false ],
            [ '/en/licensing/get_license_switch_list', 'contributor', true ],
            [ '/en/licensing/get_license_switch_list', 'kazuki', false ],
            [ '/en/licensing/get_license_switch_list', 'admin', false ],
            [ '/en/licensing/refresh_license_switch_list', null, false ],
            [ '/en/licensing/refresh_license_switch_list', 'contributor', true ],
            [ '/en/licensing/refresh_license_switch_list', 'kazuki', false ],
            [ '/en/licensing/refresh_license_switch_list', 'admin', false ],
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testExportsControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }
}
