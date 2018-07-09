<?php
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('Shell', 'Console');
App::uses('SentenceDerivationShell', 'Console/Command');

class SentenceDerivationShellTest extends CakeTestCase
{
    public $fixtures = array(
        'app.contribution',
        'app.sentence',
        'app.reindex_flag',
    );

    public function setUp()
    {
        parent::setUp();
        $out = $this->getMock('ConsoleOutput', array(), array(), '', false);
        $in = $this->getMock('ConsoleInput', array(), array(), '', false);
        
        $this->SentenceDerivationShell = $this->getMock(
            'SentenceDerivationShell', 
            array('in', 'err', 'createFile', '_stop', 'clear'),
            array($out, $out, $in)
        );
    }
    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->SentenceDerivationShell);
    }

    public function testWalkerLoops() {
        $model = $this->SentenceDerivationShell->Contribution;
        $expected = $model->find('all');

        $walker = new Walker($model);
        $walker->bufferSize = 10;
        $walker->allowRewindSize = 4;
        $actual = array();
        while ($row = $walker->next()) {
            $actual[] = $row;
        }

        $this->assertEquals($expected, $actual);
    }

    public function testWalkerFindAfter() {
        $model = $this->SentenceDerivationShell->Contribution;
        $expected = $model->find('all', array(
            'conditions' => array(
                'type' => 'link',
                'id >' => 2,
            ),
            'limit' => 2
        ));

        $walker = new Walker($model);
        $walker->next();
        $walker->next(); // set pointer on id 2
        $actual = $walker->findAfter(3, function ($row) {
            return $row['Contribution']['type'] == 'link';
        });

        $this->assertEquals($expected, $actual);
    }

    public function testWalkerFindBefore() {
        $model = $this->SentenceDerivationShell->Contribution;
        $expected = $model->find('all', array(
            'conditions' => array(
                'translation_id' => NULL,
                'id >' => 1,
                'id <=' => 2,
            )
        ));

        $walker = new Walker($model);
        $walker->next(); $walker->next();
        $walker->next(); $walker->next(); // set pointer on id 4
        $actual = $walker->findBefore(2, function ($row) {
            return $row['Contribution']['translation_id'] == NULL;
        });

        $this->assertEquals($expected, $actual);
    }

    public function testWalkerFindBeforeHitsBufferStart() {
        $model = $this->SentenceDerivationShell->Contribution;
        $expected = $model->find('all', array('conditions' => array('id' => 1)));

        $walker = new Walker($model);
        $walker->next();
        $walker->next();
        $actual = $walker->findBefore(3, function ($row) {
            return true;
        });

        $this->assertEquals($expected, $actual);
    }

    public function testWalkerFindAfterHitsBufferEnd() {
        $model = $this->SentenceDerivationShell->Contribution;
        $all = $model->find('all');
        $expected = array(end($all));

        $walker = new Walker($model);
        for ($i = 0; $i < count($all) - 1; $i++) {
            $walker->next();
        }
        $actual = $walker->findAfter(3, function ($row) {
            return true;
        });

        $this->assertEquals($expected, $actual);
    }

    public function testWalkerFindAround() {
        $model = $this->SentenceDerivationShell->Contribution;
        $expected = $model->find('all', array(
            'conditions' => array(
                'id' => array(1, 3, 5, 7),
            ),
        ));
        $walker = new Walker($model);
        $walker->next(); $walker->next();
        $walker->next(); $walker->next(); // set pointer on id 4
        $actual = $walker->findAround(3, function ($row) {
            return $row['Contribution']['id'] != 2 && $row['Contribution']['id'] != 6;
        });

        $this->assertEquals($expected, $actual);
    }

    private function findSentencesDerivation($ids = array())
    {
        $ids = array_keys($ids);
        $result = $this->SentenceDerivationShell->Sentence->findAllById($ids, array('id', 'based_on_id'));
        return Set::combine($result, '{n}.Sentence.id', '{n}.Sentence.based_on_id');
    }

    public function testSetSentenceBasedOnId_findsBasicDerivation()
    {
        $expectedDerivation = array(
            1 => 0,    /* sentence 1 is original */
            2 => 1,    /* sentence 2 is based on sentence 1 */
            3 => 1,    /* sentence 3 is based on sentence 1 */
            4 => 2,    /* and so on */
            5 => 2,
            6 => 4,
            7 => 0,
            8 => 0,
            9 => 0,
            11 => 0,
            12 => 0,
        );

        $this->SentenceDerivationShell->setSentenceBasedOnId();

        $actualDerivation = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $actualDerivation);
    }

    public function testSetSentenceBasedOnId_doesNotRecreateRemovedSentences()
    {
        $removedSentenceId = 13;

        $this->SentenceDerivationShell->setSentenceBasedOnId();

        $result = $this->SentenceDerivationShell->Sentence->findById($removedSentenceId);
        $this->assertEmpty($result);
    }

    public function testSetSentenceBasedOnId_doesNotTouchSentencesWithoutLog()
    {
        $expectedDerivation = array(
            15 => null,
            16 => null,
            17 => null,
        );

        $this->SentenceDerivationShell->setSentenceBasedOnId();

        $actualDerivation = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $actualDerivation);
    }
    public function testSetSentenceBasedOnId_doesNotTouchSentencesCreatedWithDatetimeZero()
    {
        $expectedDerivation = array(
            18 => null,
            19 => null,
        );

        $this->SentenceDerivationShell->setSentenceBasedOnId();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testSetSentenceBasedOnId_pattern_createA_linkBA_linkAB()
    {
        $expectedDerivation = array(
            20 => 18
        );

        $this->SentenceDerivationShell->setSentenceBasedOnId();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testSetSentenceBasedOnId_pattern_linkBA_linkAB_createA()
    {
        $expectedDerivation = array(
            21 => 19
        );

        $this->SentenceDerivationShell->setSentenceBasedOnId();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }
}
