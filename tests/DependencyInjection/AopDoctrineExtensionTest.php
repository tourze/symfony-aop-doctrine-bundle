<?php

namespace Tourze\Symfony\AopDoctrineBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\Symfony\AopDoctrineBundle\DependencyInjection\AopDoctrineExtension;

/**
 * AopDoctrineExtension 类的单元测试
 */
class AopDoctrineExtensionTest extends TestCase
{
    /**
     * 测试扩展加载是否正常工作，不抛出异常
     */
    public function testLoad(): void
    {
        $container = new ContainerBuilder();
        $extension = new AopDoctrineExtension();

        // 不应抛出异常
        $extension->load([], $container);

        // 只要测试不抛出异常就可以
        $this->assertTrue(true, '扩展加载应该成功不抛出异常');
    }

    /**
     * 测试扩展是否正确添加了服务
     */
    public function testServicesConfiguredProperly(): void
    {
        $container = new ContainerBuilder();
        $extension = new AopDoctrineExtension();

        $extension->load([], $container);

        // 验证容器中有服务定义
        $this->assertGreaterThan(0, count($container->getDefinitions()), '容器应该包含服务定义');
    }
}
