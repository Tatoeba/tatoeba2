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
}
