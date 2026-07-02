<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4 border-b border-slate-100 pb-4">
        <h2 class="font-bold text-gray-800 text-base flex items-center gap-2">
            <i class="fa-solid fa-chart-simple text-emerald-600"></i> Rekap Kehadiran
        </h2>
        <div class="flex flex-wrap gap-2 w-full sm:w-auto">

            {{-- Pilih Mode --}}
            <select wire:model.live="mode" x-data 
                @change="$dispatch('show-loading', { message: 'Menyiapkan data...' })" 
                class="border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white cursor-pointer w-full sm:w-auto focus:ring-2 focus:ring-green-500 outline-none">
                <option value="tahunan">Tahunan</option>
                <option value="bulanan">Bulanan</option>
            </select>

            {{-- Pilih Tahun --}}
            <select wire:model.live="tahun" x-data 
                @change="$dispatch('show-loading', { message: 'Menyiapkan data tahun...' })" 
                class="border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white cursor-pointer w-full sm:w-auto focus:ring-2 focus:ring-green-500 outline-none">
                @foreach ($listTahun as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>

            {{-- Pilih Bulan --}}
            @if ($mode === 'bulanan')
                <select wire:model.live="bulan" x-data 
                    @change="$dispatch('show-loading', { message: 'Menyiapkan data bulan...' })"
                    class="border border-slate-200 rounded-lg px-3 py-2 text-sm bg-white cursor-pointer w-full sm:w-auto focus:ring-2 focus:ring-green-500 outline-none">
                    @foreach ($listBulan as $key => $nama)
                        <option value="{{ $key }}">{{ $nama }}</option>
                    @endforeach
                </select>
            @endif

        </div>
    </div>

    <div class="w-full overflow-x-auto">
        <div class="min-w-200 h-100 relative" style="height: 300px;">
            <canvas id="grafikAbsensi" wire:ignore></canvas>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('livewire:initialized', () => {

                const ctx = document.getElementById('grafikAbsensi');

                if (!ctx) return;


                const initialData = @json($chartData);


                let chart = new Chart(ctx.getContext('2d'), {

                    type: 'bar',

                    data: {

                        labels: initialData.labels,

                        datasets: [

                            {
                                label: 'Hadir',
                                data: initialData.hadir,
                                backgroundColor: '#10B981',
                            },

                            {
                                label: 'Sakit',
                                data: initialData.sakit,
                                backgroundColor: '#3B82F6',
                            },

                            {
                                label: 'Izin',
                                data: initialData.izin,
                                backgroundColor: '#F59E0B',
                            },

                            {
                                label: 'Mangkir',
                                data: initialData.mankir,
                                backgroundColor: '#EF4444',
                            },
                            {
                                label: 'Terlambat',
                                data: initialData.terlambat,
                                backgroundColor: '#8B5CF6',
                            }
                        ]
                    },


                    options: {

                        responsive: true,

                        maintainAspectRatio: false,


                        scales: {

                            y: {

                                beginAtZero: true,

                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });



                Livewire.on('refreshChart', (event) => {


                    const data = event.data;


                    chart.data.labels = data.labels;


                    chart.data.datasets[0].data = data.hadir;

                    chart.data.datasets[1].data = data.sakit;

                    chart.data.datasets[2].data = data.izin;

                    chart.data.datasets[3].data = data.mankir;

                    chart.data.datasets[4].data = data.terlambat;

                    chart.update();

                });


            });
        </script>
    @endpush
</div>
