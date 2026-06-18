<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-6">
    <div class="flex justify-between items-center mb-4 gap-3">
        <h2 class="font-semibold text-gray-800">
            Rekap Kehadiran
        </h2>
        <div class="flex gap-2">


            {{-- Pilih Mode --}}
            <select wire:model.live="mode" class="border rounded-lg px-3 py-1 text-sm">

                <option value="tahunan">
                    Tahunan
                </option>

                <option value="bulanan">
                    Bulanan
                </option>

            </select>



            {{-- Pilih Tahun --}}
            <select wire:model.live="tahun" class="border rounded-lg px-3 py-1 text-sm">

                @foreach ($listTahun as $t)
                    <option value="{{ $t }}">
                        {{ $t }}
                    </option>
                @endforeach

            </select>



            {{-- Pilih Bulan --}}
            @if ($mode === 'bulanan')

                <select wire:model.live="bulan" class="border rounded-lg px-3 py-1 text-sm">


                    @foreach ($listBulan as $key => $nama)
                        <option value="{{ $key }}">
                            {{ $nama }}
                        </option>
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
