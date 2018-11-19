<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Audio;
use Cake\TestSuite\TestCase;

class AudioTest extends TestCase
{
    public $Audio;

    public function setUp()
    {
        parent::setUp();
        $this->Audio = new Audio();
    }

    public function tearDown()
    {
        unset($this->Audio);

        parent::tearDown();
    }

    public function testGet_externalSetsDefaultValues()
    {
        $this->Audio->set('external', ['username' => 'foobar']);

        $this->assertTrue(array_key_exists('license', $this->Audio->external));
    }

    public function testSet_externalIgnoresUnknownKeys()
    {
        $this->Audio->set('external', ['this_does_not_exist' => true]);

        $this->assertFalse(array_key_exists('this_does_not_exist', $this->Audio->external));
    }

    public function testSet_externalMergesExistingValues()
    {
        $this->Audio->set('external', ['username' => 'foobar']);
        $this->Audio->set('external', ['attribution_url' => 'http://example.net/']);

        $this->assertEquals('foobar', $this->Audio->external['username']);
    }
}
