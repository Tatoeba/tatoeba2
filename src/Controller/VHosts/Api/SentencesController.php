<?php
namespace App\Controller\VHosts\Api;

use App\Model\Search;
use App\Model\SearchApi;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query;

class SentencesController extends ApiController
{
    /**
     * @OA\Schema(
     *   schema="Sentence",
     *   description="A sentence object that contains both sentence text and metadata about the sentence.",
     *   @OA\Property(property="id", description="The sentence identifier", type="integer", example="1234")
     * )
     */
    private function exposedFields() {
        $sentence = [
            'fields' => ['id', 'text', 'lang', 'script', 'license', 'owner'],
            'audios' => ['fields' => [
                'author', 'license', 'attribution_url', 'download_url',
            ]],
            'transcriptions' => ['fields' => [
                'script', 'text', 'needsReview', 'type', 'html']
            ],
        ];
        $exposedFields = $sentence;
        $exposedFields['translations'] = $sentence;
        return compact('exposedFields');
    }

    private function fields() {
        return [
            'id',
            'text',
            'lang',
            'user_id',
            'correctness',
            'script',
            'license',
        ];
    }

    private function contain() {
        $audioContainment = function (Query $q) {
            $q->select(['id', 'external', 'sentence_id'])
              ->where(['audio_license !=' => '']) # exclude audio that cannot be reused outside of Tatoeba
              ->contain(['Users' => ['fields' => ['username', 'audio_license', 'audio_attribution_url']]]);
            return $q;
        };
        $transcriptionsContainment = [
            'fields' => ['sentence_id', 'script', 'text', 'needsReview'],
            'Sentences' => ['fields' => ['lang']],
        ];
        $indirTranslationsContainment = function (Query $q) use ($audioContainment, $transcriptionsContainment) {
            $q->select($this->fields())
              ->where(['IndirectTranslations.license !=' => ''])
              ->contain([
                  'Users' => ['fields' => ['id', 'username']],
                  'Audios' => $audioContainment,
                  'Transcriptions' => $transcriptionsContainment,
              ]);
            return $q;
        };
        $translationsContainment = function (Query $q) use ($audioContainment, $transcriptionsContainment, $indirTranslationsContainment) {
            $q->select($this->fields())
              ->where(['Translations.license !=' => ''])
              ->contain([
                  'Users' => ['fields' => ['id', 'username']],
                  'Audios' => $audioContainment,
                  'Transcriptions' => $transcriptionsContainment,
                  'IndirectTranslations' => $indirTranslationsContainment,
              ]);
            return $q;
        };

        return [
            'Translations' => $translationsContainment,
            'Users' => ['fields' => ['id', 'username']],
            'Audios' => $audioContainment,
            'Transcriptions' => $transcriptionsContainment,
        ];
    }

    /**
     * @OA\PathItem(path="/unstable/sentences/{id}",
     *   @OA\Parameter(name="id", in="path", required=true, description="The sentence identifier.",
     *     @OA\Schema(ref="#/components/schemas/Sentence/properties/id")
     *   ),
     *   @OA\Get(
     *     summary="Get a sentence",
     *     description="Get sentence text as well as metadata about this sentence and related sentences.",
     *     tags={"Sentences"},
     *     @OA\Response(response="200", description="Success."),
     *     @OA\Response(response="400", description="Invalid ID parameter."),
     *     @OA\Response(response="404", description="There is no sentence with that ID or it has been deleted.")
     *   )
     * )
     */
    public function get($id) {
        $this->loadModel('Sentences');
        $query = $this->Sentences
            ->find('filteredTranslations')
            ->find('exposedFields', $this->exposedFields())
            ->select($this->fields())
            ->where([
                'Sentences.id' => $id,
                'Sentences.license !=' => '',
            ])
            ->contain($this->contain());

        try {
            $results = $query->firstOrFail();
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestException('Invalid sentence id');
        }
        $response = [
            'data' => $results,
        ];

        $this->set('response', $response);
        $this->set('_serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }

    /* We use our own query parsing functions here because PHP builtins
     * are not very flexible. In particular, PHP's parse_str() does not
     * handle well multiple parameters with the same name. See:
     *   https://www.php.net/manual/en/function.parse-str.php#76792
     */
    public static function decodeQueryParameters(string $query) {
        $query  = explode('&', $query);
        $params = [];
        foreach ($query as $param) {
            $parts = explode('=', $param, 2);
            if (count($parts) == 1) {
                $value = null;
                $name = $parts[0];
            } else {
                list($name, $value) = $parts;
            }
            $uname = urldecode($name);
            if (isset($params[$uname])) {
                if (is_array($params[$uname])) {
                    $params[$uname][] = urldecode($value);
                } else {
                    $params[$uname] = [$params[$uname], urldecode($value)];
                }
            } else {
                $params[$uname] = urldecode($value);
            }
        }
        return $params;
    }

    public static function encodeQueryParameters(array $params) {
        $params = array_map(
            function($name, $values) {
                $name = urlencode($name);
                if (is_null($values)) {
                    return $name;
                } else {
                    $values = array_map(
                        function ($value) use ($name) {
                            return $name.'='.urlencode($value);
                        },
                        (array)$values
                    );
                    return implode('&', $values);
                }
            },
            array_keys($params),
            array_values($params)
        );
        return implode('&', $params);
    }

    /**
     * @OA\PathItem(path="/unstable/sentences",
     *   @OA\Parameter(name="lang", in="query", required=true, explode=false,
     *     description="A comma-separated list of languages to search in.",
     *     @OA\Examples(example="1", value="epo",      summary="sentences in Esperanto"),
     *     @OA\Examples(example="2", value="epo,sun",  summary="sentences in Esperanto or Sundanese"),
     *     @OA\Schema(ref="#/components/schemas/LanguageCodeList")
     *   ),
     *   @OA\Parameter(name="q", in="query",
     *     description="The search query. The query must follow ManticoreSearch query syntax.",
     *     @OA\Schema(type="string", example="Let's")
     *   ),
     *   @OA\Parameter(name="word_count", in="query",
     *     description="Limit to sentences having the provided number of words. For languages with word boundaries, the number of words is used. For other languages, the number of characters is used.",
     *     @OA\Examples(example="1",  value="10-",       summary="10 words or more"),
     *     @OA\Examples(example="2",  value="-10",       summary="10 words or less"),
     *     @OA\Examples(example="3",  value="5-10",      summary="between 5 and 10 words"),
     *     @OA\Examples(example="4",  value="7",         summary="exactly 7 words"),
     *     @OA\Examples(example="5",  value="!3",        summary="any number of words but 3"),
     *     @OA\Examples(example="6",  value="1,10",      summary="either 1 or 10 words"),
     *     @OA\Examples(example="7",  value="2-4,10-11", summary="2, 3, 4, 10 or 11 words"),
     *     @OA\Examples(example="8",  value="!2-",       summary="1 word only"),
     *     @OA\Examples(example="9",  value="!2-5",      summary="1 word, or more than 5"),
     *     @OA\Examples(example="10", value="!2-5,10-",  summary="1 word, or between 6 and 9 words"),
     *     @OA\Schema(ref="#/components/schemas/NegatableRangeList")
     *   ),
     *   @OA\Parameter(name="owner", in="query",
     *     description="Limit to sentences owned by the provided username. Make sure to combine with <code>is_orphan</code> filter in a way that makes sense.",
     *     @OA\Examples(example="1", value="gillux",        summary="sentences owned by gillux"),
     *     @OA\Examples(example="2", value="gillux,ajip",   summary="sentences owned by gillux or ajip"),
     *     @OA\Examples(example="3", value="!gillux",       summary="sentences orphan or owned by a different member than gillux"),
     *     @OA\Examples(example="4", value="!gillux,ajip",  summary="sentences orphan or owned by a member who is neither gillux nor ajip"),
     *     @OA\Schema(ref="#/components/schemas/NegatableMemberList")
     *   ),
     *   @OA\Parameter(name="is_orphan", in="query",
     *     description="Limit to orphan sentences (if value is yes) or sentences owned by someone (if value is no). Make sure to combine with <code>owner</code> filter in a way that makes sense.",
     *     @OA\Schema(ref="#/components/schemas/Boolean")
     *   ),
     *   @OA\Parameter(name="is_unapproved", in="query",
     *     description="Limit to <a href=""https://en.wiki.tatoeba.org/articles/show/faq#why-are-some-sentences-in-red?"">unapproved sentences</a> (if value is yes) or exclude unapproved sentences (if value is no).",
     *     @OA\Schema(ref="#/components/schemas/Boolean")
     *   ),
     *   @OA\Parameter(name="has_audio", in="query",
     *     description="Limit to sentences having one or more audio recordings (if value is yes) or no audio recording (if value is no).",
     *     @OA\Schema(ref="#/components/schemas/Boolean")
     *   ),
     *   @OA\Parameter(name="tag", in="query",
     *     description="Limit to sentences having the provided tag. This parameter can be provided multiple times to search for sentences having multiple tags at the same time.",
     *     @OA\Examples(example="1", value="OK",             summary="sentences tagged as OK"),
     *     @OA\Examples(example="2", value="idiom",          summary="sentences tagged as idiom"),
     *     @OA\Examples(example="3", value="idiom,proverb",  summary="sentences tagged as idiom or proverb (or both)"),
     *     @OA\Examples(example="4", value="!OK",            summary="exclude sentences tagged as OK"),
     *     @OA\Examples(example="5", value="!idiom,proverb", summary="exclude sentences tagged as idiom or proverb (or both)"),
     *     @OA\Schema(ref="#/components/schemas/NegatableTagList")
     *   ),
     *   @OA\Parameter(name="list", in="query",
     *     description="Limit to sentences present on the provided list id. This parameter can be provided multiple times to search for sentences present on multiple lists at the same time.",
     *     @OA\Examples(example="1", value="123",      summary="sentences on list 123"),
     *     @OA\Examples(example="2", value="123,456",  summary="sentences on list 123 or list 456 (or both)"),
     *     @OA\Examples(example="3", value="!123",     summary="exclude sentences on list 123"),
     *     @OA\Examples(example="4", value="!123,456", summary="exclude sentences on list 123 or list 456 (or both)"),
     *     @OA\Schema(ref="#/components/schemas/NegatableListIdList")
     *   ),
     *   @OA\Parameter(name="is_native", in="query",
     *     description="Limit to sentences owned by a self-identified native speaker (if value is yes) or a self-identified non-native speaker (if the value is no). This parameter can only be used when searching in a single language (not several).",
     *     @OA\Schema(ref="#/components/schemas/Boolean")
     *   ),
     *   @OA\Parameter(name="origin", in="query",
     *     description="Limit according to sentence origin. All sentences fall in two sets: <em>unknown</em> and <em>known</em>. The set <em>known</em> is composed of two subsets: <em>original</em> + <em>translation</em>.",
     *     @OA\Schema(type="enum", enum={"original", "translation", "known", "unknown"}),
     *     @OA\Examples(example="1", value="original",    summary="sentences not added as translations of other sentences"),
     *     @OA\Examples(example="2", value="translation", summary="sentences added as translations of other sentences"),
     *     @OA\Examples(example="3", value="known",       summary="sentences we know have been added or not as translations of other sentences"),
     *     @OA\Examples(example="4", value="unknown",     summary="sentences we do not know whether or not they have been added as translations of other sentences"),
     *   ),
     *   @OA\Parameter(name="trans:lang", in="query",
     *     description="Limit to sentences having translations in this language.",
     *     @OA\Examples(example="1", value="epo",      summary="sentences having translation(s) in Esperanto"),
     *     @OA\Examples(example="2", value="epo,sun",  summary="sentences having translation(s) in Esperanto or Sundanese"),
     *     @OA\Examples(example="3", value="!epo,sun", summary="sentences having translation(s) in a language that is not Esperanto or Sundanese"),
     *     @OA\Schema(ref="#/components/schemas/NegatableLanguageCodeList")
     *   ),
     *   @OA\Parameter(name="trans:is_direct", in="query",
     *     description="Limit to sentences having directly-linked translation(s) if value is yes, or indirectly-linked translations (i.e. translations of translations) if the value is no.",
     *     @OA\Schema(ref="#/components/schemas/Boolean")
     *   ),
     *   @OA\Parameter(name="trans:owner", in="query",
     *     description="Limit to sentences having translation(s) owned by the provided username. Make sure to combine with <code>trans:is_orphan</code> filter in a way that makes sense.",
     *     @OA\Examples(example="1", value="gillux",        summary="sentences having translation(s) owned by gillux"),
     *     @OA\Examples(example="2", value="gillux,ajip",   summary="sentences having translation(s) owned by gillux or ajip"),
     *     @OA\Examples(example="3", value="!gillux",       summary="sentences having translation(s) owned by a different member than gillux or orphan"),
     *     @OA\Examples(example="4", value="!gillux,ajip",  summary="sentences having translation(s) that are orphan or owned by a member who is neither gillux nor ajip"),
     *     @OA\Schema(ref="#/components/schemas/NegatableMemberList")
     *   ),
     *   @OA\Parameter(name="trans:is_unapproved", in="query",
     *     description="Limit to sentences having <a href=""https://en.wiki.tatoeba.org/articles/show/faq#why-are-some-sentences-in-red?"">unapproved</a> translation(s) (if value is yes) or having translation(s) not marked as unapproved (if value is no).",
     *     @OA\Schema(ref="#/components/schemas/Boolean")
     *   ),
     *   @OA\Parameter(name="trans:is_orphan", in="query",
     *     description="Limit to sentences having orphan translations (if value is yes) or translations owned by someone (if value is no). Make sure to combine with <code>trans:owner</code> filter in a way that makes sense.",
     *     @OA\Schema(ref="#/components/schemas/Boolean")
     *   ),
     *   @OA\Parameter(name="trans:has_audio", in="query",
     *     description="Limit to sentences having translation(s) having one or more audio recordings (if value is yes) or no audio recording (if value is no).",
     *     @OA\Schema(ref="#/components/schemas/Boolean")
     *   ),
     *   @OA\Parameter(name="trans:count", in="query",
     *     description="Limit according to the presence of translations. Zero (0) or non-zero (!0) are the only allowed values.",
     *     @OA\Schema(type="string", pattern="!?0"),
     *     @OA\Examples(example="1", value="0",  summary="sentences not having any translation"),
     *     @OA\Examples(example="2", value="!0", summary="sentences having translation(s)"),
     *   ),
     *   @OA\Parameter(name="sort", in="query", required=true,
           description="Sort order of the sentences. Prefix the value with minus <code>-</code> to reverse that order.",
     *     @OA\Examples(example="1", value="relevance", summary="prioritize sentences with exact matches, then sentences containing all the searched words, then shortest sentences"),
     *     @OA\Examples(example="2", value="words",     summary="order by number of words (or, if the language does not use spaces as word separators, by number of characters), shortest first"),
     *     @OA\Examples(example="3", value="-words",    summary="order by number of words, longest first"),
     *     @OA\Examples(example="4", value="created",   summary="order by sentence creation date (newest first)"),
     *     @OA\Examples(example="5", value="-created",  summary="order by sentence creation date (oldest first)"),
     *     @OA\Examples(example="6", value="modified",  summary="order by last sentence modification (last modified first)"),
     *     @OA\Examples(example="7", value="random",    summary="randomly sort results"),
     *     @OA\Schema(type="string", pattern="-?(relevance|words|created|modified|random)")
     *   ),
     *   @OA\Parameter(name="after", in="query",
     *     description="Cursor start position. This parameter is used to paginate results using keyset pagination method. After fetching the first page, if there are more results, you get a <code>cursor_end</code> value along with the results. To get the second page of results, execute the same query with the added <code>after=&lt;cursor_end&gt;</code> parameter. If there are more results, the second page will containg another <code>cursor_end</code> you can use to get the third page, and so on.",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(name="limit", in="query",
     *     description="Maximum number of sentences in the response.",
     *     @OA\Schema(type="integer", example="20")
     *   ),
     *   @OA\Parameter(name="showtrans", in="query", explode=false,
     *     description="By default, all the translations of matched sentences are returned, regardless of how translations filters were used. Here you can limit the language of the translations that will be displayed in the result, using a comma-separated list of languages codes. You may also use an empty value to not display any translation.",
     *     @OA\Examples(example="1", value="epo",      summary="only show translations in Esperanto, if any"),
     *     @OA\Examples(example="2", value="epo,sun",  summary="only show translations in Esperanto and Sundanese, if any"),
     *     @OA\Schema(ref="#/components/schemas/LanguageCodeList")
     *   ),
     *   @OA\Get(
     *     summary="Search sentences",
     *     description="Allows to search for sentences based on some criteria. By default, all sentences are returned, including sentences you might want to filter out, such as <a href=""https://en.wiki.tatoeba.org/articles/show/faq#why-are-some-sentences-in-red?"">unapproved</a> or orphaned (that is, likely not proofread) ones. To filter sentences, use any combination of the parameters described below.

<h3>Combining sentence filters</h3>

<p>Use <code>&</code> (the usual operator to combine query parameters) to combine filters with logical AND.</p>

<table>
  <tr><th>Example</th><th>Result</th></tr>
  <tr>
    <td style=""white-space:nowrap""><code>lang=epo<br>&has_audio=yes</code></td>
    <td>Only sentences both in Esperanto and having audio.</td>
  </tr>
  <tr>
    <td style=""white-space: nowrap""><code>tag=OK<br>&tag=colloquial</code></td>
    <td>Only sentences having both an OK tag and a colloquial tag.</td>
  </tr>
</table>

<p>Use <code>,</code> to combine several values of a filter with logical OR.</p>

<table>
  <tr><th>Example</th><th>Result</th></tr>
  <tr>
    <td style=""white-space:nowrap""><code>lang=srp,hrv,bos</code></td>
    <td>Only sentences in Serbian, Croatian, or Bosnian.</td>
  </tr>
  <tr>
    <td style=""white-space:nowrap""><code>tag=idiom,proverb</code></td>
    <td>Only sentences tagged as idiom or proverb (or both).</td>
  </tr>
</table>

<p>Use <code>!</code> as value prefix to make a logical NOT (applied to the entire list of values). It can be used to exclude sentences matched by the filter.</p>

<table>
  <tr><th>Example</th><th>Result</th></tr>
  <tr>
<td style=""white-space:nowrap""><code>tag=!colloquial</code></td>
<td>Exclude sentences tagged as colloquial.</td>
  </tr>
  <tr>
<td style=""white-space:nowrap""><code>tag=!idiom,proverb</code></td>
<td>Exclude sentences tagged as idiom or proverb or both. (In other words, only sentences neither tagged as idiom nor proverb.)</td>
  </tr>
  <tr>
<td style=""white-space:nowrap""><code>tag=!idiom<br>&tag=!proverb</code></td>
<td>Exclude sentences that are both tagged as idiom and proverb. (Should a sentence be tagged as idiom but not proverb, it won't be excluded.)</td>
  </tr>
</table>

<h3>Combining translation filters</h3>

<p>Translation filters are filters which name starts with <code>trans:</code> (or <code>!trans:</code>, covered later).</p>

<table>
  <tr><th>Example</th><th>Result</th></tr>
  <tr>
    <td style=""white-space:nowrap""><code>trans:lang=epo<br>&trans:is_direct=yes</code></td>
    <td>Only sentences having direct translation(s) in Esperanto.</td>
  </tr>
  <tr>
    <td style=""white-space:nowrap""><code>trans:lang=epo<br>&trans:owner=gillux</code></td>
    <td>Only sentences having translation(s) in Esperanto owned by ""gillux"".</td>
  </tr>
  <tr>
    <td style=""white-space:nowrap""><code>trans:count=0</code></td>
    <td>Only sentences not having any translation at all.</td>
  </tr>
</table>

<p>Each translation filter belongs to a group. First, filters belonging to the same group are applied together to the translations with a logical AND, and then each group result is combined with a logical AND.</p>
<p>Filters starting with the same prefix belong to the same group, and <code>trans:</code> is just one of these groups. It is possible to create any number of groups using the prefix <code>trans:<em>n</em>:</code>, <code><em>n</em></code> consisting of one of more digits.</p>

<table>
  <tr><th>Example</th><th>Result</th></tr>
  <tr>
    <td style=""white-space:nowrap""><code>trans:1:lang=epo<br>&trans:2:lang=sun</code></td>
    <td>Only sentences both having translation(s) in Esperanto and in Sundanese.</td>
  </tr>
  <tr>
    <td style=""white-space:nowrap""><code>trans:1:lang=epo<br>&trans:1:owner=gillux<br>&trans:2:lang=sun<br>&trans:2:owner=ajip</code></td>
    <td>Only sentences both having translation(s) in Esperanto owned by ""gillux"" and in Sundanese owned by ""ajip"".</td>
  </tr>
  <tr>
    <td style=""white-space:nowrap""><code>trans:1:lang=epo<br>&trans:1:is_orphan=yes<br>&trans:2:lang=epo<br>&trans:2:is_orphan=no</code></td>
    <td>Only sentences both having orphan and non-orphan translation(s) in Esperanto.</td>
  </tr>
</table>

<p>By prefixing a group number <code><em>n</em></code> with an exclamation mark, it is possible to perform a logical NOT on a specific group before it is combined with other groups. This can be used to exclude sentences having translation(s) matched by the group.</p>
<table>
  <tr><th>Example</th><th>Result</th></tr>
  <tr>
    <td style=""white-space:nowrap""><code>trans:1:lang=epo<br>&trans:1:is_direct=no</code></td>
    <td>Only sentences having indirect translation(s) in Esperanto.</td>
  </tr>
  <tr>
    <td style=""white-space:nowrap""><code>trans:!1:lang=epo<br>&trans:!1:is_direct=yes</code></td>
    <td>Exclude sentences having direct translation(s) in Esperanto.</td>
  </tr>
  <tr>
    <td style=""white-space:nowrap""><code>trans:1:lang=epo<br>&trans:1:is_direct=no<br>&trans:!1:lang=epo<br>&trans:!1:is_direct=yes</code></td>
    <td>Combination of the two above: only sentences having indirect, but not any direct translation(s) in Esperanto (note that <code>trans:1:</code> and <code>trans:!1:</code> are considered as different groups because the prefix is not strictly equal).</td>
  </tr>
  <tr>
    <td style=""white-space:nowrap""><code>trans:!1:lang=epo<br>&trans:!2:lang=sun</code></td>
    <td>Exclude sentences having translation(s) in Esperanto or Sundanese (or both). Why <em>or</em>? Because in boolean algebra: !epo AND !sun ⟺ !(epo OR sun)</td>
  </tr>
</table>

<p>Note that groups such as <code>trans:1:</code> are not a subgroup of the <code>trans:</code> group; they are all groups of the same level.</p>

<h4>Special <code>!trans:</code> prefix</h4>

<p>The prefix <code>!trans:</code> works just like <code>trans:</code>, except it is a separate group of groups in which a final logical NOT is performed on the top of all the groups it encompasses. This can be used to exclude sentences having translations matched by several groups at once.</p>

<table>
  <tr><th>Example</th><th>Result</th></tr>
  <tr>
    <td style=""white-space:nowrap""><code>!trans:1:lang=epo<br>&!trans:2:lang=sun</code></td>
    <td>Exclude sentences both having translation(s) in Esperanto and in Sundanese.</td>
  </tr>
  <tr>
    <td style=""white-space:nowrap""><code>!trans:1:lang=epo<br>&!trans:1:owner=gillux<br>&!trans:2:lang=sun<br>&!trans:2:owner=ajip</code></td>
    <td>Exclude sentences both having translation(s) in Esperanto owned by ""gillux"" and in Sundanese owned by ""ajip"".</td>
  </tr>
  <tr>
    <td style=""white-space:nowrap""><code>!trans:1:lang=epo<br>&!trans:1:is_orphan=yes<br>&!trans:2:lang=epo<br>&!trans:2:is_orphan=no</code></td>
    <td>Exclude sentences both having orphan and non-orphan translation(s) in Esperanto.</td>
  </tr>
</table>

<p><code>!trans:</code> can also be used as an equivalent prefix for <code>trans:!0:</code> when this would otherwise be the only group.</p>
<table>
  <tr><th>Example</th><th>Result</th></tr>
  <tr>
    <td style=""white-space:nowrap""><code>trans:0:lang=epo<br>&trans:!0:lang=sun</code></td>
    <td>Sentences having translation(s) in Esperanto and not having any translation in Sundanese.</td>
  </tr>
  <tr>
    <td style=""white-space:nowrap""><code>trans:lang=epo<br>&!trans:lang=sun</code></td>
    <td>Same as above.</td>
  </tr>
</table>
           ",
     *     tags={"Sentences"},
     *     @OA\Response(response="200", description="Success."),
     *     @OA\Response(response="400", description="Invalid parameter.")
     *   )
     * )
     */
    public function search() {
        $params = self::decodeQueryParameters($this->getRequest()->getUri()->getQuery());
        $this->setRequest($this->getRequest()->withQueryParams($params));

        $api = new SearchApi();
        $showtrans = $api->consumeShowTrans($params);
        $limit = $api->consumeInt('limit', $params, self::DEFAULT_RESULTS_NUMBER);
        $api->consumeSort($params);
        $api->setFilters($params);

        $sphinx = $api->search->asSphinx();
        $sphinx['limit'] = $limit > self::MAX_RESULTS_NUMBER ? self::MAX_RESULTS_NUMBER : $limit;

        $this->loadModel('Sentences');

        $query = $this->Sentences
            ->find('withSphinx')
            ->find('filteredTranslations', [
                'translationLang' => $showtrans,
            ])
            ->find('exposedFields', $this->exposedFields())
            ->select($this->Sentences->fields())
            ->where(['Sentences.license !=' => '']) // FIXME use Manticore filter instead
            ->contain($this->contain());

        $this->paginate = [
            'limit' => self::DEFAULT_RESULTS_NUMBER,
            'maxLimit' => self::MAX_RESULTS_NUMBER,
            'sphinx' => $sphinx,
        ];
        $results = $this->paginate($query);
        $response = [
            'data' => $results,
        ];

        $this->set('has_next', $this->Sentences->getRealTotal() > $this->Sentences->getReturnedResultsCount());
        $this->set('total', $this->Sentences->getRealTotal());

        $last = $results->last();
        if ($last) {
            $this->set('cursor_end', $last[Search::CURSOR_FIELD]);
        }
        $this->set('results', $response);
        $this->set('_serialize', 'results');
        $this->RequestHandler->renderAs($this, 'json');
    }
}
