<?php

namespace App\Form;

use App\Model\CurrentUser;
use App\Model\Exception\InvalidValueException;
use App\Model\Search;
use App\Model\Search\HasAudioFilter;
use App\Model\Search\IsOrphanFilter;
use App\Model\Search\IsUnapprovedFilter;
use App\Model\Search\OwnerFilter;
use App\Model\Search\TagsFilter;
use App\Model\Search\TranslationCountFilter;
use App\Model\Search\TranslationHasAudioFilter;
use App\Model\Search\TranslationIsDirectFilter;
use App\Model\Search\TranslationIsOrphanFilter;
use App\Model\Search\TranslationIsUnapprovedFilter;
use App\Model\Search\TranslationLangFilter;
use App\Model\Search\TranslationOwnerFilter;
use App\Model\Search\WordCountFilter;
use App\Lib\LanguagesLib;
use Cake\Event\EventManager;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

class SentencesSearchForm extends Form
{
    use \Cake\Datasource\ModelAwareTrait;

    private $search;

    private $ignored = [];

    private $ownerId;

    private $defaultCriteria = [
        'query' => '',
        'from' => '',
        'to' => '',
        'tags' => '',
        'list' => '',
        'user' => '',
        'orphans' => 'no',
        'unapproved' => 'no',
        'native' => '',
        'has_audio' => '',
        'word_count_min' => '1',
        'word_count_max' => '',
        'trans_to' => '',
        'trans_link' => '',
        'trans_user' => '',
        'trans_orphan' => '',
        'trans_unapproved' => '',
        'trans_has_audio' => '',
        'trans_filter' => 'limit',
        'sort' => 'relevance',
        'sort_reverse' => '',
        'rand_seed' => '',
    ];

    private $paramsOrder = [];

    public function __construct(EventManager $eventManager = null) {
        parent::__construct($eventManager);
        $this->search = new Search();
    }

    public function setSearch(Search $search) {
        $this->search = $search;
    }

    public function getIgnoredFields() {
        return $this->ignored;
    }

    protected function parseYesNoEmpty($value) {
        if (is_null($value)) {
            return '';
        } else {
            return $value ? 'yes' : 'no';
        }
    }

    protected function parseBoolNull($value) {
        return $value == 'yes' ? true : ($value == 'no' ? false : null);
    }

    protected function setBoolFilter(string $class, $value, object $collection) {
        $value = $this->parseBoolNull($value);
        if (is_null($value)) {
            $collection->unsetFilter($class);
        } else {
            $collection->setFilter(new $class($value));
        }
        return $this->parseYesNoEmpty($value);
    }

    protected function setDataQuery(string $query) {
        $query = str_replace(
            ['　', "\u{a0}"],
            ' ',
            $query
        );
        return $this->search->filterByQuery($query);
    }

    protected function setDataFrom(string $from) {
        try {
            $this->search->filterByLanguage([$from]);
            return $from;
        } catch (InvalidValueException $e) {
            return '';
        }
    }

    protected function setDataUser(string $user) {
        if (!empty($user)) {
            $filter = new OwnerFilter();
            $filter->setInvalidValueHandler(function($value) use (&$user) {
                $this->ignored[] = format(
                    /* @translators: This string will be preceded by “Warning:
                       the following criteria have been ignored:” */
                    __("“sentence owner”, because “{username}” is not a ".
                       "valid username", true),
                    array('username' => h($value))
                );
                $user = '';
            });
            $filter->anyOf([$user]);
            $this->search->setFilter($filter);
            $compiled = $filter->compile();
            if (count($compiled) > 0) {
                list(list(, list($this->ownerId))) = $compiled;
            }
        }
        return $user;
    }

    protected function setDataTransFilter(string $trans_filter) {
        /* If an invalid value was provided, fallback to 'limit' */
        if (!in_array($trans_filter, ['exclude', 'limit'])) {
            $trans_filter = 'limit';
        }

        /* 'limit' is the default form value and does not actually do any filtering
           on the existence of translations; only 'exclude' does. Also we need to set
           at least one translation filter otherwise setExclude() won't have any effect.
           That's why we only set the count filter if a) we are in exclude mode
           and b) no translation filter is set. */
        $this->search->getTranslationFilters()->setExclude($trans_filter == 'exclude');
        if ($trans_filter == 'exclude' && count($this->search->getTranslationFilters()->compile()) == 0) {
            $this->search->setTranslationFilter((new TranslationCountFilter())->not()->anyOf([0]));
        }

        return $trans_filter;
    }

    protected function setDataTransLink(string $link) {
        if (!in_array($link, ['direct', 'indirect'])) {
            return '';
        }
        $filter = new TranslationIsDirectFilter($link == 'direct');
        $this->search->setTranslationFilter($filter);
        return $link;
    }

    protected function setDataTransUser(string $trans_user) {
        if (strlen($trans_user)) {
            $filter = new TranslationOwnerFilter();
            $filter->setInvalidValueHandler(function($value) use (&$trans_user) {
                $this->ignored[] = format(
                    /* @translators: This string will be preceded by
                       “Warning: the following criteria have been ignored:” */
                    __("“translation owner”, because “{username}” is not ".
                       "a valid username", true),
                    ['username' => h($trans_user)]
                );
                $trans_user = '';
            });
            $filter->anyOf([$trans_user]);
            $this->search->setTranslationFilter($filter);
            $filter->compile(); // trigger validation so that we can return updated $trans_user
        }
        return $trans_user;
    }

    protected function setDataTransTo(string $lang) {
        try {
            $filter = new TranslationLangFilter();
            $filter->anyOf([$lang]);
            $this->search->setTranslationFilter($filter);
            return $lang;
        } catch (InvalidValueException $e) {
            return '';
        }
    }

    protected function setDataTransHasAudio(string $trans_has_audio) {
        return $this->setBoolFilter(
            TranslationHasAudioFilter::class,
            $trans_has_audio,
            $this->search->getTranslationFilters()
        );
    }

    protected function setDataTransUnapproved(string $trans_unapproved) {
        return $this->setBoolFilter(
            TranslationIsUnapprovedFilter::class,
            $trans_unapproved,
            $this->search->getTranslationFilters()
        );
    }

    protected function setDataTransOrphan(string $trans_orphan) {
        return $this->setBoolFilter(
            TranslationIsOrphanFilter::class,
            $trans_orphan,
            $this->search->getTranslationFilters()
        );
    }

    protected function setDataUnapproved(string $unapproved) {
        return $this->setBoolFilter(IsUnapprovedFilter::class, $unapproved, $this->search);
    }

    protected function setDataOrphans(string $orphans) {
        return $this->setBoolFilter(IsOrphanFilter::class, $orphans, $this->search);
    }

    protected function setDataHasAudio(string $has_audio) {
        return $this->setBoolFilter(HasAudioFilter::class, $has_audio, $this->search);
    }

    protected function setDataTags(string $tags) {
        if (!empty($tags)) {
            $ignoredTags = [];
            $filter = new TagsFilter();
            $filter->setInvalidValueHandler(function($tagName) use (&$ignoredTags) {
                $ignoredTags[] = $tagName;
            });

            $tagsArray = explode(',', $tags);
            $tagsArray = array_map('trim', $tagsArray);
            foreach ($tagsArray as $tag) {
                $filter->anyOf([$tag])->and();
            }
            $this->search->setFilter($filter);
            $filter->compile(); // trigger validation: fills $ignoredTags

            $appliedTagsNames = array_diff($tagsArray, $ignoredTags);
            $tags = implode(',', $appliedTagsNames);
            foreach ($ignoredTags as $tagName) {
                $this->ignored[] = format(
                    /* @translators: This string will be preceded by
                       “Warning: the following criteria have been
                       ignored:” */
                    __("“tagged as {tagName}”, because it's an invalid ".
                       "tag name", true),
                    array('tagName' => h($tagName))
                );
            }
        }
        return $tags;
    }

    protected function setDataList(string $list) {
        $searcher = CurrentUser::get('id');
        $list = is_numeric($list) ? (int)$list : null;
        if (!$this->search->filterByListId($list, $searcher)) {
            $this->ignored[] = format(
                /* @translators: This string will be preceded by
                   “Warning: the following criteria have been
                   ignored:” */
                __("“belongs to list number {listId}”, because list ".
                   "{listId} is private or does not exist", true),
                array('listId' => $list)
            );
            $list = '';
        }
        return $list;
    }

    protected function setDataNative(string $native) {
        $native = $native === 'yes' ? true : null;
        $native = $this->search->filterByNativeSpeaker($native);
        return $native ? 'yes' : '';
    }

    private function _setDataWordCountFilter(string $value, $before, $after) {
        $value = is_numeric($value) ? (int)$value : '';
        $filter = $this->search->getFilter(WordCountFilter::class) ?? new WordCountFilter();
        try {
            $range = $before . $value . $after;
            $filter->anyOf([$range])->and();
            $filter->compile(); // trigger validation
            $this->search->setFilter($filter);
            return $value;
        } catch (InvalidValueException $e) {
            return '';
        }
    }

    protected function setDataWordCountMin(string $min) {
        return $this->_setDataWordCountFilter($min, '', '-');
    }

    protected function setDataWordCountMax(string $max) {
        return $this->_setDataWordCountFilter($max, '-', '');
    }

    protected function setDataSort(string $sort) {
        $sort = $this->search->sort($sort);

        /* If an invalid sort was provided,
           fallback to default sort instead of no sort */
        return $sort ?? $this->search->sort($this->defaultCriteria['sort']);
    }

    protected function setDataTo(string $to) {
        if ($to != 'none') {
            $to = LanguagesLib::languageExists($to) ? $to : '';
        }
        return $to;
    }

    protected function setDataSortReverse(string $sort_reverse) {
        $sort_reverse = $this->search->reverseSort($sort_reverse === 'yes');
        return $sort_reverse ? 'yes' : '';
    }

    private function _newSeed() {
        /* Only use 24 bits of randomness */
        $pseudoRand = mt_rand(0, (2<<23)-1);
        return $this->_encodeSeed($pseudoRand);
    }

    private function _encodeSeed($seed) {
        $result = '';
        if (is_int($seed)) {
            $seed = base64_encode(pack('V', $seed));
            $seed = substr($seed, 0, 4);
            $result = str_replace(['+','/'], ['-','_'], $seed);
        }
        return $result;
    }

    private function _decodeSeed(string $seed) {
        $result = null;
        if (strlen($seed) >= 4) {
            $seed = str_replace(['-','_'], ['+','/'], $seed);
            $seed = substr($seed, 0, 4).'AA';
            $result = @unpack('V', base64_decode($seed))[1];
        }
        return $result;
    }

    protected function setDataRandSeed(string $seed_b64) {
        $seed_int = $this->_decodeSeed($seed_b64);
        $seed_int = $this->search->setRandSeed($seed_int);
        $seed_b64 = $this->_encodeSeed($seed_int);
        return $seed_b64;
    }

    public function setData(array $data)
    {
        /* Remember params order */
        $this->paramsOrder = array_keys($data);

        /* Convert simple search to advanced search parameters */
        if (isset($data['to']) && !isset($data['trans_to'])) {
            $data['trans_to'] = $data['to'];
        }

        /* Remove unknown parameters */
        $data = array_intersect_key($data, $this->defaultCriteria);

        /* Apply default criteria */
        $data = array_merge($this->defaultCriteria, $data);

        /* Make sure trans_filter is applied at the end
           because it depends on other trans_* filters */
        uksort($data, function ($k) {
            return $k == 'trans_filter';
        });

        /* Apply other criteria */
        foreach ($data as $key => $value) {
            $keyCamel = Inflector::camelize($key);
            $setter = "setData$keyCamel";
            $this->_data[$key] = $this->$setter($value);

            $strippedParam = ($this->_data[$key] === null
                              || $this->_data[$key] === false
                              || $this->_data[$key] === '');
            if ($strippedParam && !empty($this->defaultCriteria[$key])) {
                /* Using Router::url() to reconstruct a URL for the given data
                 * strips out empty parameters, which would lead to a non-empty
                 * default being applied instead of the empty non-default value.
                 * So represent them by "any" instead. */
                $this->_data[$key] = 'any';
            }
        }
    }

    private function paramIndex($param) {
        $index = array_search($param, $this->paramsOrder);
        if ($index === FALSE) {
            $index = PHP_INT_MAX;
        }
        return $index;
    }

    public function getData($field = null) {
        $data = parent::getData($field);
        if (is_null($field)) {
            uksort($data, function($a, $b) {
                return $this->paramIndex($a) <=> $this->paramIndex($b);
            });
        }
        return $data;
    }

    public function generateRandomSeedIfNeeded() {
        if ($this->_data['sort'] == 'random' && empty($this->_data['rand_seed'])) {
            $this->_data['rand_seed'] = $this->_newSeed();
            return true;
        } else {
            return false;
        }
    }

    public function checkUnwantedCombinations() {
        if ($this->_data['user'] && $this->_data['orphans'] === 'yes') {
            $this->ignored[] = format(
                /* @translators: This string will be preceded by
                   “Warning: the following criteria have been
                   ignored:” */
                __("“sentence is orphan”, because “sentence ".
                   "owner” is set to a username", true)
            );
            $this->_data['orphans'] = $this->setDataOrphans('');
        }

        if ($this->_data['trans_user'] && $this->_data['trans_orphan'] === 'yes') {
            $this->ignored[] = format(
                /* @translators: This string will be preceded by
                   “Warning: the following criteria have been
                   ignored:” */
                __("“translation is orphan”, because “translation ".
                   "owner” is set to a username", true)
            );
            $this->_data['trans_orphan'] = $this->setDataTransOrphan('');
        }

        if ($this->_data['native'] === 'yes' && $this->_data['from'] === '') {
            $this->ignored[] = __(
                /* @translators: This string will be preceded by “Warning: the
                   following criteria have been ignored:” */
                "“owned by a self-identified native”, because “sentence ".
                "language” is set to “any”",
                true
            );
            $this->_data['native'] = $this->setDataNative('');
        }

        if ($this->_data['native'] === 'yes' && $this->ownerId) {
            $this->loadModel('UsersLanguages');
            $natives = $this->UsersLanguages->find()
                ->where([
                    'language_code' => $this->_data['from'],
                    'level' => 5,
                ])
                ->select(['of_user_id'])
                ->toList();
            $natives = Hash::extract($natives, '{n}.of_user_id');
            if (!in_array($this->ownerId, $natives)) {
                $this->ignored[] = format(
                    /* @translators: This string will be preceded by
                       “Warning: the following criteria have been
                       ignored:” */
                    __("“owned by a self-identified native”, because the ".
                       "criterion “owned by: {username}” is set whereas ".
                       "he or she is not a self-identified native in the ".
                       "language you're searching into",
                       true),
                    array('username' => $this->_data['user'])
                );
                $this->_data['native'] = $this->setDataNative('');
            }
        }
    }

    public function getSearchableLists($byUserId) {
        $listId = $this->_data['list'] ?? null;
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
                    'visibility IN' => ['public', 'listed']
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

    public function asSphinx() {
        return $this->search->asSphinx();
    }

    public function isUsingDefaultCriteria() {
        return $this->getData() == $this->defaultCriteria;
    }
}
