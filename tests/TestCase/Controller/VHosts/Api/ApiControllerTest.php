<?php
namespace App\Test\TestCase\Controller\VHosts\Api;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class ApiControllerTest extends TestCase
{
    use IntegrationTestTrait;

    public function test_docIndex()
    {
        $this->get("http://api.example.com/");
        $this->assertResponseOk();
    }

    public function test_docUnstable()
    {
        $this->get("http://api.example.com/unstable");
        $this->assertResponseOk();
    }

    public function test_docNonInvalidVersion()
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
            # API path, should return non-404 status code
            ['/unstable/audio/1/file', true],
            [      '/v1/audio/1/file', true],
            ['/unstable/audio',        true],
            [      '/v1/audio',        false],
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
    public function testRouteAccess($apiPath, $expectsNon404)
    {
        $this->get("http://api.example.com$apiPath");
        if ($expectsNon404) {
            $this->assertResponseCodeIsNot(404);
        } else {
            $this->assertResponseCode(404);
        }
    }
}
