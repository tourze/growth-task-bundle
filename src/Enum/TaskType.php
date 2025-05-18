<?php

namespace GrowthTaskBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum TaskType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case CHECKIN = 'checkin';
    case POSTS = 'posts';
    case LIKE = 'like';
    case COMMENTS = 'comments';
    case SHARE = 'share';
    case INVITE = 'invite';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::CHECKIN => '签到',
            self::POSTS => '发布帖子',
            self::LIKE => '点赞',
            self::COMMENTS => '评论',
            self::SHARE => '分享',
            self::INVITE => '邀请',
            self::OTHER => '其他',
        };
    }
}
