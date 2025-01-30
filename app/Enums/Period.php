<?php

namespace App\Enums;

enum Period: string
{
    case Week = 'week';
    case Month = 'month';
    case Year = 'year';
    case ThreeMonths = '3-months';
    case SixMonths = '6-months';
    case None = 'none';
}
