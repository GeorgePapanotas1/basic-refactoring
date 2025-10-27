<?php

namespace App\Versions\D_NoConfigArrays\Enums;

enum RoundingModes: string
{
    case FLOOR = 'floor';
    case CEIL = 'ceil';
    case ROUND = 'round';
}
