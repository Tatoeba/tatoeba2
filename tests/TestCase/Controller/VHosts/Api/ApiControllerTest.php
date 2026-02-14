<?php
namespace App\Test\TestCase\Controller\VHosts\Api;

use App\Test\TestCase\Controller\AudioIntegrationTestTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class ApiControllerTest extends TestCase
{
    use AudioIntegrationTestTrait;

    public $fixtures = [
        'app.Audios',
        'app.Sentences',
        'app.Users',
    ];

    use IntegrationTestTrait;

    public function test_docIndex()
    {
        $this->get("http://api.example.com/");
        $this->assertResponseOk();
    }

    public function test_docOpenApi()
    {
        $this->get("http://api.example.com/openapi");
        $this->assertResponseOk();
    }

    public function test_unknownAPIVersion()
    {
        $this->get("http://api.example.com/non-existent");
        $this->assertResponseCode(404);
    }

    public function test_invalidController()
    {
        $this->get("http://api.example.com/no/such/controller");
        $this->assertResponseCode(404);
    }

    private function assertResponseCodeIsNot($exceptedCode) {
        $actualCode = $this->_response->getStatusCode();
        $this->assertNotEquals($exceptedCode, $actualCode);
    }
    public function routeAccessesProvider() {
        return [
            # API path, should return non-404 status code, create audio fixture
            ['/unstable/audios/1/file', true, true],
            [      '/v1/audios/1/file', true, true],
            ['/unstable/audios',        true],
            [      '/v1/audios',        false],
            ['/unstable/sentences',    true],
            [      '/v1/sentences',    true],
            ['/unstable/sentences/1',  true],
            [      '/v1/sentences/1',  true],
            ['/unstable/users/kazuki', true],
            [      '/v1/users/kazuki', false],
        ];
    }

    /**
     * @dataProvider routeAccessesProvider()
     */
    public function testRouteAccess($apiPath, $expectsNon404, $createAudioFixture = false)
    {
        if ($createAudioFixture) {
            $this->initAudioStorageDir();
            $this->createAudioFile(1);
        }

        $this->get("http://api.example.com$apiPath");
        if ($expectsNon404) {
            $this->assertResponseCodeIsNot(404);
        } else {
            $this->assertResponseCode(404);
        }

        if ($createAudioFixture) {
            $this->deleteAudioStorageDir();
        }
    }
}
