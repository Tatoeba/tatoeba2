<?php
namespace App\Test\TestCase\Model;

use App\Model\Licensing;
use Cake\TestSuite\TestCase;

class LicensingTest extends TestCase
{
    public $fixtures = array(
        'app.SentencesLists',
        'app.SentencesSentencesLists',
        'app.Users',
        'app.QueuedJobs',
    );

    private $Licensing;

    public function setUp(): void
    {
        parent::setUp();
        parent::loadPlugins(['Queue']);

        $this->Licensing = new Licensing();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->Licensing);
    }

    private function assertIsSwitchListOf($list, $userId) {
        $this->assertEquals('Sentences to switch to CC0', $list->name);
        $this->assertEquals('unlisted', $list->visibility);
        $this->assertEquals('creator', $list->editable_by);
        $this->assertEquals($userId, $list->user_id);
    }

    public function testRefresh_createsNewList() {
        $SentencesLists = $this->fetchTable('SentencesLists');
        $before = $SentencesLists->find()->all();

        $this->Licensing->refreshLicenseSwitchList(7);

        $after = $SentencesLists->find()->all();
        $this->assertNotEquals($before, $after);

        $lastList = $after->last();
        $this->assertIsSwitchListOf($lastList, 7);
    }

    public function testRefresh_reCreatesNewList() {
        $SentencesLists = $this->fetchTable('SentencesLists');
        $oldList = $SentencesLists->get(4);
        $SentencesLists->delete($oldList);

        $this->Licensing->refreshLicenseSwitchList(4);

        $lastList = $SentencesLists->find()->all()->last();
        $this->assertIsSwitchListOf($lastList, 4);
    }

    public function testRefresh_savesNewListId() {
        $this->Licensing->refreshLicenseSwitchList(7);

        $listId = $this->Licensing->SentencesLists->find()->all()->last()->id;
        $settings = $this->Licensing->Users->get(7)->settings;
        $this->assertEquals($listId, $settings['license_switch_list_id']);
    }

    public function testRefresh_createsJob() {
        $this->Licensing->refreshLicenseSwitchList(4);

        $QueuedJobs = $this->fetchTable('QueuedJobs');
        $job = $QueuedJobs->find()->all()->last();
        $this->assertEquals('RefreshLicenseSwitchList', $job->job_type);
        $this->assertEquals(4, $job->job_group);
    }

    public function testRefresh_usesExistingList() {
        $SentencesLists = $this->fetchTable('SentencesLists');
        $before = $SentencesLists->find()->all();

        $this->Licensing->refreshLicenseSwitchList(4);

        $after = $SentencesLists->find()->all();
        $this->assertEquals($before, $after);

        $QueuedJobs = $this->fetchTable('QueuedJobs');
        $job = $QueuedJobs->find()->all()->last();
        $this->assertEquals(4, unserialize($job->data)['listId']);
    }

    public function testRefresh_doesNotCreatesDuplicateJob() {
        $QueuedJobs = $this->fetchTable('QueuedJobs');
        $before = $QueuedJobs->find()->count();

        $this->Licensing->refreshLicenseSwitchList(4);
        $this->Licensing->refreshLicenseSwitchList(4);

        $after = $QueuedJobs->find()->count();
        $this->assertEquals(1, $after - $before);
    }

    public function testRefresh_createsDuplicateJobIfCompleted() {
        $QueuedJobs = $this->fetchTable('QueuedJobs');
        $before = $QueuedJobs->find()->count();

        $this->Licensing->refreshLicenseSwitchList(4);
        $job = $QueuedJobs->find()->all()->last();
        $job->completed = '2019-01-01 01:02:03';
        $QueuedJobs->save($job);
        $this->Licensing->refreshLicenseSwitchList(4);

        $after = $QueuedJobs->find()->count();
        $this->assertEquals(2, $after - $before);
    }
}
