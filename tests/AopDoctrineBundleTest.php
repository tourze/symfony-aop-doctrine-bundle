<?php

namespace Tourze\Symfony\AopDoctrineBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\Symfony\AopDoctrineBundle\AopDoctrineBundle;

/**
 * AopDoctrineBundle 类的单元测试
 */
class AopDoctrineBundleTest extends TestCase
{
    /**
     * 测试 Bundle 是否继承自 Symfony Bundle 类
     */
    public function testIsSymfonyBundle(): void
    {
        $bundle = new AopDoctrineBundle();
        $this->assertInstanceOf(Bundle::class, $bundle, 'AopDoctrineBundle应该继承自Symfony Bundle类');
    }

    /**
     * 测试 Bundle 是否可以正确实例化
     */
    public function testCanBeInstantiated(): void
    {
        $bundle = new AopDoctrineBundle();
        $this->assertInstanceOf(AopDoctrineBundle::class, $bundle);
    }

    /**
     * 测试 Bundle 的 getPath 方法
     */
    public function testGetPath(): void
    {
        $bundle = new AopDoctrineBundle();
        $path = $bundle->getPath();

        $this->assertDirectoryExists($path, 'getPath应返回有效目录路径');
        $this->assertStringEndsWith('src', $path, 'getPath应指向src目录');
    }
}
