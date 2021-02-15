<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class WikiArticlesTable extends Table
{
    public static function defaultConnectionName() {
        return 'wiki';
    }

    public function getArticleTranslations($lang, $slug) {
        $article = $this->find()
            ->select(['group_id'])
            ->where(compact('lang', 'slug'))
            ->first();

        if (!$article) {
            return [];
        }
        $group_id = $article->group_id;

        return $this->find()
            ->select(['lang', 'slug'])
            ->where(compact('group_id'))
            ->enableHydration(false)
            ->combine('lang', 'slug')
            ->toArray();
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
