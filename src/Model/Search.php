<?php
namespace App\Model;

use App\Lib\LanguagesLib;
include_once(APP.'Lib/SphinxClient.php'); // needed to get the constants
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\Utility\Hash;

class Search {
    use \Cake\Datasource\ModelAwareTrait;

    const OPERATOR_LOWER_OR_EQUAL = 'le';
    const OPERATOR_GREATER_OR_EQUAL = 'ge';
    const OPERATOR_EQUAL = 'eq';
    const OPERATORS_SPHINXQL_MAP = [
        self::OPERATOR_LOWER_OR_EQUAL   => '<=',
        self::OPERATOR_GREATER_OR_EQUAL => '>=',
        self::OPERATOR_EQUAL            => '=',
    ];

    private $query;
    private $lang;
    private $ownerId;
    private $isOrphan;
    private $correctness;
    private $hasAudio;
    private $listId;
    private $tagsIds = [];
    private $native;
    private $sort;
    private $sortReversed = false;
    private $randSeed;
    private $wordCount = [];

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
            'isOrphan' => function($v) { return 't.u'.($v ? '=' : '<>').'0'; },
            'correctness' => function($v) { return 't.c='.(int)!$v; },
        ];
        foreach ($this->getTranslationFilters() as $filter => $value) {
            $transFilter[] = $sphinxMap[$filter]($value);
        }
        return $transFilter;
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
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => "*",
        ];
        if (!is_null($this->query)) {
            $sphinx['query'] = $this->query;
        }
        if ($this->ownerId) {
            $sphinx['filter'][] = ['user_id', $this->ownerId];
        }
        if (!is_null($this->isOrphan)) {
            $sphinx['filter'][] = ['user_id', 0, !$this->isOrphan];
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
        foreach ($this->wordCount as $op => $count) {
            $sqlOp = self::OPERATORS_SPHINXQL_MAP[$op];
            $filterName = "word_count_filter_$op";
            $sphinx['filter'][] = array($filterName, 1);
            $sphinx['select'] .= ", (text_len $sqlOp $count) as $filterName";
        }
        if (!is_null($this->translationFilter)) {
            $transFilter = $this->getTranslationFiltersAsSphinx();
            if (empty($transFilter)) {
                $transFilter = [1];
            }
            $filter = implode(' & ', $transFilter);
            $sphinx['select'] .= ", ANY($filter FOR t IN trans) as filter";

            $filter = $this->translationFilter == 'limit' ? 1 : 0;
            $sphinx['filter'][] = ['filter', $filter];
        }
        if ($this->sort) {
            $randomExpr = "RAND({$this->randSeed})*16777216";
            if (empty($this->query)) {
                // When the query is empty, Manticore does not perform any
                // ranking, so we need to rely on ordering instead
                if ($this->sort == 'created' || $this->sort == 'modified') {
                    $sortOrder = $this->orderby($this->sort, $this->sortReversed);
                } elseif ($this->sort == 'random') {
                    $sortOrder = $this->orderby($randomExpr, $this->sortReversed);
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
                } elseif ($this->sort == 'random') {
                    $rankingExpr = $randomExpr;
                }
            }
            if ($this->sort == 'random') {
                $sortOrder .= ', '.$this->orderby('id', $this->sortReversed);
            }
            $sphinx['sortMode'] = [SPH_SORT_EXTENDED => $sortOrder];
            if (isset($rankingExpr)) {
                $sphinx['rankingMode'] = [SPH_RANK_EXPR => $rankingExpr];
            }
        }
        return $sphinx;
    }

    public function filterByQuery($query) {
        return $this->query = $query;
    }

    public function filterByLanguage($lang) {
        $this->lang = null;
        if (LanguagesLib::languageExists($lang)) {
            $this->lang = $lang;
        }
        return $this->lang;
    }

    public function filterByOwnerId($ownerId) {
        return $this->ownerId = $ownerId;
    }

    public function filterByOrphanship($isOrphan) {
        return $this->isOrphan = $isOrphan;
    }

    public function sort($sort) {
        $this->sort = null;
        if (in_array($sort, ['relevance', 'words', 'created', 'modified', 'random'])) {
            $this->sort = $sort;
        }
        return $this->sort;
    }

    public function reverseSort(bool $reversed) {
        return $this->sortReversed = $reversed;
    }

    public function filterByCorrectness($correctness) {
        return $this->correctness = $correctness;
    }

    public function filterByAudio($hasAudio) {
        return $this->hasAudio = $hasAudio;
    }

    public function filterByListId($listId, $currentUserId) {
        $this->listId = null;
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
        $this->tagsIds = [];
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
        return $this->native = $filter;
    }

    public function filterByWordCount($op, $count) {
        if (array_key_exists($op, self::OPERATORS_SPHINXQL_MAP)) {
            if (is_int($count) && $count >= 0) {
                $this->wordCount[$op] = $count;
                return $this->wordCount[$op];
            } else {
                unset($this->wordCount[$op]);
            }
        }
        return null;
    }

    public function filterByTranslation($filter) {
        $this->translationFilter = null;
        if (in_array($filter, ['exclude', 'limit'])) {
            $this->translationFilter = $filter;
        }
        return $this->translationFilter;
    }

    public function filterByTranslationLanguage($lang) {
        $this->translationFilters['language'] = null;
        if (LanguagesLib::languageExists($lang)) {
            $this->translationFilters['language'] = $lang;
        }
        return $this->translationFilters['language'];
    }

    public function filterByTranslationLink($link) {
        $this->translationFilters['link'] = null;
        if (in_array($link, ['direct', 'indirect'])) {
            $this->translationFilters['link'] = $link;
        }
        return $this->translationFilters['link'];
    }

    public function filterByTranslationOwnerId($ownerId) {
        return $this->translationFilters['ownerId'] = $ownerId;
    }

    public function filterByTranslationOrphanship($isOrphan) {
        return $this->translationFilters['isOrphan'] = $isOrphan;
    }

    public function filterByTranslationCorrectness($correctness) {
        return $this->translationFilters['correctness'] = $correctness;
    }

    public function filterByTranslationAudio($filter) {
        return $this->translationFilters['hasAudio'] = $filter;
    }

    public function setSphinxFilterArrayLimit($limit) {
        $this->sphinxFilterArrayLimit = $limit;
    }

    public function getTranslationFilter($filterName) {
        if (array_key_exists($filterName, $this->translationFilters)) {
            return $this->translationFilters[$filterName];
        } else {
            return null;
        }
    }

    public function getTranslationFilters() {
        return array_filter(
            $this->translationFilters,
            function ($v) { return !is_null($v); }
        );
    }

    public static function exactSearchQuery($text) {
        $from = array('\\', '(', ')', '|', '-', '!', '@', '~', '"', '&', '/', '^', '$', '=');
        $to = array('\\\\', '\(', '\)', '\|', '\-', '\!', '\@', '\~', '\"', '\&', '\/', '\^', '\$', '\=' );
        $escaped = str_replace($from, $to, $text);
        return '="'.$escaped.'"';
    }

    public function setRandSeed($seed) {
        return $this->randSeed = $seed;
    }
}
