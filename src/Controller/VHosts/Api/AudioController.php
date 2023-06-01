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

class AudioController extends ApiController
{
    public function download($id) {
        $this->loadModel('Audios');
        $audio = $this->Audios->find()
            ->select(['id', 'sentence_id'])
            ->where(['Audios.id' => $id])
            ->first();

        if ($audio) {
            $options = [
                'download' => true,
                'name' => $audio->pretty_filename,
            ];
            return $this->getResponse()
                        ->withFile($audio->file_path, $options);
        } else {
            throw new \Cake\Http\Exception\NotFoundException();
        }
    }
}
