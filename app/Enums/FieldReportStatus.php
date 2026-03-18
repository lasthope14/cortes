<?php

namespace App\Enums;

enum FieldReportStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
}
