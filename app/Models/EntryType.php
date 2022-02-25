<?php

namespace App\Models;

enum EntryType : string
{
    case EXPENSE = "EXPENSE";
    case INCOME = "INCOME";
}
