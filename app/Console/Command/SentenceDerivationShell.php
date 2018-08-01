<?php

class Walker {
    private $model;
    private $startAtId;
    private $buffer = array();
    public $bufferSize = 1000;
    public $allowRewindSize = 20;

    public function __construct($model, $startAtId = 1) {
        $this->model = $model;
        $this->startAtId = $startAtId;
    }

    private function setBufferPointerAt($i) {
        reset($this->buffer);
        while (key($this->buffer) !== $i) {
            next($this->buffer);
        }
    }

    public function next() {
        $next = next($this->buffer);
        if ($next === false) {
            if (empty($this->buffer)) {
                $lastId = $this->startAtId - 1;
                $fetchSize = $this->bufferSize;
            } else {
                $last = end($this->buffer);
                $lastId = $last[$this->model->alias][$this->model->primaryKey];
                $fetchSize = $this->bufferSize - $this->allowRewindSize;
            }
            $rows = $this->model->find('all', array(
                'conditions' => array('id > ' => $lastId),
                'limit' => $fetchSize,
            ));
            if (empty($rows)) {
                return false;
            }
            $remainder = array_slice($this->buffer, -$this->allowRewindSize, $this->allowRewindSize);
            $this->buffer = array_merge($remainder, $rows);
            $this->setBufferPointerAt(count($remainder));
            $next = current($this->buffer);
        }
        return $next;
    }

    public function findAround($range, $matchFunction) {
        return array_merge(
            $this->findBefore($range, $matchFunction),
            $this->findAfter($range, $matchFunction)
        );
    }

    public function findAfter($range, $matchFunction) {
        $matches = array();
        $max = $range;
        for ($i = 0; $i < $max; $i++) {
           $row = $this->next($this->buffer);
           if ($row === false) {
              $range--;
           } else {
               if ($matchFunction($row)) {
                   $matches[] = $row;
               }
           }
        }
        if ($range != $max) {
           end($this->buffer);
        }
        for ($i = 0; $i < $range; $i++) {
           prev($this->buffer);
        }
        return $matches;
    }

    public function findBefore($range, $matchFunction) {
        $matches = array();
        $max = $range;
        for ($i = 0; $i < $max; $i++) {
           if (prev($this->buffer) === false) {
              $range--;
           }
        }
        if ($range != $max) {
           reset($this->buffer);
        }
        for ($i = 0; $i < $range; $i++) {
           $row = current($this->buffer);
           if ($matchFunction($row)) {
               $matches[] = $row;
           }
           $this->next($this->buffer);
        }
        return $matches;
    }
}

class SentenceDerivationShell extends AppShell {

    public $uses = array('Sentence', 'Contribution');
    public $batchSize = 1000;
    public $linkEraFirstId = 330930;
    public $linkABrange = array(890774, 909052);
    private $maxFindAroundRange = 15;

    public function main() {
        $proceeded = $this->run();
        $this->out("\n$proceeded sentences proceeded.");
    }

    private function findLinkedSentence($sentenceId, $matches) {
        // pattern link B-A, link A-B
        $linkBA = $matches[0]['Contribution'];
        $linkAB = $matches[1]['Contribution'];
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

    private function calcBasedOnId($walker, $log) {
        $matches = $walker->findAround($this->maxFindAroundRange, function ($elem) use ($log) {
            $elem = $elem['Contribution'];
            $isInsertLink = $elem['action'] == 'insert' && $elem['type'] == 'link';
            $creatDate = strtotime($log['datetime']);
            $otherDate = strtotime($elem['datetime']);
            $closeDatetime = abs($otherDate - $creatDate) <= 27;

            $isRelated = ($elem['translation_id'] == $log['sentence_id'] && $elem['sentence_id'] < $log['sentence_id'])
                         || ($elem['sentence_id'] == $log['sentence_id'] && $elem['translation_id'] < $log['sentence_id']);
            return $isInsertLink && $isRelated && $closeDatetime;
        });
        if (count($matches) == 0) {
            return 0;
        } elseif (count($matches) >= 2) {
            return $this->findLinkedSentence($log['sentence_id'], $matches);
        } else {
            return null;
        }
    }

    private function saveDerivations($derivations) {
        if ($this->Sentence->saveAll($derivations)) {
            $this->out('.', 0);
            return count($derivations);
        } else {
            return 0;
        }
    }

    public function findDuplicateCreationRecords() {
        $this->out("Finding duplicate creation records... ", 0);
        $result = $this->Contribution->find('all', array(
            'fields' => array('min(id)' => 'id', 'sentence_id'),
            'conditions' => array('action' => 'insert', 'type' => 'sentence'),
            'group' => array('sentence_id having count(sentence_id) > 1'),
        ));
        $result = Set::combine($result, '{n}.Contribution.sentence_id', '{n}.Contribution.id');
        $this->out('done ('.count($result).' sentences affected)');
        return $result;
    }

    public function setSentenceBasedOnId($creationDups) {
        $total = 0;
        $derivations = array();
        $saveExtraOptions = array(
            'modified' => false,
            'callbacks' => false
        );
        $this->out("Setting 'based_on_id' field for all sentences", 0);
        $walker = new Walker($this->Contribution, $this->linkEraFirstId);
        $walker->allowRewindSize = $this->maxFindAroundRange;
        while ($log = $walker->next()) {
            $log = $log['Contribution'];
            if ($log['action']   == 'insert' &&
                $log['type']     == 'sentence' &&
                $log['datetime'] != '0000-00-00 00:00:00')
            {
                $sentenceId = $log['sentence_id'];
                $sentence = $this->Sentence->findById($sentenceId, 'based_on_id');
                if (!$sentence || !is_null($sentence['Sentence']['based_on_id']) ||
                    (isset($creationDups[$sentenceId]) && $creationDups[$sentenceId] != $log['id'])
                   ) {
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

    public function run() {
        $creationDups = $this->findDuplicateCreationRecords();
        $total = $this->setSentenceBasedOnId($creationDups);
        return $total;
    }
}
