<?php

namespace GrowthTaskBundle\Exception;

use RuntimeException;

class RewardNotFoundException extends RuntimeException
{
    public function __construct(string $message = '获取奖品失败', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}