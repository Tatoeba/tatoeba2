<?php
namespace App\Model;

class Licensing {
    use \Cake\Datasource\ModelAwareTrait;

    private function get_license_switch_list_id($userId) {
        $this->loadModel('Users');
        $user = $this->Users->get($userId);
        if (!$user->settings['license_switch_list_id']) {
            $this->loadModel('SentencesLists');
            $list = $this->SentencesLists->createList(__('Sentences to switch to CC0'), $userId);
            if ($list) {
                $user->settings = ['license_switch_list_id' => $list->id];
                $this->Users->save($user);
            }
        }
        return $user->settings['license_switch_list_id'];
    }

    private function start_refresh_list($listId, $userId) {
        $this->loadModel('Queue.QueuedJobs');
        $currentJob = $this->QueuedJobs->find()
            ->where([
                'job_type' => 'RefreshLicenseSwitchList',
                'job_group' => $userId,
            ])
            ->first();

        if (!$currentJob || $currentJob->completed) {
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
}
