# Symfony AOP Doctrine Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/symfony-aop-doctrine-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-doctrine-bundle)
[![Build Status](https://img.shields.io/travis/tourze/symfony-aop-doctrine-bundle/master.svg?style=flat-square)](https://travis-ci.org/tourze/symfony-aop-doctrine-bundle)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/symfony-aop-doctrine-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/symfony-aop-doctrine-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/symfony-aop-doctrine-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-doctrine-bundle)

A Symfony bundle that enhances Doctrine ORM with declarative transaction management via AOP (Aspect-Oriented Programming), inspired by the design of Spring Boot.

## Features

- Declarative transaction support with `#[Transactional]` attribute
- Automatic transaction begin/commit/rollback
- Nested transaction and transaction propagation
- Detailed transaction logging
- Smart transaction reuse and performance optimization

## Installation

```bash
composer require tourze/symfony-aop-doctrine-bundle
```

## Quick Start

```php
use Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional;

class YourService
{
    #[Transactional]
    public function doSomething()
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        // If an exception is thrown, the transaction will be rolled back automatically.
        // If completed normally, the transaction will be committed automatically.
    }
}
```

### Nested Transactions

```php
class YourService
{
    #[Transactional]
    public function outerMethod()
    {
        $this->innerMethod();
        // The transaction is committed here.
    }

    #[Transactional]
    public function innerMethod()
    {
        // This method reuses the outer transaction, no new transaction is created.
    }
}
```

### Transaction Logging

```php
use Psr\Log\LoggerInterface;

class YourService
{
    public function __construct(private LoggerInterface $logger) {}

    #[Transactional]
    public function doSomething()
    {
        // Transaction start and end will be automatically logged.
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
```

## Notes

- The `#[Transactional]` attribute can only be applied to public methods.
- Nested transactions are automatically reused.
- Any exception thrown in a transactional method will cause a rollback.
- Make sure to use the correct EntityManager inside transactional methods.
- Avoid long-running transactions and non-DB operations inside transactions.
- Distributed and cross-database transactions are not supported.

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

MIT License. See [LICENSE](LICENSE) for details.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and upgrade notes.
