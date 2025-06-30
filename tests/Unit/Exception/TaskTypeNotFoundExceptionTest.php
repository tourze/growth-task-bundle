<?php

namespace GrowthTaskBundle\Tests\Unit\Exception;

use GrowthTaskBundle\Exception\TaskTypeNotFoundException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class TaskTypeNotFoundExceptionTest extends TestCase
{
    public function testExceptionExtends(): void
    {
        $exception = new TaskTypeNotFoundException('test_type');
        $this->assertInstanceOf(RuntimeException::class, $exception);
    }

    public function testMessage(): void
    {
        $exception = new TaskTypeNotFoundException('test_type');
        $this->assertSame('任务类型[test_type]不存在', $exception->getMessage());
    }
}