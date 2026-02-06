<?php
namespace App\Test\TestCase\Model\Search;

use App\Model\Search\ShowtransLimiter;
use App\Model\Search\TranslationHasAudioFilter;
use App\Model\Search\TranslationIsDirectFilter;
use App\Model\Search\TranslationIsNativeFilter;
use App\Model\Search\TranslationIsOrphanFilter;
use App\Model\Search\TranslationIsUnapprovedFilter;
use App\Model\Search\TranslationLangFilter;
use App\Model\Search\TranslationOwnerFilter;
use App\Model\Search\TranslationFilterGroup;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class ShowtransLimiterTest extends TestCase
{
    public $fixtures = [
        'app.audios',
        'app.links',
        'app.sentences',
        'app.transcriptions',
        'app.users',
        'app.users_languages',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Sentences = TableRegistry::getTableLocator()->get('Sentences');
        $this->Sentences->addBehavior('ExposedOnApi');
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->Sentences);
    }

    public function showtransFiltersProvider() {
        return [
            // filters, expected shown translation ids [, original sentence id = 1]
            'no filters' => [
                [],
                [2, 3, 4, 5, 6],
            ],
            'single language' => [
                [(new TranslationLangFilter())->anyOf(['spa'])],
                [3],
            ],
            'multiple languages' => [
                [(new TranslationLangFilter())->anyOf(['spa', 'cmn'])],
                [2, 3],
            ],
            'exclude languages' => [
                [(new TranslationLangFilter())->not()->anyOf(['spa', 'cmn'])],
                [4, 5, 6],
            ],
            'is direct translation' => [
                [new TranslationIsDirectFilter()],
                [2, 3, 4],
            ],
            'is indirect translation' => [
                [(new TranslationIsDirectFilter())->not()],
                [5, 6],
            ],
            'owned by kazuki' => [
                [(new TranslationOwnerFilter())->anyOf(['kazuki'])],
                [2, 4, 6],
            ],
            'not owned by kazuki' => [
                [(new TranslationOwnerFilter())->not()->anyOf(['kazuki'])],
                [3],
            ],
            'owned by kazuki or spammer' => [
                [(new TranslationOwnerFilter())->anyOf(['kazuki', 'advanced_contributor'])],
                [2, 3, 4, 6],
            ],
            'is orphan' => [
                [new TranslationIsOrphanFilter()],
                [5],
            ],
            'is not orphan' => [
                [(new TranslationIsOrphanFilter())->not()],
                [2, 3, 4, 6],
            ],
            'is unapproved' => [
                [new TranslationIsUnapprovedFilter()],
                [65],
                55
            ],
            'is not unapproved' => [
                [(new TranslationIsUnapprovedFilter())->not()],
                [56, 57],
                55
            ],
            'has audio' => [
                [new TranslationHasAudioFilter()],
                [3, 4],
            ],
            'does not have audio' => [
                [(new TranslationHasAudioFilter())->not()],
                [2, 5, 6],
            ],
            'owner is native' => [
                [new TranslationIsNativeFilter()],
                [6],
            ],
            'owner is not native' => [
                [(new TranslationIsNativeFilter())->not()],
                [2, 3, 4, 5],
            ],

            '"not owned by kazuki" or "is orphan"' => [
                [
                    (new TranslationOwnerFilter())->not()->anyOf(['kazuki']),
                    new TranslationIsOrphanFilter(),
                ],
                [3, 5],
            ],
            '"multiple languages" and "has audio"' => [
                [
                    (new TranslationFilterGroup())
                        ->setFilter((new TranslationLangFilter())->anyOf(['spa', 'cmn']))
                        ->setFilter(new TranslationHasAudioFilter()),
                ],
                [3],
            ],
            '"exclude languages" and "is direct translation"' => [
                [
                    (new TranslationFilterGroup())
                        ->setFilter((new TranslationLangFilter())->not()->anyOf(['spa', 'cmn']))
                        ->setFilter(new TranslationIsDirectFilter()),
                ],
                [4],
            ],
            '"multiple languages" and "has audio", or "exclude languages" and "is direct translation"' => [
                [
                    (new TranslationFilterGroup())
                        ->setFilter((new TranslationLangFilter())->anyOf(['spa', 'cmn']))
                        ->setFilter(new TranslationHasAudioFilter()),
                    (new TranslationFilterGroup())
                        ->setFilter((new TranslationLangFilter())->not()->anyOf(['spa', 'cmn']))
                        ->setFilter(new TranslationIsDirectFilter()),
                ],
                [3, 4],
            ],
        ];
    }

    /**
     * @dataProvider showtransFiltersProvider
     */
    public function testLimitTranslations($filters, $expectedTranslationsIds, $sentenceId = 1) {
        $groups = [];
        foreach ($filters as $filter) {
            $groups[] = (new TranslationFilterGroup())->setFilter($filter);
        }
        $showtrans = new ShowtransLimiter($groups);
        $containOnApi = ['translations' => function (Query $q) use ($showtrans) {
            return $q->find('translationsOnApi', compact('showtrans'));
        }];

        $result = $this->Sentences
                       ->findById($sentenceId)
                       ->find('sentencesOnApi')
                       ->find('containOnApi', compact('containOnApi'))
                       ->extract('translations.{*}.id')
                       ->toList();

        $this->assertEquals($expectedTranslationsIds, $result);
    }
}
