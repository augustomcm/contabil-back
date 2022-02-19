<?php

namespace App\Models;

enum EntryType: string
{
    case DEFAULT = 'DEFAULT';
    case CREDIT_CARD = 'CREDIT_CARD';
}
