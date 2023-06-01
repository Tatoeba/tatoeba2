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
use Exception;


class SphinxBehavior extends Behavior
{
    /**
     * Used for runtime configuration of model
     */
    public $runtime = array();
    public $_defaults = array('host' => 'localhost', 'port' => 9312);

    public $_cached_result = null;
    public $_cached_query = null;
    private $_cached_options = null;

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

        $client = isset($databases['client']) ? $databases['client'] : new SphinxClient();
        $this->runtime[$alias]['sphinx'] = $client;
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
        /* CakePHP's paginator makes two calls to the database: the first for the actual
         * query and the second for the total count. But when we use the search engine
         * we already get the total count with the first call. The 'withSphinx' finder
         * from the model will use the cached count so we don't need to do anything
         * when this callback gets called with the same options a second time. */
        if (empty($options['sphinx']) ||
            $options['sphinx'] === $this->_cached_options) {
            return true;
        }

        $alias = $event->getSubject()->getAlias();
        $page = isset($options['sphinx']['page']) ? $options['sphinx']['page']: 1;
        $limit = isset($options['sphinx']['limit']) ? (int)$options['sphinx']['limit'] : 1;
        
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
        $sphinx->SetLimits(($page - 1) * $limit, $limit);

        $indexes = !empty($options['sphinx']['index']) ? implode(',' , $options['sphinx']['index']) : '*';

        $search = $options['sphinx']['query'] ?? '';
        $result = $sphinx->Query($search, $indexes);

        // avoid failing just because search operators are being misused
        if ($result === false) {
            $gotSyntaxError = strpos($sphinx->GetLastError(), 'syntax error,') !== FALSE;
            if ($gotSyntaxError) {
                $quotedQuery = $sphinx->EscapeString($search);
                $result = $sphinx->Query($quotedQuery, $indexes);
                if ($result) {
                    $search = $quotedQuery;
                }
            }
        }

        if ($result === false) {
            throw new Exception($sphinx->GetLastError());
        } else if(isset($result['matches'])) {
            if ($sphinx->GetLastWarning()) {
                trigger_error("Search query warning: " . $sphinx->GetLastWarning());
            }
        }

        $this->_cached_result = $result;
        $this->_cached_query = $search;
        $this->_cached_options = $options['sphinx'];
        if (isset($result['matches'])) {
            $ids = array_keys($result['matches']);
        } else {
            $ids = array(0);
        }
        $query->where(['Sentences.id IN' => $ids]);

        // Make sure that we order results according to the $ids array,
        // which contains the order provided by Sphinx.
        // We have to set the second param of order() to true to override
        // some previous ordering on the created/modified columns.
        $query->order(['FIND_IN_SET(Sentences.id, \'' . implode(',', $ids) . '\')'], true);

        // CakePHP's paginator sets the limit and offset before this method is
        // called, but we don't use them so we can remove them from the query.
        $query->offset(null);
        $query->limit(null);
        return $query;
    }

    public function findWithSphinx($query, $options)
    {
        $query->counter(function($query) { return $this->getTotal(); });

        return $query;
    }

    public function getTotal()
    {
        return $this->_cached_result['total'];
    }

    public function getRealTotal()
    {
        return $this->_cached_result['total_found'];
    }

    public function addHighlightMarkers($results) {
        $alias = $this->getConfig('alias');

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
                $this->_cached_query,
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
}
