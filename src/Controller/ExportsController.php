<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\CurrentUser;

class ExportsController extends AppController
{
    public function index()
    {
        $exports = $this->Exports->getExportsOf(CurrentUser::get('id'));

        $this->set(compact('exports'));
    }
}
