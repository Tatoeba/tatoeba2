<?php
namespace App\Mailer;

use Cake\Mailer\Mailer;

/**
 * Mailer for emails related to messaging
 * (sentence comments, private messages, wall posts)
 */
class MessagingMailer extends Mailer
{
    public function wall_reply($recipient, $author, $post)
    {
        $this
            ->setTo($recipient)
            ->setSubject("Tatoeba - $author has replied to you on the Wall")
            ->setEmailFormat('html')
            ->setViewVars(compact('post'));
    }

    public function wall_mention($recipient, $author, $post)
    {
        $this
            ->setTo($recipient)
            ->setSubject("Tatoeba - $author mentioned you on the Wall")
            ->setEmailFormat('html')
            ->setTemplate('wall_reply')
            ->setViewVars(compact('post'));
    }

    public function new_private_message($recipient, $sender, $message)
    {
        $this
            ->setTo($recipient)
            ->setSubject("Tatoeba PM - {$message->title}")
            ->setEmailFormat('html')
            ->setViewVars(compact('sender', 'message'));
    }

    public function comment_on_sentence($recipient, $author, $comment, $sentence)
    {
        if ($sentence) {
            $subject = "Tatoeba - Comment on sentence : {$sentence->text}";
        } else {
            $subject = "Tatoeba - Comment on deleted sentence #{$comment->sentence_id}";
        }

        $this
            ->setTo($recipient)
            ->setSubject($subject)
            ->setEmailFormat('html')
            ->setViewVars(compact('author', 'comment', 'sentence'));
    }
}
