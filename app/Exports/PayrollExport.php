<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayrollExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $exportData;
    protected $employeesByOffice;
    protected $totalsByOffice;
    protected $officeName;

    public function __construct(array $exportData)
    {
        $this->employeesByOffice = $exportData['employeesByOffice'];
        $this->totalsByOffice = $exportData['totalsByOffice'];
        $this->officeName = $this->employeesByOffice->keys()->first();
        $this->exportData = $exportData;
    }

    public function collection()
    {
        $employees = $this->employeesByOffice[$this->officeName] ?? collect();
        $totals = $this->totalsByOffice[$this->officeName] ?? [];

        $employees->push((object)['is_total' => false, 'is_spacer' => true]);

        if (!empty($totals)) {
            $employees->push((object) array_merge($totals, ['is_total' => true]));
        }

        return $employees;
    }

    public function headings(): array
    {
        return [
            ['REPUBLIC OF THE PHILIPPINES'],
            ['DEPARTMENT OF AGRICULTURE'],
            ['BUREAU OF FISHERIES AND AQUATIC RESOURCES XI'],
            ['R. Magsaysay Ave., Davao City'],
            [''],
            ['GENERAL PAYROLL'],

            [
                'NAME OF EMPLOYEE/POSITION',
                'MONTHLY SALARY',
                'EARNED FOR PERIOD',
                'DEDUCTIONS',
                'NET PAY',
                'NET DUE',
            ],

            [
                '',
                'AMOUNT',
                '',
                'W/TAX',
                'PHIC',
                'LIFE &',
                'GOIS',
                'PAGIBIG 1 & 2',
                'OTHERS',
                'TOTAL DEDUCTIONS',
                'NET PAY',
                '1ST',
                '2ND',
            ],
        ];
    }

    public function map($employeeOrTotal): array
    {
        if (isset($employeeOrTotal->is_total) && $employeeOrTotal->is_total) {
            $data = $employeeOrTotal;
            return [
                'TOTAL SALARY',
                number_format($data->monthly_rate, 2),
                number_format($data->uacs, 2),
                number_format($data->tax, 2),
                number_format($data->phic, 2),
                number_format($data->gsis_ps, 2),
                number_format($data->hdmf_ps + $data->hdmf_mp2, 2),
                number_format($data->gsis_pol + $data->gsis_cpl + $data->gsis_gfal, 2),

                number_format($data->totalOthers, 2),

                number_format($data->totalDeductions, 2),
                number_format($data->net_pay, 2),
                number_format($data->first, 2),
                number_format($data->second, 2),
            ];
        }

        if (isset($employeeOrTotal->is_spacer) && $employeeOrTotal->is_spacer) {
             return array_fill(0, 17, '');
        }


        $employee = $employeeOrTotal;
        $contribution = $employee->contribution;
        $filteredContributions = $employee->filtered_contribution ?? new \stdClass();

        $earnedForPeriod = ($employee->monthly_rate ?? 0) + ($contribution->pera ?? 0);

        $otherDeductions = [];
        $otherDeductionsAmount = 0;
        $othersMap = [
            'hdmf_mpl' => 'HDMF MPL', 'hdmf_hl' => 'HDMF HL', 'gsis_pol' => 'GSIS POL', 'gsis_consoloan' => 'GSIS CONSOLOAN',
            'gsis_emer' => 'G EMER', 'gsis_cpl' => 'GSIS CPL', 'gsis_gfal' => 'GSIS GFAL', 'g_mpl' => 'G MPL',
            'g_lite' => 'G LITE', 'bfar_provident' => 'BFAR PROVIDENT', 'dareco' => 'DARECO', 'ucpb_savings' => 'UCPB SAVINGS',
            'isda_savings_loan' => 'ISDA SL', 'tagumcoop_sl' => 'TAGUMCOOP SL', 'tagum_coop_cl' => 'TAGUM COOP CL',
            'tagum_coop_sc' => 'TAGUM COOP SC', 'tagum_coop_rs' => 'TAGUM COOP RS', 'nd' => 'ND', 'lbp_sl' => 'LBP SL',
        ];

        foreach ($othersMap as $field => $label) {
            $amount = $contribution->$field ?? 0;
            if ($amount > 0) {
                $otherDeductions[] = ['code' => $label, 'amount' => $amount];
                $otherDeductionsAmount += $amount;
            }
        }

        $otherCodes = collect($otherDeductions)->pluck('code')->implode("\n");
        $otherAmounts = collect($otherDeductions)->pluck('amount')->map(fn($a) => number_format($a, 2))->implode("\n");


        return [
            strtoupper($employee->first_name . ' ' . $employee->last_name) . "\n" . ($employee->position ?? 'N/A'),

            number_format($employee->monthly_rate, 2) . "\n" . number_format($contribution->pera ?? 0, 2),

            number_format($earnedForPeriod, 2),

            number_format($contribution->tax ?? 0, 2),

            number_format($contribution->phic ?? 0, 2),

            number_format($contribution->gsis_ps ?? 0, 2),

            number_format(($contribution->hdmf_ps ?? 0) + ($contribution->hdmf_mp2 ?? 0), 2),

            number_format(($contribution->gsis_pol ?? 0) + ($contribution->gsis_cpl ?? 0) + ($contribution->gsis_gfal ?? 0), 2),

            $otherCodes,

            $otherAmounts,

            '',
            '',
            '',

            number_format(($contribution->total_deductions ?? 0), 2),

            number_format($earnedForPeriod - ($contribution->total_deductions ?? 0), 2),

            number_format(($earnedForPeriod - ($contribution->total_deductions ?? 0)) / 2, 2),

            number_format(($earnedForPeriod - ($contribution->total_deductions ?? 0)) / 2, 2),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $titleRow1 = 1;
        $titleRow2 = 2;
        $titleRow3 = 3;
        $titleRow4 = 4;
        $generalPayrollRow = 6;
        $mainHeaderRow = 7;
        $subHeaderRow = 8;
        $dataStartRow = 9;

        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
        $sheet->getStyle('A:Q')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getStyle("A{$titleRow1}:A{$titleRow4}")->getFont()->setBold(true);

        $sheet->mergeCells("A{$generalPayrollRow}:Q{$generalPayrollRow}");
        $sheet->getStyle("A{$generalPayrollRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$generalPayrollRow}")->getFont()->setBold(true)->setSize(12);

        $sheet->mergeCells("D{$mainHeaderRow}:M{$mainHeaderRow}");
        $sheet->getStyle("D{$mainHeaderRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D{$mainHeaderRow}")->getFont()->setBold(true);

        $sheet->mergeCells("P{$mainHeaderRow}:Q{$mainHeaderRow}");
        $sheet->getStyle("P{$mainHeaderRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("P{$mainHeaderRow}")->getFont()->setBold(true);

        $sheet->mergeCells("I{$subHeaderRow}:M{$subHeaderRow}");
        $sheet->getStyle("I{$subHeaderRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("I{$subHeaderRow}")->getFont()->setBold(true);

        $sheet->getStyle("B{$subHeaderRow}:Q{$subHeaderRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B{$mainHeaderRow}:Q{$subHeaderRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$mainHeaderRow}:Q{$subHeaderRow}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9E1F2');

        $lastRow = $sheet->getHighestDataRow();

        $currencyColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'N', 'O', 'P', 'Q'];
        foreach ($currencyColumns as $col) {
            $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$lastRow}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        $sheet->getStyle("A{$dataStartRow}:A{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("I{$dataStartRow}:I{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $totalRow = $lastRow;
        $sheet->getStyle("A{$totalRow}:Q{$totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$totalRow}:Q{$totalRow}")->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        $sheet->getStyle("A{$totalRow}:Q{$totalRow}")->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);

        $sheet->getStyle("A{$dataStartRow}:A{$lastRow}")->getAlignment()->setWrapText(true);
        $sheet->getStyle("I{$dataStartRow}:J{$lastRow}")->getAlignment()->setWrapText(true);
        $sheet->getStyle("B{$dataStartRow}:B{$lastRow}")->getAlignment()->setWrapText(true);
    }
}