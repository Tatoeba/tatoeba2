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
    var $runtime = array();
    var $_defaults = array('host' => 'localhost', 'port' => 9312);

    var $_cached_result = null;

    /**
     * Spinx client object
     *
     * @var SphinxClient
     */
    var $sphinx = null;

    function setup(&$model)
    {
        $config = array(
            'host' => Configure::read('Search.host'),
            'port' => Configure::read('Search.port'),
        );
        $config = array_filter($config);
        $settings = array_merge($this->_defaults, $config);

        $this->settings[$model->alias] = $settings;

        App::import('Vendor', 'sphinxapi');
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
    function beforeFind(&$model, $query)
    {
        if (empty($query['sphinx']))
            return true;

        if ($model->findQueryType == 'count')
        {
            $model->recursive = -1;
            $query['limit'] = 1;
            $query['page'] = 1;
        }
        else if (empty($query['limit']))
        {
            $query['limit'] = 9999999;
            $query['page'] = 1;
        }

        foreach ($query['sphinx'] as $key => $setting)
        {

            switch ($key)
            {
                case 'filter':
                    foreach ($setting as $arg)
                    {
                        $arg[2] = empty($arg[2]) ? false : $arg[2];
                        $this->runtime[$model->alias]['sphinx']->SetFilter($arg[0], (array)$arg[1], $arg[2]);
                    }
                   break;
                case 'filterRange':
                case 'filterFloatRange':
                    $method = 'Set' . $key;
                    foreach ($setting as $arg)
                    {
                        $arg[3] = empty($arg[3]) ? false : $arg[3];
                        $this->runtime[$model->alias]['sphinx']->{$method}($arg[0], (array)$arg[1], $arg[2], $arg[3]);
                    }
                   break;
                case 'matchMode':
                   $this->runtime[$model->alias]['sphinx']->SetMatchMode($setting);
                   break;
                case 'sortMode':
                    $this->runtime[$model->alias]['sphinx']->SetSortMode(key($setting), reset($setting));
                    break;
                case 'fieldWeights':
                    $this->runtime[$model->alias]['sphinx']->SetFieldWeights($setting);
                    break;
                case 'rankingMode': 
                    if (is_array($setting)) {
                        $this->runtime[$model->alias]['sphinx']->SetRankingMode(key($setting), reset($setting));
                    } else {
                        $this->runtime[$model->alias]['sphinx']->SetRankingMode($setting);
                    }
                    break;
                case 'select':
                    $this->runtime[$model->alias]['sphinx']->SetSelect($setting);
                    break;
                default:
                    break;
            }
        }
        $this->runtime[$model->alias]['sphinx']->SetLimits(($query['page'] - 1) * $query['limit'], $query['limit']);

        $indexes = !empty($query['sphinx']['index']) ? implode(',' , $query['sphinx']['index']) : '*';

        $result = $this->runtime[$model->alias]['sphinx']->Query($query['search'], $indexes);

        if ($result === false)
        {
            trigger_error("Search query failed: " . $this->runtime[$model->alias]['sphinx']->GetLastError());
            return false;
        }
        else if(isset($result['matches']))
        {
            if ($this->runtime[$model->alias]['sphinx']->GetLastWarning())
            {
                trigger_error("Search query warning: " . $this->runtime[$model->alias]['sphinx']->GetLastWarning());
            }
        }

        unset($query['conditions']);
        unset($query['order']);
        unset($query['offset']);
        $query['page'] = 1;
        if ($model->findQueryType == 'count')
        {
            $result['total'] = !empty($result['total']) ? $result['total'] : 0;
            $query['fields'] = 'ABS(' . $result['total'] . ') AS count';

        }
        else
        {
            $this->_cached_result = $result;
    
            if (isset($result['matches']))
                $ids = array_keys($result['matches']);
            else
                $ids = array(0);
            $query['conditions'] = array($model->alias . '.'.$model->primaryKey => $ids);
            $query['order'] = 'FIND_IN_SET('.$model->alias.'.'.$model->primaryKey.', \'' . implode(',', $ids) . '\')';

        }

        return $query;
    }


    public function afterFind(&$model, $results, $primary) {
        
        if(!is_null($this->_cached_result)) {
            foreach($results as &$result) {
                $result[$model->name]['_weight'] = $this->_cached_result['matches'][$result[$model->name]['id']]['weight'];
                $result[$model->name]['_total_found'] = $this->_cached_result['total_found'];
            }
            $this->_cached_result = null;
        }
        return $results;
        
    }

    public function beforeDelete(&$model, $cascade = true) {
        if (!$model->data)
            $model->read();
        if ($model->data)
            array_push($this->runtime[$model->alias]['deletedData'], $model->data);
        return true;
    }

    public function afterDelete(&$model) {
        while ($data = array_shift($this->runtime[$model->alias]['deletedData'])) {
            $temp = $model->data;
            $model->data = $data;
            $this->_refreshSphinxAttributes($model);
            $model->data = $temp;
        }
    }

    public function afterSave(&$model, $created) {
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
