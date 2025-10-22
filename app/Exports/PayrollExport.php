<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PayrollExport implements ShouldAutoSize, WithColumnWidths, WithEvents
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 1,
            'B' => 30,
            'C' => 15,
            'D' => 15,
            'E' => 12,
            'F' => 12,
            'G' => 12,
            'H' => 12,
            'I' => 20,
            'J' => 20,
            'K' => 12,
            'L' => 12,
            'M' => 12,
            'N' => 12,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $currentRow = 1;

                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_LEGAL);
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.748031496062992);
                $sheet->getPageMargins()->setBottom(0.748031496062992);
                $sheet->getPageMargins()->setLeft(0.708661417322835);
                $sheet->getPageMargins()->setRight(0.708661417322835);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 9);
                $sheet->setShowGridlines(false);
                $sheet->getHeaderFooter()->setOddFooter('&CPage &P of &N');
                $sheet->getHeaderFooter()->setEvenFooter('&CPage &P of &N');

                $this->drawHeader($sheet, $currentRow);

                $currentRow = 12;
                $employeesByOffice = $this->data['employeesByOffice'];
                $totalsByOffice = $this->data['totalsByOffice'];

                foreach ($employeesByOffice as $officeName => $employees) {
                    $currentRow = $this->drawOfficeRow($sheet, $currentRow, $officeName);

                    foreach ($employees as $employee) {
                        $currentRow = $this->drawEmployeeRow($sheet, $currentRow, $employee);
                    }

                    // Draw Office Totals Row
                    $officeTotals = $totalsByOffice[$officeName];
                    $currentRow = $this->drawOfficeTotalsRow($sheet, $currentRow, $officeName, $officeTotals);
                }

                $this->drawOverallTotals($sheet, $currentRow);

                $this->drawSignatories($sheet, $currentRow, $this->data['signatories'] ?? null);
            },
        ];
    }

    private function drawHeader(Worksheet $sheet, int &$currentRow): void
    {
        $this->addImage($sheet, 'images/header.png', 'A1');
        $sheet->mergeCells('D1:H1')->setCellValue('D1', 'REPUBLIC OF THE PHILIPPINES');
        $sheet->mergeCells('D2:H2')->setCellValue('D2', 'DEPARTMENT OF AGRICULTURE');
        $sheet->mergeCells('D3:H3')->setCellValue('D3', 'BUREAU OF FISHERIES AND AQUATIC RESOURCES');
        $sheet->mergeCells('D4:H4')->setCellValue('D4', 'R. Magsaysay Ave., Davao City');
        $sheet->getStyle('D1:D4')->getFont()->setBold(true);
        $sheet->mergeCells('G6:H6')->setCellValue('G6', 'GENERAL PAYROLL');
        $sheet->getStyle('G6')->getFont()->setBold(true);
        $sheet->getStyle('G6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('B7', 'BFAR-REGIONAL OFFICE');
        $sheet->setCellValue('B8', 'PERIOD COVERED: ' . $this->data['dateRange']);
        $sheet->getStyle('B7:B8')->getFont()->setBold(true);
        $headers = [
            'B9' => 'NAME OF EMPLOYEE/POSITION',
            'C9' => 'MONTHLY SALARY OTHER INCOME AMOUNT',
            'D9' => 'EARNED FOR PERIOD',
            'E9' => 'DEDUCTIONS',
            'K9' => 'DEDUCTIONS',
            'L9' => 'NET PAY',
            'M9' => 'NET DUE',
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        $sheet->getStyle('B9')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C9')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D9')->getAlignment()->setWrapText(true);
        $subHeaders = [
            'E10' => 'W/TAX',
            'F10' => 'PHIC',
            'G10' => 'GSIS LIFE &',
            'H10' => "P'IBIG 1 & 2",
            'I10' => 'OTHERS',
            'M10' => '1ST',
            'N10' => '2ND',
        ];
        foreach ($subHeaders as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        $subSubHeaders = [
            'I11' => 'CODE',
            'J11' => 'AMOUNT',
        ];
        foreach ($subSubHeaders as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        $sheet
            ->mergeCells('B9:B11')
            ->mergeCells('C9:C11')
            ->mergeCells('D9:D11')
            ->mergeCells('E9:J9')
            ->mergeCells('E10:E11')
            ->mergeCells('F10:F11')
            ->mergeCells('G10:G11')
            ->mergeCells('H10:H11')
            ->mergeCells('I10:J10')
            ->mergeCells('K9:K11')
            ->mergeCells('L9:L11')
            ->mergeCells('M9:N9')
            ->mergeCells('M10:M11')
            ->mergeCells('N10:N11');
        $sheet->getStyle('B9:N11')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ]
        ]);
        $sheet->freezePane('A12');
    }

    private function drawOfficeRow(Worksheet $sheet, int $currentRow, string $officeName): int
    {
        $sheet->mergeCells('B' . $currentRow . ':N' . $currentRow);
        $sheet->getStyle('B' . $currentRow . ':N' . $currentRow)->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle('B' . $currentRow . ':N' . $currentRow)->getFill()->getStartColor()->setRGB('B4C6E7');
        $sheet->setCellValue('B' . $currentRow, strtoupper($officeName));
        $sheet->getStyle('B' . $currentRow)->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $currentRow++;
        return $currentRow;
    }

    private function drawEmployeeRow(Worksheet $sheet, int $currentRow, $employee): int
    {
        $contrib = $employee->contribution;
        $monthlyRate = $employee->monthly_rate ?? 0;
        $pera = $contrib->pera ?? 0;
        $earnedForPeriod = $monthlyRate + $pera;

        $tax = $contrib->tax ?? 0;
        $phic = $contrib->phic ?? 0;
        $gsisPs = $contrib->gsis_ps ?? 0;
        $hdmfPs = $contrib->hdmf_ps ?? 0;
        $hdmfMp2 = $contrib->hdmf_mp2 ?? 0;
        $hdmfTotal = $hdmfPs + $hdmfMp2;

        $formatZeroCheck = fn($val) => ($val != 0) ? $val : '-';

        $otherDeductions = [];
        $deductionMap = [
            'g_mpl' => 'G MPL',
            'g_lite' => 'G-LITE',
            'bfar_provident' => 'BFAR-Reg./Disallowances',
            'hdmf_mpl' => 'HDMF MPL',
            'dareco' => 'DARECO',
            'ucpb_savings' => 'UCPB',
            'hdmf_hl' => 'HDMF HL',
            'lbp_sl' => 'LBP SL',
            'gsis_gfal' => 'GSIS GFAL',
            'gsis_pol' => 'GSIS POL',
            'gsis_consoloan' => 'GSIS Consoloan',
            'gsis_emer' => 'GSIS EMER',
            'gsis_cpl' => 'GSIS CPL',
            'tagumcoop_sl' => 'TAGUMCOOP SL',
            'tagum_coop_cl' => 'TAGUM COOP CL',
            'tagum_coop_sc' => 'TAGUM COOP SC',
            'tagum_coop_rs' => 'TAGUM COOP RS',
            'tagum_coop_ers_gasaka_suretech_etc' => 'TAGUM COOP ETC',
            'nd' => 'ND',
            'isda_savings_loan' => 'ISDA SAVINGS LOAN',
            'isda_savings_cap_con' => 'ISDA SAVINGS CAP CON',
        ];

        $totalOtherDeductions = 0;
        foreach ($deductionMap as $dbField => $label) {
            $amount = $contrib->$dbField ?? 0;
            if ($amount > 0) {
                $otherDeductions[] = ['code' => $label, 'amount' => $amount];
                $totalOtherDeductions += $amount;
            }
        }

        $totalDeductions = $tax + $phic + $gsisPs + $hdmfTotal + $totalOtherDeductions;

        $netPay = $earnedForPeriod - $totalDeductions;
        $firstHalf = $netPay / 2;
        $secondHalf = $netPay / 2;

        $fullName = ($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '');
        $sheet->setCellValue('B' . $currentRow, $fullName);
        $sheet->setCellValue('C' . $currentRow, $monthlyRate);
        $sheet->setCellValue('D' . $currentRow, $earnedForPeriod);
        $sheet->setCellValue('E' . $currentRow, $formatZeroCheck($tax));
        $sheet->setCellValue('F' . $currentRow, $formatZeroCheck($phic));
        $sheet->setCellValue('G' . $currentRow, $formatZeroCheck($gsisPs));
        $sheet->setCellValue('H' . $currentRow, $formatZeroCheck($hdmfTotal));
        $sheet->setCellValue('K' . $currentRow, $formatZeroCheck($totalDeductions));
        $sheet->setCellValue('L' . $currentRow, $formatZeroCheck($netPay));
        $sheet->setCellValue('M' . $currentRow, $firstHalf);
        $sheet->setCellValue('N' . $currentRow, $secondHalf);

        if (isset($otherDeductions[0])) {
            $sheet->setCellValue('I' . $currentRow, $otherDeductions[0]['code']);
            $sheet->setCellValue('J' . $currentRow, $otherDeductions[0]['amount']);
        }

        $moneyRange1 = 'C' . $currentRow . ':H' . $currentRow;
        $moneyRange2 = 'K' . $currentRow . ':N' . $currentRow;
        $sheet->getStyle($moneyRange1)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle($moneyRange2)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle($moneyRange1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle($moneyRange2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('I' . $currentRow . ':J' . $currentRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('I' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('J' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $currentRow++;

        $maxDeductionLines = count($otherDeductions);

        for ($i = 1; $i < $maxDeductionLines; $i++) {
            $currentDeductionRow = $currentRow + ($i - 1);

            if ($i == 1) {
                $sheet->setCellValue('B' . $currentDeductionRow, $employee->position ?? '');
                $sheet->setCellValue('C' . $currentDeductionRow, $pera);
            }

            if (isset($otherDeductions[$i])) {
                $sheet->setCellValue('I' . $currentDeductionRow, $otherDeductions[$i]['code']);
                $sheet->setCellValue('J' . $currentDeductionRow, $otherDeductions[$i]['amount']);
            }

            $sheet->getStyle('C' . $currentDeductionRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('C' . $currentDeductionRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I' . $currentDeductionRow . ':J' . $currentDeductionRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('I' . $currentDeductionRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('J' . $currentDeductionRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('B' . $currentDeductionRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        $rowsUsedByLoop = max(0, $maxDeductionLines - 1);

        return $currentRow + $rowsUsedByLoop;
    }

    private function drawOfficeTotalsRow(Worksheet $sheet, int $currentRow, string $officeName, array $totals): int
    {
        $salaryRow = $currentRow;
        $sheet->setCellValue('B' . $salaryRow, 'TOTAL SALARY:');
        $sheet->setCellValue('C' . $salaryRow, $totals['monthly_rate'] ?? 0);
        $currentRow++;


        $peraRow = $currentRow;
        $sheet->setCellValue('B' . $peraRow, 'OTHER IN CODE TOTAL (PERA):');
        $sheet->setCellValue('C' . $peraRow, $totals['pera'] ?? 0);
        $currentRow++;


        $uacsRow = $currentRow;
        $uacsCode = '14.1002';
        $sheet->setCellValue('B' . $uacsRow, 'TOTAL UACS ' . $uacsCode . ':');
        $totalUACS_Earned = $totals['uacs'] ?? 0;
        $sheet->setCellValue('C' . $uacsRow, ($totalUACS_Earned ?? 0) == 0 ? '-' : $totalUACS_Earned);
        $sheet->setCellValue('D' . $uacsRow, ($totalUACS_Earned ?? 0) == 0 ? '-' : $totalUACS_Earned);
        $sheet->setCellValue('E' . $uacsRow, ($totals['tax'] ?? 0) == 0 ? '-' : $totals['tax']);
        $sheet->setCellValue('F' . $uacsRow, ($totals['phic'] ?? 0) == 0 ? '-' : $totals['phic']);
        $sheet->setCellValue('G' . $uacsRow, ($totals['gsis_ps'] ?? 0) == 0 ? '-' : $totals['gsis_ps']);
        $hdmf_total = ($totals['hdmf_ps'] ?? 0) + ($totals['hdmf_mp2'] ?? 0);
        $sheet->setCellValue('H' . $uacsRow, $hdmf_total == 0 ? '-' : $hdmf_total);
        $sheet->setCellValue('J' . $uacsRow, ($totals['totalOthers'] ?? 0) == 0 ? '-' : $totals['totalOthers']);
        $sheet->setCellValue('K' . $uacsRow, ($totals['totalDeductions'] ?? 0) == 0 ? '-' : $totals['totalDeductions']);
        $sheet->setCellValue('L' . $uacsRow, ($totals['net_pay'] ?? 0) == 0 ? '-' : $totals['net_pay']);
        $sheet->setCellValue('M' . $uacsRow, ($totals['first'] ?? 0) == 0 ? '-' : $totals['first']);
        $sheet->setCellValue('N' . $uacsRow, ($totals['second'] ?? 0) == 0 ? '-' : $totals['second']);

        $totalRange = 'C' . $salaryRow . ':N' . $uacsRow;
        $sheet->getStyle($totalRange)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle($totalRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('B' . $salaryRow . ':B' . $uacsRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $uacsRange = 'B' . $uacsRow . ':N' . $uacsRow;
        $sheet->getStyle($uacsRange)->getFont()->setBold(true);
        $sheet->getStyle($uacsRange)->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle($uacsRange)->getFill()->getStartColor()->setRGB('C6E1B4');

        $currentRow++;
        return $currentRow;
    }


    private function drawOverallTotals(Worksheet $sheet, int &$currentRow): void
    {
        $overallTotal = $this->data['overallTotal'];

        $sheet->setCellValue('B' . $currentRow, 'TOTAL SALARY');
        $sheet->setCellValue('C' . $currentRow, $overallTotal['grandTotalSalary'] ?? 0);
        $currentRow++;

        $sheet->setCellValue('B' . $currentRow, 'OTHER IN CODE TOTAL');
        $sheet->setCellValue('C' . $currentRow, $overallTotal['otherTotal'] ?? 0);
        $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $currentRow++;

        $startTotalRow = $currentRow;
        $grandTotalSum = ($overallTotal['grandTotalSalary'] ?? 0) + ($overallTotal['otherTotal'] ?? 0);

        $sheet->setCellValue('B' . $currentRow, 'GRAND TOTAL UACS/SA14.505');
        $sheet->setCellValue('C' . $currentRow, $grandTotalSum);
        $sheet->setCellValue('D' . $currentRow, $grandTotalSum);
        $sheet->setCellValue('E' . $currentRow, $overallTotal['tax'] ?? 0);
        $sheet->setCellValue('F' . $currentRow, $overallTotal['phic'] ?? 0);
        $sheet->setCellValue('G' . $currentRow, $overallTotal['gsis_ps'] ?? 0);
        $sheet->setCellValue('H' . $currentRow, $overallTotal['ps_mp2'] ?? 0);
        $sheet->setCellValue('I' . $currentRow, 'TOTAL OTHERS');
        $sheet->setCellValue('J' . $currentRow, $overallTotal['totalOthers'] ?? 0);
        $sheet->setCellValue('K' . $currentRow, $overallTotal['totalDeduction'] ?? 0);
        $sheet->setCellValue('L' . $currentRow, $overallTotal['netPay'] ?? 0);
        $sheet->setCellValue('M' . $currentRow, $overallTotal['firstHalf'] ?? 0);
        $sheet->setCellValue('N' . $currentRow, $overallTotal['secondHalf'] ?? 0);

        $currentRow++;

        $grandTotalRange = 'C' . ($startTotalRow - 2) . ':N' . ($currentRow - 1);

        $sheet->getStyle($grandTotalRange)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle($grandTotalRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('B' . ($startTotalRow - 2) . ':N' . ($currentRow - 1))->getFont()->setBold(true);

        $finalRowRange = 'B' . $startTotalRow . ':N' . ($currentRow - 1);
        $sheet->getStyle($finalRowRange)->getFill()->setFillType(Fill::FILL_SOLID);
        $sheet->getStyle($finalRowRange)->getFill()->getStartColor()->setRGB('F5B084');
    }


    private function drawSignatories(Worksheet $sheet, int &$currentRow, $assigned): void
    {
        $labelsRow = $currentRow;

        $sheet->setCellValue('B' . $labelsRow, 'Prepared by:');
        $sheet->setCellValue('D' . $labelsRow, 'Checked by:');
        $sheet->setCellValue('G' . $labelsRow, 'Certified/Noted by:');
        $sheet->setCellValue('J' . $labelsRow, 'Funds available:');
        $sheet->setCellValue('M' . $labelsRow, 'Approved for Payment:');
        $sheet->getStyle('B' . $labelsRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('D' . $labelsRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('G' . $labelsRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('J' . $labelsRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('M' . $labelsRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $currentRow = $labelsRow + 2;

        $currentRow++;
        $namesRow = $currentRow;

        $preparedName = strtoupper($assigned?->prepared?->name ?? 'NASH R. HABEL');
        $sheet->mergeCells('B' . $namesRow . ':C' . $namesRow)->setCellValue('B' . $namesRow, $preparedName);

        $checkedName = strtoupper($assigned?->noted?->name ?? 'RESSA D. TARAZONA');
        $sheet->mergeCells('D' . $namesRow . ':F' . $namesRow)->setCellValue('D' . $namesRow, $checkedName);

        $certifiedName = strtoupper($assigned?->noted?->name ?? 'ANGELI D. DELIGERO, CPA');
        $sheet->mergeCells('G' . $namesRow . ':I' . $namesRow)->setCellValue('G' . $namesRow, $certifiedName);

        $fundsName = strtoupper($assigned?->funds?->name ?? 'EMMULOJ. UY, CPA, MBA');
        $sheet->mergeCells('J' . $namesRow . ':L' . $namesRow)->setCellValue('J' . $namesRow, $fundsName);

        $approvedName = strtoupper($assigned?->approved?->name ?? 'RELLY B. GARCIA');
        $sheet->mergeCells('M' . $namesRow . ':N' . $namesRow)->setCellValue('M' . $namesRow, $approvedName);

        $sheet->getStyle('B' . $namesRow . ':N' . $namesRow)->getFont()->setBold(true);

        $currentRow++;
        $designationsRow = $currentRow;

        $preparedDesignation = $assigned?->prepared?->designation ?? 'Payroll Clerk';
        $sheet->mergeCells('B' . $designationsRow . ':C' . $designationsRow)->setCellValue('B' . $designationsRow, $preparedDesignation);

        $checkedDesignation = $assigned?->noted?->designation ?? 'OIC, ADMS';
        $sheet->mergeCells('D' . $designationsRow . ':F' . $designationsRow)->setCellValue('D' . $designationsRow, $checkedDesignation);

        $certifiedDesignation = $assigned?->noted?->designation ?? 'OIC, Finance';
        $sheet->mergeCells('G' . $designationsRow . ':I' . $designationsRow)->setCellValue('G' . $designationsRow, $certifiedDesignation);

        $fundsDesignation = $assigned?->funds?->designation ?? 'OIC, Accounting Unit';
        $sheet->mergeCells('J' . $designationsRow . ':L' . $designationsRow)->setCellValue('J' . $designationsRow, $fundsDesignation);

        $approvedDesignation = $assigned?->approved?->designation ?? 'Regional Director';
        $sheet->mergeCells('M' . $designationsRow . ':N' . $designationsRow)->setCellValue('M' . $designationsRow, $approvedDesignation);

        // $sheet->getStyle('B' . $designationsRow . ':N' . $designationsRow)->getFont()->setSize(9);

        $currentRow = $designationsRow;
    }

    private function addImage(Worksheet $sheet, string $path, string $coordinates): void
    {
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            $drawing = new Drawing();
            $drawing->setPath($fullPath);
            $drawing->setCoordinates($coordinates);
            $drawing->setHeight(120);
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }
    }
}
