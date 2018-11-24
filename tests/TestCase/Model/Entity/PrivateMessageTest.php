<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\PrivateMessage;
use App\Model\Entity\User;
use Cake\TestSuite\TestCase;

class PrivateMessageTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->PrivateMessage = new PrivateMessage();
    }

    public function tearDown()
    {
        unset($this->PrivateMessage);
        parent::tearDown();
    }

    public function testGetType_isHuman()
    {
        $author = new User();
        $author->id = 1;
        $this->PrivateMessage->author = $author;
        $this->assertEquals('human', $this->PrivateMessage->type);
    }

    public function testGetType_isMachine()
    {
        $this->PrivateMessage->author = null;
        $this->assertEquals('machine', $this->PrivateMessage->type);
    }

    public function testGetOrigin_isInbox()
    {
        $this->PrivateMessage->user_id = 1;
        $this->PrivateMessage->recpt = 1;
        $this->assertEquals('Inbox', $this->PrivateMessage->origin);
    }

    public function testGetOrigin_isDrafts()
    {
        $this->PrivateMessage->sent = false;
        $this->PrivateMessage->recpt = 1;
        $this->assertEquals('Drafts', $this->PrivateMessage->origin);
    }

    public function testGetOrigin_isSent()
    {
        $this->PrivateMessage->user_id = 2;
        $this->PrivateMessage->recpt = 1;
        $this->PrivateMessage->sent = true;
        $this->assertEquals('Sent', $this->PrivateMessage->origin);
    }
}