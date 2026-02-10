<?php

namespace App\Http\Controllers;

use App\Models\SupplierTicket;
use App\Services\POService;
use App\Services\SupplierTicketNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SupplierTicketController extends Controller
{
    protected $posService;

    public function __construct(POService $posService)
    {
        $this->posService = $posService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SupplierTicket::with(['creator', 'processor'])
            ->where('created_by', Auth::id());

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by PO number
        if ($request->has('po_number') && $request->po_number !== '') {
            $query->where('po_number', 'like', '%' . $request->po_number . '%');
        }

        // Filter by supplier name
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

        // Using client-side DataTables on the view, fetch all rows for the current user
        $tickets = $query->orderBy('created_at', 'desc')->get();

        return view('supplier-tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier-tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'po_number' => 'required|string|max:50',
            'supplier_delivery_doc' => 'required|string|max:50',
            'delivery_date' => 'required|date|after_or_equal:today',
            'supplier_name' => 'required|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'supplier_email' => 'nullable|email|max:255',
            'supplier_address' => 'nullable|string',
            'description' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate PO exists
        if (!$this->posService->validatePO($request->po_number)) {
            return redirect()->back()
                ->withErrors(['po_number' => 'PO number tidak ditemukan dalam sistem'])
                ->withInput();
        }

        // Get PO details to populate supplier info
        $poDetails = $this->posService->getCompletePO($request->po_number);
        // dd($poDetails);
        $supplierInfo = null;
        if ($poDetails && isset($poDetails['header']->SupplierCode)) {
            $supplierInfo = $this->posService->getSupplierInfo($poDetails['header']->SupplierCode);
        }

        $ticket = SupplierTicket::create([
            'ticket_number' => SupplierTicket::generateTicketNumber(),
            'po_number' => $request->po_number,
            'supplier_delivery_doc' => $request->supplier_delivery_doc,
            'delivery_date' => $request->delivery_date,
            'supplier_name' => $supplierInfo->SupplierName ?? $request->supplier_name,
            'supplier_contact' => $supplierInfo->Contact ?? $request->supplier_contact,
            'supplier_email' => $supplierInfo->Email ?? $request->supplier_email,
            'supplier_address' => $supplierInfo->Address ?? $request->supplier_address,
            'description' => $request->description,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'status' => SupplierTicket::STATUS_PROCESSED
        ]);

        // Kirim notifikasi email
        try {
            $notificationService = new SupplierTicketNotificationService();
            $notificationService->sendSupplierTicketNotification($ticket);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send supplier ticket notification: ' . $e->getMessage());
        }

        return redirect()->route('supplier-tickets.index')
            ->with('success', 'Supplier ticket berhasil dibuat dengan nomor: ' . $ticket->ticket_number);
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierTicket $supplierTicket)
    {
        $supplierTicket->load(['creator', 'processor']);
        return view('supplier-tickets.show', compact('supplierTicket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierTicket $supplierTicket)
    {
        // Check if ticket can be edited (only pending status)
        if ($supplierTicket->status !== SupplierTicket::STATUS_PROCESSED) {
            return redirect()->route('supplier-tickets.index')
                ->with('error', 'Ticket hanya bisa diedit jika statusnya PROCESSED.');
        }
        
        return view('supplier-tickets.edit', compact('supplierTicket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierTicket $supplierTicket)
    {
        // Check if ticket can be edited (only pending status)
        if ($supplierTicket->status !== SupplierTicket::STATUS_PROCESSED) {
            return redirect()->route('supplier-tickets.index')
                ->with('error', 'Ticket hanya bisa diedit jika statusnya PENDING.');
        }
        
        $validator = Validator::make($request->all(), [
            'po_number' => 'required|string|max:50',
            'supplier_delivery_doc' => 'required|string|max:50',
            'delivery_date' => 'required|date',
            'supplier_name' => 'required|string|max:255',
            'supplier_contact' => 'nullable|string|max:255',
            'supplier_email' => 'nullable|email|max:255',
            'supplier_address' => 'nullable|string',
            'description' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate PO exists if changed
        if ($request->po_number !== $supplierTicket->po_number) {
            if (!$this->posService->validatePO($request->po_number)) {
                return redirect()->back()
                    ->withErrors(['po_number' => 'PO number tidak ditemukan dalam sistem'])
                    ->withInput();
            }
        }

        $supplierTicket->update([
            'po_number' => $request->po_number,
            'supplier_delivery_doc' => $request->supplier_delivery_doc,
            'delivery_date' => $request->delivery_date,
            'supplier_name' => $request->supplier_name,
            'supplier_contact' => $request->supplier_contact,
            'supplier_email' => $request->supplier_email,
            'supplier_address' => $request->supplier_address,
            'description' => $request->description,
            'notes' => $request->notes
        ]);

        return redirect()->route('supplier-tickets.index')
            ->with('success', 'Supplier ticket berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierTicket $supplierTicket)
    {
        try {
            // Check if ticket can be deleted (only processed status)
            if ($supplierTicket->status !== 'processed') {
                return redirect()->back()->with('error', 'Ticket hanya dapat dihapus jika status masih "Processed".');
            }

            // Check if user is authorized to delete this ticket
            if (auth()->user()->divisi_id == 8) {
                // Supplier can only delete their own tickets
                if ($supplierTicket->supplier_id !== auth()->user()->id) {
                    return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus ticket ini.');
                }
            }

            $ticketNumber = $supplierTicket->ticket_number;
            
            // Delete the ticket
            $supplierTicket->forceDelete();

            return redirect()->route('supplier-tickets.index')
                ->with('success', "Ticket {$ticketNumber} berhasil dihapus.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus ticket: ' . $e->getMessage());
        }
    }

    /**
     * Approve a supplier ticket
     */
    public function approve(SupplierTicket $supplierTicket)
    {
        if ($supplierTicket->status !== SupplierTicket::STATUS_PROCESSED) {
            return redirect()->back()
                ->with('error', 'Hanya ticket dengan status pending yang dapat diapprove.');
        }

        $supplierTicket->update([
            'status' => SupplierTicket::STATUS_APPROVED,
            'processed_by' => Auth::id(),
            'processed_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Supplier ticket berhasil diapprove.');
    }

    /**
     * Reject a supplier ticket
     */
    public function reject(Request $request, SupplierTicket $supplierTicket)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        if ($supplierTicket->status !== SupplierTicket::STATUS_PROCESSED) {
            return redirect()->back()
                ->with('error', 'Hanya ticket dengan status pending yang dapat direject.');
        }

        $supplierTicket->update([
            'status' => SupplierTicket::STATUS_REJECTED,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
            'notes' => $supplierTicket->notes . "\n\nRejection Reason: " . $request->rejection_reason
        ]);

        return redirect()->back()
            ->with('success', 'Supplier ticket berhasil direject.');
    }

    /**
     * Mark ticket as processed
     */
    public function process(SupplierTicket $supplierTicket)
    {
        if (!$supplierTicket->canBeProcessed()) {
            return redirect()->back()
                ->with('error', 'Ticket tidak dapat diproses dalam status saat ini.');
        }

        $supplierTicket->update([
            'status' => SupplierTicket::STATUS_PROCESSED,
            'processed_by' => Auth::id(),
            'processed_at' => now()
        ]);

        return redirect()->back()
            ->with('success', 'Supplier ticket berhasil ditandai sebagai diproses.');
    }

    /**
     * Complete a supplier ticket
     */
    public function complete(SupplierTicket $supplierTicket)
    {
        if ($supplierTicket->status !== SupplierTicket::STATUS_PROCESSED) {
            return redirect()->back()
                ->with('error', 'Hanya ticket yang sudah diproses yang dapat diselesaikan.');
        }

        $supplierTicket->update([
            'status' => SupplierTicket::STATUS_COMPLETED
        ]);

        return redirect()->back()
            ->with('success', 'Supplier ticket berhasil diselesaikan.');
    }

    /**
     * Get ticket statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => SupplierTicket::count(),
            'pending' => SupplierTicket::pending()->count(),
            'approved' => SupplierTicket::approved()->count(),
            'processed' => SupplierTicket::where('status', SupplierTicket::STATUS_PROCESSED)->count(),
            'completed' => SupplierTicket::where('status', SupplierTicket::STATUS_COMPLETED)->count(),
            'rejected' => SupplierTicket::where('status', SupplierTicket::STATUS_REJECTED)->count()
        ];

        return response()->json($stats);
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

        // dd($pos);
        
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
        // dd($poDetails);
        
        if (!$poDetails) {
            return response()->json(['error' => 'PO not found'], 404);
        }

        return response()->json($poDetails);
    }

    /**
     * List PO for the current user's supplier (mastersupplier.AccountName / Name)
     */
    public function listMyPOs(Request $request)
    {
        $accountName = Auth::user()->name ?? null;
        if (!$accountName) {
            return response()->json(['pos' => []]);
        }

        $supplierCode = $this->posService->getSupplierCodeByAccountName($accountName);
        if (!$supplierCode) {
            return response()->json(['pos' => []]);
        }

        $pos = $this->posService->listPOBySupplier($supplierCode, 200);
        // dd($pos);   
        return response()->json([
            'supplier_code' => $supplierCode,
            'pos' => $pos,
        ]);
    }

    public function getSupplierInfo(Request $request)
    {
        try {
            $poService = new POService();
            $supplierInfo = $poService->getSupplierInfo(Auth::user()->name);
            // dd($supplierInfo);
            if ($supplierInfo) {
                return response()->json([
                    'success' => true,
                    'data' => $supplierInfo
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier information not found'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching supplier info: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get dashboard data for supplier
     */
    public function dashboardData()
    {
        try {
            $supplierId = auth()->user()->id;
            
            // Get ticket statistics
            $totalTickets = SupplierTicket::where('supplier_id', $supplierId)->count();
            $pendingTickets = SupplierTicket::where('supplier_id', $supplierId)
                ->where('status', 'PENDING')->count();
            $inProgressTickets = SupplierTicket::where('supplier_id', $supplierId)
                ->where('status', 'IN_PROGRESS')->count();
            $completedTickets = SupplierTicket::where('supplier_id', $supplierId)
                ->where('status', 'COMPLETED')->count();
            
            // Get overdue tickets (tickets older than 3 days and not completed)
            $overdueTickets = SupplierTicket::where('supplier_id', $supplierId)
                ->where('status', '!=', 'COMPLETED')
                ->where('created_at', '<', now()->subDays(3))
                ->count();
            
            // Get today's tickets
            $todayTickets = SupplierTicket::where('supplier_id', $supplierId)
                ->whereDate('created_at', today())
                ->count();
            
            // Calculate response rate (tickets responded within 24 hours)
            $respondedWithin24h = SupplierTicket::where('supplier_id', $supplierId)
                ->where('status', '!=', 'PENDING')
                ->whereRaw('TIMESTAMPDIFF(HOUR, created_at, updated_at) <= 24')
                ->count();
            
            $responseRate = $totalTickets > 0 ? round(($respondedWithin24h / $totalTickets) * 100, 1) : 0;
            
            // Calculate average resolution time
            $completedTicketsWithTime = SupplierTicket::where('supplier_id', $supplierId)
                ->where('status', 'COMPLETED')
                ->whereNotNull('updated_at')
                ->get();
            
            $avgResolutionDays = 0;
            if ($completedTicketsWithTime->count() > 0) {
                $totalDays = $completedTicketsWithTime->sum(function ($ticket) {
                    return $ticket->created_at->diffInDays($ticket->updated_at);
                });
                $avgResolutionDays = round($totalDays / $completedTicketsWithTime->count(), 1);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $totalTickets,
                    'pending' => $pendingTickets,
                    'in_progress' => $inProgressTickets,
                    'completed' => $completedTickets,
                    'overdue' => $overdueTickets,
                    'today' => $todayTickets,
                    'response_rate' => $responseRate,
                    'avg_resolution' => $avgResolutionDays
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching dashboard data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get dashboard tickets for supplier
     */
    public function dashboardTickets()
    {
        try {
            $name = auth()->user()->name;
            // dd($name);
            
            $tickets = SupplierTicket::where('supplier_name', $name)
                // ->with(['purchaseOrder'])
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($ticket) {
                    return [
                        'id' => $ticket->id,
                        'ticket_number' => $ticket->ticket_number,
                        'subject' => $ticket->subject,
                        'priority' => $ticket->priority,
                        'status' => $ticket->status,
                        'created_at' => $ticket->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $ticket->updated_at->format('Y-m-d H:i:s'),
                        'po_number' => $ticket->purchaseOrder ? $ticket->purchaseOrder->po_number : null
                    ];
                });
            
            return response()->json([
                'success' => true,
                'tickets' => $tickets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching dashboard tickets: ' . $e->getMessage()
            ]);
        }
    }

}
