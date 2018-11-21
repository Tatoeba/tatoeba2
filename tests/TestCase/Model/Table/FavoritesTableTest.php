<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FavoritesTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

class FavoritesTableTest extends TestCase {
    public $fixtures = array(
        'app.favorites_users',
    );

    function setUp() {
        parent::setUp();
        $this->Favorites = TableRegistry::getTableLocator()->get('Favorites');
    }

    function tearDown() {
        unset($this->Favorites);
        parent::tearDown();
    }

    function testNumberOfFavoritesOfUser() {
        $result = $this->Favorites->numberOfFavoritesOfUser(7);
        $this->assertEquals(1, $result);
    }
}