<?php
namespace App\View;

use Cake\View\View;

class AppView extends View
{
    public function initialize(): void
    {
        parent::initialize();

        // At least thest helpers are needed on every single page, so preload them.
        // The rest is lazy-loaded.
        $this->addHelper('AssetCompress.AssetCompress');
        $this->addHelper('AngularTemplate');
        $this->addHelper('Flash');
        $this->addHelper('Html');
        $this->addHelper('Languages');
    }

    /**
     * Sanitize user input sent to AngularJS
     *
     * If user input contains '{{...}}' the part inside the braces will be interpolated
     * by AngularJS which could lead to XSS attacks. So we have to sanitize all unsafe
     * strings in our templates.
     *
     * @param string|array $subject If $subject is an array, all values will be sanitized
     *                              recursively
     *
     * @return string
     **/
    public function safeForAngular($subject) {
        if (is_array($subject)) {
            return array_map([$this, 'safeForAngular'], $subject);
        } else {
            return str_replace('{{', "{{ '{{' }}", $subject);
        }
    }
}
