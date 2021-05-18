<?php
namespace App\Test\TestCase\Model\Table;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class WikiArticlesTableTest extends TestCase
{
    public $WikiArticles;

    public $autoFixtures = false;

    public $fixtures = [
        'app.WikiArticles',
    ];

    public function setupFailingConnection() {
        $config = \Cake\Datasource\ConnectionManager::getConfig('test_wiki');
        $config['database'] = '/proc/you-should-never-be-able-to-write-here';
        ConnectionManager::setConfig('test_wiki2', $config);
        return ConnectionManager::get('test_wiki2');
    }

    public function setUp() {
        parent::setUp();

        $this->oldLocale = I18n::getLocale();

        Configure::write('Tatowiki.baseHost', 'wiki.example.com:1234');

        $options = [];
        if ($this->getName() == 'testGetArticleTranslations_dbAccessFail') {
            $options['connection'] = $this->setupFailingConnection();
        } else {
            $this->loadFixtures(); // load all $this->fixtures
        }
        $this->WikiArticles = TableRegistry::get('WikiArticles', $options);
    }

    public function tearDown() {
        unset($this->WikiArticles);
        I18n::setLocale($this->oldLocale);
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
        I18n::setLocale('fr');
        $expected = '//fr.wiki.example.com:1234/articles/show/premiers-pas';
        $result = $this->WikiArticles->getWikiLink('quick-start');
        $this->assertEquals($expected, $result);
    }

    public function testGetWikiLink_escape() {
        I18n::setLocale('ru');
        $expected = '//ru.wiki.example.com:1234/articles/show/%D0%BA%D1%80%D0%B0%D1%82%D0%BA%D0%BE%D0%B5_%D1%80%D1%83%D0%BA%D0%BE%D0%B2%D0%BE%D0%B4%D1%81%D1%82%D0%B2%D0%BE';
        $result = $this->WikiArticles->getWikiLink('quick-start');
        $this->assertEquals($expected, $result);
    }

    public function testGetWikiLink_noSuchArticle() {
        I18n::setLocale('fr');
        $expected = '//en.wiki.example.com:1234/articles/show/does-not-exists';
        $result = $this->WikiArticles->getWikiLink('does-not-exists');
        $this->assertEquals($expected, $result);
    }

    public function testGetWikiLink_noSuchTranslation() {
        I18n::setLocale('doesnotexists');
        $expected = '//en.wiki.example.com:1234/articles/show/quick-start';
        $result = $this->WikiArticles->getWikiLink('quick-start');
        $this->assertEquals($expected, $result);
    }

    public function testDefaultConnectionName() {
        $this->assertEquals('wiki', $this->WikiArticles->defaultConnectionName());
    }

    public function testGetArticleTranslations_dbAccessFail() {
        // See specific initialization code in setUp()
        $result = $this->WikiArticles->getArticleTranslations('en', 'quick-start');
        $this->assertEmpty($result);
    }
}
