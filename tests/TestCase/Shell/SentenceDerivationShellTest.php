<?php
namespace App\Test\TestCase\Shell;

use App\Shell\SentenceDerivationShell;
use Cake\Console\ConsoleIo;
use Cake\Console\Shell;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;
use App\Shell\Walker;
use Cake\Utility\Hash;

class SentenceDerivationShellTest extends TestCase
{
    public $fixtures = array(
        'app.audios',
        'app.disabled_audios',
        'app.contributions',
        'app.sentences',
        'app.languages',
        'app.reindex_flags',
        'app.links',
        'app.users'
    );

    public function setUp()
    {
        parent::setUp();
        $io = $this->getMockBuilder(ConsoleIo::class)->getMock();
        $this->SentenceDerivationShell = $this->getMockBuilder(SentenceDerivationShell::class)
            ->setMethods(['in', 'err', 'createFile', '_stop', 'clear'])
            ->setConstructorArgs([$io])
            ->getMock();
        
        $this->SentenceDerivationShell->batchSize = 10;
        $this->SentenceDerivationShell->linkEraFirstId = 1;
        $this->SentenceDerivationShell->linkABrange = array(29, 31);

        $this->Contributions = TableRegistry::getTableLocator()->get('Contributions');
        $this->Sentences = TableRegistry::getTableLocator()->get('Sentences');
    }
    
    public function tearDown()
    {
        parent::tearDown();
        unset($this->SentenceDerivationShell);
    }

    public function testWalkerLoops() {
        $model = $this->Contributions;
        $expected = $model->find('all')->toList();

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
        $model = $this->Contributions;
        $expected = $model->find()
            ->where([
                'type' => 'link',
                'id >' => 2,
            ])
            ->limit(2)
            ->toList();

        $walker = new Walker($model);
        $walker->next();
        $walker->next(); // set pointer on id 2
        $actual = $walker->findAfter(3, function ($row) {
            return $row['type'] == 'link';
        });
        $this->assertEquals($expected, $actual);
    }

    public function testWalkerFindBefore() {
        $model = $this->Contributions;
        $expected = $model->find()
            ->where([
                'translation_id IS' => NULL,
                'id >' => 1,
                'id <=' => 2,
            ])
            ->toList();

        $walker = new Walker($model);
        $walker->next(); $walker->next();
        $walker->next(); $walker->next(); // set pointer on id 4
        $actual = $walker->findBefore(2, function ($row) {
            return $row['translation_id'] == NULL;
        });

        $this->assertEquals($expected, $actual);
    }

    public function testWalkerFindBefore_justAfterBufferRefill() {
        $model = $this->Contributions;
        $walker = new Walker($model);
        $walker->bufferSize = 10; // make buffer refill occur when loading fixture with id = 11
        $walker->allowRewindSize = 2;
        $expected = $model->find()
            ->where(['id IN' => [9, 10]])
            ->toList();

        // position pointer just after a buffer refill
        for ($i = 0; $i < $walker->bufferSize + 1; $i++) {
            $walker->next();
        }
        $actual = $walker->findBefore(2, function ($row) { return true; });

        $this->assertEquals($expected, $actual);
    }

    public function testWalkerFindBeforeHitsBufferStart() {
        $model = $this->Contributions;
        $expected = $model->get(1);

        $walker = new Walker($model);
        $walker->next();
        $walker->next();
        $actual = $walker->findBefore(3, function ($row) {
            return true;
        });

        $this->assertEquals($expected, $actual[0]);
    }

    public function testWalkerFindAfterHitsBufferEnd() {
        $model = $this->Contributions;
        $all = $model->find()->toList();
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
        $model = $this->Contributions;
        $expected = $model->find()
            ->where([
                'id IN' => [1, 3, 5, 7],
            ])
            ->toList();
        $walker = new Walker($model);
        $walker->next(); $walker->next();
        $walker->next(); $walker->next(); // set pointer on id 4
        $actual = $walker->findAround(3, function ($row) {
            return $row['id'] != 2 && $row['id'] != 6;
        });

        $this->assertEquals($expected, $actual);
    }

    private function findSentencesDerivation($ids = array())
    {
        $ids = array_keys($ids);
        $result = $this->Sentences->find()
            ->where(['id IN' => $ids])
            ->select(['id', 'based_on_id'])
            ->toList();
        return Hash::combine($result, '{n}.id', '{n}.based_on_id');
    }

    public function testRun()
    {
        $result = $this->SentenceDerivationShell->run();

        // execute all the _testRun*() methods
        // they only contain assertions about the above code
        foreach (get_class_methods($this) as $method) {
            if (substr($method, 0, 8) === "_testRun") {
                $this->{$method}($result);
            }
        }
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_findsBasicDerivation()
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

        $actualDerivation = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $actualDerivation);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_doesNotRecreateRemovedSentences()
    {
        $removedSentenceId = 13;

        $result = $this->Sentences->findById($removedSentenceId)->toList();
        $this->assertEmpty($result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_doesNotTouchSentencesWithoutLog()
    {
        $expectedDerivation = array(
            15 => null,
            16 => null,
            17 => null,
        );

        $actualDerivation = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $actualDerivation);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_doesNotTouchSentencesCreatedWithDatetimeZero()
    {
        $expectedDerivation = array(
            18 => null,
            19 => null,
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_pattern_createA_linkBA_linkAB()
    {
        $expectedDerivation = array(
            20 => 18
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_pattern_linkBA_linkAB_createA()
    {
        $expectedDerivation = array(
            21 => 19
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_twoPairsAddedAtTheSameTime()
    {
        $expectedDerivation = array(
            22 => 0,
            23 => 15,
            24 => 22,
            25 => 17,
            26 => 16,
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_longDatetimeDifference()
    {
        $expectedDerivation = array(
            28 => 27,
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_returnsNumberOfSentencesProceeded($actual)
    {
        /* 
        Among existing sentences, 27 are being skipped:
            #10: doesn't have logs
            #15: no log of type 'sentence'
            #16: no log of type 'sentence'
            #17: no log of type 'sentence'
            #18: date 0000-00-00
            #19: date 0000-00-00
            #22: no log of type 'sentence' 
            #43: Already has based_on_id
            #46: Only has one link logged
            #48: Already has based_on_id
            #49: Already has based_on_id
            #50: Already has based_on_id
            #51: Already has based_on_id
            #52: Already has based_on_id
            #53: Already has based_on_id
            #54: Already has based_on_id
            #55: Already has based_on_id
            #56: Already has based_on_id
            #57: Already has based_on_id
            #58: Already has based_on_id
            #59: Already has based_on_id
            #60: Already has based_on_id
            #61: Already has based_on_id
            #62: Already has based_on_id
            #63: Already has based_on_id
            #64: Already has based_on_id
            #65: Already has based_on_id
        */
        
        $totalSentences = $this->Sentences->find()->count();
        $totalSkipped = 27;
        $expected = $totalSentences - $totalSkipped;
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

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_multipleCreationRecords()
    {
        $expectedDerivation = array(
            29 => '0',
            30 => 29,
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_linkedMoreThanOnceJustAfterCreation()
    {
        $expectedDerivation = array(
            31 => 28,
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_threePairsAddedAtTheSameTime()
    {
        $expectedDerivation = array(
            32 => 1,
            33 => 2,
            34 => 3,
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_updatedJustAfterCreation()
    {
        $expectedDerivation = array(
            35 => '0',
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_fivePairsAddedAtTheSameTime()
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

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_doesntUpdateIfBasedOnIdAlreadySet()
    {
        $expectedDerivation = array(
            43 => 42424242,
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_multipleCreationRecordsWithConfusingLogs()
    {
        $expectedDerivation = array(
            44 => '0',
            45 => '0',
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    // Stuff the logs with $n concurrent entries
    private function stuffLogs($n)
    {
        $Contributions = $this->Contributions;
        for ($i = 0; $i < $n%4; $i++) {
            $contribution = $Contributions->newEntity([
                'sentence_id' => 10000+$n,
                'sentence_lang' => 'eng',
                'translation_id' => null,
                'translation_lang' => null,
                'script' => null,
                'text' => "Some random sentence $i.",
                'action' => 'insert',
                'user_id' => '1',
                'datetime' => 'NOW()',
                'type' => 'sentence',
            ]);
            $Contributions->save($contribution);
        }
        for ($i = 0; $i < (int)($n/4); $i++) {
            foreach (array('delete', 'insert') as $action) {
                $Contributions->saveLinkContribution(1, 2, $action);
                $Contributions->saveLinkContribution(2, 1, $action);
            }
        }
    }

    public function testRun_hugeNumberOfRowsBetweenCreationAndLink()
    {
        $linkTo = 1;
        $sent = $this->Sentences->saveNewSentence(
            'I am terrible.', 'eng', 7, 0, null, 'CC BY 2.0 FR'
        );
        $id = $sent['id'];
        $this->stuffLogs(84);
        $this->Sentences->Links->add($linkTo, $id);

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
        $this->Contributions->deleteAll(['id > 0']);
        $sent = $this->Sentences->saveNewSentence(
            'I am terrible too.', 'eng', 7, 0, null, 'CC BY 2.0 FR'
        );
        $id = $sent['id'];
        $this->stuffLogs(85);
        $this->Sentences->Links->add($id, $linkTo);

        $expectedDerivation = array(
            $id => null, // we don't handle this special case yet
        );

        $this->SentenceDerivationShell->run();

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_sentencesWithOneLinkAreLeftUntouched()
    {
        $expectedDerivation = array(
            46 => null,
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }

    /**
     * Executed by testRun(), for faster tests
     */
    public function _testRun_sentencesLinkedByDifferentUser()
    {
        $expectedDerivation = array(
            47 => '0',
        );

        $result = $this->findSentencesDerivation($expectedDerivation);
        $this->assertEquals($expectedDerivation, $result);
    }
}
