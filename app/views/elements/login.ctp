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

if ($session->check('Message.auth')) {
    $session->flash('auth');
}

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


<a class="menuItem" onclick="javascript:openLoginForm();">
    <?php echo __('Log in', true); ?>
</a>

<?php
echo $form->create(
    'User', 
    array(
        'action' => 'check_login',
        'id' => 'UserLoginForm_FromBar',
        'style' => 'display:none;'
    )
);

echo '<fieldset>';
// Username
echo $form->input(
    'username',
    array(
        'label' => __('Username : ', true)
    )
);
// Password
echo $form->input(
    'password', 
    array(
        'label' => __('Password : ', true)
    )
);
// Checkbox
echo $form->checkbox('rememberMe'); 
echo '<label for="UserRememberMe" class="notInBlackBand">';
echo __('Remember me', true);
echo '</label>';
// Redirect
echo $form->hidden(
    'redirectTo', 
    array(
        'value' => htmlentities($_SERVER['REQUEST_URI'])
    )
);
// Login button
echo $form->submit(__('Log in', true));
echo '</fieldset>';
?>

<p>
<?php
echo $html->link(
    __('Password forgotten?', true),
    array(
        "controller" => "users",
        "action" => "new_password"
    )
);
echo $html->link(
    __('Close', true),
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
echo $form->end();
?>
