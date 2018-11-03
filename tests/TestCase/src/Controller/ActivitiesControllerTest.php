<?php
App::uses('ActivitiesController', 'Controller');

class ActivitiesControllerTest extends ControllerTestCase {

    public $fixtures = array(
        'app.sentence',
        'app.user',
        'app.users_language',
    );

    public function setUp() {
        Configure::write('App.base', ''); // prevent using the filesystem path as base
        $this->controller = $this->generate('Activities', array(
            'methods' => array('redirect'),
        ));
    }

    public function endTest($method) {
        unset($this->controller);
    }

    public function testPaginateRedirectsPageOutOfBoundsToLastPage() {
        $user = 'kazuki';
        $userId = 7;
        $lastPage = 2;

        $this->controller
             ->expects($this->once())
             ->method('redirect')
             ->with("/eng/activities/translate_sentences_of/$user/page:$lastPage");
        $this->testAction("/eng/activities/translate_sentences_of/$user/page:9999999");
    }
}
