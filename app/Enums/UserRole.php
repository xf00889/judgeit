<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case JUDGE = 'judge';
    case TABULATOR = 'tabulator';
}
