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
