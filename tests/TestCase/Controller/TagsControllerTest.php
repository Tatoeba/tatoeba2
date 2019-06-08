<?php
namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use App\Test\TestCase\Controller\TatoebaControllerTestTrait;

class TagsControllerTest extends IntegrationTestCase {
    use TatoebaControllerTestTrait;

    public $fixtures = [
        'app.aros',
        'app.acos',
        'app.aros_acos',
        'app.tags',
        'app.tags_sentences',
        'app.users',
        'app.users_languages'
    ];

    public function setUp() {
        parent::setUp();
        Configure::write('Acl.database', 'test');
    }
}
