<?php
namespace App\Model;

use App\Lib\LanguagesLib;
use App\Model\Exception\InvalidValueException;
use App\Model\Search\TranslationLangFilter;
use App\Model\Search\TranslationFilterGroup;
include_once(APP.'Lib/SphinxClient.php'); // needed to get the constants

class FiltersCollection {
    public function compile(&$select = "*") {
        $output = [];
        foreach ($this as $filter) {
            if (!is_null($filter)) {
                foreach ($filter->compile($select) as $compiled) {
                    $output[] = $compiled;
                }
            }
        }
        return $output;
    }
}

class Search {
    use \Cake\Datasource\ModelAwareTrait;

    private $query;
    private $filters;
    private $langs = [];
    private $originKnown;
    private $isOriginal;
    private $sort;
    private $sortReversed = false;
    private $randSeed;

    public function __construct() {
        $this->filters = new FiltersCollection();
    }

    private function orderby($expr, $order) {
        return $expr . ($order ? ' ASC' : ' DESC');
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

    public function asSphinx() {
        $sphinx = [
            'index' => $this->asSphinxIndex($this->langs),
            'matchMode' => SPH_MATCH_EXTENDED2,
            'select' => "*",
        ];
        if (!is_null($this->query)) {
            $sphinx['query'] = $this->query;
        }
        foreach ($this->filters->compile($sphinx['select']) as $compiled) {
            $sphinx['filter'][] = $compiled;
        }
        if (!is_null($this->originKnown)) {
            $sphinx['filter'][] = ['origin_known', $this->originKnown];
        }
        if (!is_null($this->isOriginal)) {
            $sphinx['filter'][] = ['is_original', $this->isOriginal];
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

    public static function validateLanguage($lang) {
        if (LanguagesLib::languageExists($lang)) {
            return $lang;
        } else {
            throw new InvalidValueException("Invalid language code '$lang'");
        }
    }

    public function filterByLanguage(array $langs) {
        $this->langs = array_map('self::validateLanguage', $langs);
    }

    public function filterByOriginKnown($originKnown) {
        return $this->originKnown = $originKnown;
    }

    public function filterByIsOriginal($isOriginal) {
        return $this->isOriginal = $isOriginal;
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

    public function getFilters() {
        return $this->filters;
    }

    public function getFilter($class) {
        return $this->filters->{$class::getName()} ?? null;
    }

    public function setFilter($filter) {
        $this->filters->{$filter::getName()} = $filter;
    }

    public function unsetFilter($class) {
        unset($this->filters->{$class::getName()});
    }

    public function getTranslationFilters($index = '') {
        $filterKey = TranslationFilterGroup::getName($index);
        if (isset($this->filters->{$filterKey})) {
            return $this->filters->{$filterKey};
        } else {
            // autocreate
            $filter = new TranslationFilterGroup($index);
            $this->setFilter($filter);
            return $filter;
        }
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
}
