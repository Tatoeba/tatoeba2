<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2014 Gilles Bedel
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

class TranscriptionsController extends AppController
{
    public $name = 'Transcriptions';

    public $uses = array(
        'Transcription',
    );

    public $components = array(
    );

    public $helpers = array(
        'Sentences',
    );

    public function edit() {
        $transcriptionId   = $this->params['form']['id'];
        $transcriptionText = $this->params['form']['value'];

        $ownerId = $this->Transcription->getTranscriptionOwner($transcriptionId);
        $userId = CurrentUser::get('id');
        $canEdit = ($ownerId == $userId || CurrentUser::isModerator());

        $saved = null;
        if ($canEdit) {
            $saved = $this->Transcription->save(array(
                'id' => $transcriptionId,
                'text' => $transcriptionText,
                'dirty' => false,
            ));
            if ($saved)
                $saved = $this->Transcription->findById($transcriptionId);
        }

        if (isset($this->params['requested'])) {
            return $canEdit && $saved;
        }
        $this->set('transcr', $saved);
        $this->layout = null;
    }
}
?>
