<?php
namespace App\Test\TestCase\Controller;

use App\Controller\WallController;
use Cake\Core\Configure;

class WallControllerTest extends ControllerTestCase {

    public $fixtures = array(
        'app.wall',
        'app.wall_thread',
        'app.user',
        'app.users_language'
    );

    public function setUp() {
        Configure::write('App.base', ''); // prevent using the filesystem path as base
        $this->controller = $this->generate('Wall', array(
            'methods' => array('redirect'),
        ));
    }

    public function endTest($method) {
        unset($this->controller);
    }

    public function testPaginateRedirectsPageOutOfBoundsToLastPage() {
        $userId = 1;
        $lastPage = 2;
        
        $initialDate = new DateTime('2018-01-01');
		for ($i = 0; $i <= 15; $i++) {
            $date = $initialDate->format('Y-m-d H:i:s');
			$message = array(
                'content' => "Wall message $i",
                'date' => $date,
				'owner' => $userId,
            );
            $this->controller->Wall->save($message);

            $this->controller->Wall->id = null;
            $initialDate->modify("+1 day");
		}
        
        $this->controller
             ->expects($this->once())
             ->method('redirect')
             ->with("/eng/wall/index/page:$lastPage");
        $this->testAction("/eng/wall/index/page:9999999");
    }
}
?>
