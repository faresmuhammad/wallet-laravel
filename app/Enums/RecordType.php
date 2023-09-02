<?php

namespace App\Enums;

enum RecordType: string
{
    case Expense = 'Expense';
    case Income = 'Income';
    case Transfer = 'Transfer';
}
