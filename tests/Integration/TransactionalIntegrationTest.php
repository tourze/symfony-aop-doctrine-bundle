<?php

namespace Tourze\Symfony\AopDoctrineBundle\Tests\Integration;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tourze\Symfony\Aop\Model\JoinPoint;
use Tourze\Symfony\AopDoctrineBundle\Aspect\TransactionalAspect;
use Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional;

/**
 * 集成测试 Transactional 注解功能
 */
class TransactionalIntegrationTest extends TestCase
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
     * @var TransactionalAspect
     */
    private TransactionalAspect $aspect;


    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = $this->createMock(Connection::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->aspect = new TransactionalAspect(
            $this->connection,
            $this->logger
        );
    }

    /**
     * 测试嵌套事务调用
     */
    public function testNestedTransactionalCalls(): void
    {
        // 模拟一开始没有活动事务，之后有活动事务
        $this->connection->expects($this->exactly(2))
            ->method('isTransactionActive')
            ->willReturnOnConsecutiveCalls(false, true);

        // 准备记录logger调用
        $loggerCalls = [];
        $this->logger->method('debug')
            ->will($this->returnCallback(function ($message) use (&$loggerCalls) {
                $loggerCalls[] = $message;
                return null;
            }));

        // 设置transactional方法的行为，执行回调
        $this->connection->expects($this->once())
            ->method('transactional')
            ->willReturnCallback(function (callable $callback) {
                return $callback();
            });

        // 模拟外层方法调用的JoinPoint
        /** @var JoinPoint&MockObject $outerJoinPoint */
        $outerJoinPoint = $this->createMock(JoinPoint::class);
        $outerJoinPoint->method('proceed')
            ->willReturnCallback(function () {
                // 在外层方法中会调用内层方法
                // 创建内层方法的JoinPoint
                /** @var JoinPoint&MockObject $innerJoinPoint */
                $innerJoinPoint = $this->createMock(JoinPoint::class);
                $innerJoinPoint->expects($this->once())
                    ->method('proceed')
                    ->willReturn('inner result');
                $innerJoinPoint->expects($this->once())
                    ->method('setReturnValue')
                    ->with('inner result');
                $innerJoinPoint->expects($this->once())
                    ->method('setReturnEarly')
                    ->with(true);

                // 模拟调用内层事务方法
                $this->aspect->startTransaction($innerJoinPoint);

                return 'outer result';
            });

        $outerJoinPoint->expects($this->once())
            ->method('setReturnValue')
            ->with('outer result');
        $outerJoinPoint->expects($this->once())
            ->method('setReturnEarly')
            ->with(true);

        // 调用外层事务方法
        $this->aspect->startTransaction($outerJoinPoint);

        // 验证日志记录 - 只有外层事务的开始和结束日志
        $this->assertCount(2, $loggerCalls, '只有外层事务应该记录日志');
        $this->assertEquals('通过注解开启事务', $loggerCalls[0]);
        $this->assertEquals('通过注解结束事务', $loggerCalls[1]);
    }
}

/**
 * 用于测试的服务类
 */
class TestService
{
    #[Transactional]
    public function outerMethod(): string
    {
        // 调用内层事务方法
        $this->innerMethod();
        return 'outer result';
    }

    #[Transactional]
    public function innerMethod(): string
    {
        return 'inner result';
    }
}
