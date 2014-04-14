<?php

class ApiTestController extends AppController
{
    public $name = "ApiTest";

    public $uses = "";

    public $helpers = array("Html", "Form");

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allowedActions = array("*");
    }

    public function test()
    {

    }

}

?>
