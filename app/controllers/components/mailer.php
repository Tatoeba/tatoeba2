<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Tatoeba
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */

/**
 * Component for mails.
 *
 * @category Default
 * @package  Components
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class MailerComponent extends Object
{
    public $components = array('Email');

    public $from     = 'trang.dictionary.project@gmail.com';
    public $fromName = 'Tatoeba (no-reply)';
    public $to       = null;
    public $toName   = null;
    public $subject  = null;
    public $message  = null;

    /**
     * Send email.
     *
     * @return void
     */
    public function send()
    {
        /* email notification */
        $this->_authgMail(
            $this->from,
            $this->fromName,
            $this->to,
            $this->toName,
            $this->subject,
            $this->message
        );
    }

    /**
     * Send via Gmail.
     *
     * @todo We can delete this and use the Email component instead
     *
     * @param string $from     Email of sender.
     * @param string $namefrom Name of sender.
     * @param string $to       Email of recipient.
     * @param string $nameto   Name of recipient.
     * @param string $subject  Subject.
     * @param string $message  Message.
     *
     * @return array
     */
    private function _authgMail($from, $namefrom, $to, $nameto, $subject, $message)
    {
        if (Configure::read('Mailer.enabled') == false) {
            return;
        }

        /*  your configuration here  */

        $smtpServer = "tls://smtp.gmail.com"; //does not accept STARTTLS
        $port = "465"; // try 587 if this fails
        $timeout = "45"; //typical timeout. try 45 for slow servers
        $username = Configure::read('Mailer.username'); //your gmail account
        $password = Configure::read('Mailer.password'); //the pass for your gmail
        $localhost = $_SERVER['REMOTE_ADDR']; //requires a real ip
        $newLine = "\r\n"; //var just for newlines

        /*  you shouldn't need to mod anything else */

        //connect to the host and port
        $smtpConnect = fsockopen($smtpServer, $port, $errno, $errstr, $timeout);
        //echo $errstr." - ".$errno;
        $smtpResponse = fgets($smtpConnect, 4096);
        if (empty($smtpConnect)) {
            $output = "Failed to connect: $smtpResponse";
            //echo $output;
            return $output;
        } else {
            $logArray['connection'] = "Connected to: $smtpResponse";
            //echo "connection accepted<br>".$smtpResponse."<p />Continuing<p />";
        }

        //you have to say HELO again after TLS is started
        fputs($smtpConnect, "HELO $localhost". $newLine);
        $smtpResponse = fgets($smtpConnect, 4096);
        $logArray['heloresponse2'] = "$smtpResponse";

        //request for auth login
        fputs($smtpConnect, "AUTH LOGIN" . $newLine);
        $smtpResponse = fgets($smtpConnect, 4096);
        $logArray['authrequest'] = "$smtpResponse";

        //send the username
        fputs($smtpConnect, base64_encode($username) . $newLine);
        $smtpResponse = fgets($smtpConnect, 4096);
        $logArray['authusername'] = "$smtpResponse";

        //send the password
        fputs($smtpConnect, base64_encode($password) . $newLine);
        $smtpResponse = fgets($smtpConnect, 4096);
        $logArray['authpassword'] = "$smtpResponse";

        //email from
        fputs($smtpConnect, "MAIL FROM: <$from>" . $newLine);
        $smtpResponse = fgets($smtpConnect, 4096);
        $logArray['mailfromresponse'] = "$smtpResponse";

        //email to
        fputs($smtpConnect, "RCPT TO: <$to>" . $newLine);
        $smtpResponse = fgets($smtpConnect, 4096);
        $logArray['mailtoresponse'] = "$smtpResponse";

        //the email
        fputs($smtpConnect, "DATA" . $newLine);
        $smtpResponse = fgets($smtpConnect, 4096);
        $logArray['data1response'] = "$smtpResponse";

        //construct headers
        $subject  = mb_encode_mimeheader($subject);
        $nameto   = mb_encode_mimeheader($nameto);
        $namefrom = mb_encode_mimeheader($namefrom);
        $headers = "MIME-Version: 1.0" . $newLine;
        $headers .= "Content-type: text/plain; charset=UTF-8" . $newLine;
        $headers .= "To: $nameto <$to>" . $newLine;
        $headers .= "From: $namefrom <$from>" . $newLine;
        $headers .= "Subject: $subject" . $newLine;

        //observe the . after the newline, it signals the end of message
        fputs($smtpConnect, "$headers\r\n\r\n$message\r\n.\r\n");
        $smtpResponse = fgets($smtpConnect, 4096);
        $logArray['data2response'] = "$smtpResponse";

        // say goodbye
        fputs($smtpConnect, "QUIT" . $newLine);
        $smtpResponse = fgets($smtpConnect, 4096);
        $logArray['quitresponse'] = "$smtpResponse";
        $logArray['quitcode'] = substr($smtpResponse, 0, 3);
        fclose($smtpConnect);
        //a return value of 221 in $retVal["quitcode"] is a success
        return($logArray);
    }


    public function sendBlockedOrSuspendedUserNotif(
        $username, $isSuspended
    ) {
        $this->Email->to = 'community-admins@tatoeba.org';
        $this->Email->subject = '( ! ) ' . $username;
        $this->Email->template = 'blocked_or_suspended_user';

        $User = ClassRegistry::init('User');
        $Contribution = ClassRegistry::init('Contribution');
        $userId = $User->getIdFromUsername($username);
        $suspendedUsers = $User->getUsersWithSamePassword($userId);
        $ips = $Contribution->getLastContributionOf($userId);

        $this->set('admin', CurrentUser::get('username'));
        $this->set('user', $username);
        $this->set('userId', $userId);
        $this->set('isSuspended', $isSuspended);
        $this->set('suspendedUsers', $suspendedUsers);
        $this->set('ips', $ips);

        $this->_send();
    }


    public function sendPmNotification($pm, $id)
    {
        $User = ClassRegistry::init('User');
        $recipientEmail = $User->getEmailFromId($pm['recpt']);
        $sender = $User->getUsernameFromId($pm['sender']);
        $title = $pm['title'];
        $content = $pm['content'];

        $this->Email->to = $recipientEmail;
        $this->Email->subject = 'Tatoeba PM - ' . $title;
        $this->Email->template = 'new_private_message';

        $this->set('sender', $sender);
        $this->set('title', $title);
        $this->set('message', $content);
        $this->set('messageId', $id);

        $this->_send();
    }


    private function set($key, $value)
    {
        $this->Email->Controller->set($key, $value);
    }

    private function _send()
    {
        $this->Email->smtpOptions = array(
            'port' => '465',
            'timeout' => '45',
            'host' => 'ssl://smtp.gmail.com',
            'username' => Configure::read('Mailer.username'),
            'password' => Configure::read('Mailer.password'),
        );
        $this->Email->delivery = 'smtp';
        $this->Email->sendAs = 'html';
        $this->Email->from = $this->fromName;
        $this->Email->send();
    }
}
?>
