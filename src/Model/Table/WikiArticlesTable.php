<?php
namespace App\Model\Table;

use App\Lib\LanguagesLib;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\Log\Log;
use Cake\ORM\Table;

class WikiArticlesTable extends Table
{
    public static function defaultConnectionName() {
        return 'wiki';
    }

    public function getArticleTranslations($lang, $slug) {
        $cache_key = "wiki_articles_${lang}_${slug}";
        if (($result = Cache::read($cache_key)) === false) {
            try {
                $group_id = $this->find()
                    ->select(['group_id'])
                    ->where(compact('lang', 'slug'))
                    ->limit(1);

                $result = $this->find()
                    ->select(['lang', 'slug'])
                    ->where(compact('group_id'))
                    ->enableHydration(false)
                    ->combine('lang', 'slug')
                    ->toArray();
            }
            catch (\PDOException $e) {
                if ($this->getConnection()->isQueryLoggingEnabled()) {
                    Log::error('Error while connecting to the wiki: '. $e->getMessage());
                }
                return [];
            }
            Cache::write($cache_key, $result);
        }
        return $result;
    }

    public function getWikiLink($englishSlug) {
        $uiLang = I18n::getLocale();
        $lang = 'en';
        $slug = $englishSlug;

        $articles = $this->getArticleTranslations($lang, $slug);
        if (isset($articles[$uiLang])) {
            $lang = $uiLang;
            $slug = $articles[$uiLang];
        }

        $slug = urlencode($slug);
        $baseHost = Configure::read('Tatowiki.baseHost');
        return "//$lang.$baseHost/articles/show/$slug";
    }

    public function wikiLinkLocalizer() {
        return function($slug) { return $this->getWikiLink($slug); };
    }

    public function initialize(array $config) {
        parent::initialize($config);
        $this->setTable('articles');
    }

    public function save($article, $options = []) {
        // The PDO connection is set to read-only already,
        // but let's be extra careful about not writing anything
        return false;
    }
}
