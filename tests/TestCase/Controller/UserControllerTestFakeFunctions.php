<?php

/**
 * This file is a trick to ease unit testing of UserController.
 */
namespace App\Controller;

/**
 * We create a function in the same namespace as the controller
 * so that just calling is_uploaded_file() without explicitly
 * specifying the namespace will call \App\Controller\is_uploaded_file()
 * instead of the PHP global function \is_uploaded_file().
 */
function is_uploaded_file($filename) {
    return true;
}
