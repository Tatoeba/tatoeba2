<?php
namespace App\Test\TestCase\Model;

use App\Model\TranslationImportCsv;
use Cake\TestSuite\TestCase;

class TranslationImportCsvTest extends TestCase
{
    public function testSemicolonFile()
    {
        $rows = TranslationImportCsv::parse(
            '123;"Hello.";"Hallo."',
            'semicolon'
        );

        $this->assertCount(1, $rows);
        $this->assertSame(123, $rows[0]['sentence_id']);
        $this->assertSame('Hello.', $rows[0]['source_text']);
        $this->assertSame('Hallo.', $rows[0]['translation_text']);
    }

    public function testInvalidSentenceIdIsRejected()
    {
        $this->expectException(\InvalidArgumentException::class);

        TranslationImportCsv::parse(
            'invalid;"Hello.";"Hallo."',
            'semicolon'
        );
    }

    public function testBomHeaderBlankRecordsAndQuotedText()
    {
        $rows = TranslationImportCsv::parse(
            "\xEF\xBB\xBFsentence_id,source_text,translation_text\r\n\r\n" .
            "1,\"Hello, \"\"world\"\".\",\"Grüße\nWelt.\"\r\n",
            'comma'
        );

        $this->assertCount(1, $rows);
        $this->assertSame(3, $rows[0]['record_number']);
        $this->assertSame('Hello, "world".', $rows[0]['source_text']);
        $this->assertSame("Grüße\nWelt.", $rows[0]['translation_text']);
    }

    public function testTabsAndLiteralBackslashes()
    {
        $rows = TranslationImportCsv::parse(
            "1\tHello.\tC:\\translations\\test",
            'tab'
        );

        $this->assertSame('C:\\translations\\test', $rows[0]['translation_text']);
    }

    public function invalidFilesProvider()
    {
        return [
            'empty' => ['', 'comma'],
            'unknown delimiter' => ['1,Hello.,Hallo.', 'pipe'],
            'array delimiter' => ['1,Hello.,Hallo.', ['comma']],
            'invalid UTF-8' => ["1,Hello.,\xff", 'comma'],
            'NUL' => ["1,Hello.,Hallo.\0", 'comma'],
            'missing column' => ['1,Hello.', 'comma'],
            'extra column' => ['1,Hello.,Hallo.,extra', 'comma'],
            'zero ID' => ['0,Hello.,Hallo.', 'comma'],
            'negative ID' => ['-1,Hello.,Hallo.', 'comma'],
            'overflow ID' => ['2147483648,Hello.,Hallo.', 'comma'],
            'empty source' => ['1,,Hallo.', 'comma'],
            'blank translation' => ['1,Hello.,   ', 'comma'],
            'oversized Unicode' => ['1,Hello.,' . str_repeat('ü', 751), 'comma'],
            'header only' => ['sentence_id,source_text,translation_text', 'comma'],
            'unclosed quote' => ['1,Hello.,"Hallo.', 'comma'],
            'text after quote' => ['1,Hello.,"Hallo."extra', 'comma'],
            'quote in unquoted field' => ['1,Hel"lo.,Hallo.', 'comma'],
            'too many records' => [
                implode("\n", array_fill(0, 501, '1,Hello.,Hallo.')),
                'comma',
            ],
            'oversized file' => [
                str_repeat('x', TranslationImportCsv::MAX_BYTES + 1),
                'comma',
            ],
        ];
    }

    /**
     * @dataProvider invalidFilesProvider
     */
    public function testInvalidFilesAreRejected($contents, $delimiter)
    {
        $this->expectException(\InvalidArgumentException::class);
        TranslationImportCsv::parse($contents, $delimiter);
    }

    public function testErrorIncludesRecordNumber()
    {
        $this->expectExceptionMessage('Record 2 must have three columns.');

        TranslationImportCsv::parse(
            "sentence_id,source_text,translation_text\n1,Hello.",
            'comma'
        );
    }

    public function testLimitsAreInclusive()
    {
        $rows = TranslationImportCsv::parse(
            implode("\n", array_fill(0, 500, '1,Hello.,Hallo.')),
            'comma'
        );
        $this->assertCount(500, $rows);

        $rows = TranslationImportCsv::parse(
            '2147483647,Hello.,' . str_repeat('ü', 750),
            'comma'
        );
        $this->assertSame(1500, strlen($rows[0]['translation_text']));
    }
}
