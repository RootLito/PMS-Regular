<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Office;

use Carbon\Carbon;



use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollExport;
use App\Models\Assign;

class RegularPayrollData extends Component
{
    public $officeOptions = [];
    public array $office = [];
    public bool $showOffices = false;

    public $month;
    public $year;
    public $months = [];
    public $years = [];
    public $assigned;

    public $dateRange;

    public function mount()
    {
        $this->officeOptions = Office::orderBy('order_no')->pluck('office', 'office')->values()->all();

        $this->months = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        $currentYear = now()->year;
        $this->years = range($currentYear - 5, $currentYear + 5);

        $this->month = now()->month;
        $this->year = $currentYear;

        $this->office = [];

        $this->assigned = Assign::with(['prepared', 'noted', 'funds', 'approved'])->latest()->first();


        dd($this->assigned);
    }

    public function toggleOffices()
    {
        $this->showOffices = !$this->showOffices;
    }

    public function proceed()
    {
        $this->showOffices = false;
    }

    public function updatedMonth()
    {
        $this->updateDateRange();
    }

    public function updatedYear()
    {
        $this->updateDateRange();
    }

    public function updateDateRange()
    {
        if (!$this->month || !$this->year) {
            $this->dateRange = null;
            return;
        }

        $start = Carbon::createFromDate($this->year, $this->month, 1);
        $end = $start->copy()->endOfMonth();

        $this->dateRange = strtoupper($start->format('F')) . ' ' . $start->day . '-' . $end->day . ', ' . $this->year;
    }





    public function prepareExportData($dateRange)
    {
        $employees = Employee::with(['contribution', 'officeDetails'])
            ->when(!empty($this->office), function ($query) {
                $query->whereIn('office', $this->office);
            })
            ->get();

        $otherContributions = [
            'hdmf_mpl',
            'hdmf_hl',
            'gsis_pol',
            'gsis_consoloan',
            'gsis_emer',
            'gsis_cpl',
            'gsis_gfal',
            'g_mpl',
            'g_lite',
            'bfar_provident',
            'dareco',
            'ucpb_savings',
            'isda_savings_loan',
            'isda_savings_cap_con',
            'tagumcoop_sl',
            'tagum_coop_cl',
            'tagum_coop_sc',
            'tagum_coop_rs',
            'tagum_coop_ers_gasaka_suretech_etc',
            'nd',
            'lbp_sl'
        ];

        $employees->each(function ($employee) use ($otherContributions) {
            if ($employee->contribution) {
                $filtered = collect($employee->contribution->toArray())
                    ->filter(fn($value) => !is_null($value) && $value !== 0 && $value !== '');
                $employee->filtered_contribution = (object) $filtered;

                $totalFive =
                    ($employee->contribution->tax ?? 0) +
                    ($employee->contribution->phic ?? 0) +
                    ($employee->contribution->gsis_ps ?? 0) +
                    ($employee->contribution->hdmf_ps ?? 0) +
                    ($employee->contribution->hdmf_mp2 ?? 0);

                $totalOthers = collect($otherContributions)->sum(fn($field) => $employee->contribution->$field ?? 0);

                $employee->contribution->total_deductions = $totalFive + $totalOthers;
            } else {
                $employee->filtered_contribution = null;
                $employee->contribution = (object)['total_deductions' => 0, 'pera' => 0]; // Ensure it's an object to prevent errors
            }
        });


        $employeesByOffice = $employees->groupBy(function ($employee) {
            return $employee->officeDetails->name ?? $employee->office ?? 'Unknown Office';
        });

        $totalsByOffice = [];


        foreach ($employeesByOffice as $officeName => $officeEmployees) {
            $monthlyRateSum = $officeEmployees->sum('monthly_rate');
            $peraSum = $officeEmployees->sum(fn($e) => $e->contribution->pera ?? 0);
            $totalUacs = $monthlyRateSum + $peraSum;

            $totalOthers = collect($otherContributions)->sum(fn($field) => $officeEmployees->sum(fn($e) => $e->contribution->$field ?? 0));

            $totalFive =
                $officeEmployees->sum(fn($e) => $e->contribution->tax ?? 0) +
                $officeEmployees->sum(fn($e) => $e->contribution->phic ?? 0) +
                $officeEmployees->sum(fn($e) => $e->contribution->gsis_ps ?? 0) +
                $officeEmployees->sum(fn($e) => $e->contribution->hdmf_ps ?? 0) +
                $officeEmployees->sum(fn($e) => $e->contribution->hdmf_mp2 ?? 0);

            $totalDeductions = $totalFive + $totalOthers;

            $ratepera = $monthlyRateSum + $peraSum;
            $netPay = $ratepera - $totalDeductions;

            $first = $netPay / 2;
            $second = $netPay / 2;

            $earnPeriod = 0;

            $totalsByOffice[$officeName] = [
                'monthly_rate' => $monthlyRateSum,
                'tax' => $officeEmployees->sum(fn($e) => $e->contribution->tax ?? 0),
                'phic' => $officeEmployees->sum(fn($e) => $e->contribution->phic ?? 0),
                'gsis_ps' => $officeEmployees->sum(fn($e) => $e->contribution->gsis_ps ?? 0),
                'hdmf_ps' => $officeEmployees->sum(fn($e) => $e->contribution->hdmf_ps ?? 0),
                'hdmf_mp2' => $officeEmployees->sum(fn($e) => $e->contribution->hdmf_mp2 ?? 0),
                'hdmf_mpl' => $officeEmployees->sum(fn($e) => $e->contribution->hdmf_mpl ?? 0),
                'hdmf_hl' => $officeEmployees->sum(fn($e) => $e->contribution->hdmf_hl ?? 0),
                'gsis_pol' => $officeEmployees->sum(fn($e) => $e->contribution->gsis_pol ?? 0),
                'gsis_consoloan' => $officeEmployees->sum(fn($e) => $e->contribution->gsis_consoloan ?? 0),
                'gsis_emer' => $officeEmployees->sum(fn($e) => $e->contribution->gsis_emer ?? 0),
                'gsis_cpl' => $officeEmployees->sum(fn($e) => $e->contribution->gsis_cpl ?? 0),
                'gsis_gfal' => $officeEmployees->sum(fn($e) => $e->contribution->gsis_gfal ?? 0),
                'g_mpl' => $officeEmployees->sum(fn($e) => $e->contribution->g_mpl ?? 0),
                'g_lite' => $officeEmployees->sum(fn($e) => $e->contribution->g_lite ?? 0),
                'bfar_provident' => $officeEmployees->sum(fn($e) => $e->contribution->bfar_provident ?? 0),
                'dareco' => $officeEmployees->sum(fn($e) => $e->contribution->dareco ?? 0),
                'ucpb_savings' => $officeEmployees->sum(fn($e) => $e->contribution->ucpb_savings ?? 0),
                'isda_savings_loan' => $officeEmployees->sum(fn($e) => $e->contribution->isda_savings_loan ?? 0),
                'isda_savings_cap_con' => $officeEmployees->sum(fn($e) => $e->contribution->isda_savings_cap_con ?? 0),
                'tagumcoop_sl' => $officeEmployees->sum(fn($e) => $e->contribution->tagumcoop_sl ?? 0),
                'tagum_coop_cl' => $officeEmployees->sum(fn($e) => $e->contribution->tagum_coop_cl ?? 0),
                'tagum_coop_sc' => $officeEmployees->sum(fn($e) => $e->contribution->tagum_coop_sc ?? 0),
                'tagum_coop_rs' => $officeEmployees->sum(fn($e) => $e->contribution->tagum_coop_rs ?? 0),
                'tagum_coop_ers_gasaka_suretech_etc' => $officeEmployees->sum(fn($e) => $e->contribution->tagum_coop_ers_gasaka_suretech_etc ?? 0),
                'nd' => $officeEmployees->sum(fn($e) => $e->contribution->nd ?? 0),
                'lbp_sl' => $officeEmployees->sum(fn($e) => $e->contribution->lbp_sl ?? 0),
                'total_charges' => $officeEmployees->sum(fn($e) => $e->contribution->total_charges ?? 0),
                'pera' => $peraSum,
                'uacs' => $totalUacs,
                'totalOthers' => $totalOthers,
                'totalDeductions' => $totalDeductions,
                'net_pay' => $netPay,
                'first' => $first,
                'second' => $second,
                'earnPeriod' => $earnPeriod,
                'total_salary' => $officeEmployees->sum(fn($e) => $e->contribution->total_salary ?? 0),
                'gross' => $officeEmployees->sum(fn($e) => $e->contribution->gross ?? 0),
                'rate_per_month' => $officeEmployees->sum(fn($e) => $e->contribution->rate_per_month ?? 0),
                'leave_wo' => $officeEmployees->sum(fn($e) => $e->contribution->leave_wo ?? 0),
            ];
        }

        return [
            'employeesByOffice' => $employeesByOffice,
            'totalsByOffice' => $totalsByOffice,
            'dateRange' => $dateRange,
        ];
    }





    //ARCHIVE-------------------------------------
    public function saveArchive()
    {
        // $assignedData = $this->assigned ? $this->assigned->toArray() : [];
        // $exportData = $this->prepareExportData(
        //     $this->month,
        //     $this->year,
        //     $this->cutoff,
        //     $this->designation,
        //     $assignedData,
        //     $this->dateRange
        // );
        // $filename = 'COS Payroll ' . now()->year . ' - Region XI_' . now()->format('Ymd_His') . '.xlsx';
        // $path = 'archives/' . $filename;
        // Excel::store(new PayrollExport($exportData), $path, 'public');
        // Archived::create([
        //     'filename' => $filename,
        //     'cutoff' => $this->cutoff,
        //     'month' => $this->month,
        //     'year' => $this->year,
        //     'date_saved' => now(),
        // ]);
        // $this->dispatch('success', message: 'Archive saved successfully!');
    }
    //EXPORT --------------------------------------
    public function exportPayroll()
    {
        $exportData = $this->prepareExportData($this->dateRange);
        return Excel::download(
            new PayrollExport($exportData),
            $this->year . ' REGULAR PAYROLL-with LBP.xlsx'
        );
    }



    public function render()
    {
        $employees = Employee::with(['contribution', 'officeDetails'])
            ->when(!empty($this->office), function ($query) {
                $query->whereIn('office', $this->office);
            })
            ->get();

        $employees->each(function ($employee) {
            if ($employee->contribution) {
                $filtered = collect($employee->contribution->toArray())
                    ->filter(fn($value) => !is_null($value) && $value !== 0 && $value !== '');
                $employee->filtered_contribution = (object) $filtered;
            } else {
                $employee->filtered_contribution = null;
            }
        });

        $employeesByOffice = $employees->groupBy(function ($employee) {
            return $employee->officeDetails->name ?? $employee->office ?? 'Unknown Office';
        });

        $totalsByOffice = [];

        // $grandTotalSalary = 0;
        // $otherTotal = 0;
        // $grandTotal = 0;
        // $earnPeriodTotal = 0;
        // $taxTotal = 0;
        // $phicTotal = 0;
        // $gsisTotal = 0;
        // $pagibig1 = 0;
        // $pagibig2 = 0;


        foreach ($employeesByOffice as $officeName => $employees) {
            $monthlyRateSum = $employees->sum('monthly_rate');
            $peraSum = $employees->sum(fn($e) => $e->contribution->pera ?? 0);
            $totalUacs = $monthlyRateSum + $peraSum;
            $earnPeriod = ($employee->monthly_rate ?? 0) + ($employee->contribution->pera ?? 0);


            $otherContributions = [
                'hdmf_mpl',
                'hdmf_hl',
                'gsis_pol',
                'gsis_consoloan',
                'gsis_emer',
                'gsis_cpl',
                'gsis_gfal',
                'g_mpl',
                'g_lite',
                'bfar_provident',
                'dareco',
                'ucpb_savings',
                'isda_savings_loan',
                'isda_savings_cap_con',
                'tagumcoop_sl',
                'tagum_coop_cl',
                'tagum_coop_sc',
                'tagum_coop_rs',
                'tagum_coop_ers_gasaka_suretech_etc',
                'nd',
                'lbp_sl'
            ];
            $totalOthers = collect($otherContributions)->sum(fn($field) => $employees->sum(fn($e) => $e->contribution->$field ?? 0));


            $totalFive =
                $employees->sum(fn($e) => $e->contribution->tax ?? 0) +
                $employees->sum(fn($e) => $e->contribution->phic ?? 0) +
                $employees->sum(fn($e) => $e->contribution->gsis_ps ?? 0) +
                $employees->sum(fn($e) => $e->contribution->hdmf_ps ?? 0) +
                $employees->sum(fn($e) => $e->contribution->hdmf_mp2 ?? 0);

            $totalDeductions =  $totalFive + $totalOthers;


            $ratepera = $monthlyRateSum + $peraSum;
            $netPay = $ratepera - $totalDeductions;



            $first = $netPay / 2;
            $second = $netPay / 2;


            // Accumulate grand totals here
            // $grandTotalSalary += $monthlyRateSum;
            // $otherTotal += $peraSum;
            // $grandTotal += $totalUacs;
            // $grandTotal += $earnPeriod;

            // $tax = $employees->sum(fn($e) => $e->contribution->tax ?? 0);
            // $phic = $employees->sum(fn($e) => $e->contribution->phic ?? 0);
            // $gsis = $employees->sum(fn($e) => $e->contribution->gsis_ps ?? 0);
            // $hdmf1 = $employees->sum(fn($e) => $e->contribution->hdmf_ps ?? 0);
            // $hdmf2 = $employees->sum(fn($e) => $e->contribution->hdmf_mp2 ?? 0);

            // $taxTotal += $tax;
            // $phicTotal += $phic;
            // $gsisTotal += $gsis;
            // $pagibig1 += $hdmf1;
            // $pagibig2 += $hdmf2;






            $totalsByOffice[$officeName] = [
                'monthly_rate' => $employees->sum('monthly_rate'),
                'tax' => $employees->sum(fn($e) => $e->contribution->tax ?? 0),
                'phic' => $employees->sum(fn($e) => $e->contribution->phic ?? 0),
                'gsis_ps' => $employees->sum(fn($e) => $e->contribution->gsis_ps ?? 0),
                'hdmf_ps' => $employees->sum(fn($e) => $e->contribution->hdmf_ps ?? 0),
                'hdmf_mp2' => $employees->sum(fn($e) => $e->contribution->hdmf_mp2 ?? 0),
                'hdmf_mpl' => $employees->sum(fn($e) => $e->contribution->hdmf_mpl ?? 0),
                'hdmf_hl' => $employees->sum(fn($e) => $e->contribution->hdmf_hl ?? 0),
                'gsis_pol' => $employees->sum(fn($e) => $e->contribution->gsis_pol ?? 0),
                'gsis_consoloan' => $employees->sum(fn($e) => $e->contribution->gsis_consoloan ?? 0),
                'gsis_emer' => $employees->sum(fn($e) => $e->contribution->gsis_emer ?? 0),
                'gsis_cpl' => $employees->sum(fn($e) => $e->contribution->gsis_cpl ?? 0),
                'gsis_gfal' => $employees->sum(fn($e) => $e->contribution->gsis_gfal ?? 0),
                'g_mpl' => $employees->sum(fn($e) => $e->contribution->g_mpl ?? 0),
                'g_lite' => $employees->sum(fn($e) => $e->contribution->g_lite ?? 0),
                'bfar_provident' => $employees->sum(fn($e) => $e->contribution->bfar_provident ?? 0),
                'dareco' => $employees->sum(fn($e) => $e->contribution->dareco ?? 0),
                'ucpb_savings' => $employees->sum(fn($e) => $e->contribution->ucpb_savings ?? 0),
                'isda_savings_loan' => $employees->sum(fn($e) => $e->contribution->isda_savings_loan ?? 0),
                'isda_savings_cap_con' => $employees->sum(fn($e) => $e->contribution->isda_savings_cap_con ?? 0),
                'tagumcoop_sl' => $employees->sum(fn($e) => $e->contribution->tagumcoop_sl ?? 0),
                'tagum_coop_cl' => $employees->sum(fn($e) => $e->contribution->tagum_coop_cl ?? 0),
                'tagum_coop_sc' => $employees->sum(fn($e) => $e->contribution->tagum_coop_sc ?? 0),
                'tagum_coop_rs' => $employees->sum(fn($e) => $e->contribution->tagum_coop_rs ?? 0),
                'tagum_coop_ers_gasaka_suretech_etc' => $employees->sum(fn($e) => $e->contribution->tagum_coop_ers_gasaka_suretech_etc ?? 0),
                'nd' => $employees->sum(fn($e) => $e->contribution->nd ?? 0),
                'lbp_sl' => $employees->sum(fn($e) => $e->contribution->lbp_sl ?? 0),
                'total_charges' => $employees->sum(fn($e) => $e->contribution->total_charges ?? 0),
                'total_salary' => $employees->sum(fn($e) => $e->contribution->total_salary ?? 0),
                'pera' => $employees->sum(fn($e) => $e->contribution->pera ?? 0),
                'gross' => $employees->sum(fn($e) => $e->contribution->gross ?? 0),
                'rate_per_month' => $employees->sum(fn($e) => $e->contribution->rate_per_month ?? 0),
                'leave_wo' => $employees->sum(fn($e) => $e->contribution->leave_wo ?? 0),
                'uacs' => $totalUacs,
                'totalOthers' => $totalOthers,
                'totalDeductions' => $totalDeductions,
                'net_pay' => $netPay,
                'first' => $first,
                'second' => $second,
                'earnPeriod' => $earnPeriod,
            ];
        }


        $overallTotal = [
            'monthly_rate' => 0,
            'pera' => 0,
            'tax' => 0,
            'phic' => 0,
            'gsis_ps' => 0,
            'hdmf_ps' => 0,
            'hdmf_mp2' => 0,
            'totalFive' => 0,
            'totalOthers' => 0,
            'uacs' => 0,
            'grandTotalSalary' => 0,
            'otherTotal' => 0,
            'grandTotal' => 0,
        ];

        foreach ($employeesByOffice as $officeName => $employees) {
            $monthlyRateSum = $employees->sum('monthly_rate');
            $peraSum = $employees->sum(fn($e) => $e->contribution->pera ?? 0);
            $totalUacs = $monthlyRateSum + $peraSum;
            $earnPeriod = ($employee->monthly_rate ?? 0) + ($employee->contribution->pera ?? 0);

            $totalFive = $employees->sum(fn($e) => $e->contribution->tax ?? 0) +
                $employees->sum(fn($e) => $e->contribution->phic ?? 0) +
                $employees->sum(fn($e) => $e->contribution->gsis_ps ?? 0) +
                $employees->sum(fn($e) => $e->contribution->hdmf_ps ?? 0) +
                $employees->sum(fn($e) => $e->contribution->hdmf_mp2 ?? 0);

            $otherContributions = [
                'hdmf_mpl',
                'hdmf_hl',
                'gsis_pol',
                'gsis_consoloan',
                'gsis_emer',
                'gsis_cpl',
                'gsis_gfal',
                'g_mpl',
                'g_lite',
                'bfar_provident',
                'dareco',
                'ucpb_savings',
                'isda_savings_loan',
                'isda_savings_cap_con',
                'tagumcoop_sl',
                'tagum_coop_cl',
                'tagum_coop_sc',
                'tagum_coop_rs',
                'tagum_coop_ers_gasaka_suretech_etc',
                'nd',
                'lbp_sl'
            ];

            $totalOthers = collect($otherContributions)->sum(fn($field) => $employees->sum(fn($e) => $e->contribution->$field ?? 0));

            $totalDeductions = $totalFive + $totalOthers;
            $ratePera = $monthlyRateSum + $peraSum;
            $netPay = $ratePera - $totalDeductions;

            $first = $netPay / 2;
            $second = $netPay / 2;

            $overallTotal['monthly_rate'] += $monthlyRateSum;
            $overallTotal['pera'] += $peraSum;
            $overallTotal['tax'] += $employees->sum(fn($e) => $e->contribution->tax ?? 0);
            $overallTotal['phic'] += $employees->sum(fn($e) => $e->contribution->phic ?? 0);
            $overallTotal['gsis_ps'] += $employees->sum(fn($e) => $e->contribution->gsis_ps ?? 0);
            $overallTotal['hdmf_ps'] += $employees->sum(fn($e) => $e->contribution->hdmf_ps ?? 0);
            $overallTotal['hdmf_mp2'] += $employees->sum(fn($e) => $e->contribution->hdmf_mp2 ?? 0);
            $overallTotal['totalFive'] += $totalFive;
            $overallTotal['totalOthers'] += $totalOthers;
            $overallTotal['uacs'] += $totalUacs;
            $overallTotal['grandTotalSalary'] += $monthlyRateSum;
            $overallTotal['otherTotal'] += $peraSum;
            $overallTotal['grandTotal'] += $monthlyRateSum + $peraSum + $totalUacs;
        }



        // dd($overallTotal);
        // dd($employees);

        return view('livewire.regular-payroll-data', [
            'employeesByOffice' => $employeesByOffice,
            'totalsByOffice' => $totalsByOffice,
            'overallTotal' => $overallTotal,
        ]);
    }
}
