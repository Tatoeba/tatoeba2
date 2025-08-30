<?php
namespace App\View;

use App\Controller\VHosts\Api\SentencesController;
use Cake\Core\Configure;
use Cake\View\JsonView;

class ApiView extends JsonView
{
    public $helpers = [
        'Paginator',
        'Url',
    ];

    private function buildUrl($originalParams, $newParams = [])
    {
        $params = $originalParams;
        foreach ($newParams as $newParam => $newValue) {
            if (is_null($newValue)) {
                unset($params[$newParam]);
            } else {
                $params[$newParam] = $newValue;
            }
        }
        $query = SentencesController::encodeQueryParameters($params);
        $url = $this->Url->build(['?' => null], ['escape' => false, 'fullBase' => true]);
        $url .= rtrim('?'.$query, '?');
        return $url;
    }

    protected function pagination($params)
    {
        $links = new \stdClass();

        $query = SentencesController::decodeQueryParameters($this->getRequest()->getUri()->getQuery());

        if (isset($query['after'])) {
            $links->first = $this->buildUrl($query, ['after' => null]);
        } else {
            $links->total = $this->get('total');
        }

        $links->has_next = $this->get('has_next');
        $cursor_end = $this->get('cursor_end');
        if ($links->has_next && !is_null($cursor_end)) {
            $links->cursor_end = $cursor_end;
            $links->next = $this->buildUrl($query, ['after' => $cursor_end]);
        }

        return $links;
    }

    /** 
     * Returns data to be serialized.
     *
     * @param array|string|bool $serialize The name(s) of the view variable(s) that
     *   need(s) to be serialized. If true all available view variables will be used.
     * @return mixed The data to serialize.
     */
    protected function _dataToSerialize($serialize = true) {
        $data = parent::_dataToSerialize($serialize);

        $params = $this->Paginator->params();
        if ($params) {
            $data['paging'] = $this->pagination($params);
        }

        return $data;
    }
}
