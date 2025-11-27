<?php

namespace App\Versions\D_NextLesson\Enums;

enum RoundingModes: string
{
    case FLOOR = 'floor';
    case CEIL = 'ceil';
    case ROUND = 'round';
}
