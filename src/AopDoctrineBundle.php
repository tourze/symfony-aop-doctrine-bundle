<?php

namespace Tourze\Symfony\AopDoctrineBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\Symfony\Aop\AopBundle;

class AopDoctrineBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            AopBundle::class => ['all' => true],
        ];
    }
}
