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
    public function testSetSentenceBasedOnId_findsOriginalSentence()
    {
        $expectedOriginalSentences = array(1, 7, 8, 9, 11, 12, 14, 18);

        $this->SentenceDerivationShell->setSentenceBasedOnId();

        $actualOriginals = $this->SentenceDerivationShell
                                ->Sentence
                                ->findAllByBasedOnId(null);
        $actualOriginals = Set::classicExtract($actualOriginals, '{n}.Sentence.id');
        $this->assertEquals($expectedOriginalSentences, $actualOriginals);
    }
}
