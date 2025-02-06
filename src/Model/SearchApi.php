<?php

namespace App\Model;

use App\Model\Exception\InvalidValueException;
use App\Model\Search;
use App\Model\Search\BaseSearchFilter;
use App\Model\Search\OwnerFilter;
use App\Model\Search\TagsFilter;
use App\Model\Search\IsUnapprovedFilter;
use Cake\Http\Exception\BadRequestException;

class SearchApi
{
    public $search;

    public function __construct($search = null) {
        $this->search = $search ?? new Search();
    }

    private function parseParamValueBool($value, BaseSearchFilter $filter) {
        if ($value == 'no') {
            $filter->not();
        } elseif ($value != 'yes') {
            throw new InvalidValueException("must be 'yes' or 'no'");
        }
    }

    private function parseParamValueList($value, BaseSearchFilter $filter) {
        if (isset($value[0]) && $value[0] == '!') {
            $value = substr($value, 1);
            $filter->not();
        }
        $value = explode(',', $value);
        $value = array_map(
            function ($value) {
                if (ctype_digit($value)) {
                    return (int)$value;
                } else {
                    return $value;
                }
            },
            $value
        );
        $filter->anyOf($value);
    }

    private function parseParamValue($filtervalue, BaseSearchFilter $filter) {
        $filtervalue = (array)$filtervalue;
        foreach ($filtervalue as $key => $value) {
            if ($filter instanceof Search\BoolFilter) {
                $this->parseParamValueBool($value, $filter);
            } else {
                $this->parseParamValueList($value, $filter);
            }
            if ($key !== array_key_last($filtervalue)) {
                $filter->and();
            }
        }
    }

    private function parseParamName(string $param) {
        $ns = explode(':', $param, 3);
        $key = null;
        $group = null;
        $collection = $this->search;
        if (count($ns) == 1) {
            $key = $ns[0];
            $filterMap = [
                'after'         => Search\CursorFilter::class,
                'has_audio'     => Search\HasAudioFilter::class,
                'is_native'     => Search\IsNativeFilter::class,
                'is_orphan'     => Search\IsOrphanFilter::class,
                'is_unapproved' => Search\IsUnapprovedFilter::class,
                'lang'          => Search\LangFilter::class,
                'list'          => Search\ListFilter::class,
                'origin'        => Search\OriginFilter::class,
                'owner'         => Search\OwnerFilter::class,
                'tag'           => Search\TagFilter::class,
                'word_count'    => Search\WordCountFilter::class,
            ];
        } elseif ($ns[0] == 'trans' || $ns[0] == '!trans') {
            if (count($ns) == 2) {
                $key = $ns[1];
                $group = '';
            } elseif (count($ns) == 3) {
                $group = $ns[1];
                $key = $ns[2];
                $check = $group;
                if (isset($group[0]) && $group[0] == '!') {
                    $group[0] = '_';
                    $check = substr($check, 1);
                }
                if (strlen($check) == 0) {
                    throw new BadRequestException("Invalid parameter '$param': group name cannot be empty");
                }
                if (!ctype_digit($check)) {
                    throw new BadRequestException("Invalid parameter '$param': '${ns[1]}' is not a valid group name: it must consist of non-empty digits with optional exclamation mark prefix");
                }
            }
            if ($ns[0] == 'trans') {
                $collection = $this->search->getTranslationFilters($group);
            } else { // $ns[0] == '!trans'
                // 'e' is just some id that can't be overlapped by API consumers
                $egroup = $this->search->getTranslationFilters('e')->setExclude(true);
                $collection = $egroup->getTranslationFilters($group);
            }
            if (isset($group[0]) && $group[0] == '_') {
                $collection->setExclude(true);
            }
            $filterMap = [
                'count'         => Search\TranslationCountFilter::class,
                'has_audio'     => Search\TranslationHasAudioFilter::class,
                'is_direct'     => Search\TranslationIsDirectFilter::class,
                'is_orphan'     => Search\TranslationIsOrphanFilter::class,
                'is_unapproved' => Search\TranslationIsUnapprovedFilter::class,
                'lang'          => Search\TranslationLangFilter::class,
                'owner'         => Search\TranslationOwnerFilter::class,
            ];
        }
        if (isset($filterMap[$key])) {
            return [new $filterMap[$key], $collection];
        } else {
            $error = "Unknown parameter '$param'";
            if (!is_null($key) && $param != $key) {
                $error .= ": unknown suffix '$key'";
            }
            throw new BadRequestException($error);
        }
    }

    public function consumeSort(&$params) {
        if (isset($params['sort'])) {
            $sort = $params['sort'];
        } else {
            throw new BadRequestException('Required parameter "sort" missing');
        }

        if (is_array($sort)) {
            throw new BadRequestException("Invalid usage of parameter 'sort': cannot be provided multiple times");
        }
        if (isset($sort[0]) && $sort[0] == '-') {
            $this->search->reverseSort(true);
            $sort = substr($sort, 1);
        }
        if (!$this->search->sort($sort)) {
            throw new BadRequestException('Invalid value for parameter "sort"');
        }
        unset($params['sort']);
    }

    public function consumeValue($key, &$params, $default = null) {
        if (isset($params[$key])) {
            if (is_array($params[$key])) {
                throw new BadRequestException("Invalid usage of parameter '$key': cannot be provided multiple times");
            } else {
                $value = $params[$key];
            }
        } else {
            $value = $default;
        }

        unset($params[$key]);

        return $value;
    }

    public function consumeShowTrans(&$params) {
        $showtrans = $this->consumeValue('showtrans', $params, []);
        if ($showtrans === '') {
            $showtrans = ['none'];
        } elseif (is_string($showtrans)) {
            $showtrans = explode(',', $showtrans);
            try {
                $showtrans = array_map('\App\Model\Search::validateLanguage', $showtrans);
            } catch (InvalidValueException $e) {
                throw new BadRequestException("Invalid value for parameter 'showtrans': ".$e->getMessage());
            }
        }
        return $showtrans;
    }

    public function consumeInt($key, &$params, $default = null) {
        $value = $this->consumeValue($key, $params, $default);
        if ($value !== $default) {
            if (ctype_digit($value)) {
                $value = (int)$value;
            } else {
                throw new BadRequestException("Invalid value for parameter '$key': must be a positive integer");
            }
        }
        return $value;
    }

    public function setFilters(array $filters) {
        if (!isset($filters['lang'])) {
            throw new BadRequestException('Required parameter "lang" missing');
        }

        if (isset($filters['q'])) {
            $q = $filters['q'];
            if (is_array($q)) {
                throw new BadRequestException("Invalid usage of parameter 'q': cannot be provided multiple times");
            }
            $this->search->filterByQuery($q);
        }
        unset($filters['q']);

        try {
            foreach ($filters as $key => $value) {
                list($filter, $collection) = $this->parseParamName($key);
                $this->parseParamValue($value, $filter, $key);
                $collection->setFilter($filter);
                if (method_exists($filter, 'setSearch')) {
                    $filter->setSearch($this->search);
                }
            }
            $this->search->compile(); // trigger validation
        } catch (InvalidValueException $e) {
            throw new BadRequestException("Invalid value for parameter '$key': ".$e->getMessage());
        } catch (\App\Model\Exception\InvalidAndOperatorException $e) {
            throw new BadRequestException("Invalid usage of parameter '$key': cannot be provided multiple times");
        } catch (\App\Model\Exception\InvalidNotOperatorException $e) {
            throw new BadRequestException("Invalid usage of parameter '$key': value cannot be negated with '!'");
        } catch (\App\Model\Exception\InvalidFilterUsageException $e) {
            throw new BadRequestException("Invalid usage of parameter '$key': ".$e->getMessage());
        }
    }
}
