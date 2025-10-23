<?php

namespace App\Livewire;


use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Contribution;

class RegularContributionData extends Component
{

    public $search = '';
    public $office = '';
    public $sortOrder = '';
    public $deletingId = null;
    public $offices = [];
    public $pera = 2000;


    public $selectedEmployee = null;
    public $monthly_rate;

    public $tax, $phic, $gsis_ps, $hdmf_ps, $hdmf_mp2, $hdmf_mpl, $hdmf_hl, $gsis_pol, $gsis_consoloan,
        $gsis_emer, $gsis_cpl, $gsis_gfal, $g_mpl, $g_lite, $bfar_provident, $dareco, $ucpb_savings,
        $isda_savings_loan, $isda_savings_cap_con, $tagumcoop_sl, $tagum_coop_cl, $tagum_coop_sc,
        $tagum_coop_rs, $tagum_coop_ers_gasaka_suretech_etc, $nd, $lbp_sl, $leave_wo;

    protected $rules = [
        'tax' => 'nullable|numeric',
        'phic' => 'nullable|numeric',
        'gsis_ps' => 'nullable|numeric',
        'hdmf_ps' => 'nullable|numeric',
        'hdmf_mp2' => 'nullable|numeric',
        'hdmf_mpl' => 'nullable|numeric',
        'hdmf_hl' => 'nullable|numeric',
        'gsis_pol' => 'nullable|numeric',
        'gsis_consoloan' => 'nullable|numeric',
        'gsis_emer' => 'nullable|numeric',
        'gsis_cpl' => 'nullable|numeric',
        'gsis_gfal' => 'nullable|numeric',
        'g_mpl' => 'nullable|numeric',
        'g_lite' => 'nullable|numeric',
        'bfar_provident' => 'nullable|numeric',
        'dareco' => 'nullable|numeric',
        'ucpb_savings' => 'nullable|numeric',
        'isda_savings_loan' => 'nullable|numeric',
        'isda_savings_cap_con' => 'nullable|numeric',
        'tagumcoop_sl' => 'nullable|numeric',
        'tagum_coop_cl' => 'nullable|numeric',
        'tagum_coop_sc' => 'nullable|numeric',
        'tagum_coop_rs' => 'nullable|numeric',
        'tagum_coop_ers_gasaka_suretech_etc' => 'nullable|numeric',
        'nd' => 'nullable|numeric',
        'lbp_sl' => 'nullable|numeric',
        'pera' => 'nullable|numeric',
        'leave_wo' => 'nullable|numeric',
    ];


    public function mount()
    {
        $this->offices = Office::orderBy('office')->pluck('office')->toArray();
    }

    public function employeeSelected($employeeId)
    {
        $this->selectedEmployee = $employeeId;
        $employee = Employee::find($employeeId);

        $this->monthly_rate = $employee->monthly_rate;

        $contribution = Contribution::where('employee_id', $employeeId)->first();

        if ($contribution) {
            $this->tax = $contribution->tax;
            $this->phic = $contribution->phic;
            $this->gsis_ps = $contribution->gsis_ps;
            $this->hdmf_ps = $contribution->hdmf_ps;
            $this->hdmf_mp2 = $contribution->hdmf_mp2;
            $this->hdmf_mpl = $contribution->hdmf_mpl;
            $this->hdmf_hl = $contribution->hdmf_hl;
            $this->gsis_pol = $contribution->gsis_pol;
            $this->gsis_consoloan = $contribution->gsis_consoloan;
            $this->gsis_emer = $contribution->gsis_emer;
            $this->gsis_cpl = $contribution->gsis_cpl;
            $this->gsis_gfal = $contribution->gsis_gfal;
            $this->g_mpl = $contribution->g_mpl;
            $this->g_lite = $contribution->g_lite;
            $this->bfar_provident = $contribution->bfar_provident;
            $this->dareco = $contribution->dareco;
            $this->ucpb_savings = $contribution->ucpb_savings;
            $this->isda_savings_loan = $contribution->isda_savings_loan;
            $this->isda_savings_cap_con = $contribution->isda_savings_cap_con;
            $this->tagumcoop_sl = $contribution->tagumcoop_sl;
            $this->tagum_coop_cl = $contribution->tagum_coop_cl;
            $this->tagum_coop_sc = $contribution->tagum_coop_sc;
            $this->tagum_coop_rs = $contribution->tagum_coop_rs;
            $this->tagum_coop_ers_gasaka_suretech_etc = $contribution->tagum_coop_ers_gasaka_suretech_etc;
            $this->nd = $contribution->nd;
            $this->lbp_sl = $contribution->lbp_sl;
            $this->leave_wo = $contribution->leave_wo;
            if (!is_null($contribution->pera) && $contribution->pera != 0) {
                $this->pera = $contribution->pera;
            }
        } else {
            $this->resetContributionFields();
        }
    }


    protected function resetContributionFields()
    {
        $this->tax = null;
        $this->phic = null;
        $this->gsis_ps = null;
        $this->hdmf_ps = null;
        $this->hdmf_mp2 = null;
        $this->hdmf_mpl = null;
        $this->hdmf_hl = null;
        $this->gsis_pol = null;
        $this->gsis_consoloan = null;
        $this->gsis_emer = null;
        $this->gsis_cpl = null;
        $this->gsis_gfal = null;
        $this->g_mpl = null;
        $this->g_lite = null;
        $this->bfar_provident = null;
        $this->dareco = null;
        $this->ucpb_savings = null;
        $this->isda_savings_loan = null;
        $this->isda_savings_cap_con = null;
        $this->tagumcoop_sl = null;
        $this->tagum_coop_cl = null;
        $this->tagum_coop_sc = null;
        $this->tagum_coop_rs = null;
        $this->tagum_coop_ers_gasaka_suretech_etc = null;
        $this->nd = null;
        $this->lbp_sl = null;
        $this->pera = null;
        $this->leave_wo = null;
    }


    public function save()
    {
        $this->validate();

        if (is_null($this->pera)) {
            $this->pera = 2000;
        }

        $rate_per_month = $this->monthly_rate;

        $gross = $this->monthly_rate + $this->pera;

        $total_salary = $gross;

        $total_charges =
            ($this->tax ?? 0)
            + ($this->phic ?? 0)
            + ($this->gsis_ps ?? 0)
            + ($this->hdmf_ps ?? 0)
            + ($this->hdmf_mp2 ?? 0)
            + ($this->hdmf_mpl ?? 0)
            + ($this->hdmf_hl ?? 0)
            + ($this->gsis_pol ?? 0)
            + ($this->gsis_consoloan ?? 0)
            + ($this->gsis_emer ?? 0)
            + ($this->gsis_cpl ?? 0)
            + ($this->gsis_gfal ?? 0)
            + ($this->g_mpl ?? 0)
            + ($this->g_lite ?? 0)
            + ($this->bfar_provident ?? 0)
            + ($this->dareco ?? 0)
            + ($this->ucpb_savings ?? 0)
            + ($this->isda_savings_loan ?? 0)
            + ($this->isda_savings_cap_con ?? 0)
            + ($this->tagumcoop_sl ?? 0)
            + ($this->tagum_coop_cl ?? 0)
            + ($this->tagum_coop_sc ?? 0)
            + ($this->tagum_coop_rs ?? 0)
            + ($this->tagum_coop_ers_gasaka_suretech_etc ?? 0)
            + ($this->nd ?? 0)
            + ($this->lbp_sl ?? 0);

        $total_net_amount = $gross - $total_charges;

        $first_half = $total_net_amount / 2;

        $second_half = $total_net_amount / 2;

        $gsis_gs = $rate_per_month * 0.12;


        $contribution = Contribution::updateOrCreate(
            ['employee_id' => $this->selectedEmployee],
            [
                'first_half' => $first_half,
                'second_half' => $second_half,
                'total_net_amount' => $total_net_amount,

                'tax' => $this->tax,
                'phic' => $this->phic,
                'gsis_ps' => $this->gsis_ps,
                'hdmf_ps' => $this->hdmf_ps,
                'hdmf_mp2' => $this->hdmf_mp2,
                'hdmf_mpl' => $this->hdmf_mpl,
                'hdmf_hl' => $this->hdmf_hl,
                'gsis_pol' => $this->gsis_pol,
                'gsis_consoloan' => $this->gsis_consoloan,
                'gsis_emer' => $this->gsis_emer,
                'gsis_cpl' => $this->gsis_cpl,
                'gsis_gfal' => $this->gsis_gfal,
                'g_mpl' => $this->g_mpl,
                'g_lite' => $this->g_lite,
                'bfar_provident' => $this->bfar_provident,
                'dareco' => $this->dareco,
                'ucpb_savings' => $this->ucpb_savings,
                'isda_savings_loan' => $this->isda_savings_loan,
                'isda_savings_cap_con' => $this->isda_savings_cap_con,
                'tagumcoop_sl' => $this->tagumcoop_sl,
                'tagum_coop_cl' => $this->tagum_coop_cl,
                'tagum_coop_sc' => $this->tagum_coop_sc,
                'tagum_coop_rs' => $this->tagum_coop_rs,
                'tagum_coop_ers_gasaka_suretech_etc' => $this->tagum_coop_ers_gasaka_suretech_etc,
                'nd' => $this->nd,
                'lbp_sl' => $this->lbp_sl,

                'total_charges' => $total_charges,
                'total_salary' => $total_salary,
                'pera' => $this->pera,
                'gross' => $gross,
                'rate_per_month' => $rate_per_month,
                'gsis_gs' => $gsis_gs,

                'leave_wo' => $this->leave_wo,
            ]
        );

        // dd($contribution);

        $this->dispatch('success', message: 'Contribution added.');
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        $employees = Employee::query()
            ->when($this->office, function ($query) {
                $query->where('office', $this->office);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $searchTerm = '%' . $this->search . '%';
                    $q->where('last_name', 'like', $searchTerm)
                        ->orWhere('first_name', 'like', $searchTerm)
                        ->orWhere('middle_initial', 'like', $searchTerm);
                });
            })
            ->when(in_array(strtolower($this->sortOrder), ['asc', 'desc']), function ($query) {
                $query->orderByRaw('LOWER(TRIM(last_name)) ' . $this->sortOrder);
            }, function ($query) {
                $query->orderByRaw('LOWER(TRIM(last_name)) asc');
            })
            ->paginate(10);


        return view('livewire.regular-contribution-data', [
            'employees' => $employees,
        ]);
    }
}
