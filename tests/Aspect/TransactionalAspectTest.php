<?php

namespace Tourze\Symfony\AopDoctrineBundle\Tests\Aspect;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tourze\Symfony\Aop\Model\JoinPoint;
use Tourze\Symfony\AopDoctrineBundle\Aspect\TransactionalAspect;

class TransactionalAspectTest extends TestCase
{
    /**
     * @var Connection&MockObject
     */
    private MockObject $connection;

    /**
     * @var LoggerInterface&MockObject
     */
    private MockObject $logger;

    /**
     * @var JoinPoint&MockObject
     */
    private MockObject $joinPoint;

    /**
     * @var TransactionalAspect
     */
    private TransactionalAspect $aspect;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = $this->createMock(Connection::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->joinPoint = $this->createMock(JoinPoint::class);

        $this->aspect = new TransactionalAspect(
            $this->connection,
            $this->logger
        );
    }

    /**
     * 测试已有活动事务时的行为 - 应复用已有事务
     */
    public function testStartTransactionWithActiveTransaction(): void
    {
        // 模拟已有活动事务
        $this->connection->expects($this->once())
            ->method('isTransactionActive')
            ->willReturn(true);

        // 期望调用proceed一次
        $this->joinPoint->expects($this->once())
            ->method('proceed')
            ->willReturn('result');

        // 期望设置返回值
        $this->joinPoint->expects($this->once())
            ->method('setReturnValue')
            ->with('result');

        // 期望设置提前返回
        $this->joinPoint->expects($this->once())
            ->method('setReturnEarly')
            ->with(true);

        // 不应调用transactional方法
        $this->connection->expects($this->never())
            ->method('transactional');

        // 不应记录日志
        $this->logger->expects($this->never())
            ->method('debug');

        $this->aspect->startTransaction($this->joinPoint);
    }

    /**
     * 测试没有活动事务时的行为 - 应创建新事务
     */
    public function testStartTransactionWithoutActiveTransaction(): void
    {
        // 模拟没有活动事务
        $this->connection->expects($this->once())
            ->method('isTransactionActive')
            ->willReturn(false);

        // 准备记录logger调用
        $loggerCalls = [];
        $this->logger->method('debug')
            ->will($this->returnCallback(function ($message) use (&$loggerCalls) {
                $loggerCalls[] = $message;
                return null;
            }));

        // 设置transactional方法的行为
        $this->connection->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(function (callable $callback) {
                // 手动调用回调函数
                return $callback();
            });

        // 期望调用proceed一次
        $this->joinPoint->expects($this->once())
            ->method('proceed')
            ->willReturn('result');

        // 期望设置返回值
        $this->joinPoint->expects($this->once())
            ->method('setReturnValue')
            ->with('result');

        // 期望设置提前返回
        $this->joinPoint->expects($this->once())
            ->method('setReturnEarly')
            ->with(true);

        // 执行测试方法
        $this->aspect->startTransaction($this->joinPoint);

        // 验证日志记录
        $this->assertCount(2, $loggerCalls, '应该记录两条日志');
        $this->assertEquals('通过注解开启事务', $loggerCalls[0], '第一条日志应为开启事务');
        $this->assertEquals('通过注解结束事务', $loggerCalls[1], '第二条日志应为结束事务');
    }

    /**
     * 测试事务中抛出异常的情况
     */
    public function testTransactionWithException(): void
    {
        // 模拟没有活动事务
        $this->connection->expects($this->once())
            ->method('isTransactionActive')
            ->willReturn(false);

        // 准备记录logger调用
        $loggerCalls = [];
        $this->logger->method('debug')
            ->will($this->returnCallback(function ($message) use (&$loggerCalls) {
                $loggerCalls[] = $message;
                return null;
            }));

        // 设置transactional方法的行为
        $this->connection->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(function (callable $callback) {
                // 手动调用回调函数，捕获异常
                try {
                    return $callback();
                } catch (\Exception $e) {
                    // 捕获异常但不传播，模拟事务回滚
                    return null;
                }
            });

        // 期望proceed会抛出异常
        $this->joinPoint->expects($this->once())
            ->method('proceed')
            ->willThrowException(new \RuntimeException('测试异常'));

        // 期望不会设置返回值（因为抛出了异常）
        $this->joinPoint->expects($this->never())
            ->method('setReturnValue');

        // 期望不会设置提前返回（因为抛出了异常）
        $this->joinPoint->expects($this->never())
            ->method('setReturnEarly');

        // 执行测试方法
        $this->aspect->startTransaction($this->joinPoint);

        // 验证日志记录
        $this->assertCount(2, $loggerCalls, '即使发生异常也应该记录两条日志');
        $this->assertEquals('通过注解开启事务', $loggerCalls[0], '第一条日志应为开启事务');
        $this->assertEquals('通过注解结束事务', $loggerCalls[1], '第二条日志应为结束事务');
    }
}
