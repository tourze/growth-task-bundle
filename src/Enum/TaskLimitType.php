<?php

namespace GrowthTaskBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum TaskLimitType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case DAY = 'day';
    case MONTH = 'month';
    case YEAR = 'year';
    case ACTIVITY_TIME = 'activity_time';

    public function getLabel(): string
    {
        return match ($this) {
            self::DAY => '每日',
            self::MONTH => '每月',
            self::YEAR => '每年',
            self::ACTIVITY_TIME => '活动期间',
        };
    }
}
