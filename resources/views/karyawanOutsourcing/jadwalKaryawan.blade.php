@extends('layouts.karyawanOutsourcing')

@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 bg-white/70 rounded-xl shadow">
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800 md:text-2xl">Jadwal Kerjaku </h1>
                <p class="text-gray-500 text-sm">Lihat detail waktu shift kerjamu untuk bulan ini.</p>
            </div>
            <button
                class="bg-white border-2 border-[#3C8B5E] text-[#3C8B5E] px-4 py-2 rounded-lg font-medium shadow-sm hover:bg-emerald-50 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-download"></i> Download PDF
            </button>
        </div>

        <div class="bg-white rounded-xl shadow p-4 md:p-6 overflow-hidden border-t-4 border-[#3C8B5E]">

            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <div class="flex items-center gap-2 md:gap-4">
                    <button class="p-1 md:p-2 border rounded-lg hover:bg-gray-100 transition"><i
                            class="fa-solid fa-chevron-left text-gray-600"></i></button>
                    <h2 class="text-lg md:text-xl font-bold text-gray-800 w-28 md:w-32 text-center">April 2026</h2>
                    <button class="p-1 md:p-2 border rounded-lg hover:bg-gray-100 transition"><i
                            class="fa-solid fa-chevron-right text-gray-600"></i></button>
                </div>

                <button class="text-sm font-medium text-emerald-600 hover:underline">Ke Hari Ini</button>
            </div>

            <div class="overflow-x-auto">
                <div class="min-w-700px">

                    <div class="grid grid-cols-7 gap-2 mb-2 text-center">
                        <div class="font-bold text-sm text-gray-500 py-2">Senin</div>
                        <div class="font-bold text-sm text-gray-500 py-2">Selasa</div>
                        <div class="font-bold text-sm text-gray-500 py-2">Rabu</div>
                        <div class="font-bold text-sm text-gray-500 py-2">Kamis</div>
                        <div class="font-bold text-sm text-gray-500 py-2">Jumat</div>
                        <div class="font-bold text-sm text-red-500 py-2">Sabtu</div>
                        <div class="font-bold text-sm text-red-500 py-2">Minggu</div>
                    </div>

                    <div class="grid grid-cols-7 gap-2">

                        <div class="border rounded-lg min-h-100px p-2 bg-gray-50 border-gray-100 opacity-50 flex flex-col">
                            <p class="text-right text-sm text-gray-400 font-medium mb-1">30</p>
                        </div>
                        <div class="border rounded-lg min-h-100px p-2 bg-gray-50 border-gray-100 opacity-50 flex flex-col">
                            <p class="text-right text-sm text-gray-400 font-medium mb-1">31</p>
                        </div>

                        <div
                            class="border-2 border-emerald-400 rounded-lg min-h-100px p-2 bg-emerald-50 shadow-sm relative group cursor-pointer flex flex-col">
                            <div
                                class="absolute -top-2 -right-2 bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">
                                Hari Ini</div>
                            <p class="text-right text-sm text-emerald-700 font-bold mb-1">1</p>
                            <div
                                class="bg-emerald-200 text-emerald-800 text-[11px] font-bold p-1.5 rounded text-center truncate mt-auto mb-1">
                                <i class="fa-solid fa-sun mr-1"></i> Pagi (08:00 - 17:00)
                            </div>
                        </div>

                        <script>
                            const shifts = [{
                                    shift: '<i class="fa-solid fa-sun mr-1"></i> Pagi (08:00 - 17:00)',
                                    bg: "bg-emerald-100",
                                    text: "text-emerald-700",
                                    border: "hover:border-emerald-400"
                                },
                                {
                                    shift: '<i class="fa-solid fa-moon mr-1"></i> Malam (20:00 - 05:00)',
                                    bg: "bg-blue-100",
                                    text: "text-blue-700",
                                    border: "hover:border-blue-400"
                                },
                                {
                                    shift: '<i class="fa-solid fa-mug-hot mr-1"></i> LIBUR / OFF',
                                    bg: "bg-red-100",
                                    text: "text-red-700",
                                    border: "hover:border-red-400"
                                }
                            ];

                            // Simulasi jadwal
                            const pola = [0, 1, 1, 2, 2, 0, 0, 0, 1, 1, 2, 2, 0, 0, 0, 1, 1, 2, 2, 0, 0, 0, 1, 1, 2, 2, 0, 0, 0];

                            for (let i = 2; i <= 30; i++) {
                                let s = shifts[pola[i - 2]];
                                document.write(`
                                    <div class="border border-gray-200 rounded-lg min-h-[100px] p-2 bg-white transition cursor-pointer flex flex-col ${s.border}">
                                        <p class="text-right text-sm text-gray-700 font-bold mb-1">${i}</p>
                                        <div class="${s.bg} ${s.text} text-[11px] font-bold p-1.5 rounded text-center truncate mt-auto mb-1">
                                            ${s.shift}
                                        </div>
                                    </div>
                                    `);
                            }
                        </script>

                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-6 border-t pt-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-sun text-emerald-500"></i>
                    <span class="text-sm font-medium text-gray-600">Shift Pagi</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-moon text-blue-500"></i>
                    <span class="text-sm font-medium text-gray-600">Shift Malam</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-mug-hot text-red-500"></i>
                    <span class="text-sm font-medium text-gray-600">Libur / OFF</span>
                </div>
            </div>

        </div>
    </div>
@endsection()
