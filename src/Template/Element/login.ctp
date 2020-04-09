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
 * @link     https://tatoeba.org
 */

use Cake\Controller\Component\AuthComponent;

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
        /* @translators: link to the Register page in the top bar (verb) */
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
        /* @translators: link to open the Login box in the top bar (verb) */
        __('Log in'),
        array(
            'controller' => 'users',
            'action' => 'login',
            '?' => array(AuthComponent::QUERY_STRING_REDIRECT => $this->Pages->currentPageUrl()),
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
            'controller' => 'users',
            'action' => 'check_login',
            '?' => array(AuthComponent::QUERY_STRING_REDIRECT => $this->Pages->currentPageUrl()),
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
        'label' => __('Username: '),
        'value' => false,
    )
);
// Password
echo $this->Form->input(
    'password',
    array(
        'label' => __('Password: '),
        'value' => false,
    )
);
// Checkbox
echo $this->Form->checkbox('rememberMe', ['id' => 'rememberMe']);
echo '<label for="rememberMe" class="notInBlackBand">';
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
    /* @translators: button to close the top-right login box (verb) */
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
