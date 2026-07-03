<?php
declare(strict_types=1);

namespace App\Command\SentenceDerivation;

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
                $lastId = $last['id'];
                $fetchSize = $this->bufferSize - $this->allowRewindSize;
            }
            $rows = $this->model->find('all')
                ->where(['id > ' => $lastId])
                ->limit($fetchSize)
                ->all()
                ->toList();

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
