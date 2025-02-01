<?php
namespace App\View;

use App\Controller\VHosts\Api\SentencesController;
use Cake\Core\Configure;
use Cake\View\JsonView;

class ApiView extends JsonView
{
    public $helpers = [
        'Url',
    ];

    private function buildUrl($originalParams, $newParams = [])
    {
        $params = $originalParams;
        foreach ($newParams as $newParam => $newValue) {
            $params[$newParam] = $newValue;
        }
        if (isset($params['page']) && $params['page'] == 1) {
            unset($params['page']);
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

        if ($params['pageCount'] == 1) {
            return $links;
        }

        $links->first = $this->buildUrl($query, ['page' => 1]);

        if ($this->Paginator->hasPrev()) {
            $links->prev = $this->buildUrl($query, ['page' => $params['page'] - 1]);
        }

        if ($this->Paginator->hasNext()) {
            $links->next = $this->buildUrl($query, ['page' => $params['page'] + 1]);
        }

        $links->last = $this->buildUrl($query, ['page' => $params['pageCount']]);

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
