<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Office;

use Carbon\Carbon;

class RegularPayrollData extends Component
{
    public $officeOptions = [];
    public array $office = [];
    public bool $showOffices = false;

    public $month;
    public $year;
    public $months = [];
    public $years = [];

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
    }

    public function toggleOffices()
    {
        $this->showOffices = !$this->showOffices;
    }

    public function proceed()
    {
        $this->showOffices = false;
    }


    // public function render()
    // {
    //     $employees = Employee::with(['contribution', 'officeDetails'])
    //         ->when(!empty($this->office), function ($query) {
    //             $query->whereHas('officeDetails', function ($q) {
    //                 $q->whereIn('office', $this->office);
    //             });
    //         })
    //         ->get();
    //     $employees->each(function ($employee) {
    //         if ($employee->contribution) {
    //             $filtered = collect($employee->contribution->toArray())
    //                 ->filter(fn($value) => !is_null($value) && $value !== 0 && $value !== '');
    //             $employee->filtered_contribution = (object) $filtered;
    //         } else {
    //             $employee->filtered_contribution = null;
    //         }
    //     });
    //     $groupedByOffice = $employees->groupBy(function ($employee) {
    //         return $employee->officeDetails->office ?? 'Unknown Office';
    //     });
    //     return view('livewire.regular-payroll-data', [
    //         'employeesByOffice' => $groupedByOffice,
    //     ]);
    // }
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


        foreach ($employeesByOffice as $officeName => $employees) {
            $monthlyRateSum = $employees->sum('monthly_rate');
            $peraSum = $employees->sum(fn($e) => $e->contribution->pera ?? 0);
            $totalUacs = $monthlyRateSum + $peraSum;


            $otherContributions = [
                'hdmf_mpl', 'hdmf_hl', 'gsis_pol', 'gsis_consoloan', 'gsis_emer', 'gsis_cpl', 'gsis_gfal',
                'g_mpl', 'g_lite', 'bfar_provident', 'dareco', 'ucpb_savings', 'isda_savings_loan',
                'isda_savings_cap_con', 'tagumcoop_sl', 'tagum_coop_cl', 'tagum_coop_sc', 'tagum_coop_rs',
                'tagum_coop_ers_gasaka_suretech_etc', 'nd', 'lbp_sl'
            ];
            $totalOthers = collect($otherContributions)->sum(fn($field) => $employees->sum(fn($e) => $e->contribution->$field ?? 0));


            $totalFive = 
            $employees->sum(fn($e) => $e->contribution->tax ?? 0) +
            $employees->sum(fn($e) => $e->contribution->phic ?? 0) +
            $employees->sum(fn($e) => $e->contribution->gsis_ps ?? 0) +
            $employees->sum(fn($e) => $e->contribution->hdmf_ps ?? 0) +
            $employees->sum(fn($e) => $e->contribution->hdmf_mp2 ?? 0);

            $totalDeductions =  $totalFive + $totalOthers;


            $netPay = $monthlyRateSum - $totalDeductions;







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
            ];
        }






        return view('livewire.regular-payroll-data', [
            'employeesByOffice' => $employeesByOffice,
            'totalsByOffice' => $totalsByOffice,
        ]);
    }
}
