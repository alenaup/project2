<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Kerja {{ $monthName }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #3C8B5E; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #3C8B5E; font-size: 20px; }
        .header p { margin: 5px 0 0; color: #666; font-size: 14px; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; }
        .info-label { font-weight: bold; width: 100px; color: #555; }
        
        .calendar-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .calendar-table th, .calendar-table td { border: 1px solid #ddd; padding: 8px; text-align: center; vertical-align: top; width: 14.28%; }
        .calendar-table th { background-color: #f8f9fa; font-weight: bold; color: #555; padding: 10px; }
        .calendar-table td { height: 80px; }
        
        .day-num { text-align: right; font-weight: bold; font-size: 11px; margin-bottom: 5px; color: #777; }
        .shift-label { background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 4px; border-radius: 4px; font-size: 10px; font-weight: bold; margin-top: 5px; word-wrap: break-word; }
        .shift-label.malam { background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
        .shift-label.lain { background-color: #fff7ed; border: 1px solid #fed7aa; color: #9a3412; }
        .shift-label.off { background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Jadwal Kerja Karyawan</h1>
        <p>Bulan: {{ $monthName }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Nama:</td>
            <td>{{ $user->nama ?? 'Karyawan' }}</td>
            <td class="info-label">Divisi:</td>
            <td>Outsourcing</td>
        </tr>
    </table>

    <table class="calendar-table">
        <thead>
            <tr>
                <th>Senin</th>
                <th>Selasa</th>
                <th>Rabu</th>
                <th>Kamis</th>
                <th>Jumat</th>
                <th style="color:red">Sabtu</th>
                <th style="color:red">Minggu</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @for ($i = 1; $i < $firstDayOfWeek; $i++)
                    <td style="background-color: #f9fafb;"></td>
                @endfor

                @php
                    $currentDayOfWeek = $firstDayOfWeek;
                @endphp

                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                        $hasJadwal = isset($calendarData[$dateStr]);
                    @endphp

                    <td>
                        <div class="day-num">{{ $day }}</div>
                        @if($hasJadwal)
                            @php
                                $shift = $calendarData[$dateStr];
                                $tipe = strtolower($shift->tipe_shift);
                                $class = '';
                                if (str_contains($tipe, 'malam')) {
                                    $class = 'malam';
                                } elseif (!str_contains($tipe, 'pagi') && !str_contains($tipe, 'malam')) {
                                    $class = 'lain';
                                }
                            @endphp
                            <div class="shift-label {{ $class }}">
                                {{ $shift->tipe_shift }}<br>
                                {{ substr($shift->jam_masuk,0,5) }} - {{ substr($shift->jam_keluar,0,5) }}
                            </div>
                        @else
                            <div class="shift-label off">
                                OFF / Libur
                            </div>
                        @endif
                    </td>

                    @if ($currentDayOfWeek == 7)
                        </tr>
                        @if ($day < $daysInMonth)
                            <tr>
                        @endif
                        @php $currentDayOfWeek = 1; @endphp
                    @else
                        @php $currentDayOfWeek++; @endphp
                    @endif
                @endfor

                {{-- Fill remaining days of the week --}}
                @if ($currentDayOfWeek > 1)
                    @for ($i = $currentDayOfWeek; $i <= 7; $i++)
                        <td style="background-color: #f9fafb;"></td>
                    @endfor
                    </tr>
                @endif
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
    </div>

</body>
</html>
