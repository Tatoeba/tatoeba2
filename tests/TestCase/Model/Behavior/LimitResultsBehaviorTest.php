<?php
namespace App\Test\TestCase\Model\Behavior;

use App\Model\Behavior\LimitResultsBehavior;
use Cake\TestSuite\TestCase;
use Cake\ORM\Query;

class LimitResultsBehaviorTest extends TestCase
{
    private $query;
    private $behavior;

    public $fixtures = [
        'app.Sentences',
        'app.Users',
    ];

    protected function buildProxy(object $instance)
    {
        $reflection = new \ReflectionClass(get_class($instance));

        $proxy = $this->createMock($reflection->getName());
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            $callback = [$instance, $methodName];
            if (!$method->isConstructor() && !$method->isDestructor() && !$method->isFinal() && $methodName != '__clone' && is_callable($callback)) {
                $proxy->method($method->getName())
                    ->willReturnCallback(
                        fn() => call_user_func_array($callback, func_get_args())
                    );
            }
        }

        return $proxy;
    }

    public function setUp(): void
    {
        parent::setUp();

        $s = $this->getTableLocator()->get('Sentences');
        $this->behavior = new LimitResultsBehavior($s);
        $query = new Query($s->getConnection(), $s);
        $this->query = $this->buildProxy($query);
        $this->query->order(['Sentences.id' => 'DESC']);

        $this->Sentences = $s;
    }

    public function tearDown(): void
    {
        unset($this->query);
        unset($this->behavior);

        parent::tearDown();
    }

    public function testFindLatest_simple()
    {
        $this->query
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id >=' => 47]);

        $this->behavior->findLatest($this->query, ['maxResults' => 20]);
    }

    public function testFindLatest_reverse()
    {
        $this->query->order(['Sentences.id' => 'ASC'], true);
        $this->query
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id <=' => 21]);

        $this->behavior->findLatest($this->query, ['maxResults' => 20]);
    }

    public function testFindLatest_noOrder()
    {
        $this->query->order(false, true);
        $this->expectException(\Cake\Http\Exception\BadRequestException::class);

        $this->behavior->findLatest($this->query, ['maxResults' => 20]);
    }

    public function testFindLatest_noExplicitDirection()
    {
        $this->query->order('Sentences.id', true);
        $this->query
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id <=' => 21]);

        $this->behavior->findLatest($this->query, ['maxResults' => 20]);
    }

    public function testFindLatest_whereOnMainTable()
    {
        $this->query
             ->where(['Sentences.lang' => 'cmn']);

        $this->query
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id >=' => 24]);

        $this->behavior->findLatest($this->query, ['maxResults' => 2]);
    }

    public function testFindLatest_whereOnMainTable_noLimitNeeded()
    {
        $this->query
             ->where(['Sentences.lang' => 'cmn']);

        $this->query
             ->expects($this->never())
             ->method('where');

        $this->behavior->findLatest($this->query, ['maxResults' => 20]);
    }

    public function testFindLatest_whereOnJoinedTable()
    {
        $this->query
             ->where(['Users.role' => 'contributor'])
             ->contain(['Users']);

        $this->query
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id >=' => 35]);

        $this->behavior->findLatest($this->query, ['maxResults' => 20]);
    }

    public function testFindLatest_whereOnMainTableWithoutResults()
    {
        $this->query
             ->where(['Sentences.id' => 0]);

        $this->query
             ->expects($this->never())
             ->method('where');

        $this->behavior->findLatest($this->query, ['maxResults' => 20]);
    }

    public function testFindLatest_whereIsNullOnMainTable()
    {
        $this->query
             ->where(['Sentences.lang IS' => null]);

        $this->query
             ->expects($this->never())
             ->method('where');

        $this->behavior->findLatest($this->query, ['maxResults' => 20]);
    }

    public function testFindLatest_withLeftJoinWithoutWhere()
    {
        $this->query
             ->join([
                 'table' => 'users',
                 'alias' => 'Users',
                 'type' => 'LEFT',
                 'conditions' => ['Users.id = Sentences.user_id'],
             ]);


        $this->query
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id >=' => 47]);

        $this->behavior->findLatest($this->query, ['maxResults' => 20]);
    }

    public function testFindLatest_withLeftJoinWithWhere()
    {
        $this->query
             ->join([
                 'table' => 'users',
                 'alias' => 'Users',
                 'type' => 'LEFT',
                 'conditions' => ['Users.id = Sentences.user_id'],
             ])
             ->where(['Users.role' => 'contributor']);


        $this->query
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id >=' => 35]);

        $this->behavior->findLatest($this->query, ['maxResults' => 20]);
    }

    public function testFindLatest_limitsOffset()
    {
        $query = $this->Sentences
            ->find()
            ->order(['Sentences.id' => 'DESC'])
            ->offset(9999999);

        $result = $this->behavior->findLatest($query, ['maxResults' => 20]);

        $this->assertEquals(20, $result->clause('offset'));
    }
}
