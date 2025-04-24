<?php

namespace Tourze\Symfony\AopDoctrineBundle\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional;

/**
 * Transactional 属性类的单元测试
 */
class TransactionalTest extends TestCase
{
    /**
     * 测试 Transactional 属性标记是否正确生效
     */
    public function testTransactionalAttribute(): void
    {
        // 通过反射API检查属性是否正确设置
        $reflectionClass = new \ReflectionClass(Transactional::class);

        // 检查类是否有正确的属性标记
        $attributes = $reflectionClass->getAttributes(\Attribute::class);
        $this->assertCount(1, $attributes, 'Transactional类应该有#[Attribute]标记');

        // 检查属性是否设置了正确的目标
        $attribute = $attributes[0]->newInstance();
        $this->assertEquals(\Attribute::TARGET_METHOD, $attribute->flags, 'Transactional属性应只能用于方法');
    }

    /**
     * 测试 Transactional 属性类可以被实例化
     */
    public function testCanBeInstantiated(): void
    {
        $transactional = new Transactional();
        $this->assertInstanceOf(Transactional::class, $transactional);
    }
}
