<?php
namespace App\Test\TestCase\Controller\VHosts\Api;

use App\Test\TestCase\SearchMockTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Helmich\JsonAssert\JsonAssertions;

class MainControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use JsonAssertions;
    use SearchMockTrait;

    const AUDIO_JSON_SCHEMA = [
      'type'     => 'object',
      'required' => ['id', 'author', 'license', 'attribution_url', 'download_url', 'created', 'modified'],
      'properties' => [
        'id'              => ['type' => 'integer'],
        'author'          => ['type' => 'string'],
        'license'         => ['type' => 'string'],
        'attribution_url' => ['type' => 'string'],
        'download_url'    => ['type' => 'string'],
        'created'         => ['type' => 'string'],
        'modified'        => ['type' => 'string'],
      ]
    ];

    const TRANSCRIPTION_JSON_SCHEMA = [
      'type'     => 'object',
      'required' => ['script', 'text', 'needsReview', 'type', 'html'],
    ];

    const SENTENCE_JSON_SCHEMA = [
      'type'       => 'object',
      'required'   => ['id', 'text', 'lang', 'script', 'license', 'owner'],
      'properties' => [
        'id'       => ['type' => 'integer'],
        'text'     => ['type' => 'string'],
        'lang'     => ['type' => ['string', 'null']],
        'script'   => ['type' => ['string', 'null']],
        'license'  => ['type' => ['string', 'null']],
        'owner'    => ['type' => ['string', 'null']],
      ],
    ];

    const PAGING_JSON_SCHEMA = [
      'type'       => 'object',
      'required'   => ['total', 'has_next'],
      'properties' => [
        'total'      => ['type' => 'integer'],
        'first'      => ['type' => 'string'],
        'has_next'   => ['type' => 'boolean'],
        'cursor_end' => ['type' => 'string'],
        'next'       => ['type' => 'string'],
      ],
    ];

    public $fixtures = [
        'app.Audios',
        'app.Sentences',
        'app.Transcriptions',
        'app.Users',
        'app.Links',
    ];

    private function sentenceSchema(bool $translationsRequired) {
        $sentenceSchema = self::SENTENCE_JSON_SCHEMA;

        // add audio
        $sentenceSchema['required'][] = 'audios';
        $sentenceSchema['properties']['audios'] = [
            'type' => 'array',
            'items' => self::AUDIO_JSON_SCHEMA,
        ];

        // add transcriptions
        $sentenceSchema['required'][] = 'transcriptions';
        $sentenceSchema['properties']['transcriptions'] = [
            'type' => 'array',
            'items' => self::TRANSCRIPTION_JSON_SCHEMA,
        ];

        // add translations
        $sentenceSchema['properties']['translations'] = [
            'type' => 'array',
            'items' => $sentenceSchema,
        ];
        if ($translationsRequired) {
            $sentenceSchema['required'][] = 'translations';
        }
        $sentenceSchema['properties']['translations']['items']['required'][] = 'is_direct';
        $sentenceSchema['properties']['translations']['items']['properties']['is_direct'] = [
            'type' => 'boolean',
        ];

        return $sentenceSchema;
    }

    public function testGetSentence_doesNotExist()
    {
        $this->get("http://api.example.com/unstable/sentences/999999999");
        $this->assertResponseCode(404);
    }

    public function testGetSentence_invalidId()
    {
        $this->get("http://api.example.com/unstable/sentences/notAnInt");
        $this->assertResponseCode(400);
    }

    public function testGetSentence_ok()
    {
        $this->get("http://api.example.com/unstable/sentences/1");
        $this->assertResponseOk();
        $this->assertContentType('application/json');
        $actual = $this->_getBodyAsString();

        $schema = [
            'type'       => 'object',
            'required'   => ['data'],
            'properties' => [
                'data' => $this->sentenceSchema(true),
            ]
        ];
        $this->assertJsonDocumentMatchesSchema($actual, $schema);
    }

    public function testGetSentence_returnsOwner()
    {
        $this->get("http://api.example.com/unstable/sentences/1");
        $actual = $this->_getBodyAsString();
        $constraint = [
            '$.data.owner' => 'kazuki',
            '$.data.translations[0].owner' => 'kazuki',
            '$.data.translations[1].owner' => 'kazuki',
            '$.data.translations[2].owner' => 'kazuki',
            '$.data.translations[3].owner' => 'kazuki',
            '$.data.translations[4].owner' => 'kazuki',
        ];
        $this->assertJsonDocumentMatches($actual, $constraint);
    }

    public function testGetSentence_returnsAudioUserProfileURL()
    {
        $this->get("http://api.example.com/unstable/sentences/57");
        $actual = $this->_getBodyAsString();
        $expected = 'http://example.com/user/profile/kazuki';
        $this->assertJsonValueEquals($actual, '$.data.audios[0].attribution_url', $expected);
    }

    public function testGetSentence_returnsAudioUserProfileURL_withNonStandardPort()
    {
        $this->configRequest([
            // Need to set this manually, otherwise IntegrationTestTrait
            // will strip the port from the HTTP_HOST environement variable,
            // regardless of the URL used to perform the request
            'headers' => ['Host' => 'api.example.com:8080']
        ]);
        $this->get("http://api.example.com:8080/unstable/sentences/57");
        $actual = $this->_getBodyAsString();
        $expected = 'http://example.com:8080/user/profile/kazuki';
        $this->assertJsonValueEquals($actual, '$.data.audios[0].attribution_url', $expected);
    }

    public function testGetSentence_doesNotReturnUnreusableAudio()
    {
        $this->get("http://api.example.com/unstable/sentences/15");
        $actual = $this->_getBodyAsString();
        $this->assertJsonValueEquals($actual, '$.data.audios', []);
    }

    public function testGetSentence_cannotGetSentencesWithLicenseIssue()
    {
        $this->get("http://api.example.com/unstable/sentences/52");
        $this->assertResponseCode(404);
    }

    public function testGetSentence_cannotGetTranslationsWithLicenseIssue()
    {
        $this->markTestSkipped('Cannot handle the special case of hiding indirect translations going through a translation having a license issues');
        $this->get("http://api.example.com/unstable/sentences/58");
        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data.translations' => new \PHPUnit\Framework\Constraint\Count(2),
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_requiresLangAndSortParam()
    {
        $this->get("http://api.example.com/unstable/sentences?q=hello");
        $this->assertResponseCode(400);
    }

    public function testSearch_requiresSortParam()
    {
        $this->get("http://api.example.com/unstable/sentences?q=hello&lang=epo");
        $this->assertResponseCode(400);
    }

    public function testSearch_requiresLangParam()
    {
        $this->get("http://api.example.com/unstable/sentences?q=hello&sort=created");
        $this->assertResponseCode(400);
    }

    public function testSearch_requiresValidLang()
    {
        $this->get("http://api.example.com/unstable/sentences?lang=invalid&q=hello&sort=created");
        $this->assertResponseCode(400);
    }

    public function testSearch_requiresValidSort()
    {
        $this->get("http://api.example.com/unstable/sentences?lang=eng&sort=invalid");
        $this->assertResponseCode(400);
    }

    public function testSearch_acceptsSort()
    {
        $this->enableMockedSearch([1,2,3]);

        $this->get("http://api.example.com/unstable/sentences?lang=eng&q=hello&sort=created");
        $this->assertResponseOk();
    }

    public function testSearch_acceptsReversedSort()
    {
        $this->enableMockedSearch([1,2,3]);

        $this->get("http://api.example.com/unstable/sentences?lang=eng&q=hello&sort=-created");
        $this->assertResponseOk();
    }

    public function testSearch_randomSortCannotBePaged()
    {
        $this->enableMockedSearch([1,2,3], 42, false);

        // GET-ing with sort=random, but the $selectCursor=false above really is what's being tested
        $this->get("http://api.example.com/unstable/sentences?lang=eng&q=hello&sort=random&limit=1");
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $actual = $this->_getBodyAsString();
        $expected = [
            '$.paging' => new \PHPUnit\Framework\Constraint\Callback(function ($obj) {
                $nbProps = count((array)$obj);
                return $nbProps == 2;
            }),
            '$.paging.total' => 42,
            '$.paging.has_next' => true,
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_returnsResults()
    {
        $this->enableMockedSearch([1,2,3]);

        $this->get("http://api.example.com/unstable/sentences?lang=eng&q=hello&sort=created&limit=1");
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $actual = $this->_getBodyAsString();
        $schema = [
            'type'       => 'object',
            'required'   => ['data', 'paging'],
            'properties' => [
                'data'       => [
                    'type'       => 'array',
                    'items'      => $this->sentenceSchema(false),
                ],
                'paging' => self::PAGING_JSON_SCHEMA,
            ]
        ];
        $this->assertJsonDocumentMatchesSchema($actual, $schema);
        $this->assertJsonValueEquals($actual, '$.data[1].transcriptions[0].type', 'altscript');
    }

    public function testSearch_limitsTranslationsLanguage()
    {
        $this->enableMockedSearch([2]);

        $this->get("http://api.example.com/unstable/sentences?lang=cmn&sort=created&showtrans:lang=jpn&q=hello");
        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data[0].translations' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.data[0].translations[0].id' => 6,
            '$.data[0].translations[0].is_direct' => false,
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_returnsEmptyPagination()
    {
        $this->enableMockedSearch([1]);

        $this->get("http://api.example.com/unstable/sentences?lang=eng&sort=created&q=hello");
        $actual = $this->_getBodyAsString();
        $expectedPaging = (object)[
            'total' => 1,
            'has_next' => false,
        ];
        $this->assertJsonValueEquals($actual, '$.paging', $expectedPaging);
    }

    public function testSearch_returnsFirstPage()
    {
        $this->enableMockedSearch([1], 2);

        $this->get("http://api.example.com/unstable/sentences?lang=eng&sort=created&q=hello&limit=1");
        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.paging' => (object)[
                'total' => 2,
                'has_next' => true,
                'cursor_end' => '123456,1',
                'next'  => 'http://api.example.com/unstable/sentences?lang=eng&sort=created&q=hello&limit=1&after=123456%2C1',
            ],
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_returnsMiddlePage()
    {
        $this->enableMockedSearch([1], 2);

        $this->get("http://api.example.com/unstable/sentences?lang=eng&sort=created&q=hello&limit=1&after=123,345");
        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.paging' => (object)[
                'first' => 'http://api.example.com/unstable/sentences?lang=eng&sort=created&q=hello&limit=1',
                'has_next' => true,
                'cursor_end' => '123456,1',
                'next'  => 'http://api.example.com/unstable/sentences?lang=eng&sort=created&q=hello&limit=1&after=123456%2C1',
            ],
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_returnsLastPage()
    {
        $this->enableMockedSearch([2], 1);

        $this->get("http://api.example.com/unstable/sentences?lang=eng&sort=created&q=hello&limit=1&after=123,456");
        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.paging' => (object)[
                'first' => 'http://api.example.com/unstable/sentences?lang=eng&sort=created&q=hello&limit=1',
                'has_next' => false,
            ],
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }
}
