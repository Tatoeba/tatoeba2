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

    public function testfilterByQuery() {
        $expected = ['index' => ['und_index'], 'query' => 'comme ci comme ça'];
        $this->Search->filterByQuery('comme ci comme ça');

        $result = $this->Search->asSphinx();

        $this->assertEquals($expected, $result);
    }

    public function testfilterByQuery_empty() {
        $expected = ['index' => ['und_index'], 'query' => ''];
        $this->Search->filterByQuery('');

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

    public function testfilterByOwnership_yes() {
        $this->Search->filterByOwnership('yes');

        $expected = ['index' => ['und_index'], 'filter' => [['user_id', 0, false]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwnership_no() {
        $this->Search->filterByOwnership('no');

        $expected = ['index' => ['und_index'], 'filter' => [['user_id', 0, true]]];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwnership_invalid() {
        $this->Search->filterByOwnership('invalid value');

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }

    public function testfilterByOwnership_empty() {
        $this->Search->filterByOwnership('');

        $expected = ['index' => ['und_index']];
        $result = $this->Search->asSphinx();
        $this->assertEquals($expected, $result);
    }
}
