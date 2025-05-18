<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Lib\LanguagesLib;
use Cake\Event\Event;

class AngularTemplatesController extends AppController
{
    public $name = 'AngularTemplates';

    public function beforeRender(Event $event)
    {
        $this->viewBuilder()->enableAutoLayout(false);
        return parent::beforeRender($event);
    }

    public function interface_language()
    {
    }

    public function show_all_sentences_button_text($code)
    {
        $this->response = $this->response->withType('application/json');

        if (!LanguagesLib::languageExists($code) && $code != 'unknown') {
            return $this->response->withStatus(404);
        } else {
            $this->set(compact('code'));
        }
    }
}
