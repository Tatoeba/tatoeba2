<?php
namespace App\Test\TestCase\Controller;

use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use App\Test\TestCase\FaultyMailerTrait;
use Cake\Core\Configure;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class ReportContentControllerTest extends TestCase
{
    use EmailTrait;
    use FaultyMailerTrait;
    use IntegrationTestTrait;
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.private_messages',
        'app.users',
        'app.users_languages',
        'app.walls',
        'app.wiki_articles',
    ];

    public function setUp() {
        Configure::write('App.fullBaseUrl', 'https://example.org');
        parent::setUp();
    }

    public function accessesProvider() {
        return [
            // url; user; is accessible or redirection url
            [ '/en/report_content/wall_post/1', null, '/en/users/login?redirect=%2Fen%2Freport_content%2Fwall_post%2F1' ],
            [ '/en/report_content/wall_post/1', 'contributor', true ],
            [ '/en/report_content/wall_post/9999', 'contributor', 404 ],
        ];
    }

    /**
     * @dataProvider accessesProvider
     */
    public function testControllerAccess($url, $user, $response) {
        $this->assertAccessUrlAs($url, $user, $response);
        $this->assertMailCount(0);
    }

    private function assertFlashMessageContains($expected, $message = '') {
        $this->assertContains($expected, $this->_requestSession->read('Flash.flash.0.message'));
    }

    public function testWallPost() {
        $this->enableRetainFlashMessages();
        $this->logInAs('contributor');

        $this->post('http://example.net/en/report_content/wall_post/1', [
            'origin' => '/en/wall/index',
            'details' => 'this is spam',
        ]);

        $this->assertRedirect('/en/wall/index');
        $this->assertFlashMessageContains('Thank you');
        $this->assertMailCount(1);
    }

    public function testWallPost_fail() {
        $this->enableFaultyMailer();
        $this->enableRetainFlashMessages();
        $this->logInAs('contributor');

        $this->post('http://example.net/en/report_content/wall_post/1', [
            'origin' => '/en/wall/index',
            'details' => 'this is spam',
        ]);

        $this->assertNoRedirect();
        $this->assertFlashMessageContains('Sorry');
        $this->assertMailCount(0);
    }
}
