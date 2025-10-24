<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PayrollAnalysis implements ShouldAutoSize, WithColumnWidths, WithEvents
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 30,
            'C' => 20,
            'D' => 20,
            'E' => 14,
            'F' => 10,
            'G' => 10,
            'H' => 12,
            'I' => 12,
            'J' => 12,
            'K' => 12,
            'L' => 12,
            'M' => 14,
            'N' => 14,
            'O' => 12,
            'P' => 12,
            'Q' => 12,
            'R' => 12,
            'S' => 12,
            'T' => 16,
            'U' => 12,
            'V' => 14,
            'W' => 14,
            'X' => 16,
            'Y' => 14,
            'Z' => 16,
            'AA' => 14,
            'AB' => 14,
            'AC' => 22,
            'AD' => 10,
            'AE' => 10,
            'AF' => 14,
            'AG' => 14,
            'AH' => 10,
            'AI' => 12,
            'AJ' => 14,
            'AK' => 12,
            'AL' => 12,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $currentRow = 1;

                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

                $sheet->freezePane('C2');

                $this->drawHeader($sheet, $currentRow);

                $sheet->getRowDimension(1)->setRowHeight(30);

                $employeesByOffice = $this->data['employeesByOffice'] ?? [];
                $officeTotals = $this->data['officeTotals'] ?? [];

                foreach ($employeesByOffice as $officeName => $employees) {
                    $currentRow = $this->drawOfficeRow($sheet, $currentRow, $officeName);

                    foreach ($employees as $employee) {
                        $currentRow = $this->drawEmployeeRow($sheet, $currentRow, $employee);
                    }

                    if (isset($officeTotals[$officeName])) {
                        $totals = $officeTotals[$officeName];
                        $row = $currentRow;
                        
                        $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", 'Total');
                        
                        $sheet->setCellValue('C' . $row, ($totals['first_half'] ?? 0) == 0 ? '-' : $totals['first_half']);
                        $sheet->setCellValue('D' . $row, ($totals['second_half'] ?? 0) == 0 ? '-' : $totals['second_half']);
                        
                        $sheet->setCellValue('E' . $row, ($totals['total_net_amount'] ?? 0) == 0 ? '-' : $totals['total_net_amount']);
                        $sheet->setCellValue('F' . $row, ($totals['tax'] ?? 0) == 0 ? '-' : $totals['tax']);
                        $sheet->setCellValue('G' . $row, ($totals['phic'] ?? 0) == 0 ? '-' : $totals['phic']);
                        $sheet->setCellValue('H' . $row, ($totals['gsis_ps'] ?? 0) == 0 ? '-' : $totals['gsis_ps']);
                        $sheet->setCellValue('I' . $row, ($totals['hdmf_ps'] ?? 0) == 0 ? '-' : $totals['hdmf_ps']);
                        $sheet->setCellValue('J' . $row, ($totals['hdmf_mp2'] ?? 0) == 0 ? '-' : $totals['hdmf_mp2']);
                        $sheet->setCellValue('K' . $row, ($totals['hdmf_mpl'] ?? 0) == 0 ? '-' : $totals['hdmf_mpl']);
                        $sheet->setCellValue('L' . $row, ($totals['hdmf_hl'] ?? 0) == 0 ? '-' : $totals['hdmf_hl']);
                        $sheet->setCellValue('M' . $row, ($totals['gsis_pol'] ?? 0) == 0 ? '-' : $totals['gsis_pol']);
                        $sheet->setCellValue('N' . $row, ($totals['gsis_consoloan'] ?? 0) == 0 ? '-' : $totals['gsis_consoloan']);
                        $sheet->setCellValue('O' . $row, ($totals['gsis_emer'] ?? 0) == 0 ? '-' : $totals['gsis_emer']);
                        $sheet->setCellValue('P' . $row, ($totals['gsis_cpl'] ?? 0) == 0 ? '-' : $totals['gsis_cpl']);
                        $sheet->setCellValue('Q' . $row, ($totals['gsis_gfal'] ?? 0) == 0 ? '-' : $totals['gsis_gfal']);
                        $sheet->setCellValue('R' . $row, ($totals['g_mpl'] ?? 0) == 0 ? '-' : $totals['g_mpl']);
                        $sheet->setCellValue('S' . $row, ($totals['g_lite'] ?? 0) == 0 ? '-' : $totals['g_lite']);
                        $sheet->setCellValue('T' . $row, ($totals['bfar_provident'] ?? 0) == 0 ? '-' : $totals['bfar_provident']);
                        $sheet->setCellValue('U' . $row, ($totals['dareco'] ?? 0) == 0 ? '-' : $totals['dareco']);
                        $sheet->setCellValue('V' . $row, ($totals['ucpb_savings'] ?? 0) == 0 ? '-' : $totals['ucpb_savings']);
                        $sheet->setCellValue('W' . $row, ($totals['isda_savings_loan'] ?? 0) == 0 ? '-' : $totals['isda_savings_loan']);
                        $sheet->setCellValue('X' . $row, ($totals['isda_savings_cap_con'] ?? 0) == 0 ? '-' : $totals['isda_savings_cap_con']);
                        $sheet->setCellValue('Y' . $row, ($totals['tagumcoop_sl'] ?? 0) == 0 ? '-' : $totals['tagumcoop_sl']);
                        $sheet->setCellValue('Z' . $row, ($totals['tagum_coop_cl'] ?? 0) == 0 ? '-' : $totals['tagum_coop_cl']);
                        $sheet->setCellValue('AA' . $row, ($totals['tagum_coop_sc'] ?? 0) == 0 ? '-' : $totals['tagum_coop_sc']);
                        $sheet->setCellValue('AB' . $row, ($totals['tagum_coop_rs'] ?? 0) == 0 ? '-' : $totals['tagum_coop_rs']);
                        $sheet->setCellValue('AC' . $row, ($totals['tagum_coop_ers_gasaka_suretech_etc'] ?? 0) == 0 ? '-' : $totals['tagum_coop_ers_gasaka_suretech_etc']);
                        $sheet->setCellValue('AD' . $row, ($totals['nd'] ?? 0) == 0 ? '-' : $totals['nd']);
                        $sheet->setCellValue('AE' . $row, ($totals['lbp_sl'] ?? 0) == 0 ? '-' : $totals['lbp_sl']);
                        $sheet->setCellValue('AF' . $row, ($totals['total_charges'] ?? 0) == 0 ? '-' : $totals['total_charges']);
                        $sheet->setCellValue('AG' . $row, ($totals['total_salary'] ?? 0) == 0 ? '-' : $totals['total_salary']);
                        $sheet->setCellValue('AH' . $row, ($totals['pera'] ?? 0) == 0 ? '-' : $totals['pera']);
                        $sheet->setCellValue('AI' . $row, ($totals['gross'] ?? 0) == 0 ? '-' : $totals['gross']);
                        $sheet->setCellValue('AJ' . $row, ($totals['rate_per_month'] ?? 0) == 0 ? '-' : $totals['rate_per_month']);
                        $sheet->setCellValue('AK' . $row, ($totals['gsis_gs'] ?? 0) == 0 ? '-' : $totals['gsis_gs']);
                        $sheet->setCellValue('AL' . $row, ($totals['leave_wo'] ?? 0) == 0 ? '-' : $totals['leave_wo']);

                        $sheet->getStyle("A{$row}:AL{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial Narrow'],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'C6E1B4']],
                        ]);
                        
                        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        
                        $currentRow++;
                        $currentRow++;
                    }
                }
                
                $currentRow++;
                $this->drawOverallTotals($sheet, $currentRow);

                $currencyFormat = NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1;
                $sheet->getStyle('C2:AL' . ($currentRow - 1))->getNumberFormat()->setFormatCode($currencyFormat);

                $sheet->getStyle('C2:AL' . ($currentRow - 1))
                      ->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                $sheet->getStyle('A1:AL1')
                      ->getAlignment()
                      ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                      ->setVertical(Alignment::VERTICAL_CENTER);
            },
        ];
    }

    private function drawHeader(Worksheet $sheet, int &$currentRow): void
    {
        $sheet->setCellValue('A' . $currentRow, 'SL Code');
        $sheet->setCellValue('B' . $currentRow, 'SL Name');
        $sheet->setCellValue('C' . $currentRow, $this->data['first_half_data'] ?? '1st Half');
        $sheet->setCellValue('D' . $currentRow, $this->data['second_half_data'] ?? '2nd Half');
        $sheet->setCellValue('E' . $currentRow, 'Total Net Amount');
        $sheet->setCellValue('F' . $currentRow, 'W/ Tax');
        $sheet->setCellValue('G' . $currentRow, 'PHIC');
        $sheet->setCellValue('H' . $currentRow, 'GSIS PS (9%)');
        $sheet->setCellValue('I' . $currentRow, 'HDMF PS (1)');
        $sheet->setCellValue('J' . $currentRow, 'HDMF-MP2 (2)');
        $sheet->setCellValue('K' . $currentRow, 'HDMF-MPL (3)');
        $sheet->setCellValue('L' . $currentRow, 'HDMF/ HL (1)');
        $sheet->setCellValue('M' . $currentRow, 'GSIS- POL (PLREG)');
        $sheet->setCellValue('N' . $currentRow, 'GSIS- CONSOLOAN');
        $sheet->setCellValue('O' . $currentRow, 'GSIS- EMER');
        $sheet->setCellValue('P' . $currentRow, 'GSIS- CPL');
        $sheet->setCellValue('Q' . $currentRow, 'GSIS-GFAL');
        $sheet->setCellValue('R' . $currentRow, 'G-MPL');
        $sheet->setCellValue('S' . $currentRow, 'G-LITE');
        $sheet->setCellValue('T' . $currentRow, 'BFAR Provident Fund');
        $sheet->setCellValue('U' . $currentRow, 'DARECO');
        $sheet->setCellValue('V' . $currentRow, 'UCPB Savings');
        $sheet->setCellValue('W' . $currentRow, 'ISDA SAVINGS LOAN');
        $sheet->setCellValue('X' . $currentRow, 'ISDA SAVINGS CAP CON.');
        $sheet->setCellValue('Y' . $currentRow, 'TAGUM COOP- SL');
        $sheet->setCellValue('Z' . $currentRow, 'TAGUM COOP- CL ADD ON');
        $sheet->setCellValue('AA' . $currentRow, 'TAGUM COOP- SC');
        $sheet->setCellValue('AB' . $currentRow, 'TAGUM COOP- RS');
        $sheet->setCellValue('AC' . $currentRow, 'TAGUM COOP- ERS, GASAKA, SURETECH, ETC');
        $sheet->setCellValue('AD' . $currentRow, 'ND');
        $sheet->setCellValue('AE' . $currentRow, 'LBP SL');
        $sheet->setCellValue('AF' . $currentRow, 'Total Charges');
        $sheet->setCellValue('AG' . $currentRow, 'TOTAL SALARY');
        $sheet->setCellValue('AH' . $currentRow, 'PERA');
        $sheet->setCellValue('AI' . $currentRow, 'GROSS');
        $sheet->setCellValue('AJ' . $currentRow, 'Rate Per Month');
        $sheet->setCellValue('AK' . $currentRow, 'GSIS- GS (12%)');
        $sheet->setCellValue('AL' . $currentRow, 'Leave w/o');

        $sheet->getStyle('A1:AL1')->getFont()->setBold(true);
        $sheet->getStyle('A1:AL1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E0E0E0');
        
        $sheet->getStyle('A1:AL1')->getAlignment()->setWrapText(true);
        
        $currentRow++;
    }

    private function drawOfficeRow(Worksheet $sheet, int $currentRow, string $officeName): int
    {
        $sheet->mergeCells("A{$currentRow}:AL{$currentRow}")->setCellValue("A{$currentRow}", $officeName);
        $sheet->getStyle("A{$currentRow}:AL{$currentRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial Narrow'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'B4C6E7']],
        ]);
        return $currentRow + 1;
    }

    private function drawEmployeeRow(Worksheet $sheet, int $currentRow, $employee): int
    {
        $c = is_array($employee) && isset($employee['contribution']) ? (object)$employee['contribution'] : $employee->contribution;
        
        $employeeName = strtoupper($employee->last_name) . ', ' . $employee->first_name;
        if (!empty($employee->middle_initial)) {
            $employeeName .= ' ' . strtoupper(substr($employee->middle_initial, 0, 1)) . '.';
        }
        if (!empty($employee->suffix)) {
            $employeeName .= ' ' . $employee->suffix;
        }

        // Note: Individual employee rows still display null/0 values as their original value
        // as the requirement was only for the 'Total' rows.
        $sheet->setCellValue('A' . $currentRow, $employee->sl_code ?? '');
        $sheet->setCellValue('B' . $currentRow, $employeeName);
        
        $sheet->setCellValue('C' . $currentRow, $c->first_half ?? null);
        $sheet->setCellValue('D' . $currentRow, $c->second_half ?? null);
        $sheet->setCellValue('E' . $currentRow, $c->total_net_amount ?? null);
        $sheet->setCellValue('F' . $currentRow, $c->tax ?? null);
        $sheet->setCellValue('G' . $currentRow, $c->phic ?? null);
        $sheet->setCellValue('H' . $currentRow, $c->gsis_ps ?? null);
        $sheet->setCellValue('I' . $currentRow, $c->hdmf_ps ?? null);
        $sheet->setCellValue('J' . $currentRow, $c->hdmf_mp2 ?? null);
        $sheet->setCellValue('K' . $currentRow, $c->hdmf_mpl ?? null);
        $sheet->setCellValue('L' . $currentRow, $c->hdmf_hl ?? null);
        $sheet->setCellValue('M' . $currentRow, $c->gsis_pol ?? null);
        $sheet->setCellValue('N' . $currentRow, $c->gsis_consoloan ?? null);
        $sheet->setCellValue('O' . $currentRow, $c->gsis_emer ?? null);
        $sheet->setCellValue('P' . $currentRow, $c->gsis_cpl ?? null);
        $sheet->setCellValue('Q' . $currentRow, $c->gsis_gfal ?? null);
        $sheet->setCellValue('R' . $currentRow, $c->g_mpl ?? null);
        $sheet->setCellValue('S' . $currentRow, $c->g_lite ?? null);
        $sheet->setCellValue('T' . $currentRow, $c->bfar_provident ?? null);
        $sheet->setCellValue('U' . $currentRow, $c->dareco ?? null);
        $sheet->setCellValue('V' . $currentRow, $c->ucpb_savings ?? null);
        $sheet->setCellValue('W' . $currentRow, $c->isda_savings_loan ?? null);
        $sheet->setCellValue('X' . $currentRow, $c->isda_savings_cap_con ?? null);
        $sheet->setCellValue('Y' . $currentRow, $c->tagumcoop_sl ?? null);
        $sheet->setCellValue('Z' . $currentRow, $c->tagum_coop_cl ?? null);
        $sheet->setCellValue('AA' . $currentRow, $c->tagum_coop_sc ?? null);
        $sheet->setCellValue('AB' . $currentRow, $c->tagum_coop_rs ?? null);
        $sheet->setCellValue('AC' . $currentRow, $c->tagum_coop_ers_gasaka_suretech_etc ?? null);
        $sheet->setCellValue('AD' . $currentRow, $c->nd ?? null);
        $sheet->setCellValue('AE' . $currentRow, $c->lbp_sl ?? null);
        $sheet->setCellValue('AF' . $currentRow, $c->total_charges ?? null);
        $sheet->setCellValue('AG' . $currentRow, $c->total_salary ?? null);
        $sheet->setCellValue('AH' . $currentRow, $c->pera ?? null);
        $sheet->setCellValue('AI' . $currentRow, $c->gross ?? null);
        $sheet->setCellValue('AJ' . $currentRow, $c->rate_per_month ?? null);
        $sheet->setCellValue('AK' . $currentRow, $c->gsis_gs ?? null);
        $sheet->setCellValue('AL' . $currentRow, $c->leave_wo ?? null);

        return $currentRow + 1;
    }

    private function drawOverallTotals(Worksheet $sheet, int &$currentRow): void
    {
        $overallTotal = $this->data['overallTotal'] ?? [];
        $row = $currentRow;

        $sheet->mergeCells("A{$row}:B{$row}")->setCellValue("A{$row}", 'Overall Total');

        // **Overall Totals Logic (Applying the '-' for 0/null/empty)**
        $sheet->setCellValue('C' . $row, ($overallTotal['first_half'] ?? 0) == 0 ? '-' : $overallTotal['first_half']);
        $sheet->setCellValue('D' . $row, ($overallTotal['second_half'] ?? 0) == 0 ? '-' : $overallTotal['second_half']);
        $sheet->setCellValue('E' . $row, ($overallTotal['total_net_amount'] ?? 0) == 0 ? '-' : $overallTotal['total_net_amount']);
        $sheet->setCellValue('F' . $row, ($overallTotal['tax'] ?? 0) == 0 ? '-' : $overallTotal['tax']);
        $sheet->setCellValue('G' . $row, ($overallTotal['phic'] ?? 0) == 0 ? '-' : $overallTotal['phic']);
        $sheet->setCellValue('H' . $row, ($overallTotal['gsis_ps'] ?? 0) == 0 ? '-' : $overallTotal['gsis_ps']);
        $sheet->setCellValue('I' . $row, ($overallTotal['hdmf_ps'] ?? 0) == 0 ? '-' : $overallTotal['hdmf_ps']);
        $sheet->setCellValue('J' . $row, ($overallTotal['hdmf_mp2'] ?? 0) == 0 ? '-' : $overallTotal['hdmf_mp2']);
        $sheet->setCellValue('K' . $row, ($overallTotal['hdmf_mpl'] ?? 0) == 0 ? '-' : $overallTotal['hdmf_mpl']);
        $sheet->setCellValue('L' . $row, ($overallTotal['hdmf_hl'] ?? 0) == 0 ? '-' : $overallTotal['hdmf_hl']);
        $sheet->setCellValue('M' . $row, ($overallTotal['gsis_pol'] ?? 0) == 0 ? '-' : $overallTotal['gsis_pol']);
        $sheet->setCellValue('N' . $row, ($overallTotal['gsis_consoloan'] ?? 0) == 0 ? '-' : $overallTotal['gsis_consoloan']);
        $sheet->setCellValue('O' . $row, ($overallTotal['gsis_emer'] ?? 0) == 0 ? '-' : $overallTotal['gsis_emer']);
        $sheet->setCellValue('P' . $row, ($overallTotal['gsis_cpl'] ?? 0) == 0 ? '-' : $overallTotal['gsis_cpl']);
        $sheet->setCellValue('Q' . $row, ($overallTotal['gsis_gfal'] ?? 0) == 0 ? '-' : $overallTotal['gsis_gfal']);
        $sheet->setCellValue('R' . $row, ($overallTotal['g_mpl'] ?? 0) == 0 ? '-' : $overallTotal['g_mpl']);
        $sheet->setCellValue('S' . $row, ($overallTotal['g_lite'] ?? 0) == 0 ? '-' : $overallTotal['g_lite']);
        $sheet->setCellValue('T' . $row, ($overallTotal['bfar_provident'] ?? 0) == 0 ? '-' : $overallTotal['bfar_provident']);
        $sheet->setCellValue('U' . $row, ($overallTotal['dareco'] ?? 0) == 0 ? '-' : $overallTotal['dareco']);
        $sheet->setCellValue('V' . $row, ($overallTotal['ucpb_savings'] ?? 0) == 0 ? '-' : $overallTotal['ucpb_savings']);
        $sheet->setCellValue('W' . $row, ($overallTotal['isda_savings_loan'] ?? 0) == 0 ? '-' : $overallTotal['isda_savings_loan']);
        $sheet->setCellValue('X' . $row, ($overallTotal['isda_savings_cap_con'] ?? 0) == 0 ? '-' : $overallTotal['isda_savings_cap_con']);
        $sheet->setCellValue('Y' . $row, ($overallTotal['tagumcoop_sl'] ?? 0) == 0 ? '-' : $overallTotal['tagumcoop_sl']);
        $sheet->setCellValue('Z' . $row, ($overallTotal['tagum_coop_cl'] ?? 0) == 0 ? '-' : $overallTotal['tagum_coop_cl']);
        $sheet->setCellValue('AA' . $row, ($overallTotal['tagum_coop_sc'] ?? 0) == 0 ? '-' : $overallTotal['tagum_coop_sc']);
        $sheet->setCellValue('AB' . $row, ($overallTotal['tagum_coop_rs'] ?? 0) == 0 ? '-' : $overallTotal['tagum_coop_rs']);
        $sheet->setCellValue('AC' . $row, ($overallTotal['tagum_coop_ers_gasaka_suretech_etc'] ?? 0) == 0 ? '-' : $overallTotal['tagum_coop_ers_gasaka_suretech_etc']);
        $sheet->setCellValue('AD' . $row, ($overallTotal['nd'] ?? 0) == 0 ? '-' : $overallTotal['nd']);
        $sheet->setCellValue('AE' . $row, ($overallTotal['lbp_sl'] ?? 0) == 0 ? '-' : $overallTotal['lbp_sl']);
        $sheet->setCellValue('AF' . $row, ($overallTotal['total_charges'] ?? 0) == 0 ? '-' : $overallTotal['total_charges']);
        $sheet->setCellValue('AG' . $row, ($overallTotal['total_salary'] ?? 0) == 0 ? '-' : $overallTotal['total_salary']);
        $sheet->setCellValue('AH' . $row, ($overallTotal['pera'] ?? 0) == 0 ? '-' : $overallTotal['pera']);
        $sheet->setCellValue('AI' . $row, ($overallTotal['gross'] ?? 0) == 0 ? '-' : $overallTotal['gross']);
        $sheet->setCellValue('AJ' . $row, ($overallTotal['rate_per_month'] ?? 0) == 0 ? '-' : $overallTotal['rate_per_month']);
        $sheet->setCellValue('AK' . $row, ($overallTotal['gsis_gs'] ?? 0) == 0 ? '-' : $overallTotal['gsis_gs']);
        $sheet->setCellValue('AL' . $row, ($overallTotal['leave_wo'] ?? 0) == 0 ? '-' : $overallTotal['leave_wo']);

        $sheet->getStyle("A{$row}:AL{$row}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial Narrow'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'F5B084']],
        ]);

        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $currentRow++;
    }
}