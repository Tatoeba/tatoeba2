<?php
namespace App\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use App\View\Helper\AppHelper;

class AppHelperTest extends TestCase {

    public function setUp() {
        parent::setUp();
        $View = new View();
        $this->AppHelper = new AppHelper($View);
    }

    public function tearDown() {
        unset($this->AppHelper);
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
        $result = $this->AppHelper->safeForAngular($string);
        $this->assertEquals($expected, $result);
    }
}
