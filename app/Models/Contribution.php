<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $table = 'regular_contributions';
    protected $fillable = [
        'employee_id',
        'first_half',
        'second_half',
        'total_net_amount',
        'tax',
        'phic',
        'gsis_ps',
        'hdmf_ps',
        'hdmf_mp2',
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
        'lbp_sl',
        'total_charges',
        'total_salary',
        'pera',
        'gross',
        'rate_per_month',
        'gsis_gs',
        'leave_wo',
    ];



    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
