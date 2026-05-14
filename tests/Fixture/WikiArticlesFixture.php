<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class WikiArticlesFixture extends TestFixture
{
    public $connection = 'test_wiki';

    public $table = 'articles';

    public function init(): void
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
