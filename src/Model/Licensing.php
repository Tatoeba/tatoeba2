<?php
namespace App\Model;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;

class Licensing {
    use \Cake\Datasource\ModelAwareTrait;

    private function create_licence_switch_list_for($user) {
        $list = $this->SentencesLists->createList(__('Sentences to switch to CC0'), $user->id);
        if ($list) {
            $user->settings = ['license_switch_list_id' => $list->id];
            $this->Users->save($user);
        }
        return $list;
    }

    private function get_license_switch_list_id($userId) {
        $this->loadModel('Users');
        $user = $this->Users->get($userId);

        $this->loadModel('SentencesLists');
        try {
            $listId = $user->settings['license_switch_list_id'];
            $list = $this->SentencesLists->get($listId);
        } catch (RecordNotFoundException $e) {
            $list = $this->create_licence_switch_list_for($user);
        } catch (InvalidPrimaryKeyException $e) {
            $list = $this->create_licence_switch_list_for($user);
        }

        return $list->id;
    }

    private function start_refresh_list($listId, $userId) {
        $this->loadModel('Queue.QueuedJobs');
        if (!$this->is_refreshing($userId)) {
            $this->QueuedJobs->createJob(
                'RefreshLicenseSwitchList',
                compact('listId', 'userId'),
                ['group' => $userId]
            );
        }
    }

    public function refresh_license_switch_list($currentUserId) {
        $listId = $this->get_license_switch_list_id($currentUserId);
        $this->start_refresh_list($listId, $currentUserId);
    }

    public function start_switch($userId, $lang) {
        $listId = $this->get_license_switch_list_id($userId);
        $options = array(
            'userId' => $userId,
            'dryRun' => false,
            'UIlang' => $lang,
            'listId' => $listId,
            'sendReport' => true,
        );
        return (bool)$this->QueuedJobs->createJob(
            'SwitchSentencesLicense',
            $options,
            ['group' => $userId]
        );
    }

    public function is_refreshing($userId) {
        $this->loadModel('Queue.QueuedJobs');
        $job = $this->QueuedJobs->find()
            ->where([
                'job_type' => 'RefreshLicenseSwitchList',
                'job_group' => $userId,
                'completed IS' => null,
            ])
            ->first();

        return (bool)$job;
    }

    public function is_switching($userId) {
        $this->loadModel('Queue.QueuedJobs');
        $job = $this->QueuedJobs->find()
            ->where([
                'job_type' => 'SwitchSentencesLicense',
                'job_group' => $userId,
                'completed IS' => null,
            ])
            ->first();

        return (bool)$job;
    }
}
