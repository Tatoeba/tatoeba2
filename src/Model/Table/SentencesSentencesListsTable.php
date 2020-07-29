<?php
/**
 * Tatoeba Project, free collaborative creation of languages corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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
use Cake\ORM\Table;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Event\Event;
use App\Event\SentencesListListener;

class SentencesSentencesListsTable extends Table
{
    public function initialize(array $config)
    {
        $this->belongsTo('Sentences');
        $this->belongsTo('SentencesLists');
        
        $this->addBehavior('Timestamp');
        if (Configure::read('Search.enabled')) {
            $this->addBehavior('Sphinx', ['alias' => $this->getAlias()]);
        }

        $this->getEventManager()->on(new SentencesListListener());
    }

    public function getListsForSentence($sentenceId)
    {
        return $this->find()
            ->where([
                'sentence_id' => $sentenceId,
                'OR' => [
                    'user_id' => CurrentUser::get('id'),
                    'visibility' => 'public',
                ]
            ])
            ->select(['created'])
            ->contain([
                'SentencesLists' => [
                    'fields' => ['id', 'name', 'visibility', 'user_id', 'editable_by']
                ]
            ])
            ->order(['visibility', 'SentencesSentencesLists.created' => 'DESC'])
            ->all();
    }

    public function sphinxAttributesChanged(&$attributes, &$values, &$isMVA, $entity) {
        $sentenceId = $entity->sentence_id;
        $isMVA = true;
        $attributes[] = 'lists_id';
        $records = $this->find('all')
            ->where(['sentence_id' => $sentenceId])
            ->select('sentences_list_id')
            ->toList();
        $listsId = Hash::extract($records, '{n}.sentences_list_id');
        $values = array($sentenceId => array($listsId));
    }

    /**
     * Call after a deletion.
     *
     * @return void
     */
    public function afterDelete($event, $entity, $options)
    {
        $event = new Event('Model.SentencesSentencesList.deleted', $this, array(
            'list_id' => $entity->sentences_list_id,
            'data' => $entity
        ));
        $this->getEventManager()->dispatch($event);
    }
}
