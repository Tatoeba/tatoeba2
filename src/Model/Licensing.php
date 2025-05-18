<?php
namespace App\Model;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;

class Licensing {
    use \Cake\Datasource\ModelAwareTrait;

    private function createLicenseSwitchList($user) {
        $list = $this->SentencesLists->createList(__('Sentences to switch to CC0'), $user->id);
        if ($list) {
            $user->settings = ['license_switch_list_id' => $list->id];
            $this->Users->save($user);
        }
        return $list;
    }

    public function getLicenseSwitchListId($userId) {
        $this->loadModel('Users');
        $user = $this->Users->get($userId);

        $this->loadModel('SentencesLists');
        try {
            $listId = $user->settings['license_switch_list_id'];
            $list = $this->SentencesLists->get($listId);
        } catch (RecordNotFoundException $e) {
            $list = $this->createLicenseSwitchList($user);
        } catch (InvalidPrimaryKeyException $e) {
            $list = $this->createLicenseSwitchList($user);
        }

        return $list->id;
    }

    private function startListRefresh($listId, $userId) {
        $this->loadModel('Queue.QueuedJobs');
        if (!$this->is_refreshing($userId)) {
            $this->QueuedJobs->createJob(
                'RefreshLicenseSwitchList',
                compact('listId', 'userId'),
                ['group' => $userId]
            );
            $this->QueuedJobs->wakeUpWorkers();
        }
    }

    public function refreshLicenseSwitchList($currentUserId) {
        $listId = $this->getLicenseSwitchListId($currentUserId);
        $this->startListRefresh($listId, $currentUserId);
    }

    public function startLicenseSwitch($userId, $locale) {
        $listId = $this->getLicenseSwitchListId($userId);
        $options = array(
            'userId' => $userId,
            'locale' => $locale,
            'listId' => $listId,
            'sendReport' => true,
        );
        $ok = (bool)$this->QueuedJobs->createJob(
            'SwitchSentencesLicense',
            $options,
            ['group' => $userId]
        );
        if ($ok) {
            $this->QueuedJobs->wakeUpWorkers();
        }
        return $ok;
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
