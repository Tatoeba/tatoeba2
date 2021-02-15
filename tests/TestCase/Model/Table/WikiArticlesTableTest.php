<?php
namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class WikiArticlesTableTest extends TestCase
{
    public $WikiArticles;

    public $fixtures = [
        'app.WikiArticles',
    ];

    public function setUp() {
        parent::setUp();
        $this->WikiArticles = TableRegistry::get('WikiArticles');
    }

    public function tearDown() {
        unset($this->WikiArticles);
        parent::tearDown();
    }

    public function testSave() {
        $before = $this->WikiArticles->find()->enableHydration(false)->toArray();

        $test = $this->WikiArticles->newEntity([
            'group_id' => 23,
            'lang' => 'en',
            'slug' => 'writing-test',
            'title' => 'Writing test',
            'content' => 'Testing testing testing...',
            'locked' => false,
        ]);
        $result = $this->WikiArticles->save($test);

        $after = $this->WikiArticles->find()->enableHydration(false)->toArray();
        $this->assertFalse($result);
        $this->assertEquals($before, $after);
    }

    public function testGetArticleTranslations() {
        $expected = [
            'en' => 'quick-start',
            'fr' => 'premiers-pas',
            'tr' => 'hizli-baslangic',
        ];
        $result = $this->WikiArticles->getArticleTranslations('en', 'quick-start');
        $this->assertEquals($expected, $result);
    }

    public function testGetArticleTranslations_unknownLang() {
        $result = $this->WikiArticles->getArticleTranslations('doesnotexists', 'quick-start');
        $this->assertEmpty($result);
    }

    public function testGetArticleTranslations_unknownSlug() {
        $result = $this->WikiArticles->getArticleTranslations('en', 'does-not-exists');
        $this->assertEmpty($result);
    }

    public function testDefaultConnectionName() {
        $this->assertEquals('wiki', $this->WikiArticles->defaultConnectionName());
    }
}
