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
    public $months = [];

    public $year;
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

        $this->assigned = Assign::with(['prepared', 'checked', 'certified', 'funds', 'approved'])->latest()->first();

        // dd($this->assigned);
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

        $commonFields = [
            'tax',
            'phic',
            'gsis_ps',
            'hdmf_ps',
            'hdmf_mp2',
            'total_charges',
            'total_salary',
            'pera',
            'gross',
            'rate_per_month',
            'leave_wo'
        ];

        foreach ($employeesByOffice as $officeName => $employees) {
            $monthlyRateSum = $employees->sum('monthly_rate');
            $peraSum = $employees->sum(fn($e) => $e->contribution->pera ?? 0);
            $totalUacs = $monthlyRateSum + $peraSum;
            $earnPeriod = ($employee->monthly_rate ?? 0) + ($employee->contribution->pera ?? 0);
            $totalOthers = collect($otherContributions)->sum(
                fn($field) => $employees->sum(fn($e) => $e->contribution->$field ?? 0)
            );
            $totalFive = collect(['tax', 'phic', 'gsis_ps', 'hdmf_ps', 'hdmf_mp2'])->sum(
                fn($field) => $employees->sum(fn($e) => $e->contribution->$field ?? 0)
            );
            $totalDeductions = $totalFive + $totalOthers;
            $ratePera = $monthlyRateSum + $peraSum;
            $netPay = $ratePera - $totalDeductions;
            $first = $netPay / 2;
            $second = $netPay / 2;

            $officeTotals = [
                'monthly_rate' => $monthlyRateSum,
                'uacs' => $totalUacs,
                'totalOthers' => $totalOthers,
                'totalDeductions' => $totalDeductions,
                'net_pay' => $netPay,
                'first' => $first,
                'second' => $second,
                'earnPeriod' => $earnPeriod,
            ];
            foreach ($commonFields as $field) {
                $officeTotals[$field] = $employees->sum(fn($e) => $e->contribution->$field ?? 0);
            }
            foreach ($otherContributions as $field) {
                $officeTotals[$field] = $employees->sum(fn($e) => $e->contribution->$field ?? 0);
            }
            $totalsByOffice[$officeName] = $officeTotals;
        }



        $overallTotal = [];
        foreach ($employeesByOffice as $officeName => $employees) {
            $monthlyRateSum = $employees->sum('monthly_rate');
            $peraSum = $employees->sum(fn($e) => $e->contribution->pera ?? 0);
            $totalUacs = $monthlyRateSum + $peraSum;

            $totalFive = $employees->sum(fn($e) => $e->contribution->tax ?? 0)
                + $employees->sum(fn($e) => $e->contribution->phic ?? 0)
                + $employees->sum(fn($e) => $e->contribution->gsis_ps ?? 0)
                + $employees->sum(fn($e) => $e->contribution->hdmf_ps ?? 0)
                + $employees->sum(fn($e) => $e->contribution->hdmf_mp2 ?? 0);

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

            $totalOthers = collect($otherContributions)->sum(
                fn($field) =>
                $employees->sum(fn($e) => $e->contribution->$field ?? 0)
            );
            $totalDeductions = $totalFive + $totalOthers;
            $netPay = $monthlyRateSum + $peraSum - $totalDeductions;
            $overallTotal['monthly_rate'] = ($overallTotal['monthly_rate'] ?? 0) + $monthlyRateSum;
            $overallTotal['pera'] = ($overallTotal['pera'] ?? 0) + $peraSum;
            $overallTotal['tax'] = ($overallTotal['tax'] ?? 0) + $employees->sum(fn($e) => $e->contribution->tax ?? 0);
            $overallTotal['phic'] = ($overallTotal['phic'] ?? 0) + $employees->sum(fn($e) => $e->contribution->phic ?? 0);
            $overallTotal['gsis_ps'] = ($overallTotal['gsis_ps'] ?? 0) + $employees->sum(fn($e) => $e->contribution->gsis_ps ?? 0);
            $overallTotal['hdmf_ps'] = ($overallTotal['hdmf_ps'] ?? 0) + $employees->sum(fn($e) => $e->contribution->hdmf_ps ?? 0);
            $overallTotal['hdmf_mp2'] = ($overallTotal['hdmf_mp2'] ?? 0) + $employees->sum(fn($e) => $e->contribution->hdmf_mp2 ?? 0);
            $overallTotal['totalFive'] = ($overallTotal['totalFive'] ?? 0) + $totalFive;
            $overallTotal['totalOthers'] = ($overallTotal['totalOthers'] ?? 0) + $totalOthers;
            $overallTotal['uacs'] = ($overallTotal['uacs'] ?? 0) + $totalUacs;
            $overallTotal['grandTotalSalary'] = ($overallTotal['grandTotalSalary'] ?? 0) + $monthlyRateSum;
            $overallTotal['otherTotal'] = ($overallTotal['otherTotal'] ?? 0) + $peraSum;
            $overallTotal['totalDeduction'] = ($overallTotal['totalFive'] ?? 0) + ($overallTotal['totalOthers'] ?? 0);
            $overallTotal['ps_mp2'] = ($overallTotal['hdmf_ps'] ?? 0) + ($overallTotal['hdmf_mp2'] ?? 0);
            $overallTotal['grandTotal'] = ($overallTotal['grandTotalSalary'] ?? 0) + ($overallTotal['otherTotal'] ?? 0);
            $overallTotal['netPay'] = ($overallTotal['grandTotal'] ?? 0) - ($overallTotal['totalDeduction'] ?? 0);
            $overallTotal['firstHalf'] = $overallTotal['netPay'] / 2;
            $overallTotal['secondHalf'] = $overallTotal['netPay'] / 2;
        }

        return [
            'employeesByOffice' => $employeesByOffice,
            'totalsByOffice' => $totalsByOffice,
            'overallTotal' => $overallTotal,
            'dateRange' => $this->dateRange,
            'signatories' => $this->assigned,
        ];
    }


    //EXPORT --------------------------------------
    public function exportPayroll()
    {
        $exportData = $this->prepareExportData($this->dateRange);

        // dd($exportData);
        return Excel::download(
            new PayrollExport($exportData),
            $this->year . ' REGULAR PAYROLL-with LBP.xlsx'
        );
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

        $commonFields = [
            'tax',
            'phic',
            'gsis_ps',
            'hdmf_ps',
            'hdmf_mp2',
            'total_charges',
            'total_salary',
            'pera',
            'gross',
            'rate_per_month',
            'leave_wo'
        ];

        foreach ($employeesByOffice as $officeName => $employees) {
            $monthlyRateSum = $employees->sum('monthly_rate');
            $peraSum = $employees->sum(fn($e) => $e->contribution->pera ?? 0);
            $totalUacs = $monthlyRateSum + $peraSum;
            $earnPeriod = ($employee->monthly_rate ?? 0) + ($employee->contribution->pera ?? 0);
            $totalOthers = collect($otherContributions)->sum(
                fn($field) => $employees->sum(fn($e) => $e->contribution->$field ?? 0)
            );
            $totalFive = collect(['tax', 'phic', 'gsis_ps', 'hdmf_ps', 'hdmf_mp2'])->sum(
                fn($field) => $employees->sum(fn($e) => $e->contribution->$field ?? 0)
            );
            $totalDeductions = $totalFive + $totalOthers;
            $ratePera = $monthlyRateSum + $peraSum;
            $netPay = $ratePera - $totalDeductions;
            $first = $netPay / 2;
            $second = $netPay / 2;

            $officeTotals = [
                'monthly_rate' => $monthlyRateSum,
                'uacs' => $totalUacs,
                'totalOthers' => $totalOthers,
                'totalDeductions' => $totalDeductions,
                'net_pay' => $netPay,
                'first' => $first,
                'second' => $second,
                'earnPeriod' => $earnPeriod,
            ];
            foreach ($commonFields as $field) {
                $officeTotals[$field] = $employees->sum(fn($e) => $e->contribution->$field ?? 0);
            }
            foreach ($otherContributions as $field) {
                $officeTotals[$field] = $employees->sum(fn($e) => $e->contribution->$field ?? 0);
            }
            $totalsByOffice[$officeName] = $officeTotals;
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
            'totalDeduction' => 0,
            'ps_mp2' => 0,
            'grandTotal' => 0,
            'netPay' => 0,
            'firstHalf' => 0,
            'secondHalf' => 0,
        ];

        foreach ($employeesByOffice as $officeName => $employees) {
            $monthlyRateSum = $employees->sum('monthly_rate');
            $peraSum = $employees->sum(fn($e) => $e->contribution->pera ?? 0);
            $totalUacs = $monthlyRateSum + $peraSum;

            $totalFive = $employees->sum(fn($e) => $e->contribution->tax ?? 0)
                + $employees->sum(fn($e) => $e->contribution->phic ?? 0)
                + $employees->sum(fn($e) => $e->contribution->gsis_ps ?? 0)
                + $employees->sum(fn($e) => $e->contribution->hdmf_ps ?? 0)
                + $employees->sum(fn($e) => $e->contribution->hdmf_mp2 ?? 0);

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

            $totalOthers = collect($otherContributions)->sum(
                fn($field) =>
                $employees->sum(fn($e) => $e->contribution->$field ?? 0)
            );
            $totalDeductions = $totalFive + $totalOthers;
            $netPay = $monthlyRateSum + $peraSum - $totalDeductions;
            $overallTotal['monthly_rate'] = ($overallTotal['monthly_rate'] ?? 0) + $monthlyRateSum;
            $overallTotal['pera'] = ($overallTotal['pera'] ?? 0) + $peraSum;
            $overallTotal['tax'] = ($overallTotal['tax'] ?? 0) + $employees->sum(fn($e) => $e->contribution->tax ?? 0);
            $overallTotal['phic'] = ($overallTotal['phic'] ?? 0) + $employees->sum(fn($e) => $e->contribution->phic ?? 0);
            $overallTotal['gsis_ps'] = ($overallTotal['gsis_ps'] ?? 0) + $employees->sum(fn($e) => $e->contribution->gsis_ps ?? 0);
            $overallTotal['hdmf_ps'] = ($overallTotal['hdmf_ps'] ?? 0) + $employees->sum(fn($e) => $e->contribution->hdmf_ps ?? 0);
            $overallTotal['hdmf_mp2'] = ($overallTotal['hdmf_mp2'] ?? 0) + $employees->sum(fn($e) => $e->contribution->hdmf_mp2 ?? 0);
            $overallTotal['totalFive'] = ($overallTotal['totalFive'] ?? 0) + $totalFive;
            $overallTotal['totalOthers'] = ($overallTotal['totalOthers'] ?? 0) + $totalOthers;
            $overallTotal['uacs'] = ($overallTotal['uacs'] ?? 0) + $totalUacs;
            $overallTotal['grandTotalSalary'] = ($overallTotal['grandTotalSalary'] ?? 0) + $monthlyRateSum;
            $overallTotal['otherTotal'] = ($overallTotal['otherTotal'] ?? 0) + $peraSum;
            $overallTotal['totalDeduction'] = ($overallTotal['totalFive'] ?? 0) + ($overallTotal['totalOthers'] ?? 0);
            $overallTotal['ps_mp2'] = ($overallTotal['hdmf_ps'] ?? 0) + ($overallTotal['hdmf_mp2'] ?? 0);
            $overallTotal['grandTotal'] = ($overallTotal['grandTotalSalary'] ?? 0) + ($overallTotal['otherTotal'] ?? 0);
            $overallTotal['netPay'] = ($overallTotal['grandTotal'] ?? 0) - ($overallTotal['totalDeduction'] ?? 0);
            $overallTotal['firstHalf'] = $overallTotal['netPay'] / 2;
            $overallTotal['secondHalf'] = $overallTotal['netPay'] / 2;
        }


        return view('livewire.regular-payroll-data', [
            'employeesByOffice' => $employeesByOffice,
            'totalsByOffice' => $totalsByOffice,
            'overallTotal' => $overallTotal,
        ]);
    }
}
