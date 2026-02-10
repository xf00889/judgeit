<?php

namespace App\Enums;

enum CategoryStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case LOCKED = 'locked';
    case FINALIZED = 'finalized';
}
