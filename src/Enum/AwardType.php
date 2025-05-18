<?php

namespace GrowthTaskBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum AwardType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case COUPON = 'coupon';
    case LOTTERY = 'lottery';
    case CREDIT = 'credit';
    case MATERIAL = 'material';

    // case PRODUCT = 'product';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::COUPON => '优惠券',
            self::LOTTERY => '抽奖次数',
            self::CREDIT => '积分',
            self::MATERIAL => '实物奖',
            // self::PRODUCT => '商品',
            self::OTHER => '其他',
        };
    }
}
