<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ForecastTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        // Skip first row (headings)
        return array_slice($this->data, 1);
    }

    public function headings(): array
    {
        return $this->data[0] ?? [];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:O1')->getFont()->setBold(true);
        $sheet->getStyle('A1:O1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF4472C4');
        $sheet->getStyle('A1:O1')->getFont()->getColor()->setARGB('FFFFFFFF');

        // Center align header
        $sheet->getStyle('A1:O1')->getAlignment()->setHorizontal('center');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Material Code
            'B' => 15,  // Design Code
            'C' => 40,  // Item Name
            'D' => 20,  // DPC Group
            'E' => 20,  // Remarks
            'F' => 12,  // W1 QTY
            'G' => 12,  // W1 TON
            'H' => 12,  // W2 QTY
            'I' => 12,  // W2 TON
            'J' => 12,  // W3 QTY
            'K' => 12,  // W3 TON
            'L' => 12,  // W4 QTY
            'M' => 12,  // W4 TON
            'N' => 12,  // W5 QTY
            'O' => 12,  // W5 TON
        ];
    }
}

