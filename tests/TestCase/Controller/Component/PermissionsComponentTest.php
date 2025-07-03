<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\PermissionsComponent;
use App\Model\CurrentUser;
use App\Model\Entity\SentenceComment;
use App\Model\Entity\Wall;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class PermissionsComponentTest extends TestCase
{
    public $fixtures = [
        'app.users',
        'app.users_languages',
        'app.walls',
        'app.sentence_comments',
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
        // lastInThread, owner, currentUser, isHidden, expected
        return [
            [false, null, null, false,
             ['canReply' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false, 'canReport' => true]
            ],
            [false, null, null, true,
             ['canReply' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false, 'canReport' => false]
            ],
            [true, null, null, false,
             ['canReply' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false, 'canReport' => true]
            ],
            [true, 1, null, false,
             ['canReply' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false, 'canReport' => true]
            ],
            [true, 1, null, true,
             ['canReply' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false, 'canReport' => false]
            ],
            [false, 3, 3, false,
             ['canReply' => true, 'canDelete' => false, 'canEdit' => true, 'canPM' => false, 'canReport' => false]
            ],
            [true, 3, 3, false,
             ['canReply' => true, 'canDelete' => true, 'canEdit' => true, 'canPM' => false, 'canReport' => false]
            ],
            [false, 3, 1, false,
             ['canReply' => true, 'canDelete' => false, 'canEdit' => true, 'canPM' => true, 'canReport' => true]
            ],
            [false, 3, 1, true,
             ['canReply' => true, 'canDelete' => false, 'canEdit' => true, 'canPM' => true, 'canReport' => false]
            ],
            [true, 3, 1, false,
             ['canReply' => true, 'canDelete' => true, 'canEdit' => true, 'canPM' => true, 'canReport' => true]
            ],
            [false, 3, 2, false,
             ['canReply' => true, 'canDelete' => false, 'canEdit' => false, 'canPM' => true, 'canReport' => true]
            ],
            [false, 3, 2, true,
             ['canReply' => true, 'canDelete' => false, 'canEdit' => false, 'canPM' => true, 'canReport' => false]
            ],
            [true, 3, 2, false,
             ['canReply' => true, 'canDelete' => false, 'canEdit' => false, 'canPM' => true, 'canReport' => true]
            ],
            [true, null, 2, false,
             ['canReply' => true, 'canDelete' => false, 'canEdit' => false, 'canPM' => false, 'canReport' => true]
            ],
        ];
    }

    /**
     * @dataProvider WallMessageOptionsProvider
     */
    public function testGetWallMessageOptions($lastInThread, $owner, $currentUser, $isHidden, $expected) {
        if ($currentUser) {
            CurrentUser::store($this->Users->get($currentUser)->toArray());
        }
        $message = new Wall([
            'content' => 'Hello',
            'user' => $owner ? $this->Users->get($owner) : null,
            'hidden' => $isHidden,
        ]);
        $result = $this->component->getWallMessageOptions($lastInThread, $message, $currentUser);
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
        // owner, currentUser, isHidden, expected
        return [
            [null, null, false,
             ['canHide' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false, 'canReport' => true]
            ],
            [null, null, true,
             ['canHide' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false, 'canReport' => false]
            ],
            [null, 2, false,
             ['canHide' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false, 'canReport' => true]
            ],
            [null, 2, true,
             ['canHide' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false, 'canReport' => false]
            ],
            [null, 1, false,
             ['canHide' => true, 'canDelete' => true, 'canEdit' => true, 'canPM' => false, 'canReport' => true]
            ],
            [null, 1, true,
             ['canHide' => true, 'canDelete' => true, 'canEdit' => true, 'canPM' => false, 'canReport' => false]
            ],
            [3, null, false,
             ['canHide' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false, 'canReport' => true]
            ],
            [3, null, true,
             ['canHide' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => false, 'canReport' => false]
            ],
            [3, 3, false,
             ['canHide' => false, 'canDelete' => true, 'canEdit' => true, 'canPM' => false, 'canReport' => false]
            ],
            [3, 3, true,
             ['canHide' => false, 'canDelete' => true, 'canEdit' => true, 'canPM' => false, 'canReport' => false]
            ],
            [3, 1, false,
             ['canHide' => true, 'canDelete' => true, 'canEdit' => true, 'canPM' => true, 'canReport' => true]
            ],
            [3, 1, true,
             ['canHide' => true, 'canDelete' => true, 'canEdit' => true, 'canPM' => true, 'canReport' => false]
            ],
            [3, 2, false,
             ['canHide' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => true, 'canReport' => true]
            ],
            [3, 2, true,
             ['canHide' => false, 'canDelete' => false, 'canEdit' => false, 'canPM' => true, 'canReport' => false]
            ],
        ];
    }

    /**
     * @dataProvider CommentOptionsProvider
     */
    public function testGetCommentOptions($owner, $currentUser, $isHidden, $expected) {
        if ($currentUser) {
            CurrentUser::store($this->Users->get($currentUser)->toArray());
        }
        $comment = new SentenceComment([
            'text' => 'Hello',
            'user' => $owner ? $this->Users->get($owner) : null,
            'hidden' => $isHidden,
        ]);
        $result = $this->component->getCommentOptions($comment);
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
