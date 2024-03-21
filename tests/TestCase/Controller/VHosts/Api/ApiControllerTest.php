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
        $this->assertResponseCode(400);
    }
}
