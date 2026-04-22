<?php
namespace App\Test\TestCase\Controller\VHosts\Api;

use App\Test\TestCase\SearchMockTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Helmich\JsonAssert\JsonAssertions;

class SentencesControllerTest extends TestCase
{
    use IntegrationTestTrait {
        _getBodyAsString as protected __getBodyAsString;
    }
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
      ],
      'additionalProperties' => false,
    ];

    const TRANSCRIPTION_JSON_SCHEMA = [
      'type'     => 'object',
      'required' => ['script', 'text', 'needsReview', 'type', 'html', 'editor', 'modified'],
    ];

    const SENTENCE_JSON_SCHEMA = [
      'type'       => 'object',
      'required'   => ['id', 'text', 'lang', 'script', 'license', 'owner', 'is_unapproved'],
      'properties' => [
        'id'       => ['type' => 'integer'],
        'text'     => ['type' => 'string'],
        'lang'     => ['type' => ['string', 'null']],
        'script'   => ['type' => ['string', 'null']],
        'license'  => ['type' => ['string', 'null']],
        'owner'    => ['type' => ['string', 'null']],
        'is_unapproved' => ['type' => 'boolean'],
      ],
      'additionalProperties' => false,
    ];

    const PAGING_JSON_SCHEMA = [
      'type'       => 'object',
      'required'   => ['total', 'has_next'],
      'properties' => [
        'total'      => ['type' => 'integer'],
        'first'      => ['type' => 'string'],
        'has_next'   => ['type' => 'boolean'],
        'next'       => ['type' => 'string'],
      ],
      'additionalProperties' => false,
    ];

    public $fixtures = [
        'app.Audios',
        'app.Sentences',
        'app.Transcriptions',
        'app.Users',
        'app.Links',
    ];

    private function sentenceSchema(bool $withTranslations, bool $withAudio, bool $withTranscriptions) {
        $sentenceSchema = self::SENTENCE_JSON_SCHEMA;

        if ($withAudio) {
            // add audio
            $sentenceSchema['required'][] = 'audios';
            $sentenceSchema['properties']['audios'] = [
                'type' => 'array',
                'items' => self::AUDIO_JSON_SCHEMA,
            ];
        }

            if ($withTranscriptions) {
            // add transcriptions
            $sentenceSchema['required'][] = 'transcriptions';
            $sentenceSchema['properties']['transcriptions'] = [
                'type' => 'array',
                'items' => self::TRANSCRIPTION_JSON_SCHEMA,
            ];
        }

        // add translations
        if ($withTranslations) {
            $sentenceSchema['properties']['translations'] = [
                'type' => 'array',
                'items' => $sentenceSchema,
            ];
            $sentenceSchema['required'][] = 'translations';
            $sentenceSchema['properties']['translations']['items']['required'][] = 'is_direct';
            $sentenceSchema['properties']['translations']['items']['properties']['is_direct'] = [
                'type' => 'boolean',
            ];
        }

        return $sentenceSchema;
    }

    private function sentencesResultSchema(array $sentenceSchema) {
        return [
            'type'       => 'object',
            'required'   => ['data', 'paging'],
            'properties' => [
                'data'       => [
                    'type'       => 'array',
                    'items'      => $sentenceSchema,
                ],
                'paging' => self::PAGING_JSON_SCHEMA,
            ],
            'additionalProperties' => false,
        ];
    }

    /**
     * Overloads IntegrationTestTrait::_getBodyAsString() to support
     * getting body from responses printed to stdout, too.
     */
    protected function _getBodyAsString()
    {
        ob_start();
        $body = $this->__getBodyAsString();
        if (strlen($body) == 0 && ob_get_length()) {
            $body = ob_get_contents();
        }
        ob_end_clean();
        return $body;
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

    public function sentenceSchemaTestProvider()
    {
        return [
            // query, expected response JSON schema under .data
            'no associated data' => [
                '',
                $this->sentenceSchema(false, false, false),
            ],
            'with translations' => [
                'showtrans=all',
                $this->sentenceSchema(true, false, false),
            ],
            'with audios' => [
                'include=audios',
                $this->sentenceSchema(false, true, false),
            ],
            'with transcriptions' => [
                'include=transcriptions',
                $this->sentenceSchema(false, false, true),
            ],
            'with audios and transcriptions' => [
                'include=audios,transcriptions',
                $this->sentenceSchema(false, true, true),
            ],
            'with translations and audios' => [
                'showtrans=all&include=audios',
                $this->sentenceSchema(true, true, false),
            ],
            'with translations and transcriptions' => [
                'showtrans=all&include=transcriptions',
                $this->sentenceSchema(true, false, true),
            ],
            'with translations, audios and transcriptions' => [
                'showtrans=all&include=audios,transcriptions',
                $this->sentenceSchema(true, true, true),
            ],
        ];
    }

    /**
     * @dataProvider sentenceSchemaTestProvider
     */
    public function testGetSentence_schema($query, $expectedDataSchema)
    {
        $this->get("http://api.example.com/unstable/sentences/1?$query");
        $this->assertResponseOk();
        $this->assertContentType('application/json');
        $actual = $this->_getBodyAsString();

        $schema = [
            'type'       => 'object',
            'required'   => ['data'],
            'properties' => [
                'data' => $expectedDataSchema,
            ]
        ];
        $this->assertJsonDocumentMatchesSchema($actual, $schema);
    }

    public function testGetSentence_returnsSentenceOwner()
    {
        $this->get("http://api.example.com/unstable/sentences/1");
        $actual = $this->_getBodyAsString();
        $this->assertJsonValueEquals($actual, '$.data.owner', 'kazuki');
    }

    public function testGetSentence_returnsTranslationOwner()
    {
        $this->get("http://api.example.com/unstable/sentences/1?showtrans=all");
        $actual = $this->_getBodyAsString();
        $constraint = [
            '$.data.translations[0].owner' => 'kazuki',
            '$.data.translations[1].owner' => 'advanced_contributor',
            '$.data.translations[2].owner' => 'kazuki',
            '$.data.translations[3].owner' => null,
            '$.data.translations[4].owner' => 'kazuki',
        ];
        $this->assertJsonDocumentMatches($actual, $constraint);
    }

    public function testGetSentence_returnsAudioUserProfileURL()
    {
        $this->get("http://api.example.com/unstable/sentences/57?include=audios");
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
        $this->get("http://api.example.com:8080/unstable/sentences/57?include=audios");
        $actual = $this->_getBodyAsString();
        $expected = 'http://example.com:8080/user/profile/kazuki';
        $this->assertJsonValueEquals($actual, '$.data.audios[0].attribution_url', $expected);
    }

    public function testGetSentence_cannotGetTranslationsWithLicenseIssue()
    {
        $this->markTestSkipped('Cannot handle the special case of hiding indirect translations going through a translation having a license issues');
        $this->get("http://api.example.com/unstable/sentences/58?showtrans=all");
        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data.translations' => new \PHPUnit\Framework\Constraint\Count(2),
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testGetSentence_returnsLocalAudioFileURL()
    {
        $this->get("http://api.example.com/v1/sentences/57?include=audios");
        $actual = $this->_getBodyAsString();
        $expected = 'http://api.example.com/v1/audio/7/file';
        $this->assertJsonValueEquals($actual, '$.data.audios[0].download_url', $expected);
    }

    public function testGetSentence_returnsCommonsAudioFileURL()
    {
        $this->get("http://api.example.com/v1/sentences/66?include=audios");
        $actual = $this->_getBodyAsString();
        $expected = 'https://upload.wikimedia.example.org/wikipedia/commons/the-file.mp3';
        $this->assertJsonValueEquals($actual, '$.data.audios[0].download_url', $expected);
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

    /**
     * @dataProvider sentenceSchemaTestProvider
     */
    public function testSearch_returnsResults_withCorrectSchema($query, $expectedSentenceSchema)
    {
        $this->enableMockedSearch([1,2,3]);

        if (strlen($query) > 0) {
            $query = "&$query";
        }
        $this->get("http://api.example.com/unstable/sentences?lang=eng&q=hello&sort=created&limit=1$query");
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $actual = $this->_getBodyAsString();
        $schema = $this->sentencesResultSchema($expectedSentenceSchema);
        $this->assertJsonDocumentMatchesSchema($actual, $schema);
    }

    public function testSearch_returnsResults_withCorrectTranscriptionType()
    {
        $this->enableMockedSearch([1,2,3]);

        $this->get("http://api.example.com/unstable/sentences?lang=eng&q=hello&sort=created&limit=1&include=transcriptions");

        $actual = $this->_getBodyAsString();
        $this->assertJsonValueEquals($actual, '$.data[1].transcriptions[0].type', 'altscript');
    }

    public function testSearch_returnsResults_withMatchedTranslations()
    {
        $this->enableMockedSearch([1,2,3]);

        $this->get("http://api.example.com/unstable/sentences?lang=eng&q=hello&sort=created&trans:lang=cmn");
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $actual = $this->_getBodyAsString();
        $schema = $this->sentencesResultSchema($this->sentenceSchema(true, false, false));
        $this->assertJsonDocumentMatchesSchema($actual, $schema);
        $expected = [
            '$.data' => new \PHPUnit\Framework\Constraint\Count(3),
            '$.data[0].translations' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.data[0].translations[0].lang' => 'cmn',
            '$.data[1].translations' => new \PHPUnit\Framework\Constraint\Count(0),
            '$.data[2].translations' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.data[2].translations[0].lang' => 'cmn',
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_returnsResults_withoutMatchedTranslations()
    {
        $this->enableMockedSearch([1,2,3]);

        $this->get("http://api.example.com/unstable/sentences?lang=eng&q=hello&sort=created&!trans:lang=cmn");
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $actual = $this->_getBodyAsString();
        $schema = $this->sentencesResultSchema($this->sentenceSchema(false, false, false));
        $this->assertJsonDocumentMatchesSchema($actual, $schema);
    }

    public function testSearch_returnsResults_withAllTranslationsHidden()
    {
        $this->enableMockedSearch([1,2,3]);

        $this->get("http://api.example.com/unstable/sentences?lang=eng&q=hello&sort=created&trans:lang=cmn&showtrans=none");
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $actual = $this->_getBodyAsString();
        $schema = $this->sentencesResultSchema($this->sentenceSchema(false, false, false));
        $this->assertJsonDocumentMatchesSchema($actual, $schema);
    }

    public function testSearch_returnsResults_withAllTranslationsShowed()
    {
        $this->enableMockedSearch([1,2,3]);

        $this->get("http://api.example.com/unstable/sentences?lang=eng&q=hello&sort=created&trans:lang=cmn&showtrans=all");
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $actual = $this->_getBodyAsString();
        $schema = $this->sentencesResultSchema($this->sentenceSchema(true, false, false));
        $this->assertJsonDocumentMatchesSchema($actual, $schema);
        $expected = [
            '$.data' => new \PHPUnit\Framework\Constraint\Count(3),
            '$.data[0].translations' => new \PHPUnit\Framework\Constraint\Count(5),
            '$.data[1].translations' => new \PHPUnit\Framework\Constraint\Count(5),
            '$.data[2].translations' => new \PHPUnit\Framework\Constraint\Count(3),
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
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

    public function limitsProvider() {
        return [
            'default results limit when no translations are returned' => [
                'lang=ara&sort=created&showtrans=none', 50
            ],
            'default results limit when translations are returned' => [
                'lang=ara&sort=created&showtrans=all', 10
            ],
            'hard results limit when no translations are returned' => [
                'lang=ara&sort=created&showtrans=none&limit=999999', 500
            ],
            'hard results limit when translations are returned' => [
                'lang=ara&sort=created&showtrans=all&limit=999999', 50
            ],
        ];
    }

    /**
     * @dataProvider limitsProvider
     */
    public function testSearch_limits(string $params, int $expectedLimit)
    {
        $this->enableMockedSearch([1,2,3], 3);
        $client = \Cake\Core\Configure::read('Sphinx.client');
        $client->expects($this->once())
               ->method('SetLimits')
               ->with(0, $expectedLimit);

        $this->get("http://api.example.com/unstable/sentences?$params");
        $this->_getBodyAsString();
    }
}
