<?php
declare(strict_types=1);

namespace App\Command\SentenceDerivation;

use App\Command\BatchOperationTrait;
use Cake\Console\ConsoleIo;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\I18n\DateTime;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;

class Runner {

    use BatchOperationTrait;
    use LocatorAwareTrait;

    public $batchSize = 1000;
    public $linkEraFirstId = 330930;
    public $linkABrange = array(890774, 909052);
    private $maxFindAroundRange = 87;
    private ConsoleIo $io;
    private $Contributions;
    private $Sentences;

    public function __construct(ConsoleIo $io) {
        $this->io = $io;
    }

    private function findLinkedSentence($sentenceId, $matches) {
        if (count($matches) == 0) {
            return 0;
        } elseif (count($matches) == 1) {
            return null;
        } else {
            // pattern link B-A, link A-B
            $linkBA = $matches[0];
            $linkAB = $matches[1];
            if ($linkAB['id'] >= $this->linkABrange[0] && $linkAB['id'] <= $this->linkABrange[1]) {
                // pattern link A-B, link B-A
                $tmp = $linkBA;
                $linkBA = $linkAB;
                $linkAB = $tmp;
            }
            if ($sentenceId == $linkAB['sentence_id'] && $sentenceId == $linkBA['translation_id']) {
               return $linkAB['translation_id'];
            } else {
               return 0;
            }
        }
    }

    private function calcBasedOnId($walker, $log) {
        $matches = $walker->findAround($this->maxFindAroundRange, function ($elem) use ($log) {
            $isSameAuthor = $elem['user_id'] == $log['user_id'];
            $isInsertLink = $elem['action'] == 'insert' && $elem['type'] == 'link';
            if (!is_null($log['datetime']) && !is_null($elem['datetime'])) {
                $creatDate = $log['datetime']->getTimestamp();
                $otherDate = $elem['datetime']->getTimestamp();
                $closeDatetime = abs($otherDate - $creatDate) <= 310;
            } else {
                $closeDatetime = false;
            }

            $isRelated = ($elem['translation_id'] == $log['sentence_id'] && $elem['sentence_id'] < $log['sentence_id'])
                         || ($elem['sentence_id'] == $log['sentence_id'] && $elem['translation_id'] < $log['sentence_id']);
            return $isInsertLink && $isRelated && $closeDatetime && $isSameAuthor;
        });
        return $this->findLinkedSentence($log['sentence_id'], $matches);
    }

    private function saveDerivations($derivations) {
        $ids = Hash::extract($derivations, '{n}.id');
        if (!empty($ids)) {
            $oldData = $this->Sentences->find()
            ->where(['id IN' => $ids])
            ->all()
            ->toList();
            $entities = $this->Sentences->patchEntities($oldData, $derivations);
            if ($this->Sentences->saveMany($entities)) {
                $this->io->out('.', 0);
                return count($derivations);
            }
        }
         
        return 0;
    }

    public function findDuplicateCreationRecords() {
        $this->io->out("Finding duplicate creation records... ", 0);
        $result = $this->Contributions->find()
            ->select(['min' => 'MIN(id)', 'sentence_id'])
            ->where(['action' => 'insert', 'type' => 'sentence']) 
            ->group(['sentence_id'])
            ->having('count(sentence_id) > 1')
            ->all()
            ->toList();
        $result = Hash::combine($result, '{n}.sentence_id', '{n}.min');
        $this->io->out('done ('.count($result).' sentences affected)');
        return $result;
    }

    public function setSentenceBasedOnId($creationDups) {
        $total = 0;
        $derivations = array();
        $saveExtraOptions = array(
            'modified' => DateTime::now(),
            'callbacks' => false
        );
        $this->io->out("Setting 'based_on_id' field for all sentences", 0);
        $walker = new Walker($this->Contributions, $this->linkEraFirstId);
        $walker->allowRewindSize = $this->maxFindAroundRange;
        while ($log = $walker->next()) {
            if ($log['action']   == 'insert' &&
                $log['type']     == 'sentence' &&
                $log['datetime'] != '0000-00-00 00:00:00' && !empty($log['datetime']))
            {
                $sentenceId = $log['sentence_id'];
                try {
                    $sentence = $this->Sentences->get($sentenceId, ['fields' => ['based_on_id']]);
                    if (!is_null($sentence['based_on_id']) ||
                        (isset($creationDups[$sentenceId]) && $creationDups[$sentenceId] != $log['id'])
                    ) {
                        continue;
                    }
                } catch (RecordNotFoundException $e) {
                    continue;
                }
                $basedOnId = $this->calcBasedOnId($walker, $log);
                if (!is_null($basedOnId)) {
                    $update = array('id' => $sentenceId, 'based_on_id' => $basedOnId);
                    $derivations[$sentenceId] = array_merge($update, $saveExtraOptions);
                }
                if (count($derivations) >= $this->batchSize) {
                    $total += $this->saveDerivations($derivations);
                    $derivations = array();
                }
            }
        }
        $total += $this->saveDerivations($derivations);
        return $total;
    }

    public function main() {
        $this->Contributions = $this->fetchTable('Contributions');
        $this->Sentences = $this->fetchTable('Sentences');
        $creationDups = $this->findDuplicateCreationRecords();
        $total = $this->setSentenceBasedOnId($creationDups);
        return $total;
    }
}
