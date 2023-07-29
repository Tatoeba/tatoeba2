<?php
namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Audio;
use App\Model\Entity\User;
use Cake\TestSuite\TestCase;
use Cake\Core\Configure;

class AudioTest extends TestCase
{
    public $Audio;

    public function setUp()
    {
        parent::setUp();
        $this->Audio = new Audio();
        Configure::write('Recordings.path', '/foo/bar');
    }

    public function tearDown()
    {
        unset($this->Audio);

        parent::tearDown();
    }

    public function testGet_externalSetsDefaultValues()
    {
        $this->Audio->set('external', ['username' => 'foobar']);

        $this->assertTrue(array_key_exists('license', $this->Audio->external));
    }

    public function testSet_externalIgnoresUnknownKeys()
    {
        $this->Audio->set('external', ['this_does_not_exist' => true]);

        $this->assertFalse(array_key_exists('this_does_not_exist', $this->Audio->external));
    }

    public function testSet_externalMergesExistingValues()
    {
        $this->Audio->set('external', ['username' => 'foobar']);
        $this->Audio->set('external', ['attribution_url' => 'http://example.net/']);

        $this->assertEquals('foobar', $this->Audio->external['username']);
    }

    public function testGet_externalDoesntTouchNullValues()
    {
        $this->assertNull($this->Audio->external);
    }

    public function fileProvider() {
        // audio id, sentence id, expected audio file path, expected pretty filename
        return [
            [       1,       2, '/foo/bar/00/01/1.mp3',       '2-1.mp3'       ],
            [      99,       5, '/foo/bar/00/99/99.mp3',      '5-99.mp3'      ],
            [    1234,    5678, '/foo/bar/12/34/1234.mp3',    '5678-1234.mp3' ],
            [    9999,       3, '/foo/bar/99/99/9999.mp3',    '3-9999.mp3'    ],
            [   10000,       4, '/foo/bar/00/00/10000.mp3',   '4-10000.mp3'   ],
            [   10001,       4, '/foo/bar/00/01/10001.mp3',   '4-10001.mp3'   ],
            [ 1000000,       4, '/foo/bar/00/00/1000000.mp3', '4-1000000.mp3' ],
            [ 1000001,       4, '/foo/bar/00/01/1000001.mp3', '4-1000001.mp3' ],
        ];
    }

    /**
     * @dataProvider fileProvider
     */
    public function testGet_file($audioId, $sentenceId, $expectedPath, $expectedPrettyFilename)
    {
        $this->Audio->id = $audioId;
        $this->Audio->sentence_id = $sentenceId;
        $this->assertEquals($expectedPath, $this->Audio->file_path);
        $this->assertEquals($expectedPrettyFilename, $this->Audio->pretty_filename);
    }

    public function testGet_attributionUrl_returnsNullWithoutProperDataSet() {
        $this->assertNull($this->Audio->attribution_url);
    }

    public function testGet_attributionUrl_returnsEmptyWhenExternalSet() {
        $this->Audio->external = [];
        $this->assertEquals('', $this->Audio->attribution_url);
    }

    public function testGet_attributionUrl_fromExternal() {
        $this->Audio->external = ['attribution_url' => 'https://example.com/external'];
        $this->assertEquals('https://example.com/external', $this->Audio->attribution_url);
    }

    public function testGet_attributionUrl_fromUsername() {
        $this->Audio->external = ['attribution_url' => 'https://example.com/external'];
        $this->Audio->user = new User(['username' => 'kazuki', 'audio_attribution_url' => '']);
        $this->assertEquals('/user/profile/kazuki', $this->Audio->attribution_url);
    }

    public function testGet_attributionUrl_fromUserAudioAttributionUrl() {
        $this->Audio->external = ['attribution_url' => 'https://example.com/external'];
        $this->Audio->user = new User([
            'username' => 'kazuki',
            'audio_attribution_url' => 'https://example.com/my-audio'
        ]);
        $this->assertEquals('https://example.com/my-audio', $this->Audio->attribution_url);
    }

    public function testGet_license_returnsNullWithoutProperDataSet() {
        $this->assertNull($this->Audio->license);
    }

    public function testGet_license_returnsEmptyWhenExternalSet() {
        $this->Audio->external = [];
        $this->assertEquals('', $this->Audio->license);
    }

    public function testGet_license_fromExternal() {
        $this->Audio->external = ['license' => 'WTFPL'];
        $this->assertEquals('WTFPL', $this->Audio->license);
    }

    public function testGet_license_fromUserAudioLicense() {
        $this->Audio->external = ['license' => 'WTFPL'];
        $this->Audio->user = new User(['audio_license' => 'CC0']);
        $this->assertEquals('CC0', $this->Audio->license);
    }

    public function testGet_author_returnsNullWithoutProperDataSet() {
        $this->assertNull($this->Audio->author);
    }

    public function testGet_author_returnsEmptyWhenNoUsernameSet() {
        $this->Audio->user = new User([]);
        $this->assertNull($this->Audio->author);
    }

    public function testGet_author_returnsUsername() {
        $this->Audio->user = new User(['username' => 'kazuki']);
        $this->assertEquals('kazuki', $this->Audio->author);
    }
}
