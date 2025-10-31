<?php

namespace Tourze\Symfony\AopDoctrineBundle\Attribute;

/**
 * 参考 Spring Boot 设计
 */
#[\Attribute(flags: \Attribute::TARGET_METHOD)]
class Transactional
{
}
