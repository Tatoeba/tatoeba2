<?php
namespace App\Test\TestCase\Model;

use App\Model\Licensing;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class LicensingTest extends TestCase
{
    public $fixtures = array(
        'app.sentences_lists',
        'app.sentences_sentences_lists',
        'app.users',
        'app.queued_jobs',
    );

    public function setUp()
    {
        parent::setUp();
        parent::loadPlugins(['Queue']);

        $this->Licensing = new Licensing();
    }

    public function tearDown()
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
        $SentencesLists = TableRegistry::get('SentencesLists');
        $before = $SentencesLists->find()->all();

        $this->Licensing->refreshLicenseSwitchList(7);

        $after = $SentencesLists->find()->all();
        $this->assertNotEquals($before, $after);

        $list = $SentencesLists->find()->last();
        $this->assertIsSwitchListOf($list, 7);
    }

    public function testRefresh_reCreatesNewList() {
        $SentencesLists = TableRegistry::get('SentencesLists');
        $oldList = $SentencesLists->get(4);
        $SentencesLists->delete($oldList);

        $this->Licensing->refreshLicenseSwitchList(4);

        $list = $SentencesLists->find()->last();
        $this->assertIsSwitchListOf($list, 4);
    }

    public function testRefresh_savesNewListId() {
        $this->Licensing->refreshLicenseSwitchList(7);

        $listId = $this->Licensing->SentencesLists->find()->last()->id;
        $settings = $this->Licensing->Users->get(7)->settings;
        $this->assertEquals($listId, $settings['license_switch_list_id']);
    }

    public function testRefresh_createsJob() {
        $this->Licensing->refreshLicenseSwitchList(4);

        $QueuedJobs = TableRegistry::get('QueuedJobs');
        $job = $QueuedJobs->find()->last();
        $this->assertEquals('RefreshLicenseSwitchList', $job->job_type);
        $this->assertEquals(4, $job->job_group);
    }

    public function testRefresh_usesExistingList() {
        $SentencesLists = TableRegistry::get('SentencesLists');
        $before = $SentencesLists->find()->all();

        $this->Licensing->refreshLicenseSwitchList(4);

        $after = $SentencesLists->find()->all();
        $this->assertEquals($before, $after);

        $QueuedJobs = TableRegistry::get('QueuedJobs');
        $job = $QueuedJobs->find()->last();
        $this->assertEquals(4, unserialize($job->data)['listId']);
    }

    public function testRefresh_doesNotCreatesDuplicateJob() {
        $QueuedJobs = TableRegistry::get('QueuedJobs');
        $before = $QueuedJobs->find()->count();

        $this->Licensing->refreshLicenseSwitchList(4);
        $this->Licensing->refreshLicenseSwitchList(4);

        $after = $QueuedJobs->find()->count();
        $this->assertEquals(1, $after - $before);
    }

    public function testRefresh_createsDuplicateJobIfCompleted() {
        $QueuedJobs = TableRegistry::get('QueuedJobs');
        $before = $QueuedJobs->find()->count();

        $this->Licensing->refreshLicenseSwitchList(4);
        $job = $QueuedJobs->find()->last();
        $job->completed = '2019-01-01 01:02:03';
        $QueuedJobs->save($job);
        $this->Licensing->refreshLicenseSwitchList(4);

        $after = $QueuedJobs->find()->count();
        $this->assertEquals(2, $after - $before);
    }
}
