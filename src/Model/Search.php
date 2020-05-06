<?php
namespace App\Model;

use App\Lib\LanguagesLib;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\Utility\Hash;

class Search {
    use \Cake\Datasource\ModelAwareTrait;

    private $query;
    private $lang;
    private $ownerId;
    private $hasOwner;
    private $correctness;
    private $hasAudio;
    private $listId;
    private $tagsIds = [];
    private $native;
    private $sort;
    private $sortReversed;

    private $translationFilter;
    private $translationFilters = [];

    private $sphinxFilterArrayLimit = 4096;

    private function getNativeSpeakerFilterAsSphinx() {
        $this->loadModel('UsersLanguages');
        $natives = $this->UsersLanguages->find()
            ->where([
                'language_code' => $this->lang,
                'level' => 5,
            ])
            ->select(['of_user_id'])
            ->enableHydration(false)
            ->toList();
        $natives = Hash::extract($natives, '{n}.of_user_id');

        if (count($natives) <= $this->sphinxFilterArrayLimit) {
            $filter = [['user_id', $natives]];
        } else {
            $nonNatives = $this->UsersLanguages->find()
                ->where(function (QueryExpression $exp) {
                    $isNonNative = $exp->or(['level is' => null])->notEq('level', 5);
                    return $exp->add($isNonNative)
                               ->eq('language_code', $this->lang);
                })
                ->select(['of_user_id'])
                ->enableHydration(false)
                ->toList();
            $nonNatives = Hash::extract($nonNatives, '{n}.of_user_id');
            $filter = [];
            while (count($nonNatives)) {
                $excludedIds = array_splice($nonNatives, 0, $this->sphinxFilterArrayLimit);
                $filter[] = ['user_id', $excludedIds, true];
            }
        }
        return $filter;
    }

    private function getTranslationFiltersAsSphinx() {
        $transFilter = [];
        $sphinxMap = [
            'hasAudio' => function($v) { return 't.a='.(int)$v; },
            'language' => function($v) { return "t.l='$v'"; },
            'link'     => function($v) { return 't.d='.($v == 'direct' ? 1 : 2); },
            'ownerId'  => function($v) { return 't.u='.(int)$v; },
            'hasOwner' => function($v) { return 't.u'.($v ? '=' : '<>').'0'; },
            'correctness' => function($v) { return 't.c='.(int)!$v; },
        ];
        foreach ($this->getTranslationFilters() as $filter => $value) {
            $transFilter[] = $sphinxMap[$filter]($value);
        }
        return $transFilter;
    }

    private function parseYesNoEmpty($value) {
        if (is_null($value)) {
            return '';
        } else {
            return $value ? 'yes' : 'no';
        }
    }

    private function parseBoolean($value, &$variable) {
        if (in_array($value, ['yes', 'no'])) {
            $variable = $value == 'yes';
        }
        return $this->parseYesNoEmpty($variable);
    }

    private function orderby($expr, $order) {
        return $expr . ($order ? ' ASC' : ' DESC');
    }

    private function asSphinxIndex($lang) {
        if ($lang) {
            return [$lang. '_main_index', $lang . '_delta_index'];
        } else {
            return ['und_index'];
        }
    }

    public function asSphinx() {
        $sphinx = [
            'index' => $this->asSphinxIndex($this->lang),
        ];
        if (!is_null($this->query)) {
            $sphinx['query'] = $this->query;
        }
        if ($this->ownerId) {
            $sphinx['filter'][] = ['user_id', $this->ownerId];
        }
        if (!is_null($this->hasOwner)) {
            $sphinx['filter'][] = ['user_id', 0, !$this->hasOwner];
        }
        if (!is_null($this->correctness)) {
            // See the indexation SQL request for the value 127
            $sphinx['filter'][] = ['ucorrectness', 127, !$this->correctness];
        }
        if (!is_null($this->hasAudio)) {
            $sphinx['filter'][] = array('has_audio', $this->hasAudio);
        }
        if (!is_null($this->listId)) {
            $sphinx['filter'][] = array('lists_id', $this->listId);
        }
        foreach ($this->tagsIds as $id) {
            $sphinx['filter'][] = ['tags_id', $id];
        }
        if (!is_null($this->native)) {
            $sphinx['filter'] = $sphinx['filter'] ?? [];
            array_push($sphinx['filter'], ...$this->getNativeSpeakerFilterAsSphinx());
        }
        if (!is_null($this->translationFilter)) {
            $transFilter = $this->getTranslationFiltersAsSphinx();
            if (empty($transFilter)) {
                $transFilter = [1];
            }
            $filter = implode(' & ', $transFilter);
            $sphinx['select'] = "*, ANY($filter FOR t IN trans) as filter";

            $filter = $this->translationFilter == 'limit' ? 1 : 0;
            $sphinx['filter'][] = ['filter', $filter];
        }
        if ($this->sort) {
            if ($this->sort == 'random') {
                $sortOrder = '@random';
            } elseif (empty($this->query)) {
                // When the query is empty, Manticore does not perform any
                // ranking, so we need to rely on ordering instead
                if ($this->sort == 'created' || $this->sort == 'modified') {
                    $sortOrder = $this->orderby($this->sort, $this->sortReversed);
                } else {
                    $sortOrder = $this->orderby('text_len', !$this->sortReversed);
                }
            } else {
                // When there are keywords, Manticore will perform ranking
                $sortOrder = $this->orderby('@rank', $this->sortReversed);
                if ($this->sort == 'words') {
                    $rankingExpr = '-text_len';
                } elseif ($this->sort == 'relevance') {
                    $rankingExpr = '-text_len+top(lcs+exact_order*100)*100';
                } elseif ($this->sort == 'created' || $this->sort == 'modified') {
                    $rankingExpr = $this->sort;
                }
            }
            $sphinx['matchMode'] = SPH_MATCH_EXTENDED2;
            $sphinx['sortMode'] = [SPH_SORT_EXTENDED => $sortOrder];
            if (isset($rankingExpr)) {
                $sphinx['rankingMode'] = [SPH_RANK_EXPR => $rankingExpr];
            }
        }
        return $sphinx;
    }

    public function filterByQuery($query) {
        $this->query = $query;
    }

    public function filterByLanguage($lang) {
        if (LanguagesLib::languageExists($lang)) {
            $this->lang = $lang;
        }
        return $this->lang ?? 'und';
    }

    public function filterByOwnerName($owner) {
        if (!empty($owner)) {
            $this->loadModel('Users');
            $result = $this->Users->findByUsername($owner, ['fields' => ['id']])->first();
            if ($result) {
                $this->ownerId = $result->id;
            } else {
                return false;
            }
        }
        return true;
    }

    public function filterByOwnership($hasOwner) {
        return $this->parseBoolean($hasOwner, $this->hasOwner);
    }

    public function sort($sort) {
        if (in_array($sort, ['relevance', 'words', 'created', 'modified', 'random'])) {
            $this->sort = $sort;
        }
    }

    public function reverseSort($reversed) {
        $this->sortReversed = $reversed;
    }

    public function filterByCorrectness($correctness) {
        return $this->parseBoolean($correctness, $this->correctness);
    }

    public function filterByAudio($hasAudio) {
        return $this->parseBoolean($hasAudio, $this->hasAudio);
    }

    public function filterByListId($listId, $currentUserId) {
        if (strlen($listId)) {
            $this->loadModel('SentencesLists');
            $list = $this->SentencesLists->isSearchableList($listId, $currentUserId);
            if ($list) {
                $this->listId = $list->id;
            } else {
                return false;
            }
        }
        return true;
    }

    public function filterByTags($tags) {
        $appliedTagsNames = [];
        if ($tags) {
            $this->loadModel('Tags');
            $order = new FunctionExpression(
                'FIND_IN_SET',
                ['Tags.name' => 'literal', implode(',', $tags)]
            );
            $result = $this->Tags->find()
                ->where(['name IN' => $tags])
                ->select(['id', 'name'])
                ->order($order)
                ->enableHydration(false)
                ->toList();
            $this->tagsIds = Hash::extract($result, '{n}.id');
            $appliedTagsNames = Hash::extract($result, '{n}.name');
        }
        return $appliedTagsNames;
    }

    public function filterByNativeSpeaker($filter) {
        if ($filter == 'yes') {
            $this->native = true;
        }
        return $this->parseYesNoEmpty($this->native);
    }

    public function filterByTranslation($filter) {
        if (in_array($filter, ['exclude', 'limit'])) {
            $this->translationFilter = $filter;
        }
        return $this->translationFilter ?? '';
    }

    public function filterByTranslationLanguage($lang) {
        if (LanguagesLib::languageExists($lang)) {
            $this->translationFilters['language'] = $lang;
        }
        return $this->translationFilters['language'] ?? 'und';
    }

    public function filterByTranslationLink($link) {
        if (in_array($link, ['direct', 'indirect'])) {
            $this->translationFilters['link'] = $link;
        }
        return $this->translationFilters['link'] ?? '';
    }

    public function filterByTranslationOwnerName($owner) {
        if (!empty($owner)) {
            $this->loadModel('Users');
            $result = $this->Users->findByUsername($owner, ['fields' => ['id']])->first();
            if ($result) {
                $this->translationFilters['ownerId'] = $result->id;
            } else {
                return false;
            }
        }
        return true;
    }

    public function filterByTranslationOwnership($hasOwner) {
        return $this->parseBoolean($hasOwner, $this->translationFilters['hasOwner']);
    }

    public function filterByTranslationCorrectness($correctness) {
        return $this->parseBoolean($correctness, $this->translationFilters['correctness']);
    }

    public function filterByTranslationAudio($filter) {
        return $this->parseBoolean($filter, $this->translationFilters['hasAudio']);
    }

    public function setSphinxFilterArrayLimit($limit) {
        $this->sphinxFilterArrayLimit = $limit;
    }

    public function getTranslationFilters() {
        return array_filter(
            $this->translationFilters,
            function ($v) { return !is_null($v); }
        );
    }

    public function getSearchableLists($listId, $byUserId) {
        $this->loadModel('SentencesLists');
        $searchableLists = $this->SentencesLists->find();
        $searchableLists
            ->select([
                'additional' => $searchableLists->newExpr()->eq('id', $listId),
                'id',
                'name',
                'user_id'
            ])
            ->where([
                'OR' => [
                    'user_id' => $byUserId,
                    'visibility' => 'public',
                ]
            ]);

        if (strlen($listId)) {
            $additional = $this->SentencesLists->find()
                ->where([
                    'id' => $listId,
                    'OR' => [
                        'user_id' => $byUserId,
                        'NOT' => ['visibility' => 'private']
                    ]
                ])
                ->select(['additional' => 1, 'id', 'name', 'user_id'])
                ->limit(1);
            $searchableLists->union($additional);
        }

        $searchableLists = $this->SentencesLists->find()
            ->select([
                'id' => 'SentencesLists__id',
                'name' => 'SentencesLists__name',
                'user_id' => 'SentencesLists__user_id'
            ])
            ->from(['s' => $searchableLists])
            ->order(['additional', 'name']);

        return $searchableLists;
    }
}
