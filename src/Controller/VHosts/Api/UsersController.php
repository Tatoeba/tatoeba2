<?php
namespace App\Controller\VHosts\Api;

use App\Controller\VHosts\Api\ApiController;

class UsersController extends ApiController
{
    /**
     * @OA\Schema(
     *   schema="User",
     *   description="A user object that contains metadata about a member of Tatoeba.",
     *   @OA\Property(
     *     property="username",
     *     description="The name of the member.",
     *     type="string",
     *     example="gillux",
     *     minLength=2,
     *     maxLength=20,
     *     pattern="[a-zA-Z0-9_]+"
     *   ),
     *   @OA\Property(
     *     property="role",
     *     description="The role this member was given.",
     *     type="enum",
     *     example="contributor",
     *     enum={
     *       "admin",
     *       "corpus_maintainer",
     *       "advanced_contributor",
     *       "contributor",
     *       "inactive",
     *       "spammer"
     *     }
     *   ),
     *   @OA\Property(
     *     property="since",
     *     description="The date this member joined Tatoeba.",
     *     type="date",
     *     example="2019-12-31"
     *   ),
     *   @OA\Property(
     *     property="languages",
     *     type="array",
     *     @OA\Items(
     *       @OA\Property(
     *         property="code",
     *         ref="#/components/schemas/LanguageCode"
     *       ),
     *       @OA\Property(
     *         property="level",
     *         description="The self-proclaimed level of proficiency in this language, ranging from 0 (lowest) to 5 (highest).",
     *         type="integer",
     *         nullable=true,
     *         example="3"
     *       ),
     *       @OA\Property(
     *         property="details",
     *         description="Optional details about the language ability, such as dialect or country. This is a free-form text entered by the member, which can be written in any language.",
     *         type="string"
     *       )
     *     )
     *   )
     * )
     */
    private function exposedFields() {
        $exposedFields = ['username', 'role', 'since'];
        return compact('exposedFields');
    }

    private function fields() {
        return [
            'id',
            'username',
            'role',
            'since',
        ];
    }

    /**
     * @OA\PathItem(path="/unstable/users/{username}",
     *   @OA\Parameter(name="username", in="path", required=true, description="The user name of the member.",
     *     @OA\Schema(ref="#/components/schemas/User/properties/username")
     *   ),
     *   @OA\Get(
     *     summary="Get a user",
     *     description="Get information about a member of Tatoeba.",
     *     tags={"Users"},
     *     @OA\Response(response="200", description="Success."),
     *     @OA\Response(response="400", description="Invalid parameter."),
     *     @OA\Response(response="404", description="There is no user with that username or the account was removed.")
     *   )
     * )
     */
    public function get($name) {
        $validator = $this->Users->getValidator();
        $invalid = $validator->validate(['username' => $name], false);
        if ($invalid) {
            return $this->response->withStatus(400, 'Invalid parameter "username"');
        }

        $query = $this->Users
            ->addBehavior('ExposedOnApi')
            ->find('exposedFields', $this->exposedFields())
            ->select($this->fields())
            ->where([
                'username' => $name,
            ])
            ->find('containOnApi', ['containOnApi' => ['UsersLanguages' => fn ($q) =>
                $q
                    ->select([
                        'of_user_id',
                        'code' => 'language_code',
                        'level',
                        'details'
                    ])
                    ->find('exposedFields', ['exposedFields' => [
                        'code', 'level', 'details'
                    ]])
            ]])
            ->find('datetime2date', ['datetimefields' => ['since']]);

        $results = $query->firstOrFail();
        $response = [
            'data' => $results,
        ];

        $this->set('response', $response);
        $this->viewBuilder()->setOption('serialize', 'response');
        $this->RequestHandler->renderAs($this, 'json');
    }
}
