<?php

namespace App\Enums;

use App\Traits\EnumTrait;

enum InstanceGroup: string
{
    use EnumTrait;

    case SPD_RF2 = 'Spd RF2';

    case LEIGHTON = 'Leighton';

    case LIKE_MERABET = 'Like Merabet';
}
