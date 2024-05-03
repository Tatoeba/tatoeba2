<?php
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\LogsHelper;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\View\View;

class LogsHelperTest extends TestCase
{
    public $fixtures = [
        'app.Contributions',
    ];

    public $LogsHelper;

    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->LogsHelper = new LogsHelper($view);
    }

    public function tearDown()
    {
        unset($this->LogsHelper);
        parent::tearDown();
    }

    public function testObsoletize()
    {
        $contributions = TableRegistry::get('Contributions');
        $latests = $contributions->find()->where(['sentence_id' => 35])->toList();

        $this->LogsHelper->obsoletize($latests);

        $this->assertTrue($latests[0]->obsolete);
    }

    public function testObsoletize_withZeroDate()
    {
        $contributions = TableRegistry::get('Contributions');
        $latests = $contributions->find()->where(['sentence_id' => 18])->toList();

        $this->LogsHelper->obsoletize($latests);

        $this->assertTrue($latests[0]->obsolete);
        $this->assertFalse($latests[1]->obsolete);
    }
}
