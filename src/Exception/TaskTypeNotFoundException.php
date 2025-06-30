<?php

namespace GrowthTaskBundle\Exception;

use RuntimeException;

class TaskTypeNotFoundException extends RuntimeException
{
    public function __construct(string $taskType, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct("任务类型[{$taskType}]不存在", $code, $previous);
    }
}