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

    public function testPatchedEntity() {
        $sentence = $this->Sentences->get(7);

        $this->Sentences->patchEntity($sentence, ['lang' => 'fra']);
        $this->assertFalse($sentence->isDirty('text'));

        $sentence = $this->Sentences->get(7);

        $this->Sentences->patchEntity($sentence, ['text' => 'xxx']);
        $this->assertFalse($sentence->isDirty('lang'));
    }
}
