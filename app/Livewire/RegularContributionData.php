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

    public $selectedEmployee = null;

    public $tax, $phic, $gsis_ps, $hdmf_ps, $hdmf_mp2, $hdmf_mpl, $hdmf_hl, $gsis_pol, $gsis_consoloan,
        $gsis_emer, $gsis_cpl, $gsis_gfal, $g_mpl, $g_lite, $bfar_provident, $dareco, $ucpb_savings,
        $isda_savings_loan, $isda_savings_cap_con, $tagumcoop_sl, $tagum_coop_cl, $tagum_coop_sc,
        $tagum_coop_rs, $tagum_coop_ers_gasaka_suretech_etc, $nd, $lbp_sl, $total_charges,
        $total_salary, $pera, $gross, $rate_per_month, $leave_wo;

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
        'total_charges' => 'nullable|numeric',
        'total_salary' => 'nullable|numeric',
        'pera' => 'nullable|numeric',
        'gross' => 'nullable|numeric',
        'rate_per_month' => 'nullable|numeric',
        'leave_wo' => 'nullable|numeric',
    ];


    public function mount()
    {
        $this->offices = Office::orderBy('office')->pluck('office')->toArray();
    }

    public function employeeSelected($employeeId)
    {
        $this->selectedEmployee = $employeeId;

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
            $this->total_charges = $contribution->total_charges;
            $this->total_salary = $contribution->total_salary;
            $this->pera = $contribution->pera;
            $this->gross = $contribution->gross;
            $this->rate_per_month = $contribution->rate_per_month;
            $this->leave_wo = $contribution->leave_wo;
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
        $this->total_charges = null;
        $this->total_salary = null;
        $this->pera = null;
        $this->gross = null;
        $this->rate_per_month = null;
        $this->leave_wo = null;
    }

    public function save()
    {
        $this->validate();

        $contribution = Contribution::updateOrCreate(
            ['employee_id' => $this->selectedEmployee],
            [
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
                'total_charges' => $this->total_charges,
                'total_salary' => $this->total_salary,
                'pera' => $this->pera,
                'gross' => $this->gross,
                'rate_per_month' => $this->rate_per_month,
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
