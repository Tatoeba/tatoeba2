<?php
namespace App\Test\TestCase\Shell\Task;

use App\Console\Command\Task\QueueSwitchSentencesLicenseTask;
use Cake\Console\ConsoleInput;
use Cake\Console\ConsoleOutput;
use Cake\TestSuite\TestCase;

class QueueSwitchSentencesLicenseTaskTest extends TestCase
{
    public $fixtures = array(
        'app.sentences',
        'app.contributions',
        'app.reindex_flags',
        'app.users_languages',
        'app.private_messages'
    );

    public function setUp()
    {
        parent::setUp();
        $out = $this->getMock('ConsoleOutput', array(), array(), '', false);
        $in = $this->getMock('ConsoleInput', array(), array(), '', false);

        $this->task = $this->getMock(
            'QueueSwitchSentencesLicenseTask',
            array('in', 'err', 'createFile', '_stop', 'clear'),
            array($out, $out, $in)
        );
        $this->task->batchOperationSize = 10;
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->task);
    }

    public function testSwitchLicense()
    {
        $before = $this->task->Sentence->findAllByLicense('CC0 1.0');
        $beforeIds = Set::extract($before, '{n}.Sentence.id');
        $options = array(
            'userId' => 4,
            'dryRun' => false,
        );
        $expected = array(48, 53);

        $this->task->run($options);

        $after = $this->task->Sentence->findAllByLicense('CC0 1.0');
        $afterIds = Set::extract($after, '{n}.Sentence.id');
        $switched = array_diff($afterIds, $beforeIds);
        sort($switched);

        $this->assertNotEquals($before, $after);
        $this->assertEquals($expected, $switched);
    }

    public function testSwitchLicense_withDryRun()
    {
        $before = $this->task->Sentence->findAllByLicense('CC0 1.0');
        $options = array(
            'userId' => 4,
            'dryRun' => true,
        );

        $this->task->run($options);

        $after = $this->task->Sentence->findAllByLicense('CC0 1.0');
        $this->assertEquals($before, $after);
    }

    public function testSwitchLicense_sendsResultByPM()
    {
        $expectedPM = array(
            'recpt' => '4',
            'sender' => '0',
            'user_id' => '4',
            'folder' => 'Inbox',
            'isnonread' => '1',
            'draft_recpts' => '',
            'sent' => '1',
        );
        $options = array(
            'userId' => 4,
            'dryRun' => false,
            'sendReport' => true,
        );

        $this->task->run($options);

        $lastPMId = $this->task->PrivateMessage->getLastInsertID();
        $lastPM = $this->task->PrivateMessage->findById($lastPMId);
        $lastPM = array_intersect_key($lastPM['PrivateMessage'], $expectedPM);
        $this->assertEquals($expectedPM, $lastPM);
    }
}
