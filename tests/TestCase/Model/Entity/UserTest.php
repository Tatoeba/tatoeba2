<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\User;
use Cake\TestSuite\TestCase;

class UserTest extends TestCase
{
    public $User;

    public function setUp()
    {
        parent::setUp();
        $this->User = new User();
    }

    public function tearDown()
    {
        unset($this->User);
        parent::tearDown();
    }

    public function testSet_passwordhashesPassword()
    {
        $this->User->set('password', 'my super password');
        $this->assertContains('$', $this->User->password);
    }
}
