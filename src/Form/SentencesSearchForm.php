<?php

namespace App\Form;

use App\Model\CurrentUser;
use App\Model\Search;
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
        'original' => '',
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

    protected function setBoolFilter(string $method, string $value) {
        $value = $this->parseBoolNull($value);
        $value = $this->search->$method($value);
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
        return $this->search->filterByLanguage($from) ?? '';
    }

    protected function setDataUser(string $user) {
        if (!empty($user)) {
            $this->loadModel('Users');
            $result = $this->Users->findByUsername($user, ['fields' => ['id']])->first();
            if ($result) {
                $this->search->filterByOwnerId($result->id);
                $this->ownerId = $result->id;
            } else {
                $this->ignored[] = format(
                    /* @translators: This string will be preceded by “Warning:
                       the following criteria have been ignored:” */
                    __("“sentence owner”, because “{username}” is not a ".
                       "valid username", true),
                    array('username' => h($user))
                );
                $user = '';
            }
        }
        return $user;
    }

    protected function setDataOriginal(string $original) {
        $original = $original === 'yes';
        $this->search->filterByOriginKnown($original ? true : null);
        $this->search->filterByAddedAsTranslation($original ? false : null);
        return $original ? 'yes' : '';
    }

    protected function setDataTransFilter(string $trans_filter) {
        /* If an invalid value was provided, fallback to 'limit' */
        if (!in_array($trans_filter, ['exclude', 'limit'])) {
            $trans_filter = 'limit';
        }

        /* Only set translation filter to 'limit' if at least
           one translation filter is set */
        if ($trans_filter == 'limit' && $this->search->getTranslationFilters()
            || $trans_filter == 'exclude') {
            $trans_filter = $this->search->filterByTranslation($trans_filter);
        }

        return $trans_filter;
    }

    protected function setDataTransLink(string $link) {
        return $this->search->filterByTranslationLink($link) ?? '';
    }

    protected function setDataTransUser(string $trans_user) {
        if (strlen($trans_user)) {
            $this->loadModel('Users');
            $result = $this->Users->findByUsername($trans_user, ['fields' => ['id']])->first();
            if ($result) {
                $this->search->filterByTranslationOwnerId($result->id);
            } else {
                $this->ignored[] = format(
                    /* @translators: This string will be preceded by
                       “Warning: the following criteria have been ignored:” */
                    __("“translation owner”, because “{username}” is not ".
                       "a valid username", true),
                    ['username' => h($trans_user)]
                );
                $trans_user = '';
            }
        }
        return $trans_user;
    }

    protected function setDataTransTo(string $lang) {
        return $this->search->filterByTranslationLanguage($lang) ?? '';
    }

    protected function setDataTransHasAudio(string $trans_has_audio) {
        return $this->setBoolFilter('filterByTranslationAudio', $trans_has_audio);
    }

    protected function setDataTransUnapproved(string $trans_unapproved) {
        return $this->setBoolFilter('filterByTranslationCorrectness', $trans_unapproved);
    }

    protected function setDataTransOrphan(string $trans_orphan) {
        return $this->setBoolFilter('filterByTranslationOrphanship', $trans_orphan);
    }

    protected function setDataUnapproved(string $unapproved) {
        return $this->setBoolFilter('filterByCorrectness', $unapproved);
    }

    protected function setDataOrphans(string $orphans) {
        return $this->setBoolFilter('filterByOrphanship', $orphans);
    }

    protected function setDataHasAudio(string $has_audio) {
        return $this->setBoolFilter('filterByAudio', $has_audio);
    }

    protected function setDataTags(string $tags) {
        if (!empty($tags)) {
            $tagsArray = explode(',', $tags);
            $tagsArray = array_map('trim', $tagsArray);
            $appliedTags = $this->search->filterByTags($tagsArray);
            $tags = implode(',', $appliedTags);

            $ignoredTags = array_diff($tagsArray, $appliedTags);
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

    private function _setDataWordCountFilter(string $op, string $value) {
        $value = is_numeric($value) ? (int)$value : null;
        return $this->search->filterByWordCount($op, $value) ?? '';
    }

    protected function setDataWordCountMin(string $min) {
        return $this->_setDataWordCountFilter('ge', $min);
    }

    protected function setDataWordCountMax(string $max) {
        return $this->_setDataWordCountFilter('le', $max);
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
