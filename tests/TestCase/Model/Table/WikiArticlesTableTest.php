<?php
namespace App\Test\TestCase\Model\Table;

use Cake\Core\Configure;
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
            'ru' => 'краткое_руководство',
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

    public function testGetWikiLink() {
        Configure::write('Config.language', 'fra');
        $expected = 'https://fr.wiki.tatoeba.org/articles/show/premiers-pas';
        $result = $this->WikiArticles->getWikiLink('quick-start');
        $this->assertEquals($expected, $result);
    }

    public function testGetWikiLink_escape() {
        Configure::write('Config.language', 'rus');
        $expected = 'https://ru.wiki.tatoeba.org/articles/show/%D0%BA%D1%80%D0%B0%D1%82%D0%BA%D0%BE%D0%B5_%D1%80%D1%83%D0%BA%D0%BE%D0%B2%D0%BE%D0%B4%D1%81%D1%82%D0%B2%D0%BE';
        $result = $this->WikiArticles->getWikiLink('quick-start');
        $this->assertEquals($expected, $result);
    }

    public function testGetWikiLink_noSuchArticle() {
        Configure::write('Config.language', 'fra');
        $expected = 'https://en.wiki.tatoeba.org/articles/show/does-not-exists';
        $result = $this->WikiArticles->getWikiLink('does-not-exists');
        $this->assertEquals($expected, $result);
    }

    public function testGetWikiLink_noSuchTranslation() {
        Configure::write('Config.language', 'doesnotexists');
        $expected = 'https://en.wiki.tatoeba.org/articles/show/quick-start';
        $result = $this->WikiArticles->getWikiLink('quick-start');
        $this->assertEquals($expected, $result);
    }

    public function testDefaultConnectionName() {
        $this->assertEquals('wiki', $this->WikiArticles->defaultConnectionName());
    }
}
