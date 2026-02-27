<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DisabledAudiosTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class DisabledAudiosTableTest extends TestCase
{
    public $DisabledAudios;

    public $fixtures = [
        'app.Audios',
        'app.DisabledAudios',
        'app.Sentences',
        'app.Languages',
        'app.ReindexFlags',
        'app.Links',
    ];

    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('DisabledAudios') ? [] : ['className' => DisabledAudiosTable::class];
        $this->DisabledAudios = TableRegistry::getTableLocator()->get('DisabledAudios', $config);
    }

    public function tearDown()
    {
        unset($this->DisabledAudios);

        parent::tearDown();
    }

    public function testReenableAudio()
    {
        $audio = $this->DisabledAudios->get(4);
        $audio->enabled = true;
        $this->DisabledAudios->save($audio);

        try {
            $this->DisabledAudios->get(4);
            $result = true;
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $result = false;
        }

        $this->assertFalse($result);
        $reenabledAudio = TableRegistry::getTableLocator()->get('Audios')->get(4);
        $this->assertTrue($reenabledAudio->enabled);
        $this->assertEquals($reenabledAudio->modified, $audio->modified);
    }
}
