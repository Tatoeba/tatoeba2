<?php
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('QueueSwitchSentencesLicenseTask', 'Console/Command/Task');

class QueueSwitchSentencesLicenseTaskTest extends CakeTestCase
{
    public $fixtures = array(
        'app.sentence',
        'app.contribution',
        'app.reindex_flag',
        'app.users_language',
        'app.private_message',
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
        );

        $this->task->run($options);

        $lastPMId = $this->task->PrivateMessage->getLastInsertID();
        $lastPM = $this->task->PrivateMessage->findById($lastPMId);
        $lastPM = array_intersect_key($lastPM['PrivateMessage'], $expectedPM);
        $this->assertEquals($expectedPM, $lastPM);
    }
}
