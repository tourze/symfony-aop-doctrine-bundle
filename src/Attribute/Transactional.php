<?php

namespace Tourze\Symfony\AopDoctrineBundle\Attribute;

/**
 * 参考 Spring Boot 设计
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Transactional
{
}
