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
 * @link     https://tatoeba.org
 */
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\User;
use Cake\Controller\Component\AuthComponent;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Mailer\MailerAwareTrait;
use Cake\Routing\Router;


/**
 * Controller for users.
 *
 * @category Users
 * @package  Controllers
 * @author   HO Ngoc Phuong Trang <tranglich@gmail.com>
 * @license  Affero General Public License
 * @link     https://tatoeba.org
 */
class UsersController extends AppController
{
    use MailerAwareTrait;

    public $name = 'Users';
    public $helpers = array(
        'Html',
        'Form',
        'Date',
        'Logs',
        'Sentences',
        'Pagination'
    );
    public $components = array('Flash', 'RememberMe');

    /**
     * Before filter.
     *
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        // prevent CSRF in this controller
        // since we're handling login and registration
        $this->Security->validatePost = true;

        return parent::beforeFilter($event);
    }

    /**
     * Index of users. For admin only.
     *
     * @return void
     */
    public function index()
    {
        return $this->redirect(array('action' => 'all'));
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
        try {
            $user = $this->Users->get($id);
        } catch (RecordNotFoundException $e) {
            $this->Flash->set('Invalid User');
            return $this->redirect(array('action'=>'index'));
        }

        if (!empty($this->request->getData())) {

            $wasBlocked = $user->level == -1;
            $wasSuspended = $user->role == User::ROLE_SPAMMER;
            $isBlocked = !$wasBlocked && $this->request->getData('level') == -1;
            $isSuspended = !$wasSuspended && $this->request->getData('role') == User::ROLE_SPAMMER;

            $this->Users->patchEntity($user, $this->request->getData());
            if ($user = $this->Users->save($user)) {
                if ($isBlocked || $isSuspended) {
                    $this->getMailer('User')->send(
                        'blocked_or_suspended_user',
                        [$user, $isSuspended]
                    );
                }

                $this->Flash->set('The user information has been saved.');
            } else {
                $this->Flash->set(
                    'The user information could not be saved. Please try again.'
                );
            }
        }
        $groups = User::ALL_ROLES;
        $this->set(compact('groups', 'user'));
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
        try {
            $user = $this->Users->get($id);
            $this->Users->delete($user);
            $this->Flash->set('User deleted');
        } catch (RecordNotFoundException $e) {
            $this->Flash->set('Invalid id for User');
        }
        return $this->redirect(array('action'=>'index'));
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
        return $this->_common_login($this->Auth->redirectUrl());

    }


    /**
     * used by the element form
     *
     * @return void
     */
    public function check_login()
    {
        $user = $this->Auth->identify();

        $redirectParam = $this->request->getQuery(AuthComponent::QUERY_STRING_REDIRECT);
        $redirectUrl = $this->Auth->redirectUrl();
        $failedUrl = ['action' => 'login'];
        if (!is_null($redirectParam)) {
            $failedUrl['?'] = [AuthComponent::QUERY_STRING_REDIRECT => $redirectUrl];
        };

        if ($user) {
            if ($user['role'] == User::ROLE_INACTIVE) {
                $this->flash(
                    __(
                        'This account has been marked inactive. '.
                        'You cannot log in with it anymore. '.
                        'Please contact an admin if this is a mistake.', true
                    ),
                    $failedUrl
                );
            }
            else if ($user['role'] == User::ROLE_SPAMMER) {
                $this->flash(
                    __(
                        'This account has been marked as a spammer. '.
                        'You cannot log in with it anymore. '.
                        'Please contact an admin if this is a mistake.', true
                    ),
                    $failedUrl
                );
            } else {
                $this->Auth->setUser($user);
                return $this->_common_login($redirectUrl);
            }
        } else {
            if (empty($this->request->getData('username'))) {
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
        $userId = $this->Auth->user('id');

        // update the last login time
        $user = $this->Users->get($userId);
        $user->last_time_active = time();
        $this->Users->save($user);

        $plainTextPassword = $this->request->getData('password');
        $this->Users->updatePasswordVersion($userId, $plainTextPassword);

        if (empty($this->request->getData('rememberMe'))) {
            $this->RememberMe->delete();
        } else {
            $hashedPassword = $this->Users->get($userId, ['fields' => 'password'])->password;
            $this->RememberMe->remember(
                $this->request->getData('username'),
                $hashedPassword
            );
        }

        return $this->redirect($redirectUrl);
    }


    /**
     * Logout.
     *
     * @return void
     */
    public function logout()
    {
        $this->RememberMe->delete();
        $this->request->getSession()->delete('last_used_lang');
        return $this->redirect($this->Auth->logout());
    }


    /**
     * Register.
     *
     * @return void
     */
    public function register()
    {
        // Already logged in
        if ($this->Auth->User('id')) {
            return $this->redirect('/');
        }

        $newUser = $this->Users->newEntity();

        $honeypotTrapped = $this->request->getData('confirm') !== '';

        if ($this->request->is('post')) {
            $newUser = $this->Users->patchEntity(
                $newUser,
                $this->request->getData(),
                ['fields' => ['username', 'password', 'email']]
            );
            $newUser->since = date("Y-m-d H:i:s");
            $newUser->role = User::ROLE_CONTRIBUTOR;
            $newUser->audio_license = 'CC BY 4.0';

            if (!$honeypotTrapped
                && $this->request->getData('acceptation_terms_of_use')
                && $this->Users->save($newUser)
               ) {
                $this->loadModel('UsersLanguages');
                // Save native language
                $language = $this->request->getData('language');
                if (!empty($language)) {
                    $userLanguage = $this->UsersLanguages->newEntity([
                        'of_user_id' => $newUser->id,
                        'by_user_id' => $newUser->id,
                        'level' => 5,
                        'language_code' => $language
                    ]);
                    $this->UsersLanguages->save($userLanguage);
                }

                $user = $this->Auth->identify();
                $this->Auth->setUser($user);

                $profileUrl = Router::url(
                    array(
                        'controller' => 'user',
                        'action' => 'profile',
                        $this->Auth->user('username')
                    )
                );
                $this->loadModel('WikiArticles');
                $this->Flash->set(
                    '<p><strong>'
                    .__("Welcome to Tatoeba!")
                    .'</strong></p><p>'
                    .format(
                        __(
                            "To start things off, we encourage you to read our ".
                            "<a href='{url}'>Quick Start Guide</a>. If you want to read it later, ".
                            "you will find the link at the bottom of any page on the website.",
                            true
                        ),
                        array('url' => $this->WikiArticles->getWikiLink('quick-start'))
                    )
                    .'</p><p>'
                    .__("We hope you'll enjoy your time here with us!")
                    .'</p>'
                );

                return $this->redirect(
                    array(
                        'controller' => 'pages',
                        'action' => 'index'
                    )
                );
            } else {
                $this->Flash->set(__('Please fix the form errors.'));
            }
        }

        $this->set('user', $newUser);
        $this->set('language', $this->request->getData('language'));
    }


    /**
     * Get new password, for those who have forgotten their password.
     * TODO HACKISH FUNCTION
     *
     * @return void
     */
    public function new_password()
    {
        $sentEmail = $this->request->getData('email');
        if (!empty($sentEmail)) {
            $user = $this->Users->findByEmail($sentEmail)->first();

            // check if user exists, if so :
            if ($user) {
                $newPassword = $this->Users->generatePassword();
                $user->password = $newPassword;

                if ($this->Users->save($user)) { // if saved
                    $this->getMailer('User')->send(
                        'new_password',
                        [$user, $newPassword]
                    );

                    $flashMessage = format(
                        __('Your new password has been sent to {email}.'),
                        array('email' => $user->email)
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
                    ) . $sentEmail,
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
        $username = $this->request->getData('search_username');
        $userId = $this->Users->getIdFromUsername($username);

        if ($userId != null) {
            return $this->redirect(array("action" => "show", $userId));
        } else {
            $this->flash(
                __(
                    'No user with this username: ', true
                ).$username,
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
        if ($id == 'random') {
            $id = null;
        }

        $user = $this->Users->getUserByIdWithExtraInfo($id);

        if ($user != null) {
            $this->helpers[] = 'Wall';
            $this->helpers[] = 'Messages';
            $this->helpers[] = 'Members';

            $commentsPermissions = $this->Permissions->getCommentsOptions(
                $user->sentence_comments
            );

            $this->set('user', $user);
            $this->set('commentsPermissions', $commentsPermissions);
        } else {
            $this->request->getSession()->write('last_user_id', $id);
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

        $this->loadModel('LastContributions');
        $currentContributors = $this->LastContributions->getCurrentContributors();
        $total = $this->LastContributions->getTotal($currentContributors);

        $this->set('currentContributors', $currentContributors);
        $this->set('total', $total);

        $this->paginate = array(
            'limit' => 20,
            'order' => array('role' => 'desc', 'id' => 'asc'),
            'fields' => array('id', 'username', 'since', 'image', 'role'),
            'sort' => $this->request->getQuery('sort', 'role'),
            'direction' => $this->request->getQuery('direction', 'asc'),
        );

        $query = $this->Users->find()->where(['Users.role IN' => User::ROLE_CONTRIBUTOR_OR_HIGHER]);
        try {
            $users = $this->paginate($query);
        } catch (\Cake\Http\Exception\NotFoundException $e) {
            return $this->redirectPaginationToLastPage();
        }
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
        $this->viewBuilder()->setLayout('ajax');
        $user = $this->Users->getIdFromUsername($username);

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
        $this->viewBuilder()->setLayout('ajax');
        $userId = $this->Users->getIdFromEmail($email);

        if ($userId) {
            $this->set('data', true);
        } else {
            $this->set('data', false);
        }
    }


    public function for_language($lang = null)
    {
        $this->helpers[] = 'Members';

        $this->loadModel('UsersLanguages');
        $usersLanguages = $this->UsersLanguages->getNumberOfUsersForEachLanguage();

        if (empty($lang)) {
            $lang = $usersLanguages[0]->language_code;
        }

        $this->paginate = $this->UsersLanguages->getUsersForLanguage($lang);
        $users = $this->paginate('UsersLanguages');

        $this->set('users', $users);
        $this->set('usersLanguages', $usersLanguages);
        $this->set('lang', $lang);
    }

    public function login_dialog_template()
    {
        $redirectUrl = $this->request->getQuery('redirect');
        $this->set('redirectUrl', $redirectUrl);
        $this->viewBuilder()->enableAutoLayout(false);
    }
}
