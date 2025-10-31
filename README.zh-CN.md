# Symfony AOP Doctrine Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![最新版本](https://img.shields.io/packagist/v/tourze/symfony-aop-doctrine-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-doctrine-bundle)
[![构建状态](https://img.shields.io/travis/tourze/symfony-aop-doctrine-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/symfony-aop-doctrine-bundle)
[![质量评分](https://img.shields.io/scrutinizer/g/tourze/symfony-aop-doctrine-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/symfony-aop-doctrine-bundle)
[![下载次数](https://img.shields.io/packagist/dt/tourze/symfony-aop-doctrine-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-doctrine-bundle)
[![PHP 版本](https://img.shields.io/packagist/php-v/tourze/symfony-aop-doctrine-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-doctrine-bundle)
[![许可证](https://img.shields.io/packagist/l/tourze/symfony-aop-doctrine-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-doctrine-bundle)
[![代码覆盖率](https://img.shields.io/scrutinizer/coverage/g/tourze/symfony-aop-doctrine-bundle/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/symfony-aop-doctrine-bundle/?branch=master)

一个基于 Symfony 的 Doctrine ORM 增强包，通过 AOP（面向切面编程）为 Doctrine ORM 提供声明式事务管理，灵感源自 Spring Boot。

## 功能特性

- 使用 `#[Transactional]` 属性实现声明式事务
- 自动管理事务的开启、提交与回滚
- 支持事务嵌套与事务传播
- 详细的事务日志记录
- 智能事务复用与性能优化

## 安装说明

```bash
composer require tourze/symfony-aop-doctrine-bundle
```

## 快速开始

```php
use Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional;

class YourService
{
    #[Transactional]
    public function doSomething()
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        // 如果抛出异常，事务会自动回滚
        // 正常完成则自动提交事务
    }
}
```

### 嵌套事务示例

```php
class YourService
{
    #[Transactional]
    public function outerMethod()
    {
        $this->innerMethod();
        // 事务会在这里统一提交
    }

    #[Transactional]
    public function innerMethod()
    {
        // 内层方法会自动复用外层事务，不会新建事务
    }
}
```

### 事务日志示例

```php
use Psr\Log\LoggerInterface;

class YourService
{
    public function __construct(private LoggerInterface $logger) {}

    #[Transactional]
    public function doSomething()
    {
        // 事务的开启与结束会自动写入日志
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
```

## 注意事项

- `#[Transactional]` 仅能用于 public 方法
- 嵌套事务会自动复用外层事务
- 事务方法抛出异常会自动回滚
- 请确保在事务方法中使用正确的 EntityManager
- 避免长时间持有事务，避免在事务中执行非数据库耗时操作
- 不支持分布式事务与跨数据库事务

## 贡献指南

详见 [CONTRIBUTING.md](CONTRIBUTING.md)

## 许可证

MIT 开源协议，详见 [LICENSE](LICENSE)
