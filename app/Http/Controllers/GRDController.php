<?php

namespace App\Http\Controllers;

use App\Models\SupplierTicket;
use App\Services\POService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GRDController extends Controller
{
    protected $posService;
    protected $mysql3Connection;

    public function __construct(POService $posService)
    {
        $this->posService = $posService;
        $this->mysql3Connection = 'mysql3';
    }

    /**
     * Display a listing of GRD
     */
    public function index(Request $request)
    {
        $query = SupplierTicket::with(['creator', 'processor']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by PO number
        if ($request->has('po_number') && $request->po_number !== '') {
            $query->where('po_number', 'like', '%' . $request->po_number . '%');
        }

        // Filter by supplier
        if ($request->has('supplier') && $request->supplier !== '') {
            $query->where('supplier_name', 'like', '%' . $request->supplier . '%');
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('delivery_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('delivery_date', '<=', $request->date_to);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('grd.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new GRD
     */
    public function create(Request $request)
    {
        $supplierTicket = null;
        
        if ($request->has('ticket_id')) {
            $supplierTicket = SupplierTicket::with(['creator', 'processor'])
                ->where('id', $request->ticket_id)
                ->whereIn('status', [SupplierTicket::STATUS_APPROVED, SupplierTicket::STATUS_PROCESSED])
                ->first();
        }

        return view('grd.create', compact('supplierTicket'));
    }

    /**
     * Store a newly created GRD
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_ticket_id' => 'required|exists:supplier_tickets,id',
            'received_items' => 'required|array|min:1',
            'received_items.*.material_code' => 'required|string|max:20',
            'received_items.*.batch_no' => 'required|string|max:20',
            'received_items.*.batch_info' => 'required|string|max:50',
            'received_items.*.expiry_date' => 'required|date',
            'received_items.*.tag_no' => 'required|string|max:10',
            'received_items.*.unit' => 'required|string|max:5',
            'received_items.*.qty' => 'required|numeric|min:0',
            'received_items.*.info' => 'nullable|string',
            'location' => 'required|string|max:5',
            'zone' => 'required|string|max:10',
            'vehicle_no' => 'nullable|string|max:10',
            'information' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $supplierTicket = SupplierTicket::findOrFail($request->supplier_ticket_id);

        // Check if ticket can have GRD created
        if (!in_array($supplierTicket->status, [SupplierTicket::STATUS_APPROVED, SupplierTicket::STATUS_PROCESSED])) {
            return redirect()->back()
                ->with('error', 'GRD hanya dapat dibuat untuk ticket yang sudah diapprove atau diproses.');
        }

        try {
            DB::connection($this->mysql3Connection)->beginTransaction();

            // Generate GRD document number
            $grdNumber = $this->generateGRDNumber();
            
            // Get PO details
            $poDetails = $this->posService->getCompletePO($supplierTicket->po_number);
            if (!$poDetails) {
                throw new \Exception('PO tidak ditemukan: ' . $supplierTicket->po_number);
            }

            // Insert to goodreceipth
            $grdHeader = [
                'DocNo' => $grdNumber,
                'Series' => 'GRD',
                'DocDate' => now()->format('Y-m-d'),
                'SupplierCode' => $poDetails['header']->SupplierCode ?? '',
                'PODocNo' => $supplierTicket->po_number,
                'Location' => $request->location,
                'Zone' => $request->zone,
                'SupplierDlvDocNo' => $supplierTicket->supplier_delivery_doc,
                'VehicleNo' => $request->vehicle_no ?? '',
                'Information' => $request->information ?? '',
                'Status' => 'Open',
                'PrintCounter' => 0,
                'PrintedBy' => '',
                'PrintedDate' => null,
                'CreatedBy' => Auth::user()->name ?? 'System'
            ];

            DB::connection($this->mysql3Connection)
                ->table('goodreceipth')
                ->insert($grdHeader);

            // Insert to goodreceiptd
            foreach ($request->received_items as $index => $item) {
                $grdDetail = [
                    'DocNo' => $grdNumber,
                    'Number' => $index + 1,
                    'MaterialCode' => $item['material_code'],
                    'Info' => $item['info'] ?? '',
                    'BatchNo' => $item['batch_no'],
                    'BatchInfo' => $item['batch_info'],
                    'ExpiryDate' => $item['expiry_date'],
                    'TagNo' => $item['tag_no'],
                    'Unit' => $item['unit'],
                    'Qty' => $item['qty']
                ];

                DB::connection($this->mysql3Connection)
                    ->table('goodreceiptd')
                    ->insert($grdDetail);
            }

            // Update supplier ticket status
            $supplierTicket->update([
                'status' => SupplierTicket::STATUS_COMPLETED,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'notes' => $supplierTicket->notes . "\n\nGRD Created: " . $grdNumber . " on " . now()->format('Y-m-d H:i:s')
            ]);

            DB::connection($this->mysql3Connection)->commit();

            return redirect()->route('grd.index')
                ->with('success', 'GRD berhasil dibuat dengan nomor: ' . $grdNumber);

        } catch (\Exception $e) {
            DB::connection($this->mysql3Connection)->rollBack();
            
            return redirect()->back()
                ->with('error', 'Error creating GRD: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified GRD
     */
    public function show(SupplierTicket $supplierTicket)
    {
        $supplierTicket->load(['creator', 'processor']);
        
        // Get GRD details from MySQL3
        $grdDetails = $this->getGRDDetails($supplierTicket);
        
        return view('grd.show', compact('supplierTicket', 'grdDetails'));
    }

    /**
     * Get GRD details from MySQL3
     */
    private function getGRDDetails($supplierTicket)
    {
        try {
            // Look for GRD by PO number
            $grdHeader = DB::connection($this->mysql3Connection)
                ->table('goodreceipth')
                ->where('PODocNo', $supplierTicket->po_number)
                ->first();

            if (!$grdHeader) {
                return null;
            }

            $grdDetails = DB::connection($this->mysql3Connection)
                ->table('goodreceiptd')
                ->where('DocNo', $grdHeader->DocNo)
                ->get();

            return [
                'header' => $grdHeader,
                'details' => $grdDetails
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching GRD details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate GRD document number
     */
    private function generateGRDNumber()
    {
        $prefix = 'GRD';
        $date = now()->format('ymd');
        
        // Get last GRD number for today
        $lastGRD = DB::connection($this->mysql3Connection)
            ->table('goodreceipth')
            ->where('DocNo', 'like', $prefix . $date . '%')
            ->orderBy('DocNo', 'desc')
            ->first();

        $sequence = 1;
        if ($lastGRD) {
            $lastSequence = (int) substr($lastGRD->DocNo, -3);
            $sequence = $lastSequence + 1;
        }

        return $prefix . $date . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Search PO for autocomplete
     */
    public function searchPO(Request $request)
    {
        $searchTerm = $request->get('q', '');
        
        if (strlen($searchTerm) < 3) {
            return response()->json([]);
        }

        $pos = $this->posService->searchPO($searchTerm);
        
        return response()->json($pos);
    }

    /**
     * Get PO details for form
     */
    public function getPODetails(Request $request)
    {
        $poNumber = $request->get('po_number');
        
        if (!$poNumber) {
            return response()->json(['error' => 'PO number required'], 400);
        }

        $poDetails = $this->posService->getCompletePO($poNumber);
        
        if (!$poDetails) {
            return response()->json(['error' => 'PO not found'], 404);
        }

        return response()->json($poDetails);
    }
}
