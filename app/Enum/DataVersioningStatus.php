<?php

namespace App\Enum;

enum DataVersioningStatus: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DUPLICATED = 'duplicated';
}
