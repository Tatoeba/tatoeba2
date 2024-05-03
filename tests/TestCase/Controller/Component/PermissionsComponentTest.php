<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\PermissionsComponent;
use App\Model\CurrentUser;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class PermissionsComponentTest extends TestCase
{
    public $fixtures = [
        'app.Users',
        'app.UsersLanguages',
        'app.Walls',
        'app.SentenceComments',
    ];
    private $component;
    private $controller;
    private $Users;

    public function setUp()
    {
        parent::setUp();
        $request = new ServerRequest();
        $response = new Response();
        $this->controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->setConstructorArgs([$request, $response])
            ->setMethods(null)
            ->getMock();
        $registry = new ComponentRegistry($this->controller);
        $this->component = new PermissionsComponent($registry);
        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }

    public function tearDown()
    {
        unset($this->component, $this->controller);
        CurrentUser::store([]);
        parent::tearDown();
    }

    public function WallMessageOptionsProvider() {
        // lastInThread, owner, currentUser, expected
        return [
            [false, null, null,
             ['canReply' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false]
            ],
            [true, null, null,
             ['canReply' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false]
            ],
            [true, 1, null,
             ['canReply' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false]
            ],
            [false, 3, 3,
             ['canReply' => true, 'canDelete' => false, 'canEdit' => true, 'canPM' => false]
            ],
            [true, 3, 3,
             ['canReply' => true, 'canDelete' => true, 'canEdit' => true, 'canPM' => false]
            ],
            [false, 3, 1,
             ['canReply' => true, 'canDelete' => false, 'canEdit' => true, 'canPM' => true]
            ],
            [true, 3, 1,
             ['canReply' => true, 'canDelete' => true, 'canEdit' => true, 'canPM' => true]
            ],
            [false, 3, 2,
             ['canReply' => true, 'canDelete' => false, 'canEdit' => false, 'canPM' => true]
            ],
            [true, 3, 2,
             ['canReply' => true, 'canDelete' => false, 'canEdit' => false, 'canPM' => true]
            ],
            [true, null, 2,
             ['canReply' => true, 'canDelete' => false, 'canEdit' => false, 'canPM' => false]
            ],
        ];
    }

    /**
     * @dataProvider WallMessageOptionsProvider
     */
    public function testGetWallMessageOptions($lastInThread, $owner, $currentUser, $expected) {
        if ($currentUser) {
            CurrentUser::store($this->Users->get($currentUser)->toArray());
        }
        $result = $this->component->getWallMessageOptions($lastInThread, $owner, $currentUser);
        $this->assertEquals($expected, $result);
    }

    public function testGetWallMessagesOptions() {
        CurrentUser::store($this->Users->get(7)->toArray());
        $wallThread = TableRegistry::getTableLocator()
            ->get('Wall')
            ->getWholeThreadContaining(1);
        $wallThread = $this->component->getWallMessagesOptions($wallThread, 7);
        $this->assertTrue($wallThread[0]['Permissions']['canEdit']);
        $this->assertFalse($wallThread[0]['Permissions']['canDelete']);
        $this->assertFalse($wallThread[0]['children'][0]['Permissions']['canEdit']);
        $this->assertTrue($wallThread[0]['children'][0]['Permissions']['canPM']);
    }

    public function CommentOptionsProvider() {
        // owner, currentUser, expected
        return [
            [null, null,
             ['canHide' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false]
            ],
            [null, 2,
             ['canHide' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false]
            ],
            [null, 1,
             ['canHide' => true, 'canDelete' => true, 'canEdit' => true, 'canPM' => false]
            ],
            [3, null,
             ['canHide' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false]
            ],
            [3, 3,
             ['canHide' => false, 'canDelete' => true, 'canEdit' => true, 'canPM' => false]
            ],
            [3, 1,
             ['canHide' => true, 'canDelete' => true, 'canEdit' => true, 'canPM' => true]
            ],
            [3, 2,
             ['canHide' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => true]
            ],
        ];
    }

    /**
     * @dataProvider CommentOptionsProvider
     */
    public function testGetCommentOptions($owner, $currentUser, $expected) {
        if ($currentUser) {
            CurrentUser::store($this->Users->get($currentUser)->toArray());
        }
        $result = $this->component->getCommentOptions($owner);
        $this->assertEquals($expected, $result);
    }

    public function testGetCommentsOptions() {
        $Comments = TableRegistry::getTableLocator()->get('SentenceComments');
        CurrentUser::store($this->Users->get(2)->toArray());

        $comments = $Comments->getCommentsForSentence(14);
        $permissions = $this->component->getCommentsOptions($comments);
        $this->assertTrue($permissions[0]['canEdit']);
        $this->assertFalse($permissions[0]['canPM']);

        $comments = $Comments->getCommentsForSentence(4);
        $permissions = $this->component->getCommentsOptions($comments);
        $this->assertFalse($permissions[0]['canEdit']);
        $this->assertTrue($permissions[0]['canPM']);
    }

}
