<div class="w-full h-full flex gap-10">
    <div class="flex-1 h-full flex flex-col gap-10">
        <div class="w-full h-[200px] grid grid-cols-3 gap-10">
            <div class="bg-white rounded-lg shadow-sm flex items-center justify-between p-6">
                <div class="w-[72px] h-[72px] bg-green-100 rounded-lg grid place-items-center">
                    <i class="fas fa-users text-green-400 text-5xl"></i>
                </div>
                <div class="flex flex-col items-end">
                    <p class="text-4xl font-bold text-green-400">{{ $totalEmployees }}</p>
                    <p class="text-gray-600 font-semibold">Total Employee</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm flex items-center justify-between p-6">
                <div class="w-[72px] h-[72px] bg-blue-100 rounded-lg grid place-items-center">
                    <i class="fas fa-mars text-blue-400 text-5xl"></i>
                </div>
                <div class="flex flex-col items-end">
                    <p class="text-4xl font-bold text-blue-400">{{ $male }}</p>
                    <p class="text-gray-600 font-semibold">Male</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm flex items-center justify-between p-6">
                <div class="w-[72px] h-[72px] bg-red-100 rounded-lg grid place-items-center">
                    <i class="fas fa-venus text-red-400 text-5xl"></i>
                </div>
                <div class="flex flex-col items-end">
                    <p class="text-4xl font-bold text-red-400">{{ $female }}</p>
                    <p class="text-gray-600 font-semibold">Female</p>
                </div>
            </div>
        </div>
        <div class="flex-1 bg-white rounded-lg shadow-sm p-6">
            <p class="text-gray-600 font-semibold text-xl">Length of Service <i><b>w/Ranking</b></i></p>
            <div class="w-full h-full grid grid-cols-[2fr_1fr] gap-4 pb-6">
                <div>
                    <canvas id="serviceChart" data-chart='@json($serviceGroups)' class="w-full h-full"></canvas>
                </div>
                <div>
                    <table class="w-full text-sm text-left text-gray-700 border-collapse">
                        <thead>
                            <tr class="bg-gray-200 border-b border-gray-300">
                                <th class="font-semibold py-3 px-3">Name</th>
                                <th class="font-semibold py-3 px-3" width="15%">Year(s)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $emp)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-2 px-3">
                                        {{ $emp->last_name }}, {{ $emp->first_name }}
                                        {{ $emp->middlename
                                            ? strtoupper(substr($emp->middlename, 0, 1)) . '.'
                                            : ($emp->middle_initial
                                                ? strtoupper(substr($emp->middle_initial, 0, 1)) . '.'
                                                : '') }}
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        {{ $emp->years_of_service }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div class="w-[500px] h-full grid grid-rows-2 gap-10">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-gray-600 font-semibold text-xl">
                Leave Credits <i><b>Ranking</b></i>
            </p>

            <div class="mt-4 h-full overflow-y-auto pr-2">
                <table class="w-full text-sm text-left text-gray-700 border-collapse">
                    <thead>
                        <tr class="bg-gray-200 border-b border-gray-300">
                            <th class="py-2 px-3">#</th>
                            <th class="py-2 px-3">Name</th>
                            <th class="py-2 px-3 text-right">Vacation</th>
                            <th class="py-2 px-3 text-right">Sick</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($employees as $index => $emp)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-2 px-3">{{ $index + 1 }}</td>
                                <td class="py-2 px-3">
                                    {{ $emp->last_name }}, {{ $emp->first_name }}
                                    {{ $emp->middlename
                                        ? strtoupper(substr($emp->middlename, 0, 1)) . '.'
                                        : ($emp->middle_initial
                                            ? strtoupper(substr($emp->middle_initial, 0, 1)) . '.'
                                            : '') }}
                                </td>
                                <td class="py-2 px-3 text-right">{{ number_format($emp->latest_vac, 3) }}</td>
                                <td class="py-2 px-3 text-right">{{ number_format($emp->latest_sick, 3) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-gray-600 font-semibold text-xl">
                Net Balance <i><b>Under 5K</b></i>
            </p>

            <div class="mt-4 max-h-80 overflow-y-auto pr-2">
                <table class="w-full text-sm text-left text-gray-700 border-collapse">
                    <thead>
                        <tr class="bg-gray-200 border-b border-gray-300">
                            <th class="py-2 px-3">#</th>
                            <th class="py-2 px-3">Name</th>
                            <th class="py-2 px-3 text-right">Net Balance</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($underFiveK as $index => $u)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-2 px-3">{{ $index + 1 }}</td>
                                <td class="py-2 px-3 font-semibold">{{ $u['name'] }}</td>
                                <td class="py-2 px-3 text-right">{{ number_format($u['net'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-2 px-3 text-center font-semibold text-gray-400" colspan="3">
                                    No employee with Netpay under 5K
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0/dist/chart.umd.min.js"></script>

    <script>
        let serviceChart;

        function renderChart() {
            const canvasElement = document.getElementById('serviceChart');
            if (!canvasElement) {
                console.error("ERROR: serviceChart canvas element not found in the DOM.");
                return;
            }

            const ctx = canvasElement.getContext('2d');
            if (!ctx) {
                console.error("ERROR: Could not get 2D context from the canvas. Likely a sizing or rendering issue.");
                return;
            }

            let data;
            try {
                data = JSON.parse(canvasElement.dataset.chart);
                console.log("Chart Data Successfully Parsed:", data);
                if (Object.values(data).every(val => val === 0)) {
                    console.warn(
                        "WARNING: All data values for the chart are zero. The chart will render, but will be empty.");
                }
            } catch (e) {
                console.error("ERROR: Failed to parse chart data JSON:", e, "Raw data:", canvasElement.dataset.chart);
                return;
            }

            if (serviceChart) {
                serviceChart.destroy();
            }

            try {
                serviceChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Below 5', '5–9', '10–14', '15+'],
                        datasets: [{
                            label: 'Number of Employees',
                            data: [data.below_5, data.five_to_nine, data.ten_to_fourteen, data.fifteen_up],
                            backgroundColor: ['#34D399', '#60A5FA', '#F87171', '#FBBF24']
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                console.log("Chart successfully initialized!");
            } catch (e) {
                console.error("CRITICAL ERROR: Failed to create new Chart instance:", e);
            }
        }

        document.addEventListener('livewire:load', function() {
            renderChart();

            Livewire.hook('message.processed', () => {
                renderChart();
            });
        });
    </script>
@endpush
</script>
