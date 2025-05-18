<?php

namespace GrowthTaskBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum RangeType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case BIGGER = 'bigger';
    case LOWER = 'lower';
    case EQUALS = 'equals';

    public function getLabel(): string
    {
        return match ($this) {
            self::BIGGER => '大于',
            self::EQUALS => '等于',
            self::LOWER => '小于',
        };
    }
}
