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
    public $name = 'Users';
    public $helpers = array(
        'Html', 'Form', 'Date', 'Logs', 'Sentences', 'Navigation'
    );
    public $components = array ('Mailer', 'Captcha', 'RememberMe');
    public $paginate = array(
        'limit' => 20,
        'order' => array('last_time_active' => 'desc'),
        'contain' => array(
            "Group" => array(
                "fields" => "Group.name"
            )
        )
    );

    public $uses = array("User","Contribution");
    
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
            'check_login',
            'logout',
            'register',
            'new_password', 
            'confirm_registration',
            'resend_registration_mail',
            'captcha_image', 
            'check_username',
            'check_email'
        );
        //$this->Auth->allowedActions = array('*');
    }

    /**
     * Index of users. For admin only.
     * 
     * @return void
     */
    public function index()
    {
        $this->set('users', $this->paginate());

        // Uncomment this when you want to update users permissions.
        $this->_init_db();
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
        if (!$id && empty($this->data)) {
            $this->Session->setFlash('Invalid User');
            $this->redirect(array('action'=>'index'));
        }
        if (!empty($this->data)) {
            if ($this->User->save($this->data)) {
                // update aro table
                $aro = new Aro();
                $data = $aro->find(
                    "first", array(
                        "conditions" => array(
                            "foreign_key" => $this->data['User']['id'], 
                            "model" => "User"
                        )
                    )
                );
                $data['Aro']['parent_id'] = $this->data['User']['group_id'];
                $this->Acl->Aro->save($data);

                $this->Session->setFlash('The User has been saved');
                $this->redirect(array('action'=>'index'));
            } else {
                $this->Session->setFlash(
                    'The User could not be saved. Please, try again.'
                );
            }
        }
        if (empty($this->data)) {
            $this->data = $this->User->getUserById($id);
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
        if (!$id) {
            $this->Session->setFlash('Invalid id for User');
            $this->redirect(array('action'=>'index'));
        }
        if ($this->User->del($id)) {
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

        $this->_common_login();

    }


    /**
     * used by the element form
     *
     * @return void
     */
    public function check_login()
    {
        $this->Auth->login($this->data);


        if (!$this->Auth->user()) {
            /*TODO add a flash message "login failed" */
            $this->redirect();
        }

        $this->_common_login();

        if ( isset($_POST["redirectTo"])) {
            $this->redirect($_POST["redirectTo"]);
        } else {
            $this->redirect($this->Auth->redirect());
        }

    }

    /**
     * Used by the login functions
     *
     * @return void
     */

    private function _common_login()
    {


        // update the last login time
        $data['User']['id'] = $this->Auth->user('id');
        $data['User']['last_time_active'] = time();
        $this->User->save($data);

        // group_id 5 is the group of users who haven't valided their account
        if ($this->Auth->user('group_id') == 5) {
            $this->flash(
                __(
                    'Your account is not validated yet. You will not be able to '.
                    'add sentences, translate or post comments. To validate it, '.
                    'click on the link in the email that has been sent to you '.
                    'during your registration. You can have this email resent to '.
                    'you.', true
                ), 
                '/users/resend_registration_mail/'
            );
        }

        if (empty($this->data['User']['rememberMe'])) {
            $this->RememberMe->delete();
        } else {
            $this->RememberMe->remember(
                $this->data['User']['username'], $this->data['User']['password']
            );
        }
        
        $this->redirect($this->Auth->redirect());
    }


    /**
     * Logout.
     * 
     * @return void
     */
    public function logout()
    {
        $this->RememberMe->delete();
        $this->redirect($this->Auth->logout());
    }


    /**
     * Register.
     * 
     * @return void
     */
    public function register()
    {
        if ($this->Auth->User('id')) {
            $this->redirect('/');
        }
        if (!empty($this->data)) {
            $this->User->create();
            $this->data['User']['since'] = date("Y-m-d H:i:s");
            $this->data['User']['group_id'] = User::LOWEST_TRUST_GROUP_ID + 1;
            $nonHashedPassword = $this->data['User']['password'];

            $this->User->set($this->data);
            if (!$this->data['User']['acceptation_terms_of_use']) {
                $this->Session->setFlash(
                    __('You did not accept the terms of use.', true)
                );
                $this->data['User']['password'] = '';
                $this->data['User']['captcha'] = '';
            } else {
                if ($this->User->validates()) {
                    if ($this->Captcha->check($this->data['User']['captcha'], true) 
                        AND $this->User->save($this->data)
                    ) {
                        $pass = $this->Auth->password(
                            $this->data['User']['password']
                        );
                        $token = $this->Auth->password(
                            $pass.$this->data['User']['since']
                            .$this->data['User']['username']
                        );
                        // prepare message
                        $subject = __('Tatoeba registration', true);
                        $message = sprintf(
                            __('Dear %s,', true), $this->data['User']['username']
                        )
                        . "\n\n"
                        . __(
                            'Welcome to Tatoeba and thank you for your '.
                            'interest in this project!', true
                        )
                        . "\n\n"
                        . __(
                            'You can validate your registration by clicking '.
                            'on this link :', true
                        )
                        . "\n"
                        . 'http://' . $_SERVER['HTTP_HOST'] 
                        . '/users/confirm_registration/' 
                        . $this->User->id . '/' . $token;

                        // send email with new password
                        $this->Mailer->to = $this->data['User']['email'];
                        $this->Mailer->toName = '';
                        $this->Mailer->subject = $subject;
                        $this->Mailer->message = $message;
                        $this->Mailer->send();

                        $this->flash(
                            __(
                                'Thank you for registering. To validate your '.
                                'registration, click on the link in the email '.
                                'that has been sent to you.', true
                            ),
                            '/users/resend_registration_mail'
                        );
                    } else {
                        $this->data['User']['password'] = '';
                        $this->data['User']['captcha'] = '';
                        $this->Session->setFlash(
                            __(
                                'The code you entered did not match with the '.
                                'image, please try again.', true
                            )
                        );
                    }
                } else {
                    $this->data['User']['password'] = '';
                    $this->data['User']['captcha'] = '';
                }
            }

        }
    }

    /**
     * Send registration email again.
     * 
     * @return void
     */
    public function resend_registration_mail()
    {
        if (!empty($this->data)) {
            $user = $this->User->findByEmail($this->data['User']['email']);

            if ($user) {
                if ($user['User']['group_id'] == 5) {
                    $toHash = $this->Auth->password(
                        $user['User']['password']
                    ).$user['User']['since']
                    .$user['User']['username'];
                    
                    $token = $this->Auth->password($toHash);

                    // prepare message
                    $subject = __('Tatoeba registration', true);
                    $message = sprintf(
                        __('Dear %s,', true), $user['User']['username']
                    ). "\n\n"
                    . __(
                        'Welcome to Tatoeba and thank you for your interest '.
                        'in this project!', true
                    )
                    . "\n\n"
                    . __(
                        'You can validate your registration by clicking on '.
                        'this link :', true
                    )
                    . "\n"
                    . 'http://' . $_SERVER['HTTP_HOST'] 
                    . '/users/confirm_registration/' . $user['User']['id'] 
                    . '/' . $token;

                    // send email with new password
                    $this->Mailer->to = $user['User']['email'];
                    $this->Mailer->toName = '';
                    $this->Mailer->subject = $subject;
                    $this->Mailer->message = $message;
                    $this->Mailer->send();

                    $this->Session->setFlash(
                        __('The registration email has been resent.', true)
                    );
                    $this->redirect($this->Auth->logout());
                } else {
                    $this->flash(
                        __(
                            'Your registration has already been validated. '.
                            'Try to login again.', true
                        ),
                        '/users/resend_registration_mail'
                    );
                }
            } else {
                $this->flash(
                    __('There is no user with this email : ', true)
                    .$user['User']['email'],
                    '/users/resend_registration_mail'
                );
            }
        }
    }


    /**
     * Validation of registration.
     *
     * @param int    $id    Id of the user.
     * @param string $token Validation token.
     * 
     * @return void
     */
    public function confirm_registration($id, $token)
    {
        $this->User->id = $id; // important for when we do saveField() later
        $user = $this->User->getUserById($id);

        $toHash = $this->Auth->password(
            $user['User']['password']
        ).$user['User']['since'].$user['User']['username'];
        $correctToken = $this->Auth->password($toHash);

        if ($user['User']['group_id'] > 0 
            AND $user['User']['group_id'] < User::LOWEST_TRUST_GROUP_ID + 1
        ) {
        
            $msg = __('Your registration is already validated.', true);
            
        } else if ($token == $correctToken) {
        
            if ($this->User->saveField('group_id', User::LOWEST_TRUST_GROUP_ID)) {
                // update aro table
                $aro = new Aro();
                $data = $aro->find(
                    "first", 
                    array(
                        "conditions" => array(
                            "foreign_key" => $id, "model" => "User"
                        )
                    )
                );
                $data['Aro']['parent_id'] = User::LOWEST_TRUST_GROUP_ID;
                $this->Acl->Aro->save($data);

                $msg = __('Your registration has been validated.', true);
            } else {
                $msg = sprintf(
                    __(
                        'A problem occured. Your registration could not be '.
                        'validated. Please <a href="%s">contact us</a> to '.
                        'report the problem.', true
                    ),
                    '/' . $this->params['lang'] . '/pages/contact'
                );
            }
            
        } else {
        
            $msg = __('Non valid registration link.', true);

        }

        $this->flash($msg, '/users/login');
    }


    /**
     * Get new password, for those who have forgotten their password.
     * 
     * @return void
     */
    public function new_password()
    {
        if (!empty($this->data)) {
            $user = $this->User->findByEmail($this->data['User']['email']);

            // check if user exists, if so :
            if ($user) {
                $newPassword = $this->User->generatePassword();

                // data to save
                $this->data['User']['id'] = $user['User']['id'];
                $this->data['User']['password'] = $this->Auth->password(
                    $newPassword
                );

                if ($this->User->save($this->data)) { // if saved
                    // prepare message
                    $subject = __('Tatoeba, new password', true);
                    $message = __('Your login : ', true)
                        . $user['User']['username']
                        . "\n"
                        . __('Your new password : ', true)
                        . $newPassword;

                    // send email with new password
                    $this->Mailer->to = $this->data['User']['email'];
                    $this->Mailer->toName = '';
                    $this->Mailer->subject = $subject;
                    $this->Mailer->message = $message;
                    $this->Mailer->send();

                    $this->flash(
                        __(
                            'Your new password has been sent at ', true
                        ) . $this->data['User']['email'],
                        '/users/login'
                    );
                }
            } else {
                $this->flash(
                    __(
                        'There is no registered user with this email : ', true
                    ) . $this->data['User']['email'],
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
        Sanitize::html($this->data['User']['username']);
        $user = $this->User->findByUsername($this->data['User']['username']);
        if ($user != null) {
            $id = ($user['User']['id'] < 1) ? 1 : $user['User']['id'];
            $this->redirect(array("action" => "show", $id));
        } else {
            $this->flash(
                __(
                    'No user with this username : ', true
                ).$this->data['User']['username'], 
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
        
        $user = $this->User->getUserById($id);

        if ($user != null) {
            $this->set('user', $user);
        } else {
            $this->Session->write('last_user_id', $id);
            $this->flash(__('No user with this id : ', true).$id, '/users/all/');
        }
    }

    /**
     * Display list of all members.
     * 
     * @return void
     */
    public function all()
    {
        $topContributors = $this->Contribution->getTopContributors(20);
        
        // present result in a nicer array
        foreach ( $topContributors as $i=>$contributor) {
            $topContributors[$i] = array (
                'numberOfContributions' => $contributor[0]['total'],
                'userName' => $contributor['User']['username'],
                'group_id' => $contributor['User']['group_id']
            );
        }

        
        $this->set('topContributors', $topContributors);

        $users = $this->paginate(array('User.group_id < 5'));
        
        $this->set('users', $users);
    }

    /**
     * CAPTCHA image for registration.
     * 
     * @return void
     */
    public function captcha_image()
    {
        Configure::write('debug', 0); // NOTE: It's normally not good to set debug
                                   // in controllers, but here we really need to
                                   // have debug set to 0
        $this->layout = null;
        $this->Captcha->image();
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
        Sanitize::html($username);
        $user = $this->User->getIdFromUsername($username); // TODO move to model
                                                        // and use contain
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
        $userId = $this->User->getIdFromEmail($email); // TODO move to model 
                                                  // and use contain
        
        pr($userId);
        if ($userId) {
            $this->set('data', true);
        } else {
            $this->set('data', false);
        }
    }


    /**
     * special function to update rights
     * go on this page when you add new action
     *
     * @return void
     */
    public function update_rights()
    {
        $this->_buildAcl();
        $this->_init_db();
        die; //TODO it's a hack

    }




    /**
     * Temporary public function to grant/deny access.
     * 
     * @return void
     */
    private function _init_db()
    {
        $group =& $this->User->Group;

        //Allow admins to everything
        $group->id = 1;
        $this->Acl->allow($group, 'controllers');

        //Permissions for moderators
        $group->id = 2;
        $this->Acl->deny($group, 'controllers');
        $this->Acl->allow($group, 'controllers/SuggestedModifications');
        
        $this->Acl->allow($group, 'controllers/SentenceComments');
        $this->Acl->allow($group, 'controllers/Sentences');
        
        $this->Acl->allow($group, 'controllers/Users/my_tatoeba');
        $this->Acl->allow($group, 'controllers/Users/settings');
        $this->Acl->allow($group, 'controllers/Users/save_email');
        $this->Acl->allow($group, 'controllers/Users/save_password');
        $this->Acl->allow($group, 'controllers/Users/save_options');
        $this->Acl->allow($group, 'controllers/Users/start_following');
        $this->Acl->allow($group, 'controllers/Users/favoriting');
        
        $this->Acl->allow($group, 'controllers/Users/stop_following');
        $this->Acl->allow($group, 'controllers/Favorites/add_favorite');
        $this->Acl->allow($group, 'controllers/PrivateMessages');
        $this->Acl->allow($group, 'controllers/SentenceAnnotations');
        $this->Acl->allow($group, 'controllers/Wall');
        
        $this->Acl->allow($group, 'controllers/User'); 
        
        //Permissions for trusted_users
        $group->id = 3;
        $this->Acl->deny($group, 'controllers');
        
        $this->Acl->allow($group, 'controllers/Sentences');
        $this->Acl->deny($group, 'controllers/Sentences/delete');
        
        $this->Acl->allow($group, 'controllers/Users/my_tatoeba');
        $this->Acl->allow($group, 'controllers/Users/settings');
        $this->Acl->allow($group, 'controllers/Users/save_email');
        $this->Acl->allow($group, 'controllers/Users/save_password');
        $this->Acl->allow($group, 'controllers/Users/save_options');
        $this->Acl->allow($group, 'controllers/Users/start_following');
        $this->Acl->allow($group, 'controllers/Users/favoriting');
        $this->Acl->allow($group, 'controllers/Users/stop_following');
        
        $this->Acl->allow($group, 'controllers/Favorites/add_favorite');
        $this->Acl->allow($group, 'controllers/Favorites/remove_favorite');
        
        $this->Acl->allow($group, 'controllers/PrivateMessages');
        $this->Acl->allow($group, 'controllers/SentenceAnnotations');
        $this->Acl->allow($group, 'controllers/SentenceComments');
        $this->Acl->allow($group, 'controllers/Wall');
        $this->Acl->allow($group, 'controllers/Links');

        $this->Acl->allow($group, 'controllers/User'); 
        
        //Permissions for users
        $group->id = 4;
        $this->Acl->deny($group, 'controllers');
        $this->Acl->allow($group, 'controllers/Sentences');
        $this->Acl->deny($group, 'controllers/Sentences/delete');
        
        $this->Acl->allow($group, 'controllers/Users/my_tatoeba');
        $this->Acl->allow($group, 'controllers/Users/settings');
        $this->Acl->allow($group, 'controllers/Users/save_email');
        $this->Acl->allow($group, 'controllers/Users/save_password');
        $this->Acl->allow($group, 'controllers/Users/save_options');
        $this->Acl->allow($group, 'controllers/Users/start_following');
        $this->Acl->allow($group, 'controllers/Users/favoriting');
        $this->Acl->allow($group, 'controllers/Users/stop_following');

        $this->Acl->allow($group, 'controllers/Favorites/add_favorite');
        $this->Acl->allow($group, 'controllers/Favorites/remove_favorite');
        
        $this->Acl->allow($group, 'controllers/PrivateMessages');
        $this->Acl->allow($group, 'controllers/SentenceAnnotations');
        $this->Acl->allow($group, 'controllers/SentenceComments');
        $this->Acl->allow($group, 'controllers/Wall');
        $this->Acl->allow($group, 'controllers/User'); 
    }
}
?>
