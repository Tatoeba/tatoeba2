<?php
namespace App\Test\TestCase\View;

use Cake\TestSuite\TestCase;
use App\View\AppView;

class AppViewTest extends TestCase {

    private $AppView;

    public function setUp(): void {
        parent::setUp();
        $this->AppView = new AppView();
    }

    public function tearDown(): void {
        unset($this->AppView);
        parent::tearDown();
    }

    public function safeForAngularProvider() {
        return [
            ['', ''],
            ['abc', 'abc'],
            ['{}', '{}'],
            ['{{}}', "{{ '{{' }}}}"],
            ['{{{{}}}}', "{{ '{{' }}{{ '{{' }}}}}}"],
            ['{{{}}}}', "{{ '{{' }}{}}}}"],
            [[], []],
            [[['{{}}']], [["{{ '{{' }}}}"]]],
            [
                ['a' => ['{{}}', '{'], 'b', '{{}}', 'c' => ''],
                ['a' => ["{{ '{{' }}}}", '{'], 'b', "{{ '{{' }}}}", 'c' => '']
            ],
        ];
    }

    /**
     * @dataProvider safeForAngularProvider
     **/
    public function testSafeForAngular($string, $expected) {
        $result = $this->AppView->safeForAngular($string);
        $this->assertEquals($expected, $result);
    }
}
