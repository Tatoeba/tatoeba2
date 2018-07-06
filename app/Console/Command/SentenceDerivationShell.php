<?php

class SentenceDerivationShell extends AppShell {

    public $uses = array('Sentence', 'Contribution');

    public function main() {
        $proceeded = $this->setSentenceBasedOnId();
        $this->out("\n$proceeded sentences proceeded.\n");
    }

    public function setSentenceBasedOnId() {
    }
}
