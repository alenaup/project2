<?php
namespace App\Enums;

enum TipeKehadiran: string
{
    case Hadir      = 'hadir';
    case Sakit      = 'sakit';
    case Izin       = 'izin';
    case Mankir     = 'mankir';
    case Cuti       = 'cuti';
    case Terlambat  = 'terlambat';
}
