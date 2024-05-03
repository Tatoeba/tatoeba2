<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FavoritesTable;
use Cake\TestSuite\TestCase;
use Cake\ORM\TableRegistry;

class FavoritesTableTest extends TestCase {
    public $fixtures = array(
        'app.FavoritesUsers',
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

    function testAddFavorite() {
        $before = $this->Favorites->find()->where(['user_id' => 1])->count();
        $result = $this->Favorites->addFavorite(1, 1);
        $after = $this->Favorites->find()->where(['user_id' => 1])->count();
        $this->assertEquals(1, $after - $before);
        $this->assertNotNull($result);
    }
    
    function testRemoveFavorite() {
        $before = $this->Favorites->find()->where(['user_id' => 7])->count();
        $result = $this->Favorites->removeFavorite(4, 7);
        $after = $this->Favorites->find()->where(['user_id' => 7])->count();
        $this->assertEquals(-1, $after - $before);
    }
}
