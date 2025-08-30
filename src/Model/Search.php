<?php
namespace App\Model;

use App\Lib\LanguagesLib;
use App\Model\Exception\InvalidValueException;
use App\Model\Search\TranslationLangFilter;
include_once(APP.'Lib/SphinxClient.php'); // needed to get the constants

class Search {
    use \Cake\Datasource\ModelAwareTrait;
    use Search\FiltersCollectionTrait;

    const CURSOR_FIELD = 'cursor'; // name for calculated field

    private $query;
    private $sort;
    private $sortReversed = false;
    private $sortOrder = [];
    private $rankingExpr;
    private $randSeed;
    private $computeCursor = false;

    private function orderby($expr, $order) {
        return $expr . ($order ? ' ASC' : ' DESC');
    }

    private function orderby_(array $order) {
        return $this->orderby(...$order);
    }

    private function asSphinxIndex($langs) {
        if (count($langs) > 0) {
            $indexes = [];
            foreach ($langs as $lang) {
                $indexes[] = $lang . '_main_index';
                $indexes[] = $lang . '_delta_index';
            }
            return $indexes;
        } else {
            return ['und_index'];
        }
    }

    public function compile(&$select = "*") {
        if ($this->sort) {
            // make $this->sortOrder and $this->rankingExpr available
            // for filters during compilation
            $this->computeSortAndRanking();
        }
        $output = [];
        foreach ($this->filters as $filter) {
            if (!is_null($filter)) {
                foreach ($filter->compile() as $compiled) {
                    if (isset($compiled[1]) && is_string($compiled[1])) {
                        $select .= ", ${compiled[1]} as ${compiled[0]}";
                        $compiled[1] = (int)!($compiled[2] ?? false);
                        unset($compiled[2]);
                    }
                    $output[] = $compiled;
                }
            }
        }
        return $output;
    }

    public function asSphinx() {
        $sphinx = [
            'index' => $this->asSphinxIndex([]),
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => "*",
        ];
        if (!is_null($this->query)) {
            $sphinx['query'] = $this->query;
        }
        foreach ($this->compile($sphinx['select']) as $compiled) {
            if ($compiled[0] == 'lang') {
                $sphinx['index'] = $this->asSphinxIndex($compiled[1]);
            } else {
                $sphinx['filter'][] = $compiled;
            }
        }
        if ($this->sortOrder) {
            $sphinx['sortMode'] = [
                SPH_SORT_EXTENDED => implode(', ', array_map([$this, 'orderby_'], $this->sortOrder))
            ];
            if ($this->computeCursor && $this->sort != 'random') {
                $sphinx['select'] .= $this->computeCursor();
            }
        }
        if (isset($this->rankingExpr)) {
            $sphinx['rankingMode'] = [SPH_RANK_EXPR => $this->rankingExpr];
        }
        return $sphinx;
    }

    public function filterByQuery($query) {
        return $this->query = $query;
    }

    public static function validateLanguage($lang) {
        if (LanguagesLib::languageExists($lang)) {
            return $lang;
        } else {
            throw new InvalidValueException("Invalid language code '$lang'");
        }
    }

    private function computeCursor() {
        $attrs = array_map(
            function($s) {
                $attr = $s[0] == '@rank' ? 'WEIGHT()' : $s[0];
                return "TO_STRING($attr)";
            },
            $this->sortOrder
        );
        return ', CONCAT('. implode(", ',', ", $attrs) .') as '.self::CURSOR_FIELD;
    }

    private function computeSortAndRanking() {
        $this->sortOrder = [];
        $randomExpr = "RAND({$this->randSeed})*16777216";
        if (empty($this->query)) {
            // When the query is empty, Manticore does not perform any
            // ranking, so we need to rely on ordering instead
            if ($this->sort == 'created' || $this->sort == 'modified') {
                $this->sortOrder[] = [$this->sort, $this->sortReversed];
            } elseif ($this->sort == 'random') {
                $this->sortOrder[] = [$randomExpr, $this->sortReversed];
            } else {
                $this->sortOrder[] = ['text_len', !$this->sortReversed];
            }
        } else {
            // When there are keywords, Manticore will perform ranking
            $this->sortOrder[] = ['@rank', $this->sortReversed];
            if ($this->sort == 'words') {
                $this->rankingExpr = '-text_len';
            } elseif ($this->sort == 'relevance') {
                $this->rankingExpr = '-text_len+top(lcs+exact_order*100)*100';
            } elseif ($this->sort == 'created' || $this->sort == 'modified') {
                $this->rankingExpr = $this->sort;
            } elseif ($this->sort == 'random') {
                $this->rankingExpr = $randomExpr;
            }
        }
        $this->sortOrder[] = ['id', $this->sortReversed];
    }

    public function getInternalSortOrder() {
        return $this->sortOrder;
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

    public function getTranslationFilter($class, $index = '') {
        return $this->getTranslationFilters($index)->getFilter($class);
    }

    public function setTranslationFilter($filter, $index = '') {
        $this->getTranslationFilters($index)->setFilter($filter);
    }

    public function unsetTranslationFilter($class, $index = '') {
        $this->getTranslationFilters($index)->unsetFilter($class);
    }

    public function getFilteredTranslationLanguages($index = '') {
        $filter = $this->getTranslationFilter(TranslationLangFilter::class, $index);
        return $filter ? $filter->getAllValues() : null;
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

    public function setComputeCursor(bool $computeCursor) {
        $this->computeCursor = $computeCursor;
    }
}
