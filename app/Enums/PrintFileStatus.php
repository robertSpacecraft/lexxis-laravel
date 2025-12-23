<?php

namespace App\Enums;

enum PrintFileStatus: string
{
    case Uploaded = 'uploaded';
    case Reviewed = 'reviewed';
    case Rejected = 'rejected';
    case Approved = 'approved';
}
