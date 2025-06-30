<?php

namespace GrowthTaskBundle\Tests\Unit\Exception;

use GrowthTaskBundle\Exception\RewardNotFoundException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class RewardNotFoundExceptionTest extends TestCase
{
    public function testExceptionExtends(): void
    {
        $exception = new RewardNotFoundException();
        $this->assertInstanceOf(RuntimeException::class, $exception);
    }

    public function testDefaultMessage(): void
    {
        $exception = new RewardNotFoundException();
        $this->assertSame('获取奖品失败', $exception->getMessage());
    }

    public function testCustomMessage(): void
    {
        $exception = new RewardNotFoundException('自定义错误');
        $this->assertSame('自定义错误', $exception->getMessage());
    }
}