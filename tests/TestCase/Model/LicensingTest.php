<?php
namespace App\Test\TestCase\Model;

use App\Model\Licensing;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class LicensingTest extends TestCase
{
    public $fixtures = array(
        'app.sentences_lists',
        'app.users',
        'app.queued_jobs',
        'app.aros',
    );

    public function setUp()
    {
        parent::setUp();
        Configure::write('Acl.database', 'test');
        Plugin::load('Queue');

        $this->Licensing = new Licensing();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Licensing);
    }

    public function testRefresh_createsNewList() {
        $SentencesLists = TableRegistry::get('SentencesLists');
        $before = $SentencesLists->find()->all();

        $this->Licensing->refresh_license_switch_list(7);

        $after = $SentencesLists->find()->all();
        $this->assertNotEquals($before, $after);

        $list = $SentencesLists->find()->last();
        $this->assertEquals('Sentences to switch to CC0', $list->name);
        $this->assertEquals('unlisted', $list->visibility);
        $this->assertEquals('creator', $list->editable_by);
        $this->assertEquals(7, $list->user_id);
    }

    public function testRefresh_savesNewListId() {
        $this->Licensing->refresh_license_switch_list(7);

        $listId = $this->Licensing->SentencesLists->find()->last()->id;
        $settings = $this->Licensing->Users->get(7)->settings;
        $this->assertEquals($listId, $settings['license_switch_list_id']);
    }

    public function testRefresh_createsJob() {
        $this->Licensing->refresh_license_switch_list(4);

        $QueuedJobs = TableRegistry::get('QueuedJobs');
        $job = $QueuedJobs->find()->last();
        $this->assertEquals('RefreshLicenseSwitchList', $job->job_type);
        $this->assertEquals(4, $job->job_group);
    }

    public function testRefresh_usesExistingList() {
        $SentencesLists = TableRegistry::get('SentencesLists');
        $before = $SentencesLists->find()->all();

        $this->Licensing->refresh_license_switch_list(4);

        $after = $SentencesLists->find()->all();
        $this->assertEquals($before, $after);

        $QueuedJobs = TableRegistry::get('QueuedJobs');
        $job = $QueuedJobs->find()->last();
        $this->assertEquals(4, unserialize($job->data)['listId']);
    }

    public function testRefresh_doesNotCreatesDuplicateJob() {
        $this->Licensing->refresh_license_switch_list(4);
        $this->Licensing->refresh_license_switch_list(4);

        $QueuedJobs = TableRegistry::get('QueuedJobs');
        $this->assertEquals(1, $QueuedJobs->find()->count());
    }

    public function testRefresh_createsDuplicateJobIfCompleted() {
        $this->Licensing->refresh_license_switch_list(4);
        $QueuedJobs = TableRegistry::get('QueuedJobs');
        $job = $QueuedJobs->find()->last();
        $job->completed = '2019-01-01 01:02:03';
        $QueuedJobs->save($job);
        $this->Licensing->refresh_license_switch_list(4);

        $QueuedJobs = TableRegistry::get('QueuedJobs');
        $this->assertEquals(2, $QueuedJobs->find()->count());
    }
}
