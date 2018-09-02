<?php
App::uses('Contribution', 'Model');

/**
 * Contribution Test Case
 */
class ContributionTest extends CakeTestCase {

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

    public function testLogSentenceUpdate_logsLicenseUpdate() {
        CurrentUser::store(array('id' => 7));
        $expectedLog = array('Contribution' => array(
            'sentence_id' => '48',
            'sentence_lang' => null,
            'translation_id' => null,
            'translation_lang' => null,
            'script' => null,
            'text' => 'CC0 1.0',
            'action' => 'update',
            'user_id' => '7',
            'type' => 'license',
        ));
        $event = new CakeEvent('Model.Sentence.updated', $this, array(
            'id' => 48,
            'data' => array('license' => 'CC0 1.0'),
        ));

        $this->Contribution->Sentence->getEventManager()->dispatch($event);

        $newLog = $this->Contribution->findById($this->Contribution->getLastInsertID());
        $newLog = array_intersect_key($expectedLog, $newLog);
        $this->assertEquals($expectedLog, $newLog);
    }
}
