<?php

namespace App\Enums;

enum Validasi: string
{
    case Valid   = 'valid';
    case Invalid = 'invalid';
    case Pending = 'pending';
}
