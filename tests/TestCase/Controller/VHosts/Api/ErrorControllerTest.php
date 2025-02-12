<?php
namespace App\Test\TestCase\Controller\VHosts\Api;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class ErrorControllerTest extends TestCase
{
    use IntegrationTestTrait;

    public $fixtures = [
        'app.Audios',
        'app.Sentences',
        'app.Transcriptions',
        'app.Users',
        'app.Links',
    ];

    private function assertJsonResponse() {
        try {
            json_decode($this->_getBodyAsString(), null, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            $this->fail('Error response body does not contain valid json');
        }
        $this->assertHeader('Content-type', 'application/json');
    }

    public function testErrorsAreReturnedInJson() {
        $this->get("http://api.example.com/invalid_version");
        $this->assertResponseCode(404);
        $this->assertJsonResponse();

        $this->get("http://api.example.com/invalid_version/invalid_controller");
        $this->assertResponseCode(404);
        $this->assertJsonResponse();

        $this->get("http://api.example.com/invalid_version/sentences");
        $this->assertResponseCode(400);
        $this->assertJsonResponse();

        $this->get("http://api.example.com/unstable/sentences/invalid_id");
        $this->assertResponseCode(400);
        $this->assertJsonResponse();
    }
}
