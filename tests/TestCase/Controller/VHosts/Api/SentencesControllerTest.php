<?php
namespace App\Test\TestCase\Controller\VHosts\Api;

use App\Controller\VHosts\Api\SentenceController;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use App\Test\TestCase\SearchMockTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Helmich\JsonAssert\JsonAssertions;

class MainControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use JsonAssertions;
    use SearchMockTrait;

    const SENTENCE_AND_TRANSLATIONS_JSON_SCHEMA = [
      'type'       => 'object',
      'required'   => ['id', 'text', 'lang', 'script', 'license', 'translations', 'transcriptions', 'audios', 'owner'],
      'properties' => [
        'id'       => ['type' => 'integer'],
        'text'     => ['type' => 'string'],
        'lang'     => ['type' => ['string', 'null']],
        'script'   => ['type' => ['string', 'null']],
        'license'  => ['type' => ['string', 'null']],
        'translations' => [
          'type'         => 'array',
          'items'        => [
            'type'         => 'array',
            'items'        => [
              'type'         => 'object',
              'required'     => ['id', 'text', 'lang', 'script', 'transcriptions', 'audios', 'license', 'owner'],
              'properties'   => [
                'id'           => ['type' => 'integer'],
                'text'         => ['type' => 'string'],
                'lang'         => ['type' => ['string', 'null']],
                'script'       => ['type' => ['string', 'null']],
                'license'      => ['type' => ['string', 'null']],
                'transcriptions' => [
                  'type'           => 'array',
                  'items'          => [
                    'type'           => 'object',
                    'required'       => ['script', 'text', 'needsReview', 'type', 'html'],
                  ],
                ],
                'audios'   => [
                  'type'     => 'array',
                  'items'    => [
                    'type'     => 'object',
                    'required' => ['author', 'license', 'attribution_url'],
                  ],
                ],
              ],
            ],
          ],
          'minItems' => 2,
          'maxItems' => 2,
        ],
        'audios'   => [
          'type'     => 'array',
          'items'    => [
            'type'     => 'object',
            'required' => ['author', 'license', 'attribution_url'],
          ],
        ],
        'owner'  => ['type' => ['string', 'null']],
      ],
    ];

    const PAGING_JSON_SCHEMA = [
      'type'     => 'object',
      'properties' => [
        'first'     => ['type' => 'string'],
        'prev'      => ['type' => 'string'],
        'next'      => ['type' => 'string'],
        'last'      => ['type' => 'string'],
      ],
    ];

    public $fixtures = [
        'app.Audios',
        'app.Sentences',
        'app.Transcriptions',
        'app.Users',
        'app.Links',
    ];

    public function testGetSentence_doesNotExist()
    {
        $this->get("http://api.example.com/unstable/sentences/999999999");
        $this->assertResponseCode(404);
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
                'data' => self::SENTENCE_AND_TRANSLATIONS_JSON_SCHEMA,
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
            '$.data.translations[0][0].owner' => 'kazuki',
            '$.data.translations[1][0].owner' => 'kazuki',
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
        $this->get("http://api.example.com/unstable/sentences/58");
        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data.translations[0]' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.data.translations[0][0].id' => 60,
            '$.data.translations[1]' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.data.translations[1][0].id' => 62,
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_requiresLangParam()
    {
        $this->get("http://api.example.com/unstable/sentences/search?q=hello");
        $this->assertResponseCode(400);
    }

    public function testSearch_requiresValidLang()
    {
        $this->get("http://api.example.com/unstable/sentences/search?lang=invalid&q=hello");
        $this->assertResponseCode(400);
    }

    public function testSearch_requiresValidTrans()
    {
        $this->get("http://api.example.com/unstable/sentences/search?lang=eng&trans=invalid&q=hello");
        $this->assertResponseCode(400);
    }

    public function testSearch_returnsResults()
    {
        $this->enableMockedSearch([1,2,3]);

        $this->get("http://api.example.com/unstable/sentences/search?lang=eng&q=hello&limit=1&page=2");
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $actual = $this->_getBodyAsString();
        $schema = [
            'type'       => 'object',
            'required'   => ['data', 'paging'],
            'properties' => [
                'data'       => [
                    'type'       => 'array',
                    'items'      => self::SENTENCE_AND_TRANSLATIONS_JSON_SCHEMA,
                ],
                'paging' => self::PAGING_JSON_SCHEMA,
            ]
        ];
        $this->assertJsonDocumentMatchesSchema($actual, $schema);
    }

    public function testSearch_limitsTranslationsLanguage()
    {
        $this->enableMockedSearch([2]);

        $this->get("http://api.example.com/unstable/sentences/search?lang=cmn&trans=jpn&q=hello");
        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data[0].translations[0]' => new \PHPUnit\Framework\Constraint\Count(0),
            '$.data[0].translations[1]' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.data[0].translations[1][0].id' => 6,
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_returnsEmptyPagination()
    {
        $this->enableMockedSearch([1]);

        $this->get("http://api.example.com/unstable/sentences/search?lang=eng&trans=fra&q=hello");
        $actual = $this->_getBodyAsString();
        $this->assertJsonValueEquals($actual, '$.paging', (object)[]);
    }

    public function testSearch_returnsFirstPage()
    {
        $this->enableMockedSearch([1], 2);

        $this->get("http://api.example.com/unstable/sentences/search?lang=eng&trans=fra&q=hello&limit=1");
        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.paging' => (object)[
                'first' => 'http://api.example.com/unstable/sentences/search?lang=eng&trans=fra&q=hello&limit=1',
                'next'  => 'http://api.example.com/unstable/sentences/search?lang=eng&trans=fra&q=hello&page=2&limit=1',
                'last'  => 'http://api.example.com/unstable/sentences/search?lang=eng&trans=fra&q=hello&page=2&limit=1',
            ],
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_returnsLastPage()
    {
        $this->enableMockedSearch([2], 2);

        $this->get("http://api.example.com/unstable/sentences/search?lang=eng&trans=fra&q=hello&page=2&limit=1");
        $actual = $this->_getBodyAsString();
        $expected = [
            '$.data' => new \PHPUnit\Framework\Constraint\Count(1),
            '$.paging' => (object)[
                'first' => 'http://api.example.com/unstable/sentences/search?lang=eng&trans=fra&q=hello&limit=1',
                'prev'  => 'http://api.example.com/unstable/sentences/search?lang=eng&trans=fra&q=hello&limit=1',
                'last'  => 'http://api.example.com/unstable/sentences/search?lang=eng&trans=fra&q=hello&page=2&limit=1',
            ],
        ];
        $this->assertJsonDocumentMatches($actual, $expected);
    }

    public function testSearch_parameterIncludeUnapprovedMustBeYes()
    {
        $this->enableMockedSearch([]);

        $this->get("http://api.example.com/unstable/sentences/search?lang=eng&q=hello&include_unapproved=no");
        $this->assertResponseCode(400);

        $this->get("http://api.example.com/unstable/sentences/search?lang=eng&q=hello&include_unapproved=yes");
        $this->assertResponseOk();
    }
}
