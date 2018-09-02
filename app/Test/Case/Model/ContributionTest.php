<?php
App::uses('Contribution', 'Model');
App::uses('CurrentUser', 'Model');

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

    public function testLogSentence_logsLicense() {
        CurrentUser::store(array('id' => 7));
        $expectedLog = array(
            'sentence_id' => '48',
            'sentence_lang' => null,
            'translation_id' => null,
            'translation_lang' => null,
            'script' => null,
            'text' => 'CC0 1.0',
            'user_id' => '7',
            'type' => 'license',
        );

        $expectedLog['action'] = 'insert';
        $event = new CakeEvent('Model.Sentence.saved', $this, array(
            'id' => 48,
            'created' => true,
            'data' => array('license' => 'CC0 1.0'),
        ));
        $this->Contribution->Sentence->getEventManager()->dispatch($event);
        $newLog = $this->Contribution->findById($this->Contribution->getLastInsertID());
        $newLog = array_intersect_key($newLog['Contribution'], $expectedLog);
        $this->assertEquals($expectedLog, $newLog);

        $expectedLog['action'] = 'update';
        $event = new CakeEvent('Model.Sentence.saved', $this, array(
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
