<?php
namespace App\Test\TestCase\Datasource\Paging;

use App\Datasource\Paging\LimitedPaginator;
use Cake\TestSuite\TestCase;
use Cake\ORM\Query\SelectQuery;

class LimitedPaginatorTest extends TestCase
{
    private $mockQuery;
    private $baseQuery;
    private $paginator;

    public array $fixtures = [
        'app.Sentences',
        'app.Users',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $s = $this->fetchTable('Sentences');
        $this->paginator = new LimitedPaginator();
        $this->mockQuery = $this->getMockBuilder(SelectQuery::class)
            ->setConstructorArgs([$s])
            ->onlyMethods(['where'])
            ->getMock();

        $this->baseQuery = new SelectQuery($s);
        $this->baseQuery->order(['Sentences.id' => 'DESC']);
    }

    public function tearDown(): void
    {
        unset($this->baseQuery);
        unset($this->mockQuery);
        unset($this->paginator);

        parent::tearDown();
    }

    public function testPaginate_limitsNumberOfResults()
    {
        $results = $this->paginator->paginate($this->baseQuery, [], ['maxResults' => 20]);

        $this->assertEquals(20, $results->count());
        $this->assertEquals(20, $results->totalCount());
    }

    public function testPaginate_callsApplyLimit()
    {
        $paginator = $this->getMockBuilder(LimitedPaginator::class)
            ->onlyMethods(['applyLimit'])
            ->getMock();
        $paginator
             ->expects($this->once())
             ->method('applyLimit')
             ->with(123, $this->baseQuery, $this->baseQuery);

        $paginator->paginate($this->baseQuery, [], ['maxResults' => 123]);
    }

    public function testPaginate_callsApplyLimit_withBaseQuery()
    {
        $paginationQuery = clone $this->baseQuery;
        $paginator = $this->getMockBuilder(LimitedPaginator::class)
            ->onlyMethods(['applyLimit'])
            ->getMock();
        $paginator
             ->expects($this->once())
             ->method('applyLimit')
             ->with(123, $paginationQuery, $this->baseQuery);

        $paginator->paginate(
            $paginationQuery,
            [],
            ['maxResults' => 123, 'maxResultsBaseQuery' => $this->baseQuery]
        );
    }

    public function testApplyLimit_simple()
    {
        $this->mockQuery
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id >=' => 47]);

        $this->paginator->applyLimit(20, $this->mockQuery, $this->baseQuery);
    }

    public function testApplyLimit_reverse()
    {
        $this->baseQuery->order(['Sentences.id' => 'ASC'], true);
        $this->mockQuery
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id <=' => 21]);

        $this->paginator->applyLimit(20, $this->mockQuery, $this->baseQuery);
    }

    public function testApplyLimit_noOrder()
    {
        $this->baseQuery->order(false, true);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid sort order');

        $this->paginator->applyLimit(20, $this->mockQuery, $this->baseQuery);
    }

    public function testApplyLimit_noExplicitDirection()
    {
        $this->baseQuery->order('Sentences.id', true);
        $this->mockQuery
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id <=' => 21]);

        $this->paginator->applyLimit(20, $this->mockQuery, $this->baseQuery);
    }

    public function testApplyLimit_whereOnMainTable()
    {
        $this->baseQuery
             ->where(['Sentences.lang' => 'cmn']);

        $this->mockQuery
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id >=' => 24]);

        $this->paginator->applyLimit(2, $this->mockQuery, $this->baseQuery);
    }

    public function testApplyLimit_whereOnMainTable_noLimitNeeded()
    {
        $this->baseQuery
             ->where(['Sentences.lang' => 'cmn']);

        $this->mockQuery
             ->expects($this->never())
             ->method('where');

        $this->paginator->applyLimit(20, $this->mockQuery, $this->baseQuery);
    }

    public function testApplyLimit_whereOnJoinedTable()
    {
        $this->baseQuery
             ->where(['Users.role' => 'contributor'])
             ->contain(['Users']);

        $this->mockQuery
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id >=' => 35]);

        $this->paginator->applyLimit(20, $this->mockQuery, $this->baseQuery);
    }

    public function testApplyLimit_whereOnMainTableWithoutResults()
    {
        $this->baseQuery
             ->where(['Sentences.id' => 0]);

        $this->mockQuery
             ->expects($this->never())
             ->method('where');

        $this->paginator->applyLimit(20, $this->mockQuery, $this->baseQuery);
    }

    public function testApplyLimit_whereIsNullOnMainTable()
    {
        $this->baseQuery
             ->where(['Sentences.lang IS' => null]);

        $this->mockQuery
             ->expects($this->never())
             ->method('where');

        $this->paginator->applyLimit(20, $this->mockQuery, $this->baseQuery);
    }

    public function testApplyLimit_withLeftJoinWithoutWhere()
    {
        $this->baseQuery
             ->join([
                 'table' => 'users',
                 'alias' => 'Users',
                 'type' => 'LEFT',
                 'conditions' => ['Users.id = Sentences.user_id'],
             ]);

        $this->mockQuery
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id >=' => 47]);

        $this->paginator->applyLimit(20, $this->mockQuery, $this->baseQuery);
    }

    public function testApplyLimit_withLeftJoinWithWhere()
    {
        $this->baseQuery
             ->join([
                 'table' => 'users',
                 'alias' => 'Users',
                 'type' => 'LEFT',
                 'conditions' => ['Users.id = Sentences.user_id'],
             ])
             ->where(['Users.role' => 'contributor']);

        $this->mockQuery
             ->expects($this->once())
             ->method('where')
             ->with(['Sentences.id >=' => 35]);

        $result = $this->paginator->applyLimit(20, $this->mockQuery, $this->baseQuery);
    }

    public function testApplyLimit_limitsOffset()
    {
        $this->baseQuery
            ->offset(9999999);

        $result = $this->paginator->applyLimit(20, $this->baseQuery, $this->baseQuery);

        $this->assertEquals(20, $result->clause('offset'));
    }
}
