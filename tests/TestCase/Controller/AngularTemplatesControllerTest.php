<?php
namespace App\Test\TestCase\Controller;

use App\Controller\AngularTemplatesController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class AngularTemplatesControllerTest extends TestCase
{
    use IntegrationTestTrait, TatoebaControllerTestTrait;

    public $fixtures = [
        'app.Users',
        'app.users_languages',
    ];

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/angular_templates/show_all_sentences_button_text/por', null, true ],
            [ '/en/angular_templates/show_all_sentences_button_text/por', 'contributor', true ],
            [ '/en/angular_templates/show_all_sentences_button_text/unknown', null, true ],
            [ '/en/angular_templates/show_all_sentences_button_text/nonexistent', null, 404 ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAjaxAccessUrlAs($url, $user, $response);
        $this->assertContentType('application/json');
    }
}
