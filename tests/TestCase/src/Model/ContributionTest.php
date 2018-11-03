<?php
namespace App\Test\TestCase\Model;

use App\Model\Contribution;
use App\Model\CurrentUser;
use Cake\TestSuite\TestCase;

/**
 * Contribution Test Case
 */
class ContributionTest extends TestCase {

    public $fixtures = array(
        'app.contribution',
        'app.sentence',
        'app.users_language',
    );

    public function setUp() {
        parent::setUp();
        $this->Contribution = ClassRegistry::init('Contribution');
    }

    public function tearDown() {
        unset($this->Contribution);

        parent::tearDown();
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
        $event = new Event('Model.Sentence.saved', $this, array(
            'id' => 48,
            'created' => true,
            'data' => array('license' => 'CC0 1.0'),
        ));

        $this->Contribution->Sentence->getEventManager()->dispatch($event);

        $newLog = $this->Contribution->findById($this->Contribution->getLastInsertID());
        $newLog = array_intersect_key($newLog['Contribution'], $expectedLog);
        $this->assertEquals($expectedLog, $newLog);
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
        $event = new Event('Model.Sentence.saved', $this, array(
            'id' => 48,
            'created' => false,
            'data' => array('license' => 'CC0 1.0'),
        ));

        $this->Contribution->Sentence->getEventManager()->dispatch($event);

        $newLog = $this->Contribution->findById($this->Contribution->getLastInsertID());
        $newLog = array_intersect_key($newLog['Contribution'], $expectedLog);
        $this->assertEquals($expectedLog, $newLog);
    }
}
