<?php
/**
    Tatoeba Project, free collaborative creation of languages corpuses project
    Copyright (C) 2018  Gilles Bedel

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

App::uses('CakeEmail', 'Network/Email');
App::uses('CakeEventListener', 'Event');

class NotificationListener implements CakeEventListener {
    public function implementedEvents() {
        return array(
            'Model.PrivateMessage.messageSent' => 'sendPmNotification',
        );
    }

    public function sendPmNotification($event) {
        extract($event->data); // $message
        $User = ClassRegistry::init('User');
        $userSettings = $User->getSettings($message['recpt']);

        if (!$userSettings['User']['send_notifications']) {
            return;
        }

        $recipientEmail = $User->getEmailFromId($message['recpt']);
        $sender = $User->getUsernameFromId($message['sender']);
        $title = $message['title'];
        $content = $message['content'];

        $this->Email = new CakeEmail();
        $this->Email
            ->to($recipientEmail)
            ->subject('Tatoeba PM - ' . $title)
            ->template('new_private_message')
            ->viewVars(array(
              'sender' => $sender,
              'title' => $title,
              'message' => $content,
              'messageId' => $message['id'],
            ));

        $this->_send();
    }

    private function _send() {
        if (Configure::read('Mailer.enabled') == false) {
            return;
        }

        $this->Email->config(array(
            'port' => '465',
            'timeout' => '45',
            'host' => 'ssl://smtp.gmail.com',
            'username' => Configure::read('Mailer.username'),
            'password' => Configure::read('Mailer.password'),
            'transport' => $this->getTransport(),
        ));
        $this->Email->emailFormat('html');
        $this->Email->from(array(Configure::read('Mailer.username') => 'noreply'));
        $this->Email->send();
    }

    public function getTransport()
    {
        return 'Smtp';
    }
}
