<?php

namespace App\Model;

use App\Model\Entity\SentenceComment;
use App\Model\Entity\Wall;
use Cake\Mailer\MailerAwareTrait;
use Cake\Routing\Router;
use Cake\Log\Log;
use Cake\Network\Exception\SocketException;

class ContentReport
{
    use MailerAwareTrait;

    private $reporter;
    private $entity;
    private $details;
    private $testPrefix;

    public function __construct($reporter, $entity, $details, $testPrefix = false) {
        if (!(
                 $entity instanceof Wall
              || $entity instanceof SentenceComment
            )) {
            throw new \RuntimeException('Unsupported entity');
        }
        $this->reporter = $reporter;
        $this->entity = $entity;
        $this->details = $details;
        $this->testPrefix = $testPrefix;
    }

    public function getTitle() : string {
        $prefix = "[Content Report] ";
        if ($this->entity instanceof Wall) {
            $title = "Wall message #{$this->entity->id}";
        } elseif ($this->entity instanceof SentenceComment) {
            $title = "Comment #{$this->entity->id} (on sentence #{$this->entity->sentence_id})";
        }
        if ($this->testPrefix) {
            $prefix = "[TEST]".$prefix;
        }
        return $prefix.$title;
    }

    public function getContentUrl() : string {
        if ($this->entity instanceof Wall) {
            $url = [
                'controller' => 'wall',
                'action' => 'show_message',
                $this->entity->id,
                '#' => 'message_'.$this->entity->id,
            ];
        } elseif ($this->entity instanceof SentenceComment) {
            $url = [
                'controller' => 'sentences',
                'action' => 'show',
                $this->entity->sentence_id,
                '#' => 'comment-'.$this->entity->id,
            ];
        }
        $url['_full'] = true;
        $url['lang'] = '';
        return Router::url($url);
    }

    public function getContentName() : string {
        if ($this->entity instanceof Wall) {
            return "a wall post";
        } elseif ($this->entity instanceof SentenceComment) {
            return "a sentence comment";
        }
    }

    public function getDetails() : string {
        return $this->details;
    }

    public function getReporter() : string {
        return $this->reporter;
    }

    public function send() {
        try {
            $this->getMailer('User')->send('content_report', [$this]);
            return true;
        } catch (SocketException $e) {
            Log::error(
                "Unable to send content report email by {$this->getReporter()}"
               ." about {$this->getContentUrl()}: {$e->getMessage()}"
            );
            return false;
        }
    }
}
