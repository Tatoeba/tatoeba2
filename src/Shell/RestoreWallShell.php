<?php
/**
 *  Tatoeba Project, free collaborative creation of languages corpuses project
 *  Copyright (C) 2015  Gilles Bedel
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


class RestoreWallShell extends Shell {

    public $uses = array('Wall', 'User', 'WallThread');

    private $users_id_by_username = array();
    private $imported = array();
    private $stderr = null;

    private function get_all_users() {
        $users = $this->User->find('all', array('recursive' => 0, 'fields' => array('id', 'username')));
        $this->users_id_by_username = Set::combine($users, '{n}.User.username', '{n}.User.id');
    }

    private function read_one_line($handle) {
        $line = '';
        $is_multiline = false;
        do {
            $red = fgets($handle);
            if (!$red) {
                break;
            }
            $i = strlen($red) - 1;
            $i--; // step over \n
            $is_multiline = false;
            while ($i >= 0 && $red[$i] == '\\') {
                $i--;
                $is_multiline = !$is_multiline;
            }
            if (!$is_multiline) {
                $red = rtrim($red, "\n");
            }
            $line .= $red;
        } while ($is_multiline);
        return $line;
    }

    private function parse_line($line) {
        // explode() on tabs while ignoring backslashed tabs
        $data = preg_split('~(?<!\\\)' . "\t" . '~', $line);
        return array_map(function($v) {
            if ($v == '\N') {
                return null;
            } else {
                return preg_replace('/\\\(.|\n)/', '$1', $v);
            }
        }, $data);
    }

    private function read_csv_message($handle) {
        $line = $this->read_one_line($handle);
        if (empty($line)) {
            return false;
        }

        $data = $this->parse_line($line);

        list($id, $username, $parent_id, $date, $text) = $data;
        if ($username && isset($this->users_id_by_username[$username])) {
            $owner_id = $this->users_id_by_username[$username];
        } else {
            $owner_id = null;
        }

        if ($parent_id && !isset($this->imported[$parent_id])) {
            // Can't add a message without a valid parent
            fwrite($this->stderr, $line."\n");
            return null;
        }

        return array(
            'id' => $id,
            'owner' => $owner_id,
            'date' => $date,
            'modified' => $date,
            'title' => '',
            'content' => $text,
            'parent_id' => $parent_id,
        );
    }

    private function save_message($message) {
        // Hack to make the Tree behavior assume it's a creation while forcing the id
        $id = $message['id'];
        unset($message['id']);
        $this->Wall->create();
        $is_saved = (bool)$this->Wall->save($message);
        $is_saved = $is_saved && $this->Wall->query("UPDATE {$this->Wall->table} SET id = {$id} WHERE id = {$this->Wall->id}");

        if ($is_saved) {
            $root_id = is_null($message['parent_id']) ? $id : $message['parent_id'];
            assert((bool)$this->WallThread->save(array(
                'id' => $root_id,
                'last_message_date' => $message['date']
            )));
            $this->imported[$id] = 1;
        } else {
            print("Unable to save the following message:\n");
            var_dump($message);
        }

        return $is_saved;
    }

    public function main() {
        $this->get_all_users();
        $stdin = fopen('php://stdin', 'r');
        $this->stderr = fopen('php://stderr', 'w+');
        $nb_messages = 0;
        $nb_ignored = 0;
        while (!feof($stdin)) {
            $message = $this->read_csv_message($stdin);
            if (is_array($message)) {
                $nb_messages += $this->save_message($message);
            }
            echo ".";
            $nb_ignored += is_null($message);
        }
        fclose($stdin);
        fclose($this->stderr);

        // Restore mysql autoincrement number because of the insert hack
        $next_id = $this->Wall->query("SELECT MAX(id)+1 as v FROM {$this->Wall->table}");
        $next_id = $next_id[0][0]['v'];
        $this->Wall->query("ALTER TABLE {$this->Wall->table} AUTO_INCREMENT = $next_id");

        echo "\n$nb_messages message(s) imported, $nb_ignored message(s) discarded because their parent does not exist\n";
    }
}
