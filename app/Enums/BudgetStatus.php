<?php

namespace App\Enums;

enum BudgetStatus: string
{
    case Active = 'Active';
    case Finished = 'Finished';
    case NotStarted = 'Not Started';
}
