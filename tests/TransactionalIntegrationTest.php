<?php

namespace Tourze\Symfony\AopDoctrineBundle\Tests;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\Symfony\Aop\Model\JoinPoint;
use Tourze\Symfony\AopDoctrineBundle\Aspect\TransactionalAspect;
use Tourze\Symfony\AopDoctrineBundle\Tests\Exception\TestTransactionException;

/**
 * 集成测试 Transactional 注解功能
 *
 * @internal
 */
#[CoversClass(TransactionalAspect::class)]
#[RunTestsInSeparateProcesses]
final class TransactionalIntegrationTest extends AbstractIntegrationTestCase
{
    private Connection $connection;

    private TransactionalAspect $aspect;

    protected function onSetUp(): void
    {
        $this->connection = self::getService(Connection::class);
        $this->aspect = self::getService(TransactionalAspect::class);

        // 创建测试表
        $this->connection->executeStatement('CREATE TABLE IF NOT EXISTS test_transactional (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL
        )');

        // 清空测试数据
        $this->connection->executeStatement('DELETE FROM test_transactional');
    }

    protected function onTearDown(): void
    {
        // 清理测试表
        $this->connection->executeStatement('DROP TABLE IF EXISTS test_transactional');
    }

    /**
     * 测试startTransaction方法基本功能
     */
    public function testStartTransaction(): void
    {
        // 验证初始状态没有事务
        $this->assertFalse($this->connection->isTransactionActive());

        // 使用Mock JoinPoint测试事务启动
        $joinPoint = $this->createMockJoinPoint(function () {
            // 在这个回调中应该有活跃的事务
            return $this->connection->isTransactionActive();
        });

        $this->aspect->startTransaction($joinPoint);

        // 验证方法被调用并且在事务中返回true
        $this->assertTrue($joinPoint->getReturnValue());

        // 事务已经结束，应该没有活跃事务
        $this->assertFalse($this->connection->isTransactionActive());
    }

    /**
     * 测试Transactional注解是否正确启动事务
     */
    public function testTransactionalAnnotationStartsTransaction(): void
    {
        // 验证初始状态没有事务
        $this->assertFalse($this->connection->isTransactionActive());

        // 使用Mock JoinPoint测试事务启动
        $joinPoint = $this->createMockJoinPoint(function () {
            // 在这个回调中应该有活跃的事务
            return $this->connection->isTransactionActive();
        });

        $this->aspect->startTransaction($joinPoint);

        // 验证方法被调用并且在事务中返回true
        $this->assertTrue($joinPoint->getReturnValue());

        // 事务已经结束，应该没有活跃事务
        $this->assertFalse($this->connection->isTransactionActive());
    }

    /**
     * 测试嵌套事务的处理
     */
    public function testNestedTransactionHandling(): void
    {
        $this->connection->beginTransaction();

        // 验证已有活跃事务
        $this->assertTrue($this->connection->isTransactionActive());

        $joinPoint = $this->createMockJoinPoint(function () {
            return 'nested_result';
        });

        $this->aspect->startTransaction($joinPoint);

        // 验证嵌套事务返回正确结果但没有提交外层事务
        $this->assertSame('nested_result', $joinPoint->getReturnValue());
        $this->assertTrue($joinPoint->isReturnEarly());
        $this->assertTrue($this->connection->isTransactionActive());

        $this->connection->rollBack();
    }

    /**
     * 测试事务中的实际数据库操作
     */
    public function testRealDatabaseOperationsInTransaction(): void
    {
        $joinPoint = $this->createMockJoinPoint(function () {
            // 在事务中插入数据
            $this->connection->executeStatement(
                'INSERT INTO test_transactional (name) VALUES (?)',
                ['test_data']
            );

            // 验证数据已插入
            $count = $this->connection->fetchOne('SELECT COUNT(*) FROM test_transactional');
            $this->assertSame(1, (int) $count);

            return 'success';
        });

        $this->aspect->startTransaction($joinPoint);

        // 事务提交后，数据应该仍然存在
        $count = $this->connection->fetchOne('SELECT COUNT(*) FROM test_transactional');
        $this->assertSame(1, (int) $count);
        $this->assertSame('success', $joinPoint->getReturnValue());
    }

    /**
     * 测试异常时的事务回滚
     */
    public function testTransactionRollbackOnException(): void
    {
        $joinPoint = $this->createMockJoinPoint(function () {
            // 在事务中插入数据
            $this->connection->executeStatement(
                'INSERT INTO test_transactional (name) VALUES (?)',
                ['test_data']
            );

            // 抛出异常
            throw new TestTransactionException('Intentional test exception');
        });

        try {
            $this->aspect->startTransaction($joinPoint);
            self::fail('Exception should have been thrown');
        } catch (TestTransactionException $e) {
            $this->assertSame('Intentional test exception', $e->getMessage());
        }

        // 验证事务已回滚，数据不应该存在
        $count = $this->connection->fetchOne('SELECT COUNT(*) FROM test_transactional');
        $this->assertSame(0, (int) $count);
    }

    private function createMockJoinPoint(callable $proceedCallback): JoinPoint
    {
        $joinPoint = $this->createMock(JoinPoint::class);

        $joinPoint->method('proceed')
            ->willReturnCallback($proceedCallback)
        ;

        $returnValue = null;
        $joinPoint->method('setReturnValue')
            ->willReturnCallback(function ($value) use (&$returnValue) {
                $returnValue = $value;
            })
        ;

        $joinPoint->method('getReturnValue')
            ->willReturnCallback(function () use (&$returnValue) {
                return $returnValue;
            })
        ;

        $returnEarly = false;
        $joinPoint->method('setReturnEarly')
            ->willReturnCallback(function ($early) use (&$returnEarly) {
                $returnEarly = $early;
            })
        ;

        $joinPoint->method('isReturnEarly')
            ->willReturnCallback(function () use (&$returnEarly) {
                return $returnEarly;
            })
        ;

        return $joinPoint;
    }
}
