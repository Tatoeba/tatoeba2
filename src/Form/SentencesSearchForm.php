<?php

namespace App\Form;

use App\Model\CurrentUser;
use App\Model\Search;
use App\Lib\LanguagesLib;
use Cake\Event\EventManager;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Utility\Inflector;

class SentencesSearchForm extends Form
{
    use \Cake\Datasource\ModelAwareTrait;

    private $search;

    private $ignored = [];

    private $defaultCriteria = [
        'query' => '',
        'from' => 'und',
        'to' => 'und',
        'tags' => '',
        'list' => '',
        'user' => '',
        'orphans' => 'no',
        'unapproved' => 'no',
        'native' => '',
        'has_audio' => '',
        'trans_to' => 'und',
        'trans_link' => '',
        'trans_user' => '',
        'trans_orphan' => '',
        'trans_unapproved' => '',
        'trans_has_audio' => '',
        'trans_filter' => 'limit',
        'sort' => 'relevance',
        'sort_reverse' => '',
    ];

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
        return $this->search->filterByLanguage($from) ?? 'und';
    }

    protected function setDataUser(string $user) {
        if (!empty($user)) {
            $this->loadModel('Users');
            $result = $this->Users->findByUsername($user, ['fields' => ['id']])->first();
            if ($result) {
                $this->search->filterByOwnerId($result->id);
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
        return $this->search->filterByTranslationLanguage($lang) ?? 'und';
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

    protected function setDataSort(string $sort) {
        $sort = $this->search->sort($sort);

        /* If an invalid sort was provided,
           fallback to default sort instead of no sort */
        return $sort ?? $this->search->sort($this->defaultCriteria['sort']);
    }

    protected function setDataTo(string $to) {
        if ($to != 'none') {
            $to = LanguagesLib::languageExists($to) ? $to : 'und';
        }
        return $to;
    }

    protected function setDataSortReverse(string $sort_reverse) {
        $sort_reverse = $this->search->reverseSort($sort_reverse === 'yes');
        return $sort_reverse ? 'yes' : '';
    }

    public function setData(array $data)
    {
        /* Convert simple search to advanced search parameters */
        if (isset($data['to']) && !isset($data['trans_to'])) {
            $data['trans_to'] = $data['to'];
        }

        /* Apply default criteria */
        $data = array_merge($this->defaultCriteria, $data);

        /* Make sure trans_filter is applied at the end
           because it depends on other trans_* filters */
        uksort($data, function ($k) {
            return $k == 'trans_filter';
        });

        /* Apply given criteria */
        foreach ($data as $key => $value) {
            $keyCamel = Inflector::camelize($key);
            $setter = "setData$keyCamel";
            $this->_data[$key] = $this->$setter($value);
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
