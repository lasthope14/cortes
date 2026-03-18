<?php

namespace App\Enums;

enum EstimateStatus: string
{
    case Draft = 'draft';
    case Reviewing = 'reviewing';
    case Issued = 'issued';
    case Cancelled = 'cancelled';
}
