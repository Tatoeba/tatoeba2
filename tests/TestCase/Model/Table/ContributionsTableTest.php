<?php
namespace App\Test\TestCase\Model;

use App\Model\CurrentUser;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

/**
 * Contribution Test Case
 */
class ContributionTest extends TestCase {

    public $fixtures = array(
        'app.Contributions',
        'app.Sentences',
        'app.Languages',
        'app.Links',
        'app.UsersLanguages',
    );

    public function setUp() {
        parent::setUp();
        $this->Contribution = TableRegistry::getTableLocator()->get('Contributions');
    }

    public function tearDown() {
        unset($this->Contribution);

        parent::tearDown();
    }

    public function testLogSentenceUpdate_logsSentenceInsert() {
        CurrentUser::store(['id' => 7]);
        $expectedLog = [
            'sentence_id' => '48',
            'sentence_lang' => 'eng',
            'translation_id' => null,
            'translation_lang' => null,
            'script' => null,
            'text' => 'New sentence.',
            'action' => 'insert',
            'user_id' => '7',
            'type' => 'sentence',
        ];
        $data = $this->Contribution->Sentences->newEntity([
            'lang' => 'eng',
            'text' => 'New sentence.'
        ]);
        $event = new Event('Model.Sentence.saved', $this, array(
            'id' => 48,
            'created' => true,
            'data' => $data,
        ));
        
        $this->Contribution->Sentences->getEventManager()->dispatch($event);

        $log = $this->Contribution->find()
            ->where(['sentence_id' => 48])
            ->order(['id' => 'DESC'])
            ->disableHydration()
            ->first();
        $this->assertArraySubset($expectedLog, $log);
    }

    public function testLogSentenceUpdate_logsSentenceUpdate() {
        CurrentUser::store(['id' => 7]);
        $expectedLog = [
            'sentence_id' => '48',
            'sentence_lang' => 'eng',
            'translation_id' => null,
            'translation_lang' => null,
            'script' => null,
            'text' => 'Edited sentence.',
            'action' => 'update',
            'user_id' => '7',
            'type' => 'sentence',
        ];

        $data = $this->Contribution->Sentences->newEntity([
            'id' => 48,
            'lang' => 'eng',
            'text' => 'Edited sentence.'
        ]);
        $event = new Event('Model.Sentence.saved', $this, array(
            'id' => 48,
            'created' => false,
            'data' => $data,
        ));
        
        $this->Contribution->Sentences->getEventManager()->dispatch($event);

        $log = $this->Contribution
                    ->find()
                    ->order(['id' => 'DESC'])
                    ->disableHydration()
                    ->first();
        $this->assertArraySubset($expectedLog, $log);
    }

    public function testLogSentenceUpdate_logsLicenseInsert() {
        CurrentUser::store(array('id' => 7));
        $expectedLog = array(
            'sentence_id' => '48',
            'sentence_lang' => null,
            'translation_id' => null,
            'translation_lang' => null,
            'script' => null,
            'text' => 'CC0 1.0',
            'action' => 'insert',
            'user_id' => '7',
            'type' => 'license',
        );
        $data = $this->Contribution->Sentences->newEntity([
            'license' => 'CC0 1.0'
        ]);
        $event = new Event('Model.Sentence.saved', $this, array(
            'id' => 48,
            'created' => true,
            'data' => $data,
        ));
        
        $this->Contribution->Sentences->getEventManager()->dispatch($event);

        $log = $this->Contribution
                    ->find()
                    ->order(['id' => 'DESC'])
                    ->disableHydration()
                    ->first();
        $this->assertArraySubset($expectedLog, $log);
    }

    public function testLogSentenceUpdate_logsLicenseUpdate() {
        CurrentUser::store(array('id' => 7));
        $expectedLog = array(
            'sentence_id' => '48',
            'sentence_lang' => null,
            'translation_id' => null,
            'translation_lang' => null,
            'script' => null,
            'text' => 'CC0 1.0',
            'action' => 'update',
            'user_id' => '7',
            'type' => 'license',
        );
        $data = $this->Contribution->Sentences->get(48);
        $data->license = 'CC0 1.0';
        $event = new Event('Model.Sentence.saved', $this, array(
            'id' => 48,
            'created' => false,
            'data' => $data,
        ));

        $this->Contribution->Sentences->getEventManager()->dispatch($event);

        $log = $this->Contribution
                    ->find()
                    ->order(['id' => 'DESC'])
                    ->disableHydration()
                    ->first();
        $this->assertArraySubset($expectedLog, $log);
    }

    public function testGetTodayContributions() {
        $this->Contribution->saveSentenceContribution(123, 'eng', null, 'test', 'insert');
        $count = $this->Contribution->getTodayContributions();
        $this->assertEquals(1, $count);
    }
}
