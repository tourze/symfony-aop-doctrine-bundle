# AopDoctrineBundle

AopDoctrineBundle 是一个基于 Symfony 的 Doctrine 增强包，通过 AOP 技术为 Doctrine ORM 提供声明式事务等功能，参考了 Spring Boot 的设计理念。

## 核心功能

### 1. 声明式事务
- 支持通过 `#[Transactional]` 注解标记事务方法
- 自动处理事务的开启和提交
- 智能的事务嵌套处理
- 详细的事务日志记录

### 2. 事务管理
- 自动检测活动事务
- 支持事务传播
- 异常时自动回滚
- 支持事务超时设置

### 3. 性能优化
- 智能的事务复用
- 避免不必要的事务开启
- 事务状态追踪
- 连接池集成支持

## 使用示例

### 1. 声明式事务

```php
use AopDoctrineBundle\Attribute\Transactional;

class YourService
{
    #[Transactional]
    public function doSomething()
    {
        // 这个方法会在事务中执行
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        // 如果抛出异常，事务会自动回滚
        // 如果正常完成，事务会自动提交
    }
}
```

### 2. 嵌套事务处理

```php
class YourService
{
    #[Transactional]
    public function outerMethod()
    {
        // 外层事务
        $this->innerMethod();
        // 事务会在这里提交
    }

    #[Transactional]
    public function innerMethod()
    {
        // 内层方法会复用外层事务
        // 不会创建新的事务
    }
}
```

### 3. 事务日志

```php
use Psr\Log\LoggerInterface;

class YourService
{
    public function __construct(
        private LoggerInterface $logger
    ) {}
    
    #[Transactional]
    public function doSomething()
    {
        // 事务的开启和结束会被自动记录到日志
        // [debug] 通过注解开启事务
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        // [debug] 通过注解结束事务
    }
}
```

## 注意事项

1. 事务管理
   - 事务注解只能用于 public 方法
   - 内层事务会自动复用外层事务
   - 事务方法抛出异常会导致回滚
   - 确保在事务方法中使用正确的 EntityManager

2. 性能考虑
   - 避免过长的事务
   - 不要在事务中执行耗时的非数据库操作
   - 合理设置事务超时时间
   - 注意监控事务执行时间

3. 调试建议
   - 使用日志追踪事务执行
   - 监控事务执行时间
   - 注意检查事务是否正确提交或回滚

4. 限制
   - 不支持跨数据库事务
   - 不支持分布式事务
   - 某些特殊操作可能不适合在事务中执行

5. 最佳实践
   - 事务方法应该尽可能简短
   - 避免在事务中调用外部服务
   - 合理处理事务中的异常
   - 使用正确的事务传播行为
