namespace App\Test\TestCase\Model\Entity;
<?php

use App\Model\CurrentUser;
use App\Model\Entity\Sentence;
use App\Model\Entity\Transcription;
use Cake\TestSuite\TestCase;

class TranscriptionTest extends TestCase
{
    public $fixtures = array(
            'app.UsersLanguages'
    );

    public function setUp()
    {
        parent::setUp();
        CurrentUser::store([
            'id' => 1,
            'role' => \App\Model\Entity\User::ROLE_CORPUS_MAINTAINER,
        ]);
    }

    function makeTranscription($lang, $sourceScript, $targetScript, $text) {
        $transcription = new Transcription();
        $transcription->sentence = new Sentence();
        $transcription->sentence->lang = $lang;
        $transcription->sentence->script = $sourceScript;
        $transcription->script = $targetScript;
        $transcription->text = $text;

        return $transcription;
    }

    public function testGetHtml_jpn()
    {
        $this->assertEquals(
            '「<ruby>何<rp>（</rp><rt>なに</rt><rp>）</rp></ruby>？」',
            $this->makeTranscription('jpn', 'Jpan', 'Hrkt', '「[何|なに]？」')->html
        );

        $this->assertEquals(
            '&lt;!--',
            $this->makeTranscription('jpn', 'Jpan', 'Hrkt', '<!--')->html
        );
    }

    public function testGetHtml_cmn()
    {
        $this->assertEquals(
            '&quot;Sh&eacute;nme?&quot;',
            $this->makeTranscription('cmn', 'Hans', 'Latn', '"Shen2me5?"')->html
        );
    }

    public function testGetHtml_uzb()
    {
        $this->assertEquals(
            '&quot;Nima?&quot;',
            $this->makeTranscription('uzb', 'Cyrl', 'Latn', '"Nima?"')->html
        );
    }

    public function testGetMarkup_jpn()
    {
        $this->assertEquals(
            '「何｛なに｝？」',
            $this->makeTranscription('jpn', 'Jpan', 'Hrkt', '「[何|なに]？」')->markup
        );

        $this->assertEquals(
            '<!--',
            $this->makeTranscription('jpn', 'Jpan', 'Hrkt', '<!--')->markup
        );
    }

    public function testGetMarkup_cmn()
    {
        $this->assertEquals(
            '"Shen2me5?"',
            $this->makeTranscription('cmn', 'Hant', 'Latn', '"Shen2me5?"')->markup
        );
    }

    public function testGetMarkup_uzb()
    {
        $this->assertEquals(
            '',
            $this->makeTranscription('uzb', 'Latn', 'Cyrl', '"Nima?"')->markup
        );
    }
}
