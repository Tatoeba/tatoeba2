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
}

class Search {
    use \Cake\Datasource\ModelAwareTrait;

    private $query;
    private $filters;
    private $langs = [];
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

    public function getFilter($class, $index = '') {
        return $this->filters->{$class::getName($index)} ?? null;
    }

    public function setFilter($filter) {
        $this->filters->{$filter->getAlias()} = $filter;
        return $this;
    }

    public function unsetFilter($class, $index = '') {
        unset($this->filters->{$class::getName($index)});
    }

    public function getTranslationFilters($index = '') {
        $filter = $this->getFilter(TranslationFilterGroup::class, $index);
        if ($filter) {
            return $filter;
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
