<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\Assign;

class Payslip extends Controller
{
    public $assigned;


    public function __construct()
    {
        $this->assigned = $this->assigned = Assign::with(['prepared', 'checked', 'certified', 'funds', 'approved', 'prepared2'])->latest()->first();
    }
    public function printPayslip(Request $request, $employeeId)
    {
        $employeeData = Employee::with([
            'contribution',
            'officeDetails',
            'leaveRecordCard'
        ])->findOrFail($employeeId);

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::create()->month($m)->format('F');
        }
        $years = range(date('Y'), 1999);
        $selectedMonthInt = $request->input('month') ? (int)$request->input('month') : Carbon::now()->month;
        $selectedYear     = $request->input('year') ? (int)$request->input('year') : Carbon::now()->year;
        $monthName = Carbon::create()->month($selectedMonthInt)->format('F');
        $assigned = Assign::with(['prepared', 'checked', 'certified', 'funds', 'approved', 'prepared2'])->latest()->first();
        return view('regular.regular-payslip', compact(
            'employeeData',
            'months',
            'years',
            'selectedMonthInt', 
            'selectedYear',
            'monthName',
            'assigned'          
        ));
    }
}
