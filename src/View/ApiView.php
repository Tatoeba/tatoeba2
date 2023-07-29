<?php
namespace App\View;

use Cake\Core\Configure;
use Cake\View\JsonView;

class ApiView extends JsonView
{
    public $helpers = [
        'Paginator',
    ];

    protected function pagination($params)
    {
        $links = new \stdClass();

        if ($params['pageCount'] == 1) {
            return $links;
        }

        $links->first = $this->Paginator->generateUrl(['page' => 1], null, ['escape' => false, 'fullBase' => true]);

        if ($this->Paginator->hasPrev()) {
            $links->prev = $this->Paginator->generateUrl(['page' => $params['page'] - 1], null, ['escape' => false, 'fullBase' => true]);
        }

        if ($this->Paginator->hasNext()) {
            $links->next = $this->Paginator->generateUrl(['page' => $params['page'] + 1], null, ['escape' => false, 'fullBase' => true]);
        }

        $links->last = $this->Paginator->generateUrl(['page' => $params['pageCount']], null, ['escape' => false, 'fullBase' => true]);

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
