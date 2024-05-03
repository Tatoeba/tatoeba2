<?php
namespace App\Test\TestCase\Shell;

use App\Lib\Autotranscription;
use App\Shell\TranscriptionsShell;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\ConsoleIntegrationTestCase;

class TranscriptionsShellTest extends ConsoleIntegrationTestCase
{
    public $fixtures = [
        'app.Sentences',
        'app.Transcriptions',
        'app.Users',
        'app.Contributions',
        'app.ReindexFlags',
    ];

    public $io;
    public $TS;

    private function getAutotranscriptionMock() {
        $AT = $this->getMockBuilder(Autotranscription::class)
            ->setMethods([
                'jpn_Jpan_to_Hrkt_generate',
                'jpn_Jpan_to_Hrkt_validate',
                'cmn_detectScript',
            ])
            ->getMock();
        $AT->expects($this->any())
           ->method('jpn_Jpan_to_Hrkt_generate')
           ->will($this->returnValue('transcription in furigana'));
        $AT->expects($this->any())
           ->method('jpn_Jpan_to_Hrkt_validate')
           ->will($this->returnValue(true));
        $AT->expects($this->any())
           ->method('cmn_detectScript')
           ->will($this->returnValue('Hant'));

        return $AT;
    }

    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $this->TS = new TranscriptionsShell($this->io);
        $this->TS->initialize();

        $AT = $this->getAutotranscriptionMock();
        $this->TS->Transcriptions->setAutotranscription($AT);

        Configure::write('AutoTranscriptions.enabled', true);
    }

    public function tearDown()
    {
        unset($this->TS);

        parent::tearDown();
    }

    public function testAutogen_forAllSentences()
    {
        $transcrBefore = $this->TS->Transcriptions->find()->where(['lang' => 'jpn'])->count();

        $this->TS->autogen('jpn');

        $transcrAfter = $this->TS->Transcriptions->find()->where(['lang' => 'jpn'])->count();
        $jpnSentences = TableRegistry::get('Sentences')->find()->where(['lang' => 'jpn'])->count();

        $this->assertGreaterThan($transcrBefore, $transcrAfter);
        $this->assertEquals($transcrAfter, $jpnSentences);
    }

    public function testAutogen_regenExisting()
    {
        $before = $this->TS->Transcriptions->find()->where(['sentence_id' => 10])->first();
        $this->TS->autogen('jpn');
        $after = $this->TS->Transcriptions->find()->where(['sentence_id' => 10])->first();

        $this->assertNotEquals($before->text, $after->text);
    }

    public function testAutogen_batched()
    {
        $this->TS->batchOperationSize = 2;
        $this->testAutogen_forAllSentences();
    }

    public function testSetSentencesScript()
    {
        $expectedScripts = ['Hant'];

        $this->TS->setSentencesScript('cmn');

        $scripts = TableRegistry::get('Sentences')
            ->find('list', ['valueField' => 'script'])
            ->where(['lang' => 'cmn'])
            ->toArray();

        $scripts = array_keys(array_flip($scripts));
        $this->assertEquals($expectedScripts, $scripts);
    }

    public function testSetContributionsScript()
    {
        $expectedScripts = ['Hant'];

        $this->TS->setContributionsScript('cmn');

        $scripts = TableRegistry::get('Contributions')
            ->find('list', ['valueField' => 'script'])
            ->where(['sentence_lang' => 'cmn'])
            ->toArray();

        $scripts = array_keys(array_flip($scripts));
        $this->assertEquals($expectedScripts, $scripts);
    }

    public function testSetSentencesScriptDoesNotUpdateModifiedField()
    {
        $before = TableRegistry::get('Sentences')
            ->find('list', ['valueField' => 'modified'])
            ->where(['lang' => 'cmn'])
            ->toArray();

        $this->TS->setSentencesScript('cmn');

        $after = TableRegistry::get('Sentences')
            ->find('list', ['valueField' => 'modified'])
            ->where(['lang' => 'cmn'])
            ->toArray();

        $this->assertEquals($before, $after);
    }
}
