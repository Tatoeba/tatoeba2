<?php

namespace App\Model;

use App\Model\Exception\InvalidValueException;
use App\Model\Search;
use App\Model\Search\BaseSearchFilter;
use App\Model\Search\LicenseFilter;
use App\Model\Search\OwnerFilter;
use App\Model\Search\TagsFilter;
use App\Model\Search\IsUnapprovedFilter;
use Cake\Http\Exception\BadRequestException;

class SearchApi
{
    public $search;
    public $showtransFilters;
    public $include;

    private $limit;
    private $defaultLimit = 10;
    private $hardLimit;
    private $showtrans;

    public function __construct($search = null) {
        $this->search = $search ?? new Search();
        $this->search->setComputeCursor(true);
    }

    public function setLimits(int $defaultLimit, int $hardLimit) {
        $this->defaultLimit = $defaultLimit;
        $this->hardLimit = $hardLimit;
    }

    public function getShowtrans() {
        if ($this->showtransFilters) {
            return new Search\ShowtransLimiter($this->showtransFilters->getFilters());
        } elseif ($this->showtrans == 'matching' || is_null($this->showtrans)) {
            $showtrans = new Search\ShowtransLimiter($this->search->getFilters());
            return $showtrans->getFilters() ? $showtrans : null;
        } elseif ($this->showtrans == 'all') {
            return new Search\ShowtransLimiter([]);
        } elseif ($this->showtrans == 'none') {
            return null;
        }
    }

    private function parseParamValueBool($value, BaseSearchFilter $filter) {
        if ($value == 'no') {
            $filter->not();
        } elseif ($value != 'yes') {
            throw new InvalidValueException($filter, "must be 'yes' or 'no'");
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

    private function parseShowtransParamName(string $param) {
        $ns = explode(':', $param, 3);
        $key = null;
        $group = null;
        if (count($ns) > 1 && $ns[0] == 'showtrans') {
            if (count($ns) == 2) {
                $key = $ns[1];
                $group = '';
            } elseif (count($ns) == 3) {
                $group = $ns[1];
                $key = $ns[2];
                if (strlen($group) == 0) {
                    throw new BadRequestException("Invalid parameter '$param': group name cannot be empty");
                }
                if (!ctype_digit($group)) {
                    $error = "Invalid parameter '$param': '$group' is not a valid group name: it must consist of non-empty digits";
                    throw new BadRequestException($error);
                }
            }
            if ($this->showtrans) {
                throw new BadRequestException("Invalid usage of parameter '{$param}' or 'showtrans': these two cannot be used together");
            }
            $this->showtransFilters = $this->showtransFilters ?: new Search\TranslationFilterGroup();
            $filterMap = [
                'lang'          => Search\TranslationLangFilter::class,
                'is_direct'     => Search\TranslationIsDirectFilter::class,
                'is_unapproved' => Search\TranslationIsUnapprovedFilter::class,
                'is_orphan'     => Search\TranslationIsOrphanFilter::class,
                'owner'         => Search\TranslationOwnerFilter::class,
                'is_native'     => Search\TranslationIsNativeFilter::class,
                'has_audio'     => Search\TranslationHasAudioFilter::class,
            ];
        }
        if (isset($filterMap[$key])) {
            $filter = new $filterMap[$key]($param);
            $collection = $this->showtransFilters->getTranslationFilters($group);
            $collection->setFilter($filter);
            return $filter;
        } elseif (!is_null($key) && $param != $key) {
            // Provide a special error message for showtrans:unknown_suffix=foo case
            throw new BadRequestException("Unknown parameter '$param': unknown suffix '$key'");
        }
    }

    private function parseFilterParamName(string $param) {
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
                'license'       => Search\LicenseFilter::class,
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
                'is_native'     => Search\TranslationIsNativeFilter::class,
                'is_orphan'     => Search\TranslationIsOrphanFilter::class,
                'is_unapproved' => Search\TranslationIsUnapprovedFilter::class,
                'lang'          => Search\TranslationLangFilter::class,
                'owner'         => Search\TranslationOwnerFilter::class,
            ];
        }
        if (isset($filterMap[$key])) {
            $filter = new $filterMap[$key]($param);
            $collection->setFilter($filter);
            return $filter;
        } elseif (!is_null($key) && $param != $key) {
            // Provide a special error message for trans:unknown_suffix=foo case
            throw new BadRequestException("Unknown parameter '$param': unknown suffix '$key'");
        }
    }

    private function parseSortValue(string $sort, array &$newParams) {
        // Parse '-' prefix
        if (isset($sort[0]) && $sort[0] == '-') {
            $this->search->reverseSort(true);
            $sort = substr($sort, 1);
        }

        // Parse or add random seed value as sort=random:<seed>
        $parts = explode(':', $sort);
        if (count($parts) == 2 && $parts[0] == 'random' && ctype_digit($parts[1])) {
            $sort = $parts[0];
            $this->search->setRandSeed((int)$parts[1]);
        } elseif ($sort == 'random') {
            $newSeed = $this->search->initRandSeed();
            $newParams = ['sort' => 'random:'.$newSeed];
        }

        return $sort;
    }

    public function consumeSort(&$params): array {
        if (isset($params['sort'])) {
            $sort = $params['sort'];
        } else {
            throw new BadRequestException('Required parameter "sort" missing');
        }

        $newParams = [];
        if (is_array($sort)) {
            throw new BadRequestException("Invalid usage of parameter 'sort': cannot be provided multiple times");
        }
        $sort = $this->parseSortValue($sort, $newParams);
        if (!$this->search->sort($sort)) {
            $allsorts = array_merge(
                Search::AVAILABLE_SORTS,
                array_map(fn($s) => "-$s", Search::AVAILABLE_SORTS)
            );
            throw new BadRequestException("Invalid value for parameter 'sort': must be one of: ".join(', ', $allsorts));
        }
        unset($params['sort']);

        return $newParams;
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

    public function consumeShowtrans(&$params, array $available) {
        $showtrans = $this->consumeValue('showtrans', $params);
        if (!is_null($showtrans) && !in_array($showtrans, $available)) {
            throw new BadRequestException("Invalid value for parameter 'showtrans': must be one of: ".join(', ', $available));
        }
        return $showtrans;
    }

    public function consumeInclude(&$params) {
        $includes = $this->consumeValue('include', $params, []);
        if (!is_array($includes)) {
            $includes = explode(',', $includes);
            $available = ['audios', 'transcriptions'];
            foreach ($includes as $include) {
                if (!in_array($include, $available)) {
                    throw new BadRequestException("Invalid value for parameter 'include': must be one of: ".join(', ', $available));
                }
            }
        }
        return array_fill_keys($includes, true);
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

    public function setDefaultFilters() {
        $this->search->setFilter(
            (new LicenseFilter())
            ->not()
            ->anyOf([LicenseFilter::LICENSING_ISSUE])
        );
    }

    private function catchParamsExceptions(callable $code) {
        try {
            return $code();
        } catch (InvalidValueException $e) {
            throw new BadRequestException("Invalid value for parameter '{$e->getThrower()->getName()}': ".$e->getMessage());
        } catch (\App\Model\Exception\InvalidAndOperatorException $e) {
            throw new BadRequestException("Invalid usage of parameter '{$e->getThrower()->getName()}': cannot be provided multiple times");
        } catch (\App\Model\Exception\InvalidNotOperatorException $e) {
            throw new BadRequestException("Invalid usage of parameter '{$e->getThrower()->getName()}': value cannot be negated with '!'");
        } catch (\App\Model\Exception\InvalidFilterUsageException $e) {
            throw new BadRequestException("Invalid usage of parameter '{$e->getThrower()->getName()}': ".$e->getMessage());
        }
    }

    public function consumeFilters(array &$params) {
        $this->showtrans = $this->consumeShowtrans($params, ['all', 'none', 'matching']);
        return $this->catchParamsExceptions(function () use (&$params) {
            $this->_consumeShowtransFilters($params);
            $this->_consumeFilters($params);
        });
    }

    private function _consumeFilters(array &$params) {
        if (!isset($params['lang'])) {
            throw new BadRequestException('Required parameter "lang" missing');
        }

        if (isset($params['q'])) {
            $q = $params['q'];
            if (is_array($q)) {
                throw new BadRequestException("Invalid usage of parameter 'q': cannot be provided multiple times");
            }
            $this->search->filterByQuery($q);
        }
        unset($params['q']);

        $unusedParams = [];
        foreach ($params as $key => $value) {
            $filter = $this->parseFilterParamName($key);
            if ($filter) {
                $this->parseParamValue($value, $filter);
                if (method_exists($filter, 'setSearch')) {
                    $filter->setSearch($this->search);
                }
            } else {
                $unusedParams[$key] = $value;
            }
        }
        $this->search->compile(); // trigger validation
        $params = $unusedParams;
    }

    public function consumeShowtransFilters(array &$params) {
        $this->showtrans = $this->consumeShowtrans($params, ['all', 'none']);
        $this->catchParamsExceptions(function () use (&$params) {
            $this->_consumeShowtransFilters($params);
        });
    }

    private function _consumeShowtransFilters(array &$params) {
        $unusedParams = [];
        foreach ($params as $key => $value) {
            $filter = $this->parseShowtransParamName($key);
            if ($filter) {
                $this->parseParamValue($value, $filter);
            } else {
                $unusedParams[$key] = $value;
            }
        }
        if ($this->showtransFilters) {
            $this->showtransFilters->compile(); // trigger validation
        }
        $params = $unusedParams;
    }

    public function failIfParams(array $params) {
        foreach ($params as $param => $value) {
            throw new BadRequestException("Unknown parameter '$param'");
        }
    }

    public function readParamsForSearchSentences(array $params): array {
        $this->include = $this->consumeInclude($params);
        $this->limit = $this->consumeInt('limit', $params);
        $newParams = $this->consumeSort($params);
        $this->setDefaultFilters();
        $this->consumeFilters($params);
        $this->failIfParams($params);
        return $newParams;
    }

    public function readParamsForGetSentence(array $params) {
        $this->include = $this->consumeInclude($params);
        $this->consumeShowtransFilters($params);
        $this->failIfParams($params);
    }

    private function getLimit() {
        $limit = $this->limit ?: $this->defaultLimit;
        return $limit > $this->hardLimit ? $this->hardLimit : $limit;
    }

    public function asSphinx() {
        $sphinx = $this->search->asSphinx();
        $sphinx['limit'] = $this->getLimit();
        return $sphinx;
    }
}
