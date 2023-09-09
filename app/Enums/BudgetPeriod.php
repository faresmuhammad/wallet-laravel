<?php

namespace App\Enums;

enum BudgetPeriod: string
{
    case OneTime = 'One Time';
    case Week = 'Week';
    case Month = 'Month';
    case Year = 'Year';
}
