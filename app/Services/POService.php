<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class POService
{
    protected $mysql3Connection;

    public function __construct()
    {
        $this->mysql3Connection = 'mysql3';
    }

    /**
     * Get PO header by PO number
     */
    public function getPOHeader($poNumber)
    {
        try {
            $po = DB::connection($this->mysql3Connection)
                ->table('purchaseorderh')
                ->leftJoin('purchaseorderd', 'purchaseorderd.DocNo', 'purchaseorderh.DocNo')
                ->leftJoin('mastermaterial', 'mastermaterial.Code', 'purchaseorderd.MaterialCode')
                ->leftJoin('mastersupplier', 'mastersupplier.Code', 'purchaseorderh.SupplierCode')
                ->where('purchaseorderh.DocNo', $poNumber)
                ->select(
                    'purchaseorderh.DocNo', 'purchaseorderh.SupplierCode', 'purchaseorderh.DocDate', 'mastersupplier.Name as supplierName', 'mastermaterial.Name as materialName',
                    'mastersupplier.Contact as supplierContact',
                    'mastersupplier.Email as supplierEmail',
                    'mastersupplier.Address as supplierAddress'
                )
                ->first();

            return $po;
        } catch (\Exception $e) {
            Log::error('Error fetching PO header: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get PO details by PO number
     */
    public function getPODetails($poNumber)
    {
        try {
            $details = DB::connection($this->mysql3Connection)
                ->table('purchaseorderd')
                ->leftJoin('purchaseorderh', 'purchaseorderh.DocNo', 'purchaseorderd.DocNo')
                ->leftJoin('mastermaterial', 'mastermaterial.Code', 'purchaseorderd.MaterialCode')
                ->leftJoin('mastersupplier', 'mastersupplier.Code', 'purchaseorderh.SupplierCode')
                ->where('purchaseorderd.DocNo', $poNumber)
                ->select(
                    'purchaseorderh.SupplierCode',
                    'purchaseorderd.DocNo',
                    'purchaseorderd.MaterialCode',
                    'purchaseorderd.Qty',
                    'purchaseorderd.Unit as unit',
                    'mastermaterial.Name as materialName',
                    'mastersupplier.Name as supplierName',
                    'mastersupplier.Contact as supplierContact',
                    'mastersupplier.Email as supplierEmail',
                    'mastersupplier.Address as supplierAddress'
                )
                ->get();

            return $details;
        } catch (\Exception $e) {
            Log::error('Error fetching PO details: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get complete PO information (header + details)
     */
    public function getCompletePO($poNumber)
    {
        $header = $this->getPOHeader($poNumber);
        $details = $this->getPODetails($poNumber);

        if (!$header) {
            return null;
        }

        return [
            'header' => $header,
            'details' => $details
        ];
    }

    /**
     * Search PO by partial number
     */
    public function searchPO($searchTerm)
    {
        try {
            $pos = DB::connection($this->mysql3Connection)
                ->table('purchaseorderh')
                ->leftJoin('purchaseorderd', 'purchaseorderd.DocNo', 'purchaseorderh.DocNo')
                ->leftJoin('mastersupplier', 'mastersupplier.Code', 'purchaseorderh.SupplierCode')
                ->leftJoin('mastermaterial', 'mastermaterial.Code', 'purchaseorderd.MaterialCode')
                ->where('purchaseorderh.DocNo', 'like', '%' . $searchTerm . '%')
                ->select('purchaseorderh.DocNo', 'purchaseorderh.SupplierCode', 'purchaseorderh.DocDate', 'mastersupplier.Name as supplierName', 'mastermaterial.Name as materialName')
                ->orderBy('purchaseorderh.DocDate', 'desc')
                ->limit(10)
                ->get();

            return $pos;
        } catch (\Exception $e) {
            Log::error('Error searching PO: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * List PO numbers by supplier code
     */
    public function listPOBySupplier(string $supplierCode, int $limit = 100)
    {
        try {
            // dd($supplierCode);
            return DB::connection($this->mysql3Connection)
                ->table('purchaseorderh')
                ->leftJoin('purchaseorderd', 'purchaseorderh.DocNo', 'purchaseorderd.DocNo')
                ->leftJoin('mastermaterial', 'mastermaterial.Code', 'purchaseorderd.MaterialCode')
                ->leftJoin('mastersupplier', 'mastersupplier.Code', 'purchaseorderh.SupplierCode')
                ->where('purchaseorderh.SupplierCode', strtoupper($supplierCode))
                ->whereNotIn('purchaseorderh.Status', ['DELETED', 'COMPLETED', 'CLOSED'])
                ->where(function($query) {
                    $query->where('purchaseorderh.DocNo', 'LIKE', '%POM%')
                          ->orWhere('purchaseorderh.DocNo', 'LIKE', '%PON%');
                })
                ->limit($limit)
                ->select(
                    'purchaseorderh.SupplierCode',
                    'purchaseorderd.DocNo',
                    'purchaseorderh.DocDate',
                    'purchaseorderd.MaterialCode',
                    'purchaseorderd.Qty',
                    'purchaseorderd.Unit as unit',
                    'mastermaterial.Name as materialName',
                    'mastersupplier.Name as supplierName',
                    'mastersupplier.Contact as supplierContact',
                    'mastersupplier.Email as supplierEmail',
                    'mastersupplier.Address as supplierAddress',
                    'purchaseorderh.Status as status',
                )
                ->get();

                // dd($pos);
                // dd($pos);
                // ->table('purchaseorderh')
                // ->where('purchaseorderh.SupplierCode', $supplierCode)
                // ->orderBy('purchaseorderh.DocDate', 'desc')

                // ->get(['purchaseorderh.DocNo', 'purchaseorderh.DocDate']);
        } catch (\Exception $e) {
            Log::error('Error listing PO by supplier: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Resolve supplier code from account name in mastersupplier
     */
    public function getSupplierCodeByAccountName(string $accountName): ?string
    {
        try {
            $row = DB::connection($this->mysql3Connection)
                ->table('mastersupplier')
                ->where('mastersupplier.Name', $accountName)
                ->first(['mastersupplier.Code as SupplierCode']);

            // dd($row);

            return $row->SupplierCode ?? null;
        } catch (\Exception $e) {
            Log::error('Error resolving supplier code by account name: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get supplier information by supplier code
     */
    public function getSupplierInfo($supplierCode)
    {
        try {
            $supplier = DB::connection($this->mysql3Connection)
                ->table('mastersupplier')
                ->where('mastersupplier.Name', $supplierCode)
                ->first();

            return $supplier;
        } catch (\Exception $e) {
            Log::error('Error fetching supplier info: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validate if PO exists and is valid
     */
    public function validatePO($poNumber)
    {
        $po = $this->getPOHeader($poNumber);
        return $po !== null;
    }

    /**
     * Get PO with remaining quantity by MaterialCode
     * Qty Remain = Qty - QtyReceived
     */
    public function getPORemainByMaterialCode(array $materialCodes)
    {
        // dd($materialCodes);
        try {
            $results = DB::connection($this->mysql3Connection)
                ->table('purchaseorderd as d')
                ->leftJoin('purchaseorderh as h', 'h.DocNo', '=', 'd.DocNo')
                ->leftJoin('mastermaterial as m', 'm.Code', '=', 'd.MaterialCode')
                ->whereIn('d.MaterialCode', $materialCodes)
                ->where(function($query) {
                    $query->where('h.DocNo', 'LIKE', '%POM%')
                          ->orWhere('h.DocNo', 'LIKE', '%PON%');
                })
                ->whereRaw('(d.Qty - COALESCE(d.QtyReceived, 0)) > 0')
                ->select(
                    'd.DocNo',
                    'd.MaterialCode',
                    'd.Qty',
                    'd.QtyReceived',
                    DB::raw('(d.Qty - COALESCE(d.QtyReceived, 0)) as QtyRemain'),
                    'd.Unit',
                    'h.DocDate',
                    // 'h.SupplierCode',
                    'm.Name as MaterialName',
                )
                ->orderBy('d.DocNo')
                ->orderBy('d.MaterialCode')
                ->get();

            // dd($results);
            // dd($results);

            return $results;
        } catch (\Exception $e) {
            Log::error('Error fetching PO remain by MaterialCode: ' . $e->getMessage());
            return collect();
        }
    }
}
