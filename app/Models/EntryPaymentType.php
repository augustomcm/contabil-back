<?php

namespace App\Models;

enum EntryPaymentType: string
{
    case DEFAULT = 'DEFAULT';
    case CREDIT_CARD = 'CREDIT_CARD';
}
