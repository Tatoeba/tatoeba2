<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use App\Command\TranscriptionsCommand;
use App\Lib\Autotranscription;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

class TranscriptionsCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public array $fixtures = [
        'app.Sentences',
        'app.Transcriptions',
        'app.Users',
        'app.Contributions',
        'app.ReindexFlags',
    ];

    private $Transcriptions;

    private function getAutotranscriptionMock() {
        $AT = $this->getMockBuilder(Autotranscription::class)
            ->onlyMethods([
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

    public function setUp(): void
    {
        parent::setUp();

        $AT = $this->getAutotranscriptionMock();
        $this->Transcriptions = $this->fetchTable('Transcriptions');
        $this->Transcriptions->setAutotranscription($AT);

        Configure::write('AutoTranscriptions.enabled', true);
    }

    public function tearDown(): void
    {
        unset($this->Transcriptions);

        parent::tearDown();
    }

    /**
     * @testWith [""]
     *           ["--batchSize 2"]
     */
    public function testAutogen_forAllSentences($opts)
    {
        $transcrBefore = $this->Transcriptions->find()->where(['lang' => 'jpn'])->count();

        $this->exec("transcriptions $opts autogen_transcriptions_text jpn");

        $transcrAfter = $this->Transcriptions->find()->where(['lang' => 'jpn'])->count();
        $jpnSentences = $this->fetchTable('Sentences')->find()->where(['lang' => 'jpn'])->count();

        $this->assertGreaterThan($transcrBefore, $transcrAfter);
        $this->assertEquals($transcrAfter, $jpnSentences);
    }

    public function testAutogen_regenExisting()
    {
        $before = $this->Transcriptions->find()->where(['sentence_id' => 10])->first();
        $this->exec('transcriptions autogen_transcriptions_text jpn');
        $after = $this->Transcriptions->find()->where(['sentence_id' => 10])->first();

        $this->assertNotEquals($before->text, $after->text);
    }

    public function testSetSentencesScript()
    {
        $expectedScripts = ['Hant'];

        $this->exec('transcriptions autodetect_sentences_script cmn');

        $scripts = $this->fetchTable('Sentences')
            ->find('list', valueField: 'script')
            ->where(['lang' => 'cmn'])
            ->toArray();

        $scripts = array_keys(array_flip($scripts));
        $this->assertEquals($expectedScripts, $scripts);
    }

    public function testSetContributionsScript()
    {
        $expectedScripts = ['Hant'];

        $this->exec('transcriptions autodetect_contributions_script cmn');

        $scripts = $this->fetchTable('Contributions')
            ->find('list', valueField: 'script')
            ->where(['sentence_lang' => 'cmn'])
            ->toArray();

        $scripts = array_keys(array_flip($scripts));
        $this->assertEquals($expectedScripts, $scripts);
    }

    public function testSetSentencesScriptDoesNotUpdateModifiedField()
    {
        $before = $this->fetchTable('Sentences')
            ->find('list', valueField: 'modified')
            ->where(['lang' => 'cmn'])
            ->toArray();

        $this->exec('transcriptions autodetect_sentences_script cmn');

        $after = $this->fetchTable('Sentences')
            ->find('list', valueField: 'modified')
            ->where(['lang' => 'cmn'])
            ->toArray();

        $this->assertEquals($before, $after);
    }
}
