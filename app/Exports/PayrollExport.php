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

                // --- PRINT SETUP (already present) ---
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

                // Draw Header (up to row 11)
                $this->drawHeader($sheet, $currentRow);

                // Start of data rows
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

                // Draw Overall Totals
                $this->drawOverallTotals($sheet, $currentRow);

                // Draw Signatories 🚀 - PASSING THE DYNAMIC DATA
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
        $sheet->getStyle('D1:D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells('G6:H6')->setCellValue('G6', 'GENERAL PAYROLL');
        $sheet->getStyle('G6')->getFont()->setBold(true)->setSize(14);
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

    /**
     * Draws the office title row.
     * @param Worksheet $sheet
     * @param int $currentRow
     * @param string $officeName
     * @return int The next row number.
     */
    private function drawOfficeRow(Worksheet $sheet, int $currentRow, string $officeName): int
    {
        $sheet->mergeCells('B' . $currentRow . ':N' . $currentRow);
        $sheet->setCellValue('B' . $currentRow, strtoupper($officeName));
        $sheet->getStyle('B' . $currentRow)->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('B' . $currentRow . ':N' . $currentRow)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $currentRow++;
        return $currentRow;
    }

    /**
     * Draws an employee's payroll data row(s).
     * @param Worksheet $sheet
     * @param int $currentRow
     * @param object $employee
     * @return int The next row number.
     */
    private function drawEmployeeRow(Worksheet $sheet, int $currentRow, $employee): int
    {
        // Data for the first line of the employee row
        $name = ($employee->name ?? '') . '/' . ($employee->position ?? '');
        $monthlyRate = $employee->monthly_rate ?? 0;
        $pera = $employee->contribution->pera ?? 0;
        $earnedForPeriod = $monthlyRate + $pera;

        $contrib = $employee->contribution;
        $tax = $contrib->tax ?? 0;
        $phic = $contrib->phic ?? 0;
        $gsisPs = $contrib->gsis_ps ?? 0;
        $hdmfPs = $contrib->hdmf_ps ?? 0;
        $hdmfMp2 = $contrib->hdmf_mp2 ?? 0;

        $totalDeductions = $contrib->total_charges ?? 0;
        $netPay = $earnedForPeriod - $totalDeductions;
        $firstHalf = $netPay / 2;
        $secondHalf = $netPay / 2;

        // Line 1: Main employee data
        $sheet->setCellValue('B' . $currentRow, $name);
        $sheet->setCellValue('C' . $currentRow, $monthlyRate); // Monthly Salary
        $sheet->setCellValue('D' . $currentRow, $earnedForPeriod); // Earned for Period
        $sheet->setCellValue('E' . $currentRow, $tax); // W/TAX
        $sheet->setCellValue('F' . $currentRow, $phic); // PHIC
        $sheet->setCellValue('G' . $currentRow, $gsisPs); // GSIS
        $sheet->setCellValue('H' . $currentRow, $hdmfPs + $hdmfMp2); // P'IBIG 1 & 2 
        $sheet->setCellValue('K' . $currentRow, $totalDeductions); // Total Deductions
        $sheet->setCellValue('L' . $currentRow, $netPay); // Net Pay
        $sheet->setCellValue('M' . $currentRow, $firstHalf); // 1ST
        $sheet->setCellValue('N' . $currentRow, $secondHalf); // 2ND

        // Format money columns for line 1
        $moneyRange1 = 'C' . $currentRow . ':H' . $currentRow;
        $moneyRange2 = 'K' . $currentRow . ':N' . $currentRow;
        $sheet->getStyle($moneyRange1)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle($moneyRange2)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle($moneyRange1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle($moneyRange2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('I' . $currentRow . ':J' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Apply borders for the row
        $sheet->getStyle('B' . $currentRow . ':N' . $currentRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Line 2: Other Income (PERA) and first "Other" deduction
        $currentRow++;
        $sheet->setCellValue('B' . $currentRow, $employee->position ?? ''); // Second line of employee/position
        $sheet->setCellValue('C' . $currentRow, $pera); // Other Income Amount

        // Get the list of 'Other Deductions' that have non-zero values
        $otherDeductions = [];
        // Map database fields to their common name/code (based on your image/logic)
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
            'nd' => 'ND'
        ];

        foreach ($deductionMap as $dbField => $label) {
            $amount = $contrib->$dbField ?? 0;
            if ($amount > 0) {
                $otherDeductions[] = ['code' => $label, 'amount' => $amount];
            }
        }

        // Display the other deductions, starting from the first line
        $maxDeductionLines = max(1, count($otherDeductions));

        for ($i = 0; $i < $maxDeductionLines; $i++) {
            $currentDeductionRow = $currentRow + $i;
            
            // Re-apply borders on subsequent rows as they are outside the initial merge/style block
            $sheet->getStyle('B' . $currentDeductionRow . ':N' . $currentDeductionRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            if ($i == 0) {
                // First deduction on the second line of the main employee row
                if (isset($otherDeductions[0])) {
                    $sheet->setCellValue('I' . $currentDeductionRow, $otherDeductions[0]['code']);
                    $sheet->setCellValue('J' . $currentDeductionRow, $otherDeductions[0]['amount']);
                }
                $sheet->setCellValue('B' . $currentDeductionRow, $employee->position ?? ''); // Second line for position
                $sheet->getStyle('C' . $currentDeductionRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle('C' . $currentDeductionRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            } elseif (isset($otherDeductions[$i])) {
                // Subsequent deductions add more rows
                $sheet->setCellValue('I' . $currentDeductionRow, $otherDeductions[$i]['code']);
                $sheet->setCellValue('J' . $currentDeductionRow, $otherDeductions[$i]['amount']);
            }

            // Style for deduction rows
            $sheet->getStyle('I' . $currentDeductionRow . ':J' . $currentDeductionRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle('I' . $currentDeductionRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('J' . $currentDeductionRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Return the next available row number
        return $currentRow + $maxDeductionLines;
    }

    /**
     * Draws the office totals row.
     * @param Worksheet $sheet
     * @param int $currentRow
     * @param string $officeName
     * @param array $totals
     * @return int The next row number.
     */
    private function drawOfficeTotalsRow(Worksheet $sheet, int $currentRow, string $officeName, array $totals): int
    {
        // First line of totals
        $sheet->mergeCells('B' . $currentRow . ':B' . ($currentRow + 1));
        $sheet->setCellValue('B' . $currentRow, 'TOTAL ' . strtoupper($officeName));
        $sheet->setCellValue('C' . $currentRow, $totals['monthly_rate'] ?? 0); // Total Monthly Salary
        $sheet->setCellValue('D' . $currentRow, $totals['monthly_rate'] ?? 0); // Total Earned for Period (Salary only for line 1)
        $sheet->setCellValue('E' . $currentRow, $totals['tax'] ?? 0);
        $sheet->setCellValue('F' . $currentRow, $totals['phic'] ?? 0);
        $sheet->setCellValue('G' . $currentRow, $totals['gsis_ps'] ?? 0);
        $sheet->setCellValue('H' . $currentRow, ($totals['hdmf_ps'] ?? 0) + ($totals['hdmf_mp2'] ?? 0));
        $sheet->setCellValue('K' . $currentRow, $totals['totalDeductions'] ?? 0);
        $sheet->setCellValue('L' . $currentRow, $totals['net_pay'] ?? 0);
        $sheet->setCellValue('M' . $currentRow, $totals['first'] ?? 0);
        $sheet->setCellValue('N' . $currentRow, $totals['second'] ?? 0);

        // Second line of totals
        $currentRow++;
        $sheet->setCellValue('C' . $currentRow, $totals['pera'] ?? 0); // Other Income Total
        $sheet->setCellValue('D' . $currentRow, $totals['uacs'] ?? 0); // Total UACS

        // Style for Total rows
        $totalRange = 'C' . ($currentRow - 1) . ':N' . $currentRow;
        $sheet->getStyle($totalRange)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle($totalRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('B' . ($currentRow - 1) . ':N' . $currentRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        $sheet->getStyle('B' . ($currentRow - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B' . ($currentRow - 1) . ':B' . $currentRow)->getFont()->setBold(true);

        $currentRow++;
        return $currentRow;
    }

    /**
     * Draws the overall grand totals section.
     * @param Worksheet $sheet
     * @param int $currentRow
     */
    private function drawOverallTotals(Worksheet $sheet, int &$currentRow): void
    {
        $overallTotal = $this->data['overallTotal'];

        // Row for 'TOTAL UACS / TOTAL SALARY'
        $sheet->mergeCells('B' . $currentRow . ':B' . ($currentRow + 2));
        $sheet->setCellValue('B' . $currentRow, 'TOTAL UACS/SA14.505');
        $sheet->setCellValue('C' . $currentRow, $overallTotal['grandTotalSalary'] ?? 0); // Total Salary (Sum of monthly_rate)
        $sheet->setCellValue('D' . $currentRow, $overallTotal['grandTotalSalary'] ?? 0); // Earned for period is the salary
        $sheet->setCellValue('E' . $currentRow, $overallTotal['tax'] ?? 0);
        $sheet->setCellValue('F' . $currentRow, $overallTotal['phic'] ?? 0);
        $sheet->setCellValue('G' . $currentRow, $overallTotal['gsis_ps'] ?? 0);
        $sheet->setCellValue('H' . $currentRow, $overallTotal['ps_mp2'] ?? 0);
        $sheet->setCellValue('K' . $currentRow, $overallTotal['totalDeduction'] ?? 0);
        $sheet->setCellValue('L' . $currentRow, $overallTotal['netPay'] ?? 0);
        $sheet->setCellValue('M' . $currentRow, $overallTotal['firstHalf'] ?? 0);
        $sheet->setCellValue('N' . $currentRow, $overallTotal['secondHalf'] ?? 0);
        $currentRow++;

        // Row for 'OTHER TOTAL'
        $sheet->setCellValue('B' . $currentRow, 'OTHER IN CODE TOTAL');
        $sheet->setCellValue('C' . $currentRow, $overallTotal['otherTotal'] ?? 0); // Other Total (Sum of pera)
        $sheet->setCellValue('D' . $currentRow, $overallTotal['otherTotal'] ?? 0); // Earned for period is the other total
        $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $currentRow++;

        // Row for 'TOTAL UACS/SA14.505' (Grand Total)
        $sheet->setCellValue('B' . $currentRow, 'TOTAL UACS/SA14.505');
        $sheet->setCellValue('C' . $currentRow, $overallTotal['grandTotal'] ?? 0); // Sum of Total Salary + Other Total
        $sheet->setCellValue('D' . $currentRow, $overallTotal['grandTotal'] ?? 0); // Earned for period is the grand total
        $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $currentRow++;

        // Row for 'GRAND TOTAL SALARY'
        $sheet->setCellValue('B' . $currentRow, 'GRAND TOTAL SALARY');
        $sheet->setCellValue('C' . $currentRow, $overallTotal['grandTotalSalary'] ?? 0);
        $sheet->setCellValue('D' . $currentRow, $overallTotal['grandTotalSalary'] ?? 0);
        $sheet->getStyle('B' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $currentRow++;

        // Row for 'GRAND TOTAL'
        $sheet->mergeCells('B' . $currentRow . ':B' . ($currentRow + 1));
        $sheet->setCellValue('B' . $currentRow, 'GRAND TOTAL');
        $sheet->getStyle('B' . $currentRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('C' . $currentRow, $overallTotal['grandTotal'] ?? 0);
        $sheet->setCellValue('D' . $currentRow, $overallTotal['grandTotal'] ?? 0);
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

        // Row for other Grand Total details (The remaining other income total)
        $currentRow++;
        $sheet->setCellValue('C' . $currentRow, $overallTotal['otherTotal'] ?? 0); 
        $sheet->setCellValue('D' . $currentRow, $overallTotal['otherTotal'] ?? 0);
        $currentRow++;

        // Final styling and borders for Grand Totals
        $grandTotalRange = 'C' . ($currentRow - 6) . ':N' . ($currentRow - 1);
        $sheet->getStyle($grandTotalRange)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle($grandTotalRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('B' . ($currentRow - 6) . ':N' . ($currentRow - 1))->getFont()->setBold(true);
        $sheet->getStyle('B' . ($currentRow - 6) . ':N' . ($currentRow - 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
    }

    /**
     * Draws the signatory section dynamically using assigned data.
     * @param Worksheet $sheet
     * @param int $currentRow
     * @param object|null $assigned
     */
    private function drawSignatories(Worksheet $sheet, int &$currentRow, $assigned): void
    {
        $labelsRow = $currentRow;

        $sheet->setCellValue('B' . $labelsRow, 'Prepared by:');
        $sheet->setCellValue('D' . $labelsRow, 'Checked by:');
        $sheet->setCellValue('G' . $labelsRow, 'Certified/Noted by:');
        $sheet->setCellValue('J' . $labelsRow, 'Funds available:');
        $sheet->setCellValue('M' . $labelsRow, 'Approved for Payment:');
        $sheet->getStyle('B' . $labelsRow . ':N' . $labelsRow)->getFont()->setSize(9);
        $sheet->getStyle('B' . $labelsRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('D' . $labelsRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('G' . $labelsRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('J' . $labelsRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('M' . $labelsRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        
        $currentRow = $labelsRow + 1;

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
        
        $sheet->getStyle('B' . $namesRow . ':N' . $namesRow)->getFont()->setBold(true)->setSize(9);
        
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

        $sheet->getStyle('B' . $designationsRow . ':N' . $designationsRow)->getFont()->setSize(9);
        
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