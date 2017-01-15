<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  Etienne Deparis <etienne.deparis@umaneti.net>
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
 * @author   Etienne Deparis <etienne.deparis@umaneti.net>
 * @license  Affero General Public License
 * @link     http://tatoeba.org
 */
?>

<script type="text/javascript">
<!--
    function openLoginForm(){
            document.getElementById('UserLoginForm_FromBar').style.display = 'block';
    }
    function closeLoginForm(){
            document.getElementById('UserLoginForm_FromBar').style.display = 'none';
    }
-->
</script>

<ul>
    <li>
    <?php
    echo $this->Html->link(
        __('Register'),
        array(
            'controller' => 'users',
            'action' => 'register'
        ),
        array(
            'class' => 'menuSection'
        )
    );
    ?>
    </li>
    
    <li>
    <?php    
    echo $this->Html->link(
        __('Log in'),
        array(
            'controller' => 'users',
            'action' => 'login',
            '?' => array('redirectTo' => $this->Pages->currentPageUrl()),
        ),
        array(
            'onclick' => 'javascript:openLoginForm(); return false;',
            'class' => 'menuSection'
        )
    );
    ?>
    </li>
</ul>

<?php
$this->Security->enableCSRFProtection();
echo $this->Form->create(
    'User', 
    array(
        'url' => array(
            'action' => 'check_login',
            '?' => array('redirectTo' => $this->Pages->currentPageUrl()),
        ),
        'id' => 'UserLoginForm_FromBar',
        'style' => 'display:none;'
    )
);

echo '<fieldset>';
// Username
echo $this->Form->input(
    'username',
    array(
        'label' => __('Username: ')
    )
);
// Password
echo $this->Form->input(
    'password', 
    array(
        'label' => __('Password: ')
    )
);
// Checkbox
echo $this->Form->checkbox('rememberMe'); 
echo '<label for="UserRememberMe" class="notInBlackBand">';
echo __('Remember me');
echo '</label>';
// Login button
echo $this->Form->submit(__('Log in'));
echo '</fieldset>';
?>

<p>
<?php
echo $this->Html->link(
    __('Forgot your password?'),
    array(
        "controller" => "users",
        "action" => "new_password"
    )
);
echo $this->Html->link(
    __('Close'),
    '#',
    array(
        'class' => 'menuItem',
        'style' => 'float:right;',
        'onclick' => 'javascript:closeLoginForm();'
    )
);
?>
</p>

<?php
echo $this->Form->end();
$this->Security->disableCSRFProtection();
?>
