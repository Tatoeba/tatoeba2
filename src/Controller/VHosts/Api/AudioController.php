<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2023 Gilles Bedel
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\Controller\VHosts\Api;

use App\Controller\VHosts\Api\ApiController;
use App\Model\Exception\InvalidValueException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;

class AudioController extends ApiController
{
    /**
     * @OA\PathItem(path="/v1/audio/{id}/file",
     *   @OA\Parameter(name="id", in="path", required=true, description="The audio identifier.",
     *     @OA\Schema(ref="#/components/schemas/Audio/properties/id")
     *   ),
     *   @OA\Get(
     *     summary="Get an audio file",
     *     description="Download an audio recording of a sentence.",
     *     tags={"Audio"},
     *     @OA\Response(response="200", description="Success."),
     *     @OA\Response(response="400", description="Invalid parameter."),
     *     @OA\Response(response="404", description="There is no audio with that ID, it was removed, or the audio author does not allow reuse outside of Tatoeba.")
     *   )
     * )
     */
    public function file($id) {
        $this->loadModel('Audios');
        try {
            $audio = $this->Audios->find()
                ->select(['id', 'sentence_id'])
                ->find('hasLicense')
                ->where(['Audios.id' => $id])
                ->firstOrFail();
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestException('Invalid audio id');
        }

        $options = [
            'download' => true,
            'name' => $audio->pretty_filename,
        ];
        return $this->getResponse()
                    ->withFile($audio->file_path, $options);
    }

    /**
     * @OA\Schema(
     *   schema="AudioWithExtraInfo",
     *   description="An audio object along with related information.",
     *   allOf={
     *     @OA\Schema(ref="#/components/schemas/Audio"),
     *     @OA\Schema(@OA\Property(property="sentence", ref="#/components/schemas/Sentence"))
     *   }
     * )
     *
     * @OA\PathItem(path="/unstable/audio",
     *   @OA\Parameter(name="lang", in="query",
     *     description="Only return audio recordings in that language.",
     *     @OA\Examples(example="1", value="epo", summary="audio recordings in Esperanto"),
     *     @OA\Examples(example="2", value="sun", summary="audio recordings in Sundanese"),
     *     @OA\Schema(ref="#/components/schemas/LanguageCode")
     *   ),
     *   @OA\Parameter(name="author", in="query",
     *     description="Only return audio recordings contributed by this user.",
     *     @OA\Schema(type="string", example="kevin")
     *   ),
     *   @OA\Parameter(name="limit", in="query",
     *     description="Maximum number of audio recordings in the response.",
     *     @OA\Schema(type="integer", example="20")
     *   ),
     *   @OA\Parameter(name="after", ref="#/components/parameters/after"),
     *   @OA\Get(
     *     summary="Search audio recordings",
     *     description="Get the list of all audio recordings matching criteria.",
     *     tags={"Audio"},
     *     @OA\Response(
     *       response="200",
     *       description="Success.",
     *       @OA\JsonContent(type="object",
     *         @OA\Property(property="data", type="array",
     *           description="Array of audio objects matching the provided filters.",
     *           @OA\Items(ref="#/components/schemas/AudioWithExtraInfo")
     *         ),
     *         @OA\Property(property="paging", ref="#/components/schemas/Paging")
     *       )
     *     ),
     *     @OA\Response(response="400", ref="#/components/responses/ClientErrorResponse"),
     *     @OA\Response(response="500", ref="#/components/responses/ServerErrorResponse")
     *   )
     * )
     */
    public function search() {
        $this->loadModel('Audios');
        $query = $this->Audios->addBehavior('ExposedOnApi')->find();

        /* Read parameters */
        $limit = $this->getRequest()->getQuery('limit', (string)self::DEFAULT_RESULTS_NUMBER);
        if (ctype_digit($limit)) {
            $limit = (int)$limit;
        } else {
            throw new BadRequestException("Invalid value for parameter 'limit': must be a positive integer");
        }

        $lang = $this->getRequest()->getQuery('lang');
        if (!is_null($lang)) {
            try {
                \App\Model\Search::validateLanguage($lang);
                $query->where(['Audios.sentence_lang' => $lang]);
            } catch (InvalidValueException $e) {
                throw new BadRequestException("Invalid value for parameter 'lang': ".$e->getMessage());
            }
        }

        $author = $this->getRequest()->getQuery('author');
        if (!is_null($author)) {
            $this->loadModel('Users');
            try {
                $userId = $this->Users->findByUsername($author)->firstOrFail()->id;
                $query->where(['Audios.user_id' => $userId]);
            } catch (RecordNotFoundException $e) {
                throw new BadRequestException("Invalid value for parameter 'author': No such user");
            }
        }

        $after = $this->getRequest()->getQuery('after');
        if (!is_null($after)) {
            if (ctype_digit($after)) {
                $query->where(['Audios.id > ' => (int)$after]);
            } else {
                throw new BadRequestException("Invalid value for parameter 'after': must be a positive integer");
            }
        }

        /* Build and execute query */
        $query
            ->find('audiosOnApi')
            ->find('containOnApi', ['containOnApi' => ['Sentences' => ['finder' => 'sentencesOnApi']]])
            ->order(['Audios.id' => 'ASC']);

        $this->paginate = compact('limit');
        $results = $this->paginate($query);
        $response = [
            'data' => $results,
        ];

        $this->set('has_next', $query->count() > count($results));
        $this->set('total', $query->count());

        $last = $results->last();
        if ($last) {
            $this->set('cursor_end', $last['id']);
        }
        $this->set('results', $response);
        $this->set('_serialize', 'results');
        $this->RequestHandler->renderAs($this, 'json');
    }
}
