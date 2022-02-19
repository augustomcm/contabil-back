<?php

namespace App\Models;

enum InvoiceStatus : string
{
    case OPENED = 'OPENED';
    case CLOSED = 'CLOSED';
    case PAID = 'PAID';
}
