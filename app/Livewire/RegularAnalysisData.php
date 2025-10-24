<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\Employee;
use App\Models\Office;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollAnalysis;




class RegularAnalysisData extends Component
{
    public $search = '';
    public $office = '';
    public $sortOrder = '';
    public $offices = [];

    public $month;
    public $months = [];

    public $dateRange;
    public $first_half;
    public $second_half;




    public function mount()
    {
        $this->offices = Office::orderBy('office')->pluck('office')->toArray();


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

        $this->month = now()->month;

        $this->setDateRanges($this->month);
    }


    public function updatedMonth($month)
    {
        $this->setDateRanges($month);
    }

    public function setDateRanges($month)
    {
        $startOfMonth = Carbon::create(now()->year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $this->first_half = "{$startOfMonth->format('F j')}-" . $startOfMonth->addDays(14)->format('j');
        $this->second_half = "{$startOfMonth->format('F j')}-{$endOfMonth->format('j')}";
    }


    public function prepareExportData($first_half, $second_half)
    {
        $firstHalfDates = $first_half;  
        $secondHalfDates = $second_half;


        $employees = Employee::with('contribution')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('first_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->office, function ($query) {
                $query->where('office', $this->office);
            })
            ->when(in_array(strtolower($this->sortOrder), ['asc', 'desc']), function ($query) {
                $query->orderByRaw('LOWER(TRIM(last_name)) ' . $this->sortOrder);
            })
            ->get();


        $employeesByOffice = $employees->groupBy('office');

        $officeTotals = $employeesByOffice->map(function ($employees) {
            return [
                'first_half' => $employees->sum(fn($e) => $e->contribution->first_half ?? 0),
                'second_half' => $employees->sum(fn($e) => $e->contribution->second_half ?? 0),
                'total_net_amount' => $employees->sum(fn($e) => $e->contribution->total_net_amount ?? 0),
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
                'gsis_gs' => $employees->sum(fn($e) => $e->contribution->gsis_gs ?? 0),
                'leave_wo' => $employees->sum(fn($e) => $e->contribution->leave_wo ?? 0),
            ];
        });



        $defaultTotals = [
            'first_half' => 0,
            'second_half' => 0,
            'total_net_amount' => 0,
            'tax' => 0,
            'phic' => 0,
            'gsis_ps' => 0,
            'hdmf_ps' => 0,
            'hdmf_mp2' => 0,
            'hdmf_mpl' => 0,
            'hdmf_hl' => 0,
            'gsis_pol' => 0,
            'gsis_consoloan' => 0,
            'gsis_emer' => 0,
            'gsis_cpl' => 0,
            'gsis_gfal' => 0,
            'g_mpl' => 0,
            'g_lite' => 0,
            'bfar_provident' => 0,
            'dareco' => 0,
            'ucpb_savings' => 0,
            'isda_savings_loan' => 0,
            'isda_savings_cap_con' => 0,
            'tagumcoop_sl' => 0,
            'tagum_coop_cl' => 0,
            'tagum_coop_sc' => 0,
            'tagum_coop_rs' => 0,
            'tagum_coop_ers_gasaka_suretech_etc' => 0,
            'nd' => 0,
            'lbp_sl' => 0,
            'total_charges' => 0,
            'total_salary' => 0,
            'pera' => 0,
            'gross' => 0,
            'rate_per_month' => 0,
            'gsis_gs' => 0,
            'leave_wo' => 0,
        ];

        $overallTotal = $officeTotals->reduce(function ($carry, $officeTotal) {
            foreach ($officeTotal as $key => $value) {
                $carry[$key] = ($carry[$key] ?? 0) + $value;
            }
            return $carry;
        }, $defaultTotals);


        return [
            'employees' => $employees,
            'employeesByOffice' => $employeesByOffice,
            'officeTotals' => $officeTotals,
            'overallTotal' => $overallTotal,
            'first_half_data' => $firstHalfDates,
            'second_half_data' => $secondHalfDates,
        ];
    }

    public function exportPayrollAnalysis()
    {
        $exportData = $this->prepareExportData($this->first_half, $this->second_half);

        // dd($exportData);

        return Excel::download(
            new PayrollAnalysis($exportData),
            'PAYROLL ANALYSIS ' . now()->year . '.xlsx'
        );
    }


    public function render()
    {
        $employees = Employee::with('contribution')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('first_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->office, function ($query) {
                $query->where('office', $this->office);
            })
            ->when(in_array(strtolower($this->sortOrder), ['asc', 'desc']), function ($query) {
                $query->orderByRaw('LOWER(TRIM(last_name)) ' . $this->sortOrder);
            })
            ->get();


        $employeesByOffice = $employees->groupBy('office');

        $officeTotals = $employeesByOffice->map(function ($employees) {
            return [
                'first_half' => $employees->sum(fn($e) => $e->contribution->first_half ?? 0),
                'second_half' => $employees->sum(fn($e) => $e->contribution->second_half ?? 0),
                'total_net_amount' => $employees->sum(fn($e) => $e->contribution->total_net_amount ?? 0),
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
                'gsis_gs' => $employees->sum(fn($e) => $e->contribution->gsis_gs ?? 0),
                'leave_wo' => $employees->sum(fn($e) => $e->contribution->leave_wo ?? 0),
            ];
        });



        $defaultTotals = [
            'first_half' => 0,
            'second_half' => 0,
            'total_net_amount' => 0,
            'tax' => 0,
            'phic' => 0,
            'gsis_ps' => 0,
            'hdmf_ps' => 0,
            'hdmf_mp2' => 0,
            'hdmf_mpl' => 0,
            'hdmf_hl' => 0,
            'gsis_pol' => 0,
            'gsis_consoloan' => 0,
            'gsis_emer' => 0,
            'gsis_cpl' => 0,
            'gsis_gfal' => 0,
            'g_mpl' => 0,
            'g_lite' => 0,
            'bfar_provident' => 0,
            'dareco' => 0,
            'ucpb_savings' => 0,
            'isda_savings_loan' => 0,
            'isda_savings_cap_con' => 0,
            'tagumcoop_sl' => 0,
            'tagum_coop_cl' => 0,
            'tagum_coop_sc' => 0,
            'tagum_coop_rs' => 0,
            'tagum_coop_ers_gasaka_suretech_etc' => 0,
            'nd' => 0,
            'lbp_sl' => 0,
            'total_charges' => 0,
            'total_salary' => 0,
            'pera' => 0,
            'gross' => 0,
            'rate_per_month' => 0,
            'gsis_gs' => 0,
            'leave_wo' => 0,
        ];

        $overallTotal = $officeTotals->reduce(function ($carry, $officeTotal) {
            foreach ($officeTotal as $key => $value) {
                $carry[$key] = ($carry[$key] ?? 0) + $value;
            }
            return $carry;
        }, $defaultTotals);


        return view('livewire.regular-analysis-data', [
            'employees' => $employees,
            'employeesByOffice' => $employeesByOffice,
            'officeTotals' => $officeTotals,
            'overallTotal' => $overallTotal,
        ]);
    }
}
