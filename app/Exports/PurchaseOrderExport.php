<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseOrderExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data); // Mengembalikan data yang sudah diproses
    }

    public function headings(): array
    {
        return [
            'DocNo', 'DocDate', 'SupplierCode', 'Status', 'Information', 'MaterialCode', 'Info',
            'Qty', 'QtyReceived', 'QtyRemain', 'RelatedDocNo', 'RelatedQty', 'RelatedDocDate'
        ];
    }
}
