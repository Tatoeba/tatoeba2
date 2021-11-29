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

    public function filePathProvider() {
        // audio id, sentence id, expected audio file path
        return [
            [       1,       2, '/foo/bar/000/001/2-1.mp3'       ],
            [    1234,    5678, '/foo/bar/001/234/5678-1234.mp3' ],
            [  999999,       3, '/foo/bar/999/999/3-999999.mp3'  ],
            [ 1000000,       4, '/foo/bar/000/000/4-1000000.mp3' ],
            [ 1000001,       4, '/foo/bar/000/001/4-1000001.mp3' ],
        ];
    }

    /**
     * @dataProvider filePathProvider
     */
    public function testGet_file_path($audioId, $sentenceId, $expectedPath)
    {
        $this->Audio->id = $audioId;
        $this->Audio->sentence_id = $sentenceId;
        $this->assertEquals($expectedPath, $this->Audio->file_path);
    }

    public function testGet_attributionUrl_returnsNullWithoutProperDataSet() {
        $this->assertNull($this->Audio->attribution_url);
    }

    public function testGet_attributionUrl_fromExternal() {
        $this->Audio->external = ['attribution_url' => 'https://example.com/external'];
        $this->assertEquals('https://example.com/external', $this->Audio->attribution_url);
    }

    public function testGet_attributionUrl_fromUsername() {
        $this->Audio->external = ['attribution_url' => 'https://example.com/external'];
        $this->Audio->user = new User(['username' => 'kazuki']);
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

    public function testGet_license_fromExternal() {
        $this->Audio->external = ['license' => 'WTFPL'];
        $this->assertEquals('WTFPL', $this->Audio->license);
    }

    public function testGet_license_fromUserAudioLicense() {
        $this->Audio->external = ['license' => 'WTFPL'];
        $this->Audio->user = new User(['audio_license' => 'CC0']);
        $this->assertEquals('CC0', $this->Audio->license);
    }
}
