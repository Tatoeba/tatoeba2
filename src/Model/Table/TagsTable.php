<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010 SIMON   Allan   <allan.simon@supinfo.com>
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
namespace App\Model\Table;

use App\Model\CurrentUser;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\Table;
use Cake\Event\Event;

class TagsTable extends Table
{
    public function getChangeTagName()
    {
        return '@change';
    }
    public function getCheckTagName()
    {
        return '@check';
    }
    public function getDeleteTagName()
    {
        return '@delete';
    }
    public function getNeedsNativeCheckTagName()
    {
        return '@needs native check';
    }
    public function getOKTagName()
    {
        return 'OK';
    }

    public function initialize(array $config) 
    {
        $this->hasMany('TagsSentences');
        $this->belongsToMany('Sentences');
        $this->belongsTo('Users');

        $this->addBehavior('Timestamp');
    }

    /**
     * Add a tag (and optionally tag a sentence)
     *
     * @param string   $tagName
     * @param int      $userId
     * @param int|null $sentenceId
     *
     * @return Cake\ORM\Entity|false
     */
    public function addTag($tagName, $userId, $sentenceId = null)
    {
        $tagName = trim($tagName);
        if ($tagName == '') {
            return false;
        }
        // Truncate to a maximum byte length of 50. If a multibyte
        // character would be split, the entire character will be
        // truncated.
        $tagName = mb_strcut($tagName, 0, 50, "UTF-8");

        // Special case: don't allow the owner of a sentence to give it an OK tag.
        if ($sentenceId && $tagName == 'OK') {
            $owner = $this->Sentences->getOwnerInfoOfSentence($sentenceId);
            if ($owner && $userId == $owner['id']) {
                return false;
            }
        }

        $tag = $this->newEntity([
            'name' => $tagName,
            'user_id' => $userId,
        ]);

        if ($tag->hasErrors()) {
            return false;
        }

        $added = $this->findOrCreate(
            ['name' => $tag->name],
            function ($entity) use ($tag) { $entity = $tag; }
        );

        if ($added) {
            $event = new Event('Model.Tag.tagAdded', $this, compact('tagName'));
            $this->getEventManager()->dispatch($event);

            if ($sentenceId != null) {
                return $this->TagsSentences->tagSentence(
                    $sentenceId,
                    $added->id,
                    $userId
                );
            }

            return $added;
        }

        return false;
    }

    public function removeTagFromSentence($tagId, $sentenceId) {
        if (!$this->canRemoveTagFromSentence($tagId, $sentenceId)) {
            return false;
        }
        return $this->TagsSentences->removeTagFromSentence(
            $tagId,
            $sentenceId
        );
    }

    private function canRemoveTagFromSentence($tagId, $sentenceId) {
        $result = $this->TagsSentences->find()
            ->where([
                'tag_id' => $tagId,
                'sentence_id' => $sentenceId,
            ])
            ->first();
        return $result && CurrentUser::canRemoveTagFromSentence(
            $result->user_id
        );
    }

    /**
     * Get tag id from tag internal name.
     *
     * @param string $tagInternalName Internal name of the tag.
     *
     * @return int Id of the tag
     */
    public function getIdFromInternalName($tagInternalName) {
        $result = $this->find()
            ->where(['internal_name' => $tagInternalName])
            ->select(['id'])
            ->first();
            
        return $result ? $result->id : null;
    }


    /**
     *
     * TODO
     *
     */
    public function getIdFromName($tagName) {
        $result = $this->find('all')
            ->where(['name' => $tagName])
            ->select(['id'])
            ->first();
            
        return $result ? $result->id : null;
    }


    /**
     *
     * TODO
     *
     */
    public function tagExists($tagId) {
        $result = $this->get($tagId, ['fields' => ['name']]);
        return !empty($result);
    }


    /**
     *
     * TODO
     *
     */
    public function getNameFromId($tagId) {
        $result = $this->find()
            ->where(['id' => $tagId])
            ->select(['name'])
            ->first();

        return $result ? $result->name : null;
    }
}
