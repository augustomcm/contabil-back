<?php

namespace App\Models;

enum EntryStatus : string
{
    case PENDING = 'PENDING';
    case PAID = 'PAID';
}
