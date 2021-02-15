<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class WikiArticlesFixture extends TestFixture
{
    public $connection = 'test_wiki';

    public $table = 'articles';

    public $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'autoIncrement' => true, 'precision' => null, 'comment' => null],
        'group_id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => 0, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'lang' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'precision' => null, 'comment' => null, 'collate' => null],
        'slug' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'precision' => null, 'comment' => null, 'collate' => null],
        'title' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'precision' => null, 'comment' => null, 'collate' => null],
        'content' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'precision' => null, 'comment' => null, 'collate' => null],
        'locked' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => false, 'precision' => null, 'comment' => null],
        '_indexes' => [
            'articles_group_id_idx' => ['type' => 'index', 'columns' => ['group_id'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
//            'sqlite_autoindex_articles_1' => ['type' => 'unique', 'columns' => ['lang', 'slug'], 'length' => []],
        ],
    ];

    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'group_id' => 1,
                'lang' => 'en',
                'slug' => 'quick-start',
                'title' => 'Quick Start Guide',
                'content' => 'In this quick guide, we will go through various basic tasks that one can do on Tatoeba.',
                'locked' => false,
            ],
            [
                'id' => 2,
                'group_id' => 1,
                'lang' => 'fr',
                'slug' => 'premiers-pas',
                'title' => 'Premiers pas sur Tatoeba',
                'content' => 'In this quick guide, we will go through various basic tasks that one can do on Tatoeba.',
                'locked' => false,
            ],
            [
                'id' => 3,
                'group_id' => 1,
                'lang' => 'tr',
                'slug' => 'hizli-baslangic',
                'title' => 'Hızlı Başlangıç Kılavuzu',
                'content' => 'Bu kılavuzda Tatoeba’da yapabileceğiniz bazı temel şeyler ele alınacaktır.',
                'locked' => false,
            ],
            [
                'id' => 4,
                'group_id' => 1,
                'lang' => 'ru',
                'slug' => 'краткое_руководство',
                'title' => 'Краткое руководство',
                'content' => 'Краткое вступительное руководство для новых пользователей.',
                'locked' => false,
            ],
        ];
        parent::init();
    }
}
