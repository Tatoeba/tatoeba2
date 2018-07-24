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
    private $maxFindAroundRange = 4;

    public function main() {
        $proceeded = $this->setSentenceBasedOnId();
        $this->out("\n$proceeded sentences proceeded.");
    }

    private function calcBasedOnId($walker, $log) {
        $matches = $walker->findAround($this->maxFindAroundRange, function ($elem) use ($log) {
            $elem = $elem['Contribution'];
            $creatDate = strtotime($log['datetime']);
            $otherDate = strtotime($elem['datetime']);
            $closeDatetime = abs($otherDate - $creatDate) <= 4;

            $isRelated = $elem['translation_id'] == $log['sentence_id']
                         || $elem['sentence_id'] == $log['sentence_id'];

            return $isRelated && $closeDatetime;
        });
        if (count($matches) == 0) {
            return 0;
        } elseif (count($matches) == 2) {
            foreach ($matches as $match) {
                $match = $match['Contribution'];
                if ($match['sentence_id'] == $log['sentence_id'] &&
                    $match['translation_id'] != null) {
                    return $match['translation_id'];
                }
            }
        } else {
            return -1;
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

    public function setSentenceBasedOnId() {
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
                if (!$this->Sentence->findById($sentenceId)) {
                    continue;
                }
                $basedOnId = $this->calcBasedOnId($walker, $log);
                if ($basedOnId != -1) {
                    $update = array('id' => $sentenceId, 'based_on_id' => $basedOnId);
                    $derivations[] = array_merge($update, $saveExtraOptions);
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
}
