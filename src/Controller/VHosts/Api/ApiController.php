<?php

namespace App\Controller\VHosts\Api;

use Cake\Controller\Controller;
use Cake\Http\Exception\BadRequestException;
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
 * @OA\Schema(
 *   schema="LanguageCodeList",
 *   description="A list of ISO 639-3 codes or some <a href=""https://en.wiki.tatoeba.org/articles/show/tatoeba-supported-languages-exceptions"">exceptional code</a>. The codes will be combined with a boolean OR.",
 *   type="array",
 *   items=@OA\Items(type="string", minLength=3, maxLength=4, pattern="[a-z]+", example="epo")
 * )
 * @OA\Schema(
 *   schema="NegatableRangeList",
 *   description="A comma-separated list of inclusive ranges of the form <em>n</em>-<em>m</em>, <em>n</em> and <em>m</em> being integers. A range may be open-ended by omitting <em>n</em> or <em>m</em> (but not both). The list of ranges can be negated by prefixing it with <em>!</em>.",
 *   type="string",
 *   example="10-20",
 *   minLength=1,
 *   pattern="!?([0-9]+-[0-9]+|[0-9]+-|-[0-9]+)(,([0-9]+-[0-9]+|[0-9]+-|-[0-9]+))*"
 * )
 * @OA\Schema(
 *   schema="NegatableMemberList",
 *   description="A comma-separated list of usernames. The list of usernames can be negated by prefixing it with <em>!</em>. Empty username means orphan sentence.",
 *   type="string",
 *   example="gillux",
 *   pattern="!?[0-9a-zA-Z_]*(,[0-9a-zA-Z_]*)*"
 * )
 * @OA\Schema(
 *   schema="NegatableTagList",
 *   description="A comma-separated list of tags. The list of tags can be negated by prefixing it with <em>!</em>.",
 *   type="string",
 *   example="OK",
 *   pattern="!?[^,]+(,[^,]+)*"
 * )
 * @OA\Schema(
 *   schema="NegatableListIdList",
 *   description="A comma-separated list of list ids. The list of ids can be negated by prefixing it with <em>!</em>.",
 *   type="string",
 *   example="123",
 *   pattern="!?[0-9]+(,[0-9]+)*"
 * )
 * @OA\Schema(
 *   schema="NegatableLanguageCodeList",
 *   description="A comma-separated list of ISO 639-3 codes or some <a href=""https://en.wiki.tatoeba.org/articles/show/tatoeba-supported-languages-exceptions"">exceptional code</a>. The list of languages can be negated by prefixing it with <em>!</em>.",
 *   type="string",
 *   example="epo",
 *   pattern="!?[a-z]{3,4}(,[a-z]{3,4})*"
 * )
 * @OA\Schema(
 *   schema="Boolean",
 *   type="enum",
 *   enum={"yes", "no"}
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
        $version = $this->getRequest()->getParam('version');
        if ($version != 'unstable') {
            throw new BadRequestException("Invalid API version code: $version");
        }
    }
}
