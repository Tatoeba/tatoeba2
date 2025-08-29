<?php
namespace App\Test\TestCase\Controller\VHosts\Api;

use App\Test\TestCase\Controller\AudioIntegrationTestTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Helmich\JsonAssert\JsonAssertions;

class AudioControllerTest extends TestCase
{
    use AudioIntegrationTestTrait;
    use IntegrationTestTrait;
    use JsonAssertions;

    public $fixtures = [
        'app.Audios',
        'app.Sentences',
        'app.Users',
    ];

    public function testDownload_invalidId()
    {
        $this->get("http://api.example.com/unstable/audio/notAnInt/file");
        $this->assertResponseCode(400);
    }

    public function testDownload_ok()
    {
        $this->initAudioStorageDir();

        $audioFileContents = $this->createAudioFile(1);
        $this->get("http://api.example.com/unstable/audio/1/file");
        $this->assertResponseOk();
        $this->assertResponseEquals($audioFileContents);
        $this->assertHeader('Content-Disposition', 'attachment; filename="3-1.mp3"');

        $this->deleteAudioStorageDir();
    }

    public function testDownload_fileMissing()
    {
        $this->initAudioStorageDir();

        $this->get("http://api.example.com/unstable/audio/1/file");
        $this->assertResponseCode(404);

        $this->deleteAudioStorageDir();
    }

    public function testDownload_nonExistingAudio()
    {
        $this->get("http://api.example.com/unstable/audio/9999999999/file");
        $this->assertResponseCode(404);
    }

    public function testDownload_nonReusableAudio_fromUserAudioLicenseField()
    {
        $this->initAudioStorageDir();

        $audioFileContents = $this->createAudioFile(6);
        $this->get("http://api.example.com/unstable/audio/6/file");
        $this->assertResponseCode(404);

        $this->deleteAudioStorageDir();
    }

    public function testDownload_nonReusableAudio_fromExternalField()
    {
        $this->initAudioStorageDir();

        $audioFileContents = $this->createAudioFile(2);
        $this->get("http://api.example.com/unstable/audio/2/file");
        $this->assertResponseCode(404);

        $this->deleteAudioStorageDir();
    }

    public function testDownload_reusableAudio_fromExternalField()
    {
        $this->initAudioStorageDir();

        $audioFileContents = $this->createAudioFile(3);
        $this->get("http://api.example.com/unstable/audio/3/file");
        $this->assertResponseOk();

        $this->deleteAudioStorageDir();
    }

    public function testSearch_matchesSchema()
    {
        $this->get("http://api.example.com/unstable/audio");
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $actual = $this->_getBodyAsString();
        $schema = [
            'type'       => 'object',
            'required'   => ['data', 'paging'],
            'properties' => [
                'data'       => [
                    'type'       => 'array',
                    'items'      => SentencesControllerTest::AUDIO_JSON_SCHEMA,
                ],
                'paging' => SentencesControllerTest::PAGING_JSON_SCHEMA,
            ]
        ];
        $schema['properties']['data']['items']['required'][] = 'sentence';
        $schema['properties']['data']['items']['properties']['sentence'] = SentencesControllerTest::SENTENCE_JSON_SCHEMA;
        $this->assertJsonDocumentMatchesSchema($actual, $schema);
    }

    public function testSearch_ordersResultsByIdAsc()
    {
        $this->get("http://api.example.com/unstable/audio");

        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data[0].id' => 1,
            '$.data[1].id' => 7,
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_lang_ok()
    {
        $this->get("http://api.example.com/unstable/audio?lang=spa");
        $this->assertResponseOk();

        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.data[0].sentence.lang' => 'spa',
            '$.paging.total' => 1,
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_lang_invalid()
    {
        $this->get("http://api.example.com/unstable/audio?lang=invalid");
        $this->assertResponseCode(400);
    }

    public function testSearch_author_ok()
    {
        $this->get("http://api.example.com/unstable/audio?author=contributor");
        $this->assertResponseOk();

        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.data[0].author' => 'contributor',
            '$.paging.total' => 1,
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_author_invalid()
    {
        $this->get("http://api.example.com/unstable/audio?author=invalid");
        $this->assertResponseCode(400);
    }

    public function testSearch_limit_ok()
    {
        $this->get("http://api.example.com/unstable/audio?limit=1");
        $this->assertResponseOk();

        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.paging.has_next' => true,
            '$.paging.cursor_end' => 1,
            '$.paging.next' => 'http://api.example.com/unstable/audio?limit=1&after=1',
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_limit_invalid()
    {
        $this->get("http://api.example.com/unstable/audio?limit=invalid");
        $this->assertResponseCode(400);
    }

    public function testSearch_after_ok()
    {
        $this->get("http://api.example.com/unstable/audio?after=1");
        $this->assertResponseOk();

        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data[0].id' => 7,
            '$.paging.has_next' => false,
            '$.paging.first' => 'http://api.example.com/unstable/audio',
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_after_invalid()
    {
        $this->get("http://api.example.com/unstable/audio?after=invalid");
        $this->assertResponseCode(400);
    }

    public function testSearch_doesNotReturnAudioWithoutLicense()
    {
        $this->get("http://api.example.com/unstable/audio?lang=fra");
        $this->assertResponseOk();

        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.paging.total' => 1,
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }
}
