<?php
/**
 * Behavior for simple usage of Sphinx search engine
 * http://www.sphinxsearch.com
 *
 * @copyright 2008, Vilen Tambovtsev
 * @author  Vilen Tambovtsev
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */
namespace App\Model\Behavior;

use App\Lib\SphinxClient;
use Cake\Core\Configure;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;


class SphinxBehavior extends Behavior
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

    public function initialize(array $config)
    {
        $alias = $config['alias'];
        $databases = Configure::read('Sphinx');
        $dbConfig = array_merge(
            $this->_defaults,
            $databases
        );
        $settings = array_intersect_key($dbConfig, $this->_defaults);

        $this->settings[$alias] = $settings;

        $this->runtime[$alias]['sphinx'] = new SphinxClient();
        $this->runtime[$alias]['sphinx']->SetServer(
            $this->settings[$alias]['host'],
            $this->settings[$alias]['port']
        );
        $this->runtime[$alias]['deletedData'] = array();
    }

    /**
     * beforeFind Callback
     *
     * @param array $query
     * @return array Modified query
     * @access public
     */
    function beforeFind($event, $query, $options, $primary)
    {
        if (empty($options['sphinx'])) {
            return true;
        }

        $alias = $event->getSubject()->getAlias();

        /*if ($event->type == 'count') {
            $options['limit'] = 1;
            $options['page'] = 1;
        } else 
        */
        if (empty($options['limit'])) {
            $options['limit'] = 9999999;
            $options['page'] = 1;
        }

        $sphinx = $this->runtime[$alias]['sphinx'];
        foreach ($options['sphinx'] as $key => $setting) {
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
        $sphinx->SetLimits(($options['page'] - 1) * $options['limit'], $options['limit']);

        $indexes = !empty($options['sphinx']['index']) ? implode(',' , $options['sphinx']['index']) : '*';

        $result = $sphinx->Query($options['search'], $indexes);

        if ($result === false) {
            trigger_error("Search query failed: " . $sphinx->GetLastError());
            return false;
        } else if(isset($result['matches'])) {
            if ($sphinx->GetLastWarning()) {
                trigger_error("Search query warning: " . $sphinx->GetLastWarning());
            }
        }

        /*
        unset($query['conditions']);
        unset($query['order']);
        unset($query['offset']);
        $query['page'] = 1;
        */

        /*if ($event->type == 'count') { // TODO
            $result['total'] = !empty($result['total']) ? $result['total'] : 0;
            $query['fields'] = 'ABS(' . $result['total'] . ') AS count';
        } else {*/
            $this->_cached_result = $result;
            $this->_cached_query = $options['search'];
            if (isset($result['matches'])) {
                $ids = array_keys($result['matches']);
            } else {
                $ids = array(0);
            }
            $query->where(['Sentences.id IN' => $ids]);
            $query->order('FIND_IN_SET(Sentences.id, \'' . implode(',', $ids) . '\')');

        /*}*/

        return $query;
    }

    public function getRealTotal()
    {
        return $this->_cached_result['total_found'];
    }

    public function addHighlightMarkers($alias, $results, $search) {
        // Sort the results by lang, i.e. by index
        $docsByLang = array();
        $size = count($results);
        foreach ($results as $result) {
            if (isset($result['Transcription'])) {
                $size += count($result['Transcription']);
            }
        }
        $i = 0;
        foreach ($results as $result) {
            $lang = $result['lang'];
            $text = $result['text'];
            if (!array_key_exists($lang, $docsByLang)) {
                $docsByLang[$lang] = array_fill(0, $size, '');
            }
            $docsByLang[$lang][$i++] = $text;
        }
        foreach ($results as $result) {
            $lang = $result['lang'];
            if (isset($result['Transcription'])) {
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
            $excerpts = $this->runtime[$alias]['sphinx']->BuildExcerpts(
                $documents,
                $lang.'_main_index',
                $search,
                $options
            );
            if ($excerpts) {
                foreach ($excerpts as $i => $excerpt) {
                    if (!empty($excerpt)) {
                        $mergedExcerpts[$i] = $excerpt;
                    }
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
            $result['highlight'] = $highlight;
        }
        foreach ($results as $i => $result) {
            if (isset($result['Transcription'])) {
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

        return $results;
    }

    public function afterDelete($event, $entity, $options) {
        $alias = $event->getSubject()->getAlias();
        $this->_refreshSphinxAttributes($alias, $entity);
    }

    public function afterSave($event, $entity, $options = array()) {
        $alias = $event->getSubject()->getAlias();
        $this->_refreshSphinxAttributes($alias, $entity);
    }

    private function _refreshSphinxAttributes($alias, $entity) {
        $model = TableRegistry::getTableLocator()->get($alias);
        if (!method_exists($model, 'sphinxAttributesChanged'))
            return;

        $attributes = $values = array();
        $isMVA = false;
        $model->sphinxAttributesChanged($attributes, $values, $isMVA, $entity);

        foreach ($values as $sentenceId => &$value) {
            if ($isMVA) {
                foreach ($value as &$v) {
                    $v = array_map('intval', $v);
                }
            } else {
                $value = array_map('intval', $value);
            }
        }

        $Sentences = TableRegistry::getTableLocator()->get('Sentences');
        $langs = $Sentences->getSentencesLang(array_keys($values));
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
            $res = $this->runtime[$alias]['sphinx']->UpdateAttributes(
                "${lang}_delta_index,${lang}_main_index",
                $attributes,
                $values,
                $isMVA
            );
            if ($res < 0) {
                $error = $this->runtime[$alias]['sphinx']->GetLastError();
                trigger_error('Unable to update Sphinx attribute(s) (' . implode(', ', $attributes).'): '.$error);
            }
        }
    }

    public function buildSphinxPhraseSearchQuery($model, $text) {
        $escaped = $this->runtime[$model->alias]['sphinx']->EscapeString($text);
        return '="'.$escaped.'"';
    }
}
