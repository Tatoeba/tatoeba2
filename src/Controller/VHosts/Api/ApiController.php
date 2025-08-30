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
 * @OA\Response(
 *   response="ClientErrorResponse",
 *   description="There was an issue with the provided parameters.",
 *   @OA\JsonContent(
 *     description="Description of the error response.",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Invalid value for parameter ""sort""", description="Details about what is wrong."),
 *     @OA\Property(property="url", type="string", example="/unstable/sentences?lang=eng&amp;sort=invalid", description="URL of the request (HTML-safe)."),
 *     @OA\Property(property="code", type="integer", example=400, description="HTTP status code of the response.")
 *   )
 * )
 * @OA\Response(
 *   response="ServerErrorResponse",
 *   description="There is a problem with the API server.",
 *   @OA\JsonContent(
 *     description="Description of the error response.",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="An Internal Error Has Occurred.", description="Details about what is wrong."),
 *     @OA\Property(property="url", type="string", example="/unstable/sentences/1", description="URL of the request (HTML-safe)."),
 *     @OA\Property(property="code", type="integer", example=500, description="HTTP status code of the response.")
 *   )
 * )
 * @OA\Response(
 *   response="NotFoundErrorResponse",
 *   description="The thing could not be found.",
 *   @OA\JsonContent(
 *     description="Description of the error response.",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Not Found", description="Details about what is wrong."),
 *     @OA\Property(property="url", type="string", example="/unstable/sentences/1234", description="URL of the request (HTML-safe)."),
 *     @OA\Property(property="code", type="integer", example=404, description="HTTP status code of the response.")
 *   )
 * )
 *
 * @OA\Parameter(name="after", in="query",
 *   description="Cursor start position. This parameter is used to paginate results using keyset pagination method. After fetching the first page, if there are more results, you get a <code>cursor_end</code> value along with the results. To get the second page of results, execute the same query with the added <code>after=&lt;cursor_end&gt;</code> parameter. If there are more results, the second page will containg another <code>cursor_end</code> you can use to get the third page, and so on.",
 *   @OA\Schema(type={"string", "integer"})
 * )
 *
 * @OA\Schema(
 *   schema="Paging",
 *   description="Description of the pagination context of a response.",
 *   type="object",
 *   @OA\Property(property="first", type="string", example="https://example.com/sentences", description="URL to fetch the first page of results."),
 *   @OA\Property(property="total", type="integer", example="42", description="The total number of results among all pages."),
 *   @OA\Property(property="has_next", type="boolean", example=true, description="Whether there are more results than what was returned."),
 *   @OA\Property(property="cursor_end", type="string", example="1234,4567", description="Identifier used to fetch the next page of results (see the <code>after=</code> parameter)."),
 *   @OA\Property(property="next", type="string", example="https://example.com/sentences?after=1234,4567", description="URL to fetch the next page of results."),
 * )
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
 * @OA\Schema(
 *   schema="ScriptCode",
 *   type="string",
 *   description="ISO 15924 script code",
 *   example="Latn",
 *   minLength=4,
 *   maxLength=4,
 *   pattern="[A-Z][a-z]{3}"
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
