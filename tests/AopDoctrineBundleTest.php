<?php

declare(strict_types=1);

namespace Tourze\Symfony\AopDoctrineBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\Symfony\AopDoctrineBundle\AopDoctrineBundle;

/**
 * @internal
 */
#[CoversClass(AopDoctrineBundle::class)]
#[RunTestsInSeparateProcesses]
final class AopDoctrineBundleTest extends AbstractBundleTestCase
{
}
