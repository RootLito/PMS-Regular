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
        <div class="flex-1 grid grid-cols-3 gap-10 ">
            {{-- CHART   --}}
            <div class="grid place-items-center bg-white rounded-lg shadow-sm p-6">
                <canvas id="serviceChart" data-chart='@json($serviceGroups)' class="w-full h-full"></canvas>
            </div>


            {{-- LEAVE CREDITS   --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-gray-600 font-semibold text-xl">
                    Leave Credits <i><b>Ranking</b></i>
                </p>

                <div class="mt-4 h-full overflow-y-auto pr-2">
                    <table class="w-full text-sm text-left text-gray-700 border-collapse">
                        <thead>
                            <tr class="bg-gray-200 border-b border-gray-300 text-xs">
                                <th class="py-2 px-3">Name</th>
                                <th class="py-2 px-3 text-right">Vacation</th>
                                <th class="py-2 px-3 text-right">Sick</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($employees as $index => $emp)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-2 px-3 text-xs" width="40%">
                                        {{ ucfirst(strtolower($emp->last_name)) }},
                                        {{ ucfirst(strtolower($emp->first_name)) }}
                                        {{ $emp->middlename
                                            ? ucfirst(strtolower(substr($emp->middlename, 0, 1))) . '.'
                                            : ($emp->middle_initial
                                                ? ucfirst(strtolower(substr($emp->middle_initial, 0, 1))) . '.'
                                                : '') }}
                                    </td>
                                    <td class="py-2 px-3 text-left font-semibold text-xs">
                                        {{ number_format($emp->latest_vac, 3) }}
                                    </td>
                                    <td class="py-2 px-3 text-left font-semibold text-xs">
                                        {{ number_format($emp->latest_sick, 3) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


            {{-- NET BALANCE   --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <p class="text-gray-600 font-semibold text-xl">
                    Net Balance <i><b>Under 5K</b></i>
                </p>

                <div class="mt-4 max-h-80 overflow-y-auto pr-2">
                    <table class="w-full text-sm text-left text-gray-700 border-collapse">
                        <thead>
                            <tr class="bg-gray-200 border-b border-gray-300 text-xs">
                                <th class="py-2 px-3">Name</th>
                                <th class="py-2 px-3 text-right">Net Balance</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($underFiveK as $index => $u)
                                <tr class="border-b border-gray-200 hover:bg-gray-50 text-xs">
                                    <td class="py-2 px-3 ">{{ $u['name'] }}</td>
                                    <td class="py-2 px-3 text-right font-semibold">
                                        {{ number_format($u['net'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-2 px-3 text-center font-semibold text-gray-400" colspan="3">
                                        No Record
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- SERVICE  --}}
    <div class="w-[400px] h-full bg-white rounded-lg shadow-sm p-6">
        <p class="text-gray-600 text-5xl text-center"><i class="fa-solid fa-award"></i></p>
        <p class="text-gray-600 text-sm font-black text-center mb-8 mt-2"><i>LENGTH OF SERVICE RANKING</i></p>

        <div class="w-full grid place-items-center">
            <select wire:model.live="sort"
                class="shadow-sm border rounded border-gray-200 px-4 py-2 mb-8 mx-auto text-sm text-gray-600">
                <option value="">Overall Ranking</option>
                <option value="15_up">15+ years</option>
                <option value="10_14">10-14 years</option>
                <option value="5_9">5-9 years</option>
                <option value="below_4">Below 4 years</option>
            </select>

            {{ $sort }}
        </div>


        <ul class="divide-y divide-gray-100">
            @foreach ($employees as $emp)
                @php
                    $years = $emp->years_of_service_details['years'] ?? 0;
                    $months = $emp->years_of_service_details['months'] ?? 0;

                    $duration = '';
                    if ($years > 0) {
                        $duration .= "{$years}y ";
                    }
                    if ($months > 0) {
                        $duration .= "{$months}m";
                    }
                    $duration = trim($duration);

                    $medalClass = '';
                    $rankIcon = '';
                    $rankTextSize = 'text-2xl';

                    if ($loop->iteration === 1) {
                        $medalClass = 'text-yellow-500';
                        $rankIcon = '<i class="fa-solid fa-medal"></i>';
                    } elseif ($loop->iteration === 2) {
                        $medalClass = 'text-gray-400';
                        $rankIcon = '<i class="fa-solid fa-medal"></i>';
                    } elseif ($loop->iteration === 3) {
                        $medalClass = 'text-amber-700';
                        $rankIcon = '<i class="fa-solid fa-medal"></i>';
                    } else {
                        $rankIcon = $loop->iteration . '.';
                        $rankTextSize = 'text-lg text-gray-700';
                    }
                @endphp

                <li class="flex items-center py-1">
                    <div class="flex items-center space-x-4">
                        <span class="font-bold w-8 text-center {{ $medalClass }} {{ $rankTextSize }}"
                            title="Rank {{ $loop->iteration }}">
                            {!! $rankIcon !!}
                        </span>
                        <div class="py-1">
                            <div class="font-bold text-gray-700">
                                {{ $emp->last_name }}, {{ $emp->first_name }}
                                {{ $emp->middlename
                                    ? strtoupper(substr($emp->middlename, 0, 1)) . '.'
                                    : ($emp->middle_initial
                                        ? strtoupper(substr($emp->middle_initial, 0, 1)) . '.'
                                        : '') }}
                            </div>
                            <div class="font-mono text-xs text-gray-500">
                                {{ $duration ?: 'Less than 1 month / N/A' }}
                            </div>
                            @if (!$emp->appointed_date)
                                <span class="text-xs text-red-500 italic">No appointment date</span>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const serviceChartCanvas = document.getElementById('serviceChart');
            if (!serviceChartCanvas) return;

            const chartData = JSON.parse(serviceChartCanvas.dataset.chart);
            const dataValues = [
                chartData.below_5,
                chartData.five_to_nine,
                chartData.ten_to_fourteen,
                chartData.fifteen_up
            ];
            const labels = [
                'Below 5 Years',
                '5 to 9 Years',
                '10 to 14 Years',
                '15+ Years'
            ];
            const backgroundColors = [
                'rgb(96, 166, 252)',
                'rgb(255, 104, 108)',
                'rgb(253, 203, 0)',
                'rgba(5, 223, 114)'
            ];

            window.serviceChart = new Chart(serviceChartCanvas, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Employees by Service Years',
                        data: dataValues,
                        backgroundColor: backgroundColors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Length of Service Distribution',
                            font: {
                                size: 16
                            }
                        }
                    }
                }
            });
        });

        Livewire.hook('beforeDomUpdate', (el, component) => {
            const chartCanvas = el.querySelector('#serviceChart');
            if (chartCanvas) {
                chartCanvas.removeAttribute('data-chart'); 
            }
        });
    </script>
@endpush
