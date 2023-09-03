<?php

namespace App\Controller\VHosts\Api;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * @OA\Info(
 *   version="unstable",
 *   title="Tatoeba API",
 *   description="<h2>Welcome to the Tatoeba API</h1>
<p>
This is an ongoing effort to provide an API for tatoeba.org.
This API is currently read-only and subject to change, but open to the public without authentification.
You are encouraged to try it, feedback is welcome.
When this API will be considered mature, we will release a stable version and you will just have to change your endpoints from <em>/unstable</em> to <em>/v1</em>.
</p>",
 *   @OA\Contact(name="API support", email="team@tatoeba.org")
 * )
 * @OA\Server(url="https://api.tatoeba.org",     description="Tatoeba's production server")
 * @OA\Server(url="https://api.dev.tatoeba.org", description="Tatoeba's development server")
 *
 * @OA\Schema(
 *   schema="LanguageCode",
 *   description="The ISO 639-3 code of the language, or some <a href=""https://en.wiki.tatoeba.org/articles/show/tatoeba-supported-languages-exceptions"">exceptional code</a>.",
 *   type="string",
 *   example="epo",
 *   minLength=3,
 *   maxLength=4,
 *   pattern="[a-z]+"
 * )
 */
class ApiController extends Controller
{
    const DEFAULT_RESULTS_NUMBER = 10;
    const MAX_RESULTS_NUMBER = 100;

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler', [
            'viewClassMap' => ['json' => 'Api']
        ]);
    }

    public function beforeFilter(Event $event)
    {
        if ($this->getRequest()->getParam('version') != 'unstable') {
            return $this->default();
        }
    }

    public function default()
    {
        $this->autoRender = false;
        return $this
            ->getResponse()
            ->withStatus(404);
    }
}
