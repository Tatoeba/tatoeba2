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
 * Controller for users.
 *
 * @category Users
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
class UsersController extends AppController
{
    public $persistentModel = true;
    public $name = 'Users';
    public $helpers = array(
        'Html',
        'Form',
        'Date',
        'Logs',
        'Sentences',
        'Navigation',
        'Pagination'
    );
    public $components = array('Flash', 'Mailer', 'RememberMe');

    public $uses = array("User","Contribution","UsersLanguages");

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        // setting actions that are available to everyone, even guests
        // no need to allow login
        $this->Auth->allowedActions = array(
            'all',
            'search',
            'show',
            'login',
            'check_login',
            'logout',
            'register',
            'new_password',
            'check_username',
            'check_email',
            'for_language'
        );
        // prevent CSRF in this controller
        // since we're handling login and registration
        $this->Security->validatePost = true;
    }

    /**
     * Index of users. For admin only.
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = array(
            'limit' => 50,
            'order' => 'group_id',
            'fields' => array(
                'id', 'email', 'username', 'since', 'level'
            ),
            'contain' => array(
                "Group" => array(
                    "fields" => "Group.name"
                )
            )
        );
        $this->set('users', $this->paginate());

    }


    /**
     * Edit user. Only for admin.
     *
     * @param int $id Id of user.
     *
     * @return void
     */
    public function edit($id = null)
    {
        $id = Sanitize::paranoid($id);

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash('Invalid User');
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->request->data)) {

            $wasBlocked = $this->User->getLevelOfUser($id) == -1;
            $wasSuspended = $this->User->getGroupOfUser($id) == 6;
            $isBlocked = !$wasBlocked && $this->request->data['User']['level'] == -1;
            $isSuspended = !$wasSuspended && $this->request->data['User']['group_id'] == 6;

            if ($this->User->save($this->request->data)) {
                $username = $this->request->data['User']['username'];
                if ($isBlocked || $isSuspended) {
                    $this->Mailer->sendBlockedOrSuspendedUserNotif(
                        $username, $isSuspended
                    );
                }

                $this->Session->setFlash('The user information has been saved.');
            } else {
                $this->Session->setFlash(
                    'The user information could not be saved. Please try again.'
                );
            }
        }
        if (empty($this->request->data)) {

            $this->request->data = $this->User->getUserById($id);
        }
        $groups = $this->User->Group->find('list');
        $this->set(compact('groups'));
    }


    /**
     * Delete user. Only for admin.
     *
     * @param int $id Id of user.
     *
     * @return void
     */
    public function delete($id = null)
    {
        $id = Sanitize::paranoid($id);

        if (!$id) {
            $this->Session->setFlash('Invalid id for User');
            $this->redirect(array('action'=>'index'));
        }
        if ($this->User->delete($id)) {
            $this->Session->setFlash('User deleted');
            $this->redirect(array('action'=>'index'));
        }
    }


    /**
     * Login.
     *
     * @return void
     */
    public function login()
    {
        /*maybe factor in _common login too*/
        if (!$this->Auth->user()) {
            return;
        }
        $this->_common_login($this->Auth->redirectUrl());

    }


    /**
     * used by the element form
     *
     * @return void
     */
    public function check_login()
    {
        $this->Auth->login();

        // group_id 5 => users is inactive
        if ($this->Auth->user('group_id') == 5) {
            $this->flash(
                __(
                    'This account has been marked inactive. '.
                    'You cannot log in with it anymore. '.
                    'Please contact an admin if this is a mistake.', true
                ),
                '/users/logout/'
            );
        }
        // group_id 6 => users is spammer
        else if ($this->Auth->user('group_id') == 6) {
            $this->flash(
                __(
                    'This account has been marked as a spammer. '.
                    'You cannot log in with it anymore. '.
                    'Please contact an admin if this is a mistake.', true
                ),
                '/users/logout/'
            );
        }
        else
        {
            $redirectUrl = $this->Auth->redirectUrl();
            if (isset($this->request->query['redirectTo'])) {
                $redirectUrl = $this->request->query['redirectTo'];
            }
            $failedUrl = array(
                'action' => 'login',
                '?' => array('redirectTo' => $redirectUrl)
            );
            if ($this->Auth->user()) {
                $this->_common_login($redirectUrl);
            } elseif (empty($this->request->data["User"]['username'])) {
                $this->flash(
                    __(
                        'You must fill in your '.
                        'username and password.', true
                    ),
                    $failedUrl
                );
            } else {
                $this->flash(
                    __(
                        'Login failed. Make sure that your Caps Lock '.
                        'and Num Lock are not unintentionally turned on. '.
                        'Your password is case-sensitive.', true
                    ),
                    $failedUrl
                );
            }
        }
    }

    /**
     * Used by the login functions
     *
     * @param mixed $redirectUrl URL to which user is redirected after logged in.
     *
     * @return void
     */

    private function _common_login($redirectUrl)
    {
        // update the last login time
        $data['User']['username'] = $this->Auth->user('username');
        $data['User']['last_time_active'] = time();
        $this->User->save($data);

        if (empty($this->request->data['User']['rememberMe'])) {
            $this->RememberMe->delete();
        } else {
            $this->RememberMe->remember(
                $this->request->data['User']['username'], $this->request->data['User']['password']
            );
        }

        $this->redirect($redirectUrl);
    }


    /**
     * Logout.
     *
     * @return void
     */
    public function logout()
    {
        $this->RememberMe->delete();
        $this->Session->delete('last_used_lang');
        $this->redirect($this->Auth->logout());
    }


    /**
     * Register.
     *
     * @return void
     */
    public function register()
    {
        // --------------------------------------------------
        //   Cases where registration shouldn't work.
        // --------------------------------------------------

        // Already logged in
        if ($this->Auth->User('id')) {
            $this->redirect('/');
        }

        // No data
        if (empty($this->request->data)) {
            $this->set('username', null);
            $this->set('email', null);
            $this->set('language', null);
            return;
        }

        $this->set('username', $this->request->data['User']['username']);
        $this->set('email', $this->request->data['User']['email']);
        $this->set('language', $this->request->data['User']['language']);

        // Username does not fit requirements
        if (!$this->User->validates()) {
            $this->request->data['User']['password'] = '';
            $this->request->data['User']['quiz'] = '';
            return;
        }

        // Password is empty
        $emptyPasswordMd5 = md5(Configure::read('Security.salt'));
        if ($this->request->data['User']['password'] == ''
            || $this->request->data['User']['password'] == $emptyPasswordMd5
        ) {
            $this->Session->setFlash(
                __('Password cannot be empty.')
            );
            $this->request->data['User']['password'] = '';
            $this->request->data['User']['quiz'] = '';
            return;
        }

        // Did not answer the quiz properly
        $correctAnswer = mb_substr($this->request->data['User']['email'], 0, 5, 'UTF-8');
        if ($this->request->data['User']['quiz'] != $correctAnswer) {
            $this->Session->setFlash(
                __('Wrong answer to the question.')
            );
            $this->request->data['User']['password'] = '';
            $this->request->data['User']['quiz'] = '';
            return;
        }

        // Did not accept terms of use
        if (!$this->request->data['User']['acceptation_terms_of_use']) {
            $this->Session->setFlash(
                __('You did not accept the terms of use.')
            );
            $this->request->data['User']['password'] = '';
            $this->request->data['User']['quiz'] = '';
            return;
        }

        // --------------------------------------------------


        // At this point, we're fine, so we can create the user
        $this->User->create();
        $allowedFields = array('username', 'password', 'email');
        $newUser = $this->filterKeys($this->request->data['User'], $allowedFields);
        $newUser['password'] = Security::hash($newUser['password'], 'md5', Configure::read('Security.salt'));
        $newUser['since']    = date("Y-m-d H:i:s");
        $newUser['group_id'] = 4;

        // And we save
        if ($this->User->save($newUser)) {
            // Save native language
            $language = $this->request->data['User']['language'];
            if (!empty($language) && $language != 'none') {
                $userLanguage = array(
                    'of_user_id' => $this->User->id,
                    'by_user_id' => $this->User->id,
                    'level' => 5,
                    'language_code' => $language
                );
                $this->UsersLanguages->save($userLanguage);
            }

            $this->Auth->login($newUser);

            $profileUrl = Router::url(
                array(
                    'controller' => 'user',
                    'action' => 'profile',
                    $this->Auth->user('username')
                )
            );
            $this->Flash->set(
                '<p><strong>'
                .__("Welcome to Tatoeba!")
                .'</strong></p><p>'
                .format(
                    __(
                        "To start things off, we encourage you to go to your ".
                        "<a href='{url}'>profile</a> and let us know which ".
                        "languages you speak or are interested in.",
                        true
                    ),
                    array('url' => $profileUrl)
                )
                .'</p>'
            );

            $this->redirect(
                array(
                    'controller' => 'pages',
                    'action' => 'index'
                )
            );
        }

    }


    /**
     * Get new password, for those who have forgotten their password.
     * TODO HACKISH FUNCTION
     *
     * @return void
     */
    public function new_password()
    {
        if (!empty($this->request->data)) {
            $user = $this->User->findByEmail($this->request->data['User']['email']);

            // check if user exists, if so :
            if ($user) {
                $newPassword = $this->User->generatePassword();

                // data to save
                $updatePasswordData = array(
                    'id' => $user['User']['id'],
                    'password' => $this->Auth->password($newPassword)
                );

                if ($this->User->save($updatePasswordData)) { // if saved
                    // prepare message
                    $this->Mailer->sendNewPassword(
                        $this->request->data['User']['email'],
                        $user['User']['username'],
                        $newPassword
                    );

                    $flashMessage = format(
                        __('Your new password has been sent to {email}.'),
                        array('email' => $this->request->data['User']['email'])
                    );
                    $flashMessage .= "<br/>";
                    $flashMessage .= __(
                        'You may need to check your spam folder '.
                        'to find this message.', true
                    );
                    $this->flash($flashMessage, '/users/login');
                }
            } else {
                $this->flash(
                    __(
                        'There is no registered user with this email address: ', true
                    ) . $this->request->data['User']['email'],
                    '/users/new_password'
                );
            }
        }
    }

    /**
     * Search for user given a username.
     *
     * @return void
     */
    public function search()
    {
        $userId = $this->User->getIdFromUsername($this->request->data['User']['username']);

        if ($userId != null) {
            $this->redirect(array("action" => "show", $userId));
        } else {
            $this->flash(
                __(
                    'No user with this username: ', true
                ).$this->request->data['User']['username'],
                '/users/all/'
            );
        }
    }


    /**
     * Display information about a user.
     * NOTE : This should not be used anymore in the future.
     * We'll use user/profile/$username instead.
     *
     * @param int|string $id Id of user. For random user, parameter is 'random'.
     *
     * @return void
     */
    public function show($id)
    {
        $id = Sanitize::paranoid($id);

        if ($id == 'random') {
            $id = null;
        }

        $user = $this->User->getUserByIdWithExtraInfo($id);

        if ($user != null) {
            $this->helpers[] = 'Wall';
            $this->helpers[] = 'Messages';
            $this->helpers[] = 'Members';

            $commentsPermissions = $this->Permissions->getCommentsOptions(
                $user['SentenceComments']
            );

            $this->set('user', $user);
            $this->set('commentsPermissions', $commentsPermissions);
        } else {
            $this->Session->write('last_user_id', $id);
            $this->flash(
                format(
                    __('No user with this ID: {id}'),
                    array('id' => $id)
                ),
                '/users/all/'
            );
        }
    }

    /**
     * Display list of all members.
     *
     * @return void
     */
    public function all()
    {
        $this->helpers[] = 'Members';

        $this->loadModel('LastContribution');
        $currentContributors = $this->LastContribution->getCurrentContributors();
        $total = $this->LastContribution->getTotal($currentContributors);

        $this->set('currentContributors', $currentContributors);
        $this->set('total', $total);

        $this->paginate = array(
            'limit' => 20,
            'order' => 'group_id',
            'fields' => array('username', 'since', 'image', 'group_id'),
        );

        $users = $this->paginate(array('User.group_id < 5'));
        $this->set('users', $users);
    }


    /**
     * Check if the username already exist or not.
     *
     * @param string $username Username to check.
     *
     * @return void
     */
    public function check_username($username)
    {
        $this->layout = null;
        $user = $this->User->getIdFromUsername($username);

        if ($user) {
            $this->set('data', true);
        } else {
            $this->set('data', false);
        }
    }


    /**
     * Check if the email already exist or not.
     *
     * @param string $email Email to check.
     *
     * @return void
     */
    public function check_email($email)
    {
        $this->layout = null;
        $userId = $this->User->getIdFromEmail($email);

        if ($userId) {
            $this->set('data', true);
        } else {
            $this->set('data', false);
        }
    }


    public function for_language($lang = null)
    {
        $this->helpers[] = 'Members';

        $usersLanguages = $this->UsersLanguages->getNumberOfUsersForEachLanguage();

        if (empty($lang)) {
            $lang = $usersLanguages[0]['UsersLanguages']['language_code'];
        }

        $this->paginate = $this->UsersLanguages->getUsersForLanguage($lang);
        $users = $this->paginate('UsersLanguages');

        $this->set('users', $users);
        $this->set('usersLanguages', $usersLanguages);
        $this->set('lang', $lang);
    }
}
