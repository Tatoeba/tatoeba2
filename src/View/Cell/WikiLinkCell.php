<?php
namespace App\View\Cell;

use Cake\View\Cell;

class WikiLinkCell extends Cell
{
    public function initialize() {
        $this->loadModel('WikiArticles');
    }

    public function display($englishSlug) {
        $link = $this->WikiArticles->getWikiLink($englishSlug);
        $this->set(compact('link'));
    }
}
