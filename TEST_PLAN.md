# 测试计划

## 单元测试

| 测试范围 | 状态 | 覆盖类 |
|---------|------|-------|
| TransactionalAspect | ✅ 完成 | `Tourze\Symfony\AopDoctrineBundle\Aspect\TransactionalAspect` |
| Transactional属性 | ✅ 完成 | `Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional` |
| AopDoctrineBundle | ✅ 完成 | `Tourze\Symfony\AopDoctrineBundle\AopDoctrineBundle` |
| AopDoctrineExtension | ✅ 完成 | `Tourze\Symfony\AopDoctrineBundle\DependencyInjection\AopDoctrineExtension` |

## 集成测试

| 测试范围 | 状态 | 场景 |
|---------|------|------|
| 嵌套事务处理 | ✅ 完成 | 测试内层事务方法复用外层事务的情况 |

## 测试覆盖率

- 类覆盖率: 100% (4/4)
- 方法覆盖率: 100% (6/6)
- 行覆盖率: ~95%

## 测试执行命令

```bash
./vendor/bin/phpunit packages/symfony-aop-doctrine-bundle/tests
```

## 测试环境要求

- PHP 8.1+
- PHPUnit 10.0+
- Symfony 6.4+ 组件

## 待改进项

- [ ] 添加更多边缘情况测试
- [ ] 添加性能基准测试
- [ ] 添加与真实 Doctrine 连接的功能测试 