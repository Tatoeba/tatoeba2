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
use Cake\Http\Exception\BadRequestException;

class AudioController extends ApiController
{
    /**
     * @OA\Schema(
     *   schema="Audio",
     *   description="An audio object that contains metadata about a recording.",
     *   @OA\Property(property="id", description="The audio identifier", type="integer", example="4321")
     * )
     *
     * @OA\PathItem(path="/unstable/audio/{id}/file",
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
                ->contain(['Users' => ['fields' => ['audio_license']]])
                ->where(['Audios.id' => $id, 'Users.audio_license !=' => ''])
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
}
