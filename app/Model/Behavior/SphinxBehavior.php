<?php
/**
 * Behavior for simple usage of Sphinx search engine
 * http://www.sphinxsearch.com
 *
 * @copyright 2008, Vilen Tambovtsev
 * @author  Vilen Tambovtsev
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */


class SphinxBehavior extends ModelBehavior
{
    /**
     * Used for runtime configuration of model
     */
    public $runtime = array();
    public $_defaults = array('host' => 'localhost', 'port' => 9312);

    public $_cached_result = null;
    public $_cached_query = null;

    /**
     * Spinx client object
     *
     * @var SphinxClient
     */
    public $sphinx = null;

    function setup(Model $model, $options = array())
    {
        $databases = get_class_vars('DATABASE_CONFIG');
        $config = array_merge(
            $this->_defaults,
            $databases['sphinx']
        );
        $settings = array_intersect_key($config, $this->_defaults);

        $this->settings[$model->alias] = $settings;

        App::import('Lib', 'SphinxClient');
        $this->runtime[$model->alias]['sphinx'] = new SphinxClient();
        $this->runtime[$model->alias]['sphinx']->SetServer($this->settings[$model->alias]['host'],
                                                           $this->settings[$model->alias]['port']);
        $this->runtime[$model->alias]['deletedData'] = array();
    }

    /**
     * beforeFind Callback
     *
     * @param array $query
     * @return array Modified query
     * @access public
     */
    function beforeFind(Model $model, $query)
    {
        if (empty($query['sphinx'])) {
            return true;
        }

        if ($model->findQueryType == 'count') {
            $query['limit'] = 1;
            $query['page'] = 1;
        } else if (empty($query['limit'])) {
            $query['limit'] = 9999999;
            $query['page'] = 1;
        }

        $sphinx = $this->runtime[$model->alias]['sphinx'];
        foreach ($query['sphinx'] as $key => $setting) {
            switch ($key) {
                case 'filter':
                    foreach ($setting as $arg) {
                        $arg[2] = empty($arg[2]) ? false : $arg[2];
                        $sphinx->SetFilter($arg[0], (array)$arg[1], $arg[2]);
                    }
                   break;
                case 'filterRange':
                case 'filterFloatRange':
                    $method = 'Set' . $key;
                    foreach ($setting as $arg) {
                        $arg[3] = empty($arg[3]) ? false : $arg[3];
                        $sphinx->{$method}($arg[0], (array)$arg[1], $arg[2], $arg[3]);
                    }
                   break;
                case 'matchMode':
                   $sphinx->SetMatchMode($setting);
                   break;
                case 'sortMode':
                    $sphinx->SetSortMode(key($setting), reset($setting));
                    break;
                case 'fieldWeights':
                    $sphinx->SetFieldWeights($setting);
                    break;
                case 'rankingMode':
                    if (is_array($setting)) {
                        $sphinx->SetRankingMode(key($setting), reset($setting));
                    } else {
                        $sphinx->SetRankingMode($setting);
                    }
                    break;
                case 'select':
                    $sphinx->SetSelect($setting);
                    break;
                default:
                    break;
            }
        }
        $sphinx->SetLimits(($query['page'] - 1) * $query['limit'], $query['limit']);

        $indexes = !empty($query['sphinx']['index']) ? implode(',' , $query['sphinx']['index']) : '*';

        $result = $sphinx->Query($query['search'], $indexes);

        if ($result === false) {
            trigger_error("Search query failed: " . $sphinx->GetLastError());
            return false;
        } else if(isset($result['matches'])) {
            if ($sphinx->GetLastWarning()) {
                trigger_error("Search query warning: " . $sphinx->GetLastWarning());
            }
        }

        unset($query['conditions']);
        unset($query['order']);
        unset($query['offset']);
        $query['page'] = 1;
        if ($model->findQueryType == 'count') {
            $result['total'] = !empty($result['total']) ? $result['total'] : 0;
            $query['fields'] = 'ABS(' . $result['total'] . ') AS count';

        } else {
            $this->_cached_result = $result;
            $this->_cached_query = $query['search'];

            if (isset($result['matches'])) {
                $ids = array_keys($result['matches']);
            } else {
                $ids = array(0);
            }
            $query['conditions'] = array($model->alias . '.'.$model->primaryKey => $ids);
            $query['order'] = 'FIND_IN_SET('.$model->alias.'.'.$model->primaryKey.', \'' . implode(',', $ids) . '\')';

        }

        return $query;
    }

    private function addHighlightMarkers($model, &$results, $search) {
        // Sort the results by lang, i.e. by index
        $docsByLang = array();
        $size = count($results);
        foreach ($results as $result) {
            $size += count ($result['Transcription'] ?? 0);
        }
        $i = 0;
        foreach ($results as $result) {
            $lang = $result[$model->name]['lang'];
            $text = $result[$model->name]['text'];
            if (!array_key_exists($lang, $docsByLang)) {
                $docsByLang[$lang] = array_fill(0, $size, '');
            }
            $docsByLang[$lang][$i++] = $text;
        }
        foreach ($results as $result) {
            $lang = $result[$model->name]['lang'];
            if (isset($result['Transcription']) {
                foreach ($result['Transcription'] as $transcResult) {
                    $docsByLang[$lang][$i++] = $transcResult['text'];
                }
           }
        }

        // Call BuildExcerpts() for each index and merge the results
        $options = array(
            'query_mode' => true,
            'before_match' => '%__START_MATCH__%',
            'after_match' => '%__END_MATCH__%',
            'chunk_separator' => '%__CHUNK_SEPARATOR__%',
        );
        $mergedExcerpts = array();
        foreach ($docsByLang as $lang => $documents) {
            $excerpts = $this->runtime[$model->alias]['sphinx']->BuildExcerpts(
                $documents,
                $lang.'_main_index',
                $search,
                $options
            );
            foreach ($excerpts as $i => $excerpt) {
                if (!empty($excerpt)) {
                    $mergedExcerpts[$i] = $excerpt;
                }
            }
        }

        // Insert highlight markers in $results
        foreach ($results as $i => $result) {
            $excerpt = explode($options['chunk_separator'], array_shift($mergedExcerpts));
            $highlight = array(
                array($options['before_match'], $options['after_match']),
                $excerpt
            );
            $results[$i][$model->name]['highlight'] = $highlight;
        }
        foreach ($results as $i => $result) {
            if (isset($result['Transcription']) {
                foreach ($result['Transcription'] as $j => $transcResult) {
                    $excerpt = explode($options['chunk_separator'], array_shift($mergedExcerpts));
                    $highlight = array(
                        array($options['before_match'], $options['after_match']),
                        $excerpt
                    );
                    $results[$i]['Transcription'][$j]['highlight'] = $highlight;
                }
            }
        }
    }

    public function afterFind(Model $model, $results, $primary = false) {
        if (!is_null($this->_cached_query)) {
            $search = $this->_cached_query;
            if ($search) {
                $this->addHighlightMarkers($model, $results, $search);
            }
            $this->_cached_query = null;
        }

        if(!is_null($this->_cached_result)) {
            foreach($results as &$result) {
                $result[$model->name]['_weight'] = $this->_cached_result['matches'][$result[$model->name]['id']]['weight'];
                $result[$model->name]['_total_found'] = $this->_cached_result['total_found'];
            }
            $this->_cached_result = null;
        }
        return $results;

    }

    public function beforeDelete(Model $model, $cascade = true) {
        if (!$model->data)
            $model->read();
        if ($model->data)
            array_push($this->runtime[$model->alias]['deletedData'], $model->data);
        return true;
    }

    public function afterDelete(Model $model) {
        while ($data = array_shift($this->runtime[$model->alias]['deletedData'])) {
            $temp = $model->data;
            $model->data = $data;
            $this->_refreshSphinxAttributes($model);
            $model->data = $temp;
        }
    }

    public function afterSave(Model $model, $created, $options = array()) {
        $this->_refreshSphinxAttributes($model);
    }

    private function _refreshSphinxAttributes(&$model) {
        if (!method_exists($model, 'sphinxAttributesChanged'))
            return;

        $attributes = $values = array();
        $isMVA = false;
        $model->sphinxAttributesChanged($attributes, $values, $isMVA);

        foreach ($values as $sentenceId => &$value) {
            if ($isMVA) {
                foreach ($value as &$v) {
                    $v = array_map('intval', $v);
                }
            } else {
                $value = array_map('intval', $value);
            }
        }

        $langs = ClassRegistry::init('Sentence')->getSentencesLang(array_keys($values));
        $batchedByLang = array();
        foreach ($values as $sentenceId => $value) {
            if (!array_key_exists($sentenceId, $langs)
                || is_null($langs[$sentenceId]))
                continue;
            $lang = $langs[$sentenceId];
            if (!isset($batchedByLang[$lang]))
                $batchedByLang[$lang] = array();
            $batchedByLang[$lang][$sentenceId] = $value;
        }

        foreach ($batchedByLang as $lang => $values) {
            $res = $this->runtime[$model->alias]['sphinx']->UpdateAttributes(
                "${lang}_delta_index,${lang}_main_index",
                $attributes,
                $values,
                $isMVA
            );
            if ($res < 0) {
                $error = $this->runtime[$model->alias]['sphinx']->GetLastError();
                trigger_error('Unable to update Sphinx attribute(s) (' . implode(', ', $attributes).'): '.$error);
            }
        }
    }
}
