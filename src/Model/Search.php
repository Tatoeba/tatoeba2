<?php
namespace App\Model;

use App\Lib\LanguagesLib;
use App\Model\Exception\InvalidValueException;
use App\Model\Search\TranslationLangFilter;
include_once(APP.'Lib/SphinxClient.php'); // needed to get the constants

class Search {
    use \Cake\Datasource\ModelAwareTrait;
    use Search\FiltersCollectionTrait;

    private $query;
    private $sort;
    private $sortReversed = false;
    private $randSeed;

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

    public function compile(&$select = "*") {
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
            $sortOrder .= ', '.$this->orderby('id', $this->sortReversed);
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
}
