<?php
namespace App\Test\TestCase\Model;

use App\Model\Search;
use Cake\TestSuite\TestCase;

class SearchTest extends TestCase
{
    public $fixtures = [
        'app.users',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Search = new Search();
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Search);
    }

    public function testWithoutFilters() {
        $expected = ['index' => ['und_index']];

        $result = $this->Search->asSphinx();

        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_validLang() {
        $expected = ['index' => ['por_main_index', 'por_delta_index']];
        $this->Search->filterByLanguage('por');

        $result = $this->Search->asSphinx();

        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_und() {
        $expected = ['index' => ['und_index']];
        $this->Search->filterByLanguage('und');

        $result = $this->Search->asSphinx();

        $this->assertEquals($expected, $result);
    }

    public function testfilterByLanguage_invalidLang() {
        $expected = ['index' => ['und_index']];
        $this->Search->filterByLanguage('1234567890');

        $result = $this->Search->asSphinx();

        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwnerName_invalid() {
        $result = $this->Search->filterByOwnerName('userdoesnotexists');
        $this->assertFalse($result);
    }

    public function testfilterByOwnerName_valid() {
        $result = $this->Search->filterByOwnerName('contributor');
        $this->assertTrue($result);

        $expected = ['index' => ['und_index'], 'filter' => [['user_id', 4]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwnerName_empty() {
        $expected = ['index' => ['und_index']];
        $result = $this->Search->filterByOwnerName('');
        $this->assertTrue($result);

        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }
}
