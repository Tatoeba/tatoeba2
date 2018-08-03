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
        'app.link',
        'app.user',
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
        $this->SentenceDerivationShell->batchSize = 10;
        $this->SentenceDerivationShell->linkEraFirstId = 1;
        $this->SentenceDerivationShell->linkABrange = array(29, 31);
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

    public function testWalkerFindBefore_justAfterBufferRefill() {
        $model = $this->SentenceDerivationShell->Contribution;
        $walker = new Walker($model);
        $walker->bufferSize = 10; // make buffer refill occur when loading fixture with id = 11
        $walker->allowRewindSize = 2;
        $expected = $model->find('all', array(
            'conditions' => array(
                'id' => array(9, 10)
            )
        ));

        // position pointer just after a buffer refill
        for ($i = 0; $i < $walker->bufferSize + 1; $i++) {
            $walker->next();
        }
        $actual = $walker->findBefore(2, function ($row) { return true; });

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

    public function testRun_findsBasicDerivation()
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
            14 => 0,
        );

        $this->SentenceDerivationShell->run();

        $actualDerivation = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $actualDerivation);
    }

    public function testRun_doesNotRecreateRemovedSentences()
    {
        $removedSentenceId = 13;

        $this->SentenceDerivationShell->run();

        $result = $this->SentenceDerivationShell->Sentence->findById($removedSentenceId);
        $this->assertEmpty($result);
    }

    public function testRun_doesNotTouchSentencesWithoutLog()
    {
        $expectedDerivation = array(
            15 => null,
            16 => null,
            17 => null,
        );

        $this->SentenceDerivationShell->run();

        $actualDerivation = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $actualDerivation);
    }

    public function testRun_doesNotTouchSentencesCreatedWithDatetimeZero()
    {
        $expectedDerivation = array(
            18 => null,
            19 => null,
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_pattern_createA_linkBA_linkAB()
    {
        $expectedDerivation = array(
            20 => 18
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_pattern_linkBA_linkAB_createA()
    {
        $expectedDerivation = array(
            21 => 19
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_twoPairsAddedAtTheSameTime()
    {
        $expectedDerivation = array(
            22 => 0,
            23 => 15,
            24 => 22,
            25 => 17,
            26 => 16,
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_longDatetimeDifference()
    {
        $expectedDerivation = array(
            28 => 27,
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_returnsNumberOfSentencesProceeded()
    {
        $expected = 36;
        $actual = $this->SentenceDerivationShell->run();
        $this->assertEquals($expected, $actual);
    }

    public function testRun_honorsLinkEra()
    {
        $expectedDerivation = array(
            1 => null,
            2 => null,
            3 => null,
            4 => null,
            5 => 2,
            6 => 4,
            7 => 0,
        );
        $this->SentenceDerivationShell->linkEraFirstId = 11;

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_multipleCreationRecords()
    {
        $expectedDerivation = array(
            29 => '0',
            30 => 29,
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_linkedMoreThanOnceJustAfterCreation()
    {
        $expectedDerivation = array(
            31 => 28,
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_threePairsAddedAtTheSameTime()
    {
        $expectedDerivation = array(
            32 => 1,
            33 => 2,
            34 => 3,
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_updatedJustAfterCreation()
    {
        $expectedDerivation = array(
            35 => '0',
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_fivePairsAddedAtTheSameTime()
    {
        $expectedDerivation = array(
            36 => '0',
            37 => '0',
            38 => 1,
            39 => 4,
            40 => 3,
            41 => 5,
            42 => 2,
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_doesntUpdateIfBasedOnIdAlreadySet()
    {
        $expectedDerivation = array(
            43 => 42424242,
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_multipleCreationRecordsWithConfusingLogs()
    {
        $expectedDerivation = array(
            44 => '0',
            45 => '0',
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    // Stuff the logs with $n concurrent entries
    private function stuffLogs($n)
    {
        for ($i = 0; $i < $n%4; $i++) {
            $this->SentenceDerivationShell->Sentence->saveNewSentence(
                "Some random sentence $i.", 'eng', 1, 0, 0, 'CC BY 2.0 FR'
            );
        }
        for ($i = 0; $i < (int)($n/4); $i++) {
            foreach (array('delete', 'insert') as $action) {
                $this->SentenceDerivationShell->Contribution->saveLinkContribution(1, 2, $action);
                $this->SentenceDerivationShell->Contribution->saveLinkContribution(2, 1, $action);
            }
        }
    }

    public function testRun_hugeNumberOfRowsBetweenCreationAndLink()
    {
        $linkTo = 1;
        $sent = $this->SentenceDerivationShell->Sentence->saveNewSentence(
            'I am terrible.', 'eng', 7, 0, null, 'CC BY 2.0 FR'
        );
        $id = $sent['Sentence']['id'];
        $this->stuffLogs(85);
        $this->SentenceDerivationShell->Sentence->Link->add($linkTo, $id);

        $expectedDerivation = array(
            $id => $linkTo,
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_lookAheadStepsOnOneLinkOfUnrelatedPair()
    {
        $linkTo = 1;
        $this->SentenceDerivationShell->Contribution->deleteAll(array('1=1'));
        $sent = $this->SentenceDerivationShell->Sentence->saveNewSentence(
            'I am terrible too.', 'eng', 7, 0, null, 'CC BY 2.0 FR'
        );
        $id = $sent['Sentence']['id'];
        $this->stuffLogs(86);
        $this->SentenceDerivationShell->Sentence->Link->add($id, $linkTo);

        $expectedDerivation = array(
            $id => null, // we don't handle this special case yet
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    public function testRun_sentencesWithOneLinkAreLeftUntouched()
    {
        $expectedDerivation = array(
            46 => null,
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }
}
