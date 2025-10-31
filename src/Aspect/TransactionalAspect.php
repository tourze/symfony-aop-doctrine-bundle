<?php

namespace Tourze\Symfony\AopDoctrineBundle\Aspect;

use Doctrine\DBAL\Connection;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\Symfony\Aop\Attribute\Aspect;
use Tourze\Symfony\Aop\Attribute\Before;
use Tourze\Symfony\Aop\Model\JoinPoint;
use Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional;

#[Aspect]
#[WithMonologChannel(channel: 'aop_doctrine')]
#[Autoconfigure(public: true)]
class TransactionalAspect
{
    public function __construct(
        private readonly Connection $connection,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Before(methodAttribute: Transactional::class)]
    public function startTransaction(JoinPoint $joinPoint): void
    {
        if ($this->connection->isTransactionActive()) {
            $res = $joinPoint->proceed();
            $joinPoint->setReturnValue($res);
            $joinPoint->setReturnEarly(true);

            return;
        }

        $this->connection->transactional(function () use ($joinPoint): void {
            $this->logger->debug('通过注解开启事务');
            try {
                $res = $joinPoint->proceed();
                $joinPoint->setReturnValue($res);
                $joinPoint->setReturnEarly(true);
            } finally {
                $this->logger->debug('通过注解结束事务');
            }
        });
    }
}
