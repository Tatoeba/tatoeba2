<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;

class LicensingControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.aros',
        'app.acos',
        'app.aros_acos',
        'app.users',
        'app.users_languages',
        'app.queued_jobs',
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.sentences',
        'app.private_messages',
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }

    public function accessesProvider() {
        return [
            [ '/eng/licensing/switch_my_sentences', null, '/eng/users/login?redirect=%2Feng%2Flicensing%2Fswitch_my_sentences' ],
            [ '/eng/licensing/switch_my_sentences', 'contributor', true ], // has can_switch_license
            [ '/eng/licensing/switch_my_sentences', 'kazuki', '/eng' ], // doesn't have can_switch_license
            [ '/eng/licensing/switch_my_sentences', 'admin', '/eng' ], // doesn't have can_switch_license
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
            [ '/eng/licensing/get_license_switch_list', null, false ],
            [ '/eng/licensing/get_license_switch_list', 'contributor', true ],
            [ '/eng/licensing/get_license_switch_list', 'kazuki', false ],
            [ '/eng/licensing/get_license_switch_list', 'admin', false ],
            [ '/eng/licensing/refresh_license_switch_list', null, false ],
            [ '/eng/licensing/refresh_license_switch_list', 'contributor', true ],
            [ '/eng/licensing/refresh_license_switch_list', 'kazuki', '/eng' ],
            [ '/eng/licensing/refresh_license_switch_list', 'admin', '/eng' ],
        ];
    }

    /**
     * @dataProvider ajaxAccessesProvider
     */
    public function testExportsControllerAjaxAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
    }
}
