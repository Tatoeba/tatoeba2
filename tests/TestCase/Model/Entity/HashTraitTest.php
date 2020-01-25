<?php
namespace App\Test\TestCase\Command;

use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

class HashTraitTest extends TestCase
{

    public $fixtures = [
        'app.Sentences',
    ];

    public function setUp() {
        parent::setUp();
        $this->Sentences = TableRegistry::getTableLocator()->get('Sentences');
    }

    public function testEntityFromTable() {
        $storedHash = $this->Sentences->get(1)->hash;
        $wrongHash = $this->Sentences->get(3)->hash;
        $this->assertEquals("316iri9\0\0\0\0\0\0\0\0\0", $storedHash);
        $this->assertEquals("2hfxma4\0\0\0\0\0\0\0\0\0", $wrongHash);
     }

    public function testNewEntity() {
        $new = $this->Sentences->newEntity(
            [
                'lang' => 'jpn',
                'text' => 'ちょっと待って。'
            ]);
        $this->assertFalse($new->isEmpty('hash'));
        $this->assertTrue($new->isDirty('hash'));
        $this->assertEquals("3cekb1u\0\0\0\0\0\0\0\0\0", $new->hash);
    }

    public function testPatchedEntity() {
        $sentence = $this->Sentences->get(7);
        $this->assertFalse($sentence->isDirty('hash'));

        $this->Sentences->patchEntity($sentence, ['lang' => 'fra']);
        $this->assertTrue($sentence->isDirty('hash'));
        $this->assertFalse($sentence->isDirty('text'));
        $this->assertFalse($sentence->isEmpty('hash'));
        $this->assertEquals("1c7tbqo\0\0\0\0\0\0\0\0\0", $sentence->hash);

        $sentence = $this->Sentences->get(7);
        $this->assertFalse($sentence->isDirty('hash'));

        $this->Sentences->patchEntity($sentence, ['text' => 'xxx']);
        $this->assertTrue($sentence->isDirty('hash'));
        $this->assertFalse($sentence->isDirty('lang'));
        $this->assertFalse($sentence->isEmpty('hash'));
        $this->assertEquals("1v3tasn\0\0\0\0\0\0\0\0\0", $sentence->hash);
    }
}
