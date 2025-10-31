<?php

namespace Tourze\Symfony\AopDoctrineBundle\Tests\Aspect;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\Symfony\Aop\Model\JoinPoint;
use Tourze\Symfony\AopDoctrineBundle\Aspect\TransactionalAspect;

/**
 * @internal
 */
#[CoversClass(TransactionalAspect::class)]
#[RunTestsInSeparateProcesses]
final class TransactionalAspectTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // No additional setup needed for basic integration testing
    }

    public function testGetAspectFromContainer(): void
    {
        $aspect = self::getService(TransactionalAspect::class);
        $this->assertInstanceOf(TransactionalAspect::class, $aspect);
    }

    public function testStartTransactionBasicFunctionality(): void
    {
        $aspect = self::getService(TransactionalAspect::class);

        // 创建 JoinPoint Mock 来测试基本功能
        $joinPoint = $this->createMock(JoinPoint::class);

        // 设置基本的 Mock 行为
        $joinPoint->method('proceed')->willReturn('test result');
        $joinPoint->expects($this->once())->method('setReturnValue')->with('test result');
        $joinPoint->expects($this->once())->method('setReturnEarly')->with(true);

        // 测试方法执行不会抛出异常（使用真实的依赖服务）
        $aspect->startTransaction($joinPoint);

        // Mock验证已经包含了对setReturnValue和setReturnEarly的期望验证
    }

    public function testStartTransaction(): void
    {
        $aspect = self::getService(TransactionalAspect::class);

        // 创建 JoinPoint Mock 来测试 startTransaction 方法
        $joinPoint = $this->createMock(JoinPoint::class);

        // 设置基本的 Mock 行为
        $joinPoint->method('proceed')->willReturn('test result');
        $joinPoint->expects($this->once())->method('setReturnValue')->with('test result');
        $joinPoint->expects($this->once())->method('setReturnEarly')->with(true);

        // 直接测试 startTransaction 方法
        $aspect->startTransaction($joinPoint);

        // Mock验证已经包含了对方法调用的期望验证
    }

    public function testAspectCanBeInstantiated(): void
    {
        $aspect = self::getService(TransactionalAspect::class);
        $this->assertInstanceOf(TransactionalAspect::class, $aspect);
    }
}
