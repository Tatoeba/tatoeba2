<?php
namespace App\Test\TestCase\Controller\VHosts\Api;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Helmich\JsonAssert\JsonAssertions;

class UsersControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use JsonAssertions;

    const USERS_JSON_SCHEMA = [
      'type'       => 'object',
      'required'   => ['username', 'role', 'since'],
      'properties' => [
        'username' => ['type' => 'string'],
        'role'     => ['type' => 'string'],
        'since'    => ['type' => 'string'],
      ],
    ];

    public $fixtures = [
        'app.Users',
        'app.UsersLanguages',
    ];

    public function testGetUser_doesNotExist()
    {
        $this->get("http://api.example.com/unstable/users/doesnotexists");
        $this->assertResponseCode(404);
    }

    public function testGetUser_ok()
    {
        $this->get("http://api.example.com/unstable/users/kazuki");
        $this->assertResponseOk();
        $this->assertContentType('application/json');
        $actual = $this->_getBodyAsString();

        $schema = [
            'type'       => 'object',
            'required'   => ['data'],
            'properties' => [
                'data' => self::USERS_JSON_SCHEMA,
            ]
        ];
        $this->assertJsonDocumentMatchesSchema($actual, $schema);
    }

    public function testGetUser_doesNotReturnPreciseRegistrationTime()
    {
        $this->get("http://api.example.com/unstable/users/kazuki");
        $actual = $this->_getBodyAsString();
        $expected = '2013-04-22';
        $this->assertJsonValueEquals($actual, '$.data.since', $expected);
    }
}
