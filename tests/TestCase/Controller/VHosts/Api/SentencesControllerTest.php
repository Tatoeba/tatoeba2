<?php
namespace App\Test\TestCase\Controller\VHosts\Api;

use App\Controller\VHosts\Api\SentenceController;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Helmich\JsonAssert\JsonAssertions;

class MainControllerTest extends TestCase
{
    use IntegrationTestTrait;
    use JsonAssertions;

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
              'data'       => [
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
              ]
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
}
