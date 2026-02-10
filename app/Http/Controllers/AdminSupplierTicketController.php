<?php

namespace App\Http\Controllers;

use App\Models\SupplierTicket;
use App\Models\User;
use App\Services\SupplierTicketNotificationService;
use App\Services\POService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminSupplierTicketController extends Controller
{
    protected $posService;

    public function __construct(POService $posService)
    {
        $this->posService = $posService;
    }
    /**
     * Display a listing of all supplier tickets for admin
     */
    public function index(Request $request)
    {
        $query = SupplierTicket::with(['creator', 'processor']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by supplier
        if ($request->has('supplier') && $request->supplier !== '') {
            $query->where('supplier_name', 'like', '%' . $request->supplier . '%');
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.supplier-tickets.index', compact('tickets'));
    }

    /**
     * Display the specified supplier ticket
     */
    public function show(SupplierTicket $supplierTicket)
    {
        $supplierTicket->load(['creator', 'processor']);

        // Get PO details
        $poDetails = null;
        $poItems = [];
        try {
            $poDetails = $this->posService->getPODetails($supplierTicket->po_number);
            if ($poDetails && $poDetails->isNotEmpty()) {
                $poItems = $poDetails;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get PO details: ' . $e->getMessage());
        }

        // dd($supplierTicket);

        return view('admin.supplier-tickets.show', compact('supplierTicket', 'poDetails', 'poItems'));
    }

    /**
     * Reject a supplier ticket
     */
    public function reject(Request $request, SupplierTicket $supplierTicket)
    {
        Log::info('AdminSupplierTicketController::reject called', [
            'ticket_id' => $supplierTicket->id,
            'current_status' => $supplierTicket->status,
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|min:10',
            'rejected_quantity' => 'nullable|numeric|min:0',
            'accepted_quantity' => 'nullable|numeric|min:0',
            'rejection_date' => 'required|date|after_or_equal:today'
        ]);

        if ($validator->fails()) {
            Log::warning('Reject validation failed', [
                'ticket_id' => $supplierTicket->id,
                'errors' => $validator->errors()->toArray()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($supplierTicket->status !== 'processed') {
            Log::warning('Ticket cannot be rejected - wrong status', [
                'ticket_id' => $supplierTicket->id,
                'current_status' => $supplierTicket->status
            ]);
            return redirect()->back()
                ->with('error', 'Hanya ticket dengan status PROCESSED yang dapat direject.');
        }

        try {
            $supplierTicket->update([
                'status' => 'rejected',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'rejection_reason' => $request->rejection_reason,
                'rejected_quantity' => $request->rejected_quantity,
                'accepted_quantity' => $request->accepted_quantity,
                'rejection_date' => $request->rejection_date
            ]);

            Log::info('Ticket rejected successfully', [
                'ticket_id' => $supplierTicket->id,
                'new_status' => $supplierTicket->status
            ]);

            // Generate surat penolakan
            try {
                $rejectionLetter = $this->generateRejectionLetter($supplierTicket);

                // Kirim notifikasi email dengan surat penolakan
                $notificationService = new SupplierTicketNotificationService();
                $notificationService->sendSupplierTicketRejectionNotification($supplierTicket, $rejectionLetter);
            } catch (\Exception $e) {
                Log::error('Failed to generate rejection letter or send notification: ' . $e->getMessage());
            }

            return redirect()->back()
                ->with('success', 'Supplier ticket berhasil direject dan surat penolakan telah dikirim.');
        } catch (\Exception $e) {
            Log::error('Failed to reject ticket', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Gagal mereject ticket: ' . $e->getMessage());
        }
    }

    /**
     * Generate rejection letter (SURAT PENOLAKAN)
     */
    // private function generateRejectionLetter(SupplierTicket $supplierTicket)
    // {
    //     $rejectionNumber = 'SP-' . str_pad($supplierTicket->id, 3, '0', STR_PAD_LEFT) . '/' .
    //         strtolower(date('M')) . '/' . date('Y');

    //     $letterData = [
    //         'rejection_number' => $rejectionNumber,
    //         'date' => $supplierTicket->rejection_date ?? now(),
    //         'supplier_ticket' => $supplierTicket,
    //         'supplier_name' => $supplierTicket->supplier_name,
    //         'po_number' => $supplierTicket->po_number,
    //         'delivery_doc' => $supplierTicket->supplier_delivery_doc,
    //         'delivery_date' => $supplierTicket->delivery_date,
    //         'rejection_reason' => $supplierTicket->rejection_reason,
    //         'rejected_quantity' => $supplierTicket->rejected_quantity,
    //         'accepted_quantity' => $supplierTicket->accepted_quantity,
    //         'processed_by' => Auth::user()->name,
    //         'processed_at' => $supplierTicket->processed_at
    //     ];

    //     return $letterData;
    // }

    /**
     * Show form to create GRD after approval
     */
    public function showCreateGRDForm(SupplierTicket $supplierTicket)
    {
        if ($supplierTicket->status !== 'processed') {
            return redirect()->back()
                ->with('error', 'Hanya ticket yang sudah diapprove yang dapat dibuat dokumennya.');
        }

        // Get PO details
        $poDetails = null;
        $poItems = [];
        try {
            $poDetails = $this->posService->getPODetails($supplierTicket->po_number);
            if ($poDetails && $poDetails->isNotEmpty()) {
                $poItems = $poDetails;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get PO details: ' . $e->getMessage());
        }

        // Generate GRD number
        $grdNumber = $this->generateGRDNumber();

        // Get PO details with additional fields for GRD form
        $poItemsForGRD = $this->getPODetailsForGRD($supplierTicket->po_number);
        // dd($poItemsForGRD);

        return view('admin.supplier-tickets.create-grd-form', compact('supplierTicket', 'poDetails', 'poItems', 'poItemsForGRD', 'grdNumber'));
    }

    /**
     * Get PO details with additional fields for GRD form
     */
    private function getPODetailsForGRD($poNumber)
    {
        try {
            $sql = "
                SELECT 
                    pod.Number, 
                    mm.Code, 
                    mm.Name, 
                    mm.IsBatch, 
                    mm.Substitute, 
                    pod.MaterialCode AS POMaterialCode, 
                    pod.Info, 
                    '' AS TagNo, 
                    '' AS BatchNo, 
                    '' AS BatchInfo, 
                    grd.ExpiryDate, 
                    pod.Unit, 
                    muc.Content, 
                    pod.Qty AS QtyPOTotal, 
                    pod.QtyReceived AS QtyPOReceived,
                    (pod.Qty - pod.QtyReceived) AS QtyPORemain, 
                    pod.Qty*0 AS Qty,
                    poh.SupplierCode
                FROM purchaseorderd AS pod 
                INNER JOIN mastermaterial AS mm ON pod.MaterialCode=mm.Code 
                INNER JOIN purchaseorderh AS poh ON pod.DocNo=poh.DocNo
                LEFT JOIN masterunitconversion AS muc ON muc.MaterialCode=pod.MaterialCode AND muc.Unit=pod.Unit 
                LEFT JOIN goodsreceiptd AS grd ON grd.Number=pod.Number AND grd.MaterialCode=pod.MaterialCode AND grd.DocNo='' 
                WHERE pod.DocNo=? 
                ORDER BY pod.Number
            ";

            // dd($sql);

            $results = DB::connection('mysql6')->select($sql, [$poNumber]);

            // dd($results);

            Log::info('PO Details for GRD retrieved', [
                'poNumber' => $poNumber,
                'count' => count($results)
            ]);

            return collect($results);
        } catch (\Exception $e) {
            Log::error('Failed to get PO details for GRD', [
                'poNumber' => $poNumber,
                'error' => $e->getMessage()
            ]);

            return collect([]);
        }
    }

    /**
     * Determine document type (GRD or PQC) based on PO type and material codes
     */
    private function determineDocumentType(SupplierTicket $supplierTicket)
    {
        try {
            // Get PO details to check material codes
            $poDetails = $this->posService->getPODetails($supplierTicket->po_number);

            if (!$poDetails || $poDetails->isEmpty()) {
                Log::warning('Cannot determine document type - PO details not found', [
                    'ticket_id' => $supplierTicket->id,
                    'po_number' => $supplierTicket->po_number
                ]);
                return 'GRD'; // Default to GRD if cannot determine
            }

            // Check if any material code starts with 'BP'
            $hasBPMaterial = $poDetails->filter(function ($item) {
                return str_starts_with($item->MaterialCode ?? '', 'BP');
            })->isNotEmpty();

            // Determine PO type from PO number
            $poNumber = $supplierTicket->po_number;
            $isPON = str_contains($poNumber, 'PON');
            $isPOM = str_contains($poNumber, 'POM');

            Log::info('Document type determination', [
                'ticket_id' => $supplierTicket->id,
                'po_number' => $poNumber,
                'isPON' => $isPON,
                'isPOM' => $isPOM,
                'hasBPMaterial' => $hasBPMaterial
            ]);

            // Rules:
            // 1. PON → always GRD
            if ($isPON) {
                return 'GRD';
            }

            // 2. POM → PQC, unless material code starts with BP
            if ($isPOM) {
                return $hasBPMaterial ? 'GRD' : 'PQC';
            }

            // Default to GRD if cannot determine
            return 'GRD';
        } catch (\Exception $e) {
            Log::error('Failed to determine document type', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);
            return 'GRD'; // Default to GRD on error
        }
    }

    /**
     * Generate GRD number with format GRD-YYMMDD-0001
     */
    private function generateGRDNumber()
    {
        $prefix = 'GRD';
        $date = now()->format('ymd'); // YYMMDD format

        // Get last GRD number for today from MySQL3 goodreceipth table
        try {
            // TODO: Connect to MySQL3 database to get last GRD number
            // For now, we'll use a simple counter
            $lastGRD = DB::connection('mysql6')
                ->table('goodsreceipth')
                ->where('DocNo', 'like', $prefix . '-' . $date . '-%')
                ->orderBy('DocNo', 'desc')
                ->first();

            if ($lastGRD) {
                $lastNumber = (int)substr($lastGRD->DocNo, -4);
                $sequence = $lastNumber + 1;
            } else {
                $sequence = 1;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get last GRD number: ' . $e->getMessage());
            $sequence = 1;
        }

        return $prefix . '-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Show form to create PQC after approval
     */
    public function showCreatePQCForm(SupplierTicket $supplierTicket)
    {
        // if ($supplierTicket->status !== 'approved') {
        //     return redirect()->back()
        //         ->with('error', 'Hanya ticket yang sudah diapprove yang dapat dibuat dokumennya.');
        // }

        // Get PO details for PQC (same as GRD)
        $poItemsForGRD = $this->getPODetailsForGRD($supplierTicket->po_number);

        // Generate PQC number
        $pqcNumber = $this->generatePQCNumber();

        // Determine document type for display
        $documentType = $this->determineDocumentType($supplierTicket);

        return view('admin.supplier-tickets.create-pqc-form', compact('supplierTicket', 'poItemsForGRD', 'pqcNumber', 'documentType'));
    }

    /**
     * Generate PQC number with format PQC-YYMMDD-0001
     */
    private function generatePQCNumber()
    {
        $prefix = 'PQC';
        $date = now()->format('ymd'); // YYMMDD format

        // Get last PQC number for today from MySQL3 table
        try {
            $lastPQC = DB::connection('mysql6')
                ->table('goodsreceipth') // Assuming PQC table name
                ->where('DocNo', 'like', $prefix . '-' . $date . '-%')
                ->orderBy('DocNo', 'desc')
                ->first();

            if ($lastPQC) {
                $lastNumber = (int)substr($lastPQC->DocNo, -4);
                $sequence = $lastNumber + 1;
            } else {
                $sequence = 1;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get last PQC number: ' . $e->getMessage());
            $sequence = 1;
        }

        return $prefix . '-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate TagNo via AJAX request
     */
    public function generateTagNo(Request $request)
    {
        try {
            $request->validate([
                'docdate' => 'required|date'
            ]);

            $tagNo = $this->generateTagNoLogic($request->input('docdate'));

            return response()->json([
                'success' => true,
                'tagNo' => $tagNo,
                'message' => 'TagNo berhasil di-generate'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate TagNo via AJAX', [
                'docdate' => $request->input('docdate'),
                'error' => $e->getMessage()
            ], 500);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate TagNo: ' . $e->getMessage()
            ], 500);
        }
    }
    public function generateTagNoPQC(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'docdate' => 'required|date'
            ]);

            $tagNo = $this->generateTagNoLogicPqc($request->input('docdate'));

            return response()->json([
                'success' => true,
                'tagNo' => $tagNo,
                'message' => 'TagNo berhasil di-generate'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate TagNo via AJAX', [
                'docdate' => $request->input('docdate'),
                'error' => $e->getMessage()
            ], 500);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate TagNo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate TagNo based on DocDate using FIFO logic
     */
    private function generateTagNoLogic($docDate)
    {
        try {
            // Format date to YYMMDD (e.g., 250923 for 23 September 2025)
            $datePrefix = Carbon::parse($docDate)->format('ymd');
            // dd($datePrefix);

            // SQL query to get max TagNo from multiple tables
            $sql = "
                SELECT IFNULL(MAX(X.TagNo),0) as maxTagNo FROM (SELECT IFNULL(MAX(s.TagNo),0) AS TagNo FROM stock AS s WHERE s.TagNo LIKE '{$datePrefix}%' UNION ALL SELECT IFNULL(MAX(grd.TagNo),0) AS TagNo FROM goodsreceiptd AS grd WHERE grd.TagNo LIKE '{$datePrefix}%' UNION ALL SELECT IFNULL(MAX(jrd.TagNo),0) AS TagNo FROM jobresultd AS jrd WHERE jrd.TagNo LIKE '{$datePrefix}%' UNION ALL SELECT IFNULL(MAX(aid.TagNo),0) AS TagNo FROM adjustind AS aid WHERE aid.TagNo LIKE '{$datePrefix}%') AS X FOR UPDATE
            ";

            // dd($sql);

            $result = DB::connection('mysql6')->select($sql);
            // dd($result);
            $maxTagNo = $result[0]->maxTagNo ?? 0;

            // If no data found, start from 0001, otherwise increment by 1
            if ($maxTagNo == 0) {
                $newTagNo = $datePrefix . '0001';
            } else {
                $newTagNo = $maxTagNo + 1;
            }

            Log::info('Generated TagNo', [
                'docDate' => $docDate,
                'datePrefix' => $datePrefix,
                'maxTagNo' => $maxTagNo,
                'newTagNo' => $newTagNo
            ]);

            return $newTagNo;
        } catch (\Exception $e) {
            Log::error('Failed to generate TagNo', [
                'docDate' => $docDate,
                'error' => $e->getMessage()
            ]);

            // Fallback: use timestamp if generation fails
            $datePrefix = \Carbon\Carbon::parse($docDate)->format('ymd');
            return $datePrefix . '0001';
        }
    }
    private function generateTagNoLogicPqc($docDate)
    {
        try {
            // Format date to YYMMDD (e.g., 250923 for 23 September 2025)
            $datePrefix = Carbon::parse($docDate)->format('ymd');
            // dd($datePrefix);

            // SQL query to get max TagNo from multiple tables
            $sql = "
                SELECT IFNULL(MAX(X.TagNo),0) as maxTagNo FROM (SELECT IFNULL(MAX(s.TagNo),0) AS TagNo FROM stock AS s WHERE s.TagNo LIKE '{$datePrefix}%' UNION ALL SELECT IFNULL(MAX(grd.TagNo),0) AS TagNo FROM goodsreceiptd AS grd WHERE grd.TagNo LIKE '{$datePrefix}%' UNION ALL SELECT IFNULL(MAX(jrd.TagNo),0) AS TagNo FROM jobresultd AS jrd WHERE jrd.TagNo LIKE '{$datePrefix}%' UNION ALL SELECT IFNULL(MAX(aid.TagNo),0) AS TagNo FROM adjustind AS aid WHERE aid.TagNo LIKE '{$datePrefix}%') AS X FOR UPDATE
            ";

            // dd($sql);

            $result = DB::connection('mysql6')->select($sql);
            // dd($result);
            $maxTagNo = $result[0]->maxTagNo ?? 0;

            // dd($maxTagNo);
            // If no data found, start from 0001, otherwise increment by 1
            if ($maxTagNo == 0) {
                $newTagNo = $datePrefix . '0001';
            } else {
                $newTagNo = $maxTagNo + 1;
            }

            // dd($newTagNo);

            Log::info('Generated TagNo', [
                'docDate' => $docDate,
                'datePrefix' => $datePrefix,
                'maxTagNo' => $maxTagNo,
                'newTagNo' => $newTagNo
            ]);

            return $newTagNo;
        } catch (\Exception $e) {
            Log::error('Failed to generate TagNo', [
                'docDate' => $docDate,
                'error' => $e->getMessage()
            ]);

            // Fallback: use timestamp if generation fails
            $datePrefix = \Carbon\Carbon::parse($docDate)->format('ymd');
            return $datePrefix . '0001';
        }
    }

    /**
     * Create automatic rejection for partial delivery
     */
    private function createAutomaticRejection(SupplierTicket $supplierTicket, array $rejectionItems)
    {
        try {
            $totalRejectedQty = array_sum(array_column($rejectionItems, 'rejected_qty'));
            $totalAcceptedQty = array_sum(array_column($rejectionItems, 'received_qty'));

            $rejectionReason = "Partial delivery detected:\n";
            foreach ($rejectionItems as $item) {
                $rejectionReason .= "- {$item['material_code']} ({$item['material_name']}): " .
                    "Ordered: {$item['original_qty']} {$item['unit']}, " .
                    "Received: {$item['received_qty']} {$item['unit']}, " .
                    "Rejected: {$item['rejected_qty']} {$item['unit']}\n";
            }

            // Create rejection ticket
            $rejectionTicket = SupplierTicket::create([
                'ticket_number' => 'REJ-' . $supplierTicket->ticket_number,
                'supplier_name' => $supplierTicket->supplier_name,
                'supplier_contact' => $supplierTicket->supplier_contact,
                'supplier_email' => $supplierTicket->supplier_email,
                'supplier_address' => $supplierTicket->supplier_address,
                'po_number' => $supplierTicket->po_number,
                'supplier_delivery_doc' => $supplierTicket->supplier_delivery_doc,
                'delivery_date' => $supplierTicket->delivery_date,
                'vehicle_number' => $supplierTicket->vehicle_number,
                'arrival_date' => $supplierTicket->arrival_date,
                'status' => 'rejected',
                'rejection_reason' => $rejectionReason,
                'rejected_quantity' => $totalRejectedQty,
                'accepted_quantity' => $totalAcceptedQty,
                'rejection_date' => now(),
                'created_by' => Auth::id(),
                'processed_by' => Auth::id(),
                'processed_at' => now()
            ]);

            // Send rejection notification
            try {
                $notificationService = new SupplierTicketNotificationService();
                $rejectionLetter = "Surat penolakan otomatis untuk pengiriman sebagian dari PO {$supplierTicket->po_number}";
                $notificationService->sendSupplierTicketRejectionNotification($rejectionTicket, $rejectionLetter);
            } catch (\Exception $e) {
                Log::error('Failed to send rejection notification: ' . $e->getMessage());
            }

            Log::info('Automatic rejection created', [
                'original_ticket_id' => $supplierTicket->id,
                'rejection_ticket_id' => $rejectionTicket->id,
                'rejected_items_count' => count($rejectionItems)
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create automatic rejection', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show combined GRD/PQC and Reject form
     */
    public function showCombinedForm(SupplierTicket $supplierTicket)
    {
        // Get PO details for both forms
        $poItemsForGRD = $this->getPODetailsForGRD($supplierTicket->po_number);

        // Determine document type
        $documentType = $this->determineDocumentType($supplierTicket);

        // Generate document numbers
        $grdNumber = $this->generateGRDNumber();
        $pqcNumber = $this->generatePQCNumber();

        return view('admin.supplier-tickets.combined-form', compact(
            'supplierTicket',
            'poItemsForGRD',
            'documentType',
            'grdNumber',
            'pqcNumber'
        ));
    }

    /**
     * Show reject form
     */
    public function showRejectForm(SupplierTicket $supplierTicket)
    {
        $poItemsForGRD = $this->getPODetailsForGRD($supplierTicket->po_number);
        // dd($poItemsForGRD);

        // Debug: Log the supplier ticket data
        
        return view('admin.supplier-tickets.reject-form', compact('supplierTicket', 'poItemsForGRD'));
    }

    /**
     * Generate rejection letter
     */
    public function generateRejectionLetter(SupplierTicket $supplierTicket)
    {
        // dd($supplierTicket);
        // $supplierTicket = $supplierTicket->load('getRejectedItems');
        $rejectionNumber = 'SP-' . str_pad($supplierTicket->id, 3, '0', STR_PAD_LEFT) . '/' . 
                          strtolower(date('M')) . '/' . date('Y');

        $isPdf = false;
        return view('admin.supplier-tickets.rejection-letter', compact('supplierTicket', 'rejectionNumber', 'isPdf'));
    }

    /**
     * Send rejection letter via email with form data
     */
    public function sendRejectionEmail(Request $request, SupplierTicket $supplierTicket)
    {
        try {
            $request->validate([
                'recipient_email' => 'required|email',
                'email_subject' => 'required|string',
                'cc_email' => 'nullable|email',
                'email_message' => 'nullable|string'
            ]);

            $rejectionNumber = 'SP-' . str_pad($supplierTicket->id, 3, '0', STR_PAD_LEFT) . '/' . 
                              strtolower(date('M')) . '/' . date('Y');

            // Generate PDF
            PDF::setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                
            ]);
            $isPdf = true;
            $pdf = Pdf::loadView('admin.supplier-tickets.rejection-letter', compact('supplierTicket', 'rejectionNumber', 'isPdf'));
            $pdf->setPaper('A4', 'portrait');
            
            // Prepare email data
            $recipientEmail = $request->input('recipient_email');
            $ccEmail = $request->input('cc_email');
            $subject = $request->input('email_subject');
            $additionalMessage = $request->input('email_message', '');

            // Create email body with additional message
            $emailBody = $additionalMessage ? 
                "<p>" . nl2br(e($additionalMessage)) . "</p><hr><br><p>Surat penolakan terlampir dalam file PDF.</p>" : 
                "<p>Surat penolakan terlampir dalam file PDF.</p>";

            // Generate PDF filename
            $pdfFilename = 'Surat_Penolakan_' . $supplierTicket->po_number . '_' . date('Y-m-d') . '.pdf';

            // Send email with PDF attachment
            Mail::send([], [], function ($message) use ($recipientEmail, $ccEmail, $subject, $emailBody, $supplierTicket, $pdf, $pdfFilename) {
                $message->to($recipientEmail);
                if ($ccEmail) {
                    $message->cc($ccEmail);
                }
                $message->subject($subject);
                // Set HTML body first, then attach PDF using the wrapper methods
                $message->setBody($emailBody, 'text/html');
                $message->attachData($pdf->output(), $pdfFilename, [
                    'mime' => 'application/pdf',
                ]);
            });

            Log::info('Rejection letter sent via form', [
                'ticket_id' => $supplierTicket->id,
                'recipient_email' => $recipientEmail,
                'cc_email' => $ccEmail,
                'subject' => $subject
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Surat penolakan berhasil dikirim ke ' . $recipientEmail
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send rejection letter via form', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send rejection letter via email
     */
    public function sendRejectionLetter(SupplierTicket $supplierTicket)
    {
        try {
            $rejectionNumber = 'SP-' . str_pad($supplierTicket->id, 3, '0', STR_PAD_LEFT) . '/' . 
                              strtolower(date('M')) . '/' . date('Y');

            // Generate the letter content
            $letterContent = view('admin.supplier-tickets.rejection-letter', compact('supplierTicket', 'rejectionNumber'))->render();

            // Send email to supplier (for testing, send to jalu.bagaskara@krisanthium.com)
            $testEmail = 'jalu.bagaskara@krisanthium.com';
            // $supplierEmail = $supplierTicket->supplier_email ?? $testEmail;
            $supplierEmail = 'jalu.bagaskara@krisanthium.com';

            Mail::send([], [], function ($message) use ($supplierEmail, $supplierTicket, $rejectionNumber, $letterContent) {
                $message->to($supplierEmail)
                        ->subject('SURAT PENOLAKAN - ' . $supplierTicket->po_number)
                        ->setBody($letterContent, 'text/html');
            });

            Log::info('Rejection letter sent successfully', [
                'ticket_id' => $supplierTicket->id,
                'supplier_email' => $supplierEmail,
                'rejection_number' => $rejectionNumber
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send rejection letter', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Process reject form submission
     */
    public function processReject(Request $request, SupplierTicket $supplierTicket)
    {
        // dd($request->all());
        try {
            Log::info('Processing rejection', [
                'ticket_id' => $supplierTicket->id,
                'request_data' => $request->all()
            ]);

            // Validate required fields
            // $request->validate([
            //     'rejection_reason' => 'required|string|min:10',
            //     'rejected_items' => 'required|array',
            //     'rejected_items.*.material_code' => 'required|string',
            //     'rejected_items.*.rejected_qty' => 'required|numeric|min:0',
            //     'rejected_items.*.reason' => 'required|string'
            // ]);

            // Calculate total rejected quantity
            $rejectedItems = $request->input('rejected_items', []);
            $totalRejectedQty = 0;
            $validRejectedItems = [];

            foreach ($rejectedItems as $item) {
                $quantity_rejected = $item['rejected_qty'];
                $totalRejectedQty += (float)($quantity_rejected);
                
                // Only include items with rejection quantity > 0
                if ((float)$quantity_rejected > 0) {
                    $validRejectedItems[] = [
                        'material_code' => $item['material_code'] ?? '',
                        'rejected_qty' => (float)$quantity_rejected,
                        'reason' => $item['reason'] ?? 'Rejected by admin'
                    ];
                }
            }

            if ($totalRejectedQty <= 0) {
                return redirect()->back()
                    ->with('error', 'Minimal satu item harus memiliki quantity yang ditolak.')
                    ->withInput();
            }

            Log::info('Rejection data processed', [
                'ticket_id' => $supplierTicket->id,
                'total_rejected_qty' => $totalRejectedQty,
                'valid_rejected_items' => $validRejectedItems
            ]);

            // Update current ticket status to rejected
            $supplierTicket->update([
                // 'status' => 'rejected',
                'rejection_reason' => $request->input('rejection_reason'),
                'rejected_quantity' => $totalRejectedQty,
                'accepted_quantity' => 0,
                'rejection_date' => now(),
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'rejected_items_json' => json_encode($validRejectedItems) // Store rejected items as JSON
            ]);

            // Send rejection notification
            // try {
            //     // $notificationService = new SupplierTicketNotificationService();
            //     // $rejectionLetter = "Surat penolakan untuk PO {$supplierTicket->po_number}: " . $request->input('rejection_reason');
            //     // $notificationService->sendSupplierTicketRejectionNotification($supplierTicket, $rejectionLetter);
            // } catch (\Exception $e) {
            //     Log::error('Failed to send rejection notification: ' . $e->getMessage());
            // }

            // Send rejection letter via email
            $emailSent = $this->sendRejectionLetter($supplierTicket);

            Log::info('Rejection processed successfully', [
                'ticket_id' => $supplierTicket->id,
                'rejection_reason' => $request->input('rejection_reason'),
                'rejected_items_count' => count($validRejectedItems),
                'email_sent' => $emailSent
            ]);

            $successMessage = 'Surat reject berhasil dibuat.';
            if ($emailSent) {
                $successMessage .= ' Email telah dikirim ke supplier.';
            } else {
                $successMessage .= ' Gagal mengirim email ke supplier.';
            }

            return redirect()->route('admin.supplier-tickets.show', $supplierTicket->id)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Failed to process rejection', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal membuat surat reject: ' . $e->getMessage());
        }
    }

    /**
     * Reset ticket status back to processed (for testing)
     */
    public function resetToProcessed(SupplierTicket $supplierTicket)
    {
        try {
            $supplierTicket->update([
                'status' => 'processed',
                'processed_by' => null,
                'processed_at' => null
            ]);

            Log::info('Ticket reset to processed', [
                'ticket_id' => $supplierTicket->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('success', 'Status ticket berhasil direset ke PROCESSED.');
        } catch (\Exception $e) {
            Log::error('Failed to reset ticket status', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()
                ->with('error', 'Gagal mereset status ticket: ' . $e->getMessage());
        }
    }

    /**
     * Show form to create GRD/PQC after approval
     */
    public function showCreateDocument(SupplierTicket $supplierTicket)
    {
        if ($supplierTicket->status !== SupplierTicket::STATUS_APPROVED) {
            return redirect()->back()
                ->with('error', 'Hanya ticket yang sudah diapprove yang dapat dibuat dokumennya.');
        }

        // Cek apakah sudah ada dokumen yang dibuat
        $hasGRD = $supplierTicket->grd_documents()->exists();
        $hasPQC = $supplierTicket->pqc_documents()->exists();

        return view('admin.supplier-tickets.create-document', compact('supplierTicket', 'hasGRD', 'hasPQC'));
    }

    /**
     * Create GRD document
     */
    public function createGRD(Request $request, SupplierTicket $supplierTicket)
    {
        // dd($request->all());
        try {
            Log::info('Creating GRD document', [
                'ticket_id' => $supplierTicket->id,
                'user_id' => Auth::id()
            ]);

            // Validate required fields
            $request->validate([
                'location' => 'required|string',
                'qty_received' => 'required|array',
                'qty_received.*' => 'required|numeric|min:0'
            ]);

            // Get PO items for validation
            $poItems = $this->posService->getPODetails($supplierTicket->po_number);

            if (!$poItems || $poItems->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'PO items tidak ditemukan.');
            }

            // Check if there's rejection data
            $hasRejectionData = $request->has('rejection_reason') && 
                              !empty($request->input('rejection_reason')) && 
                              $request->has('rejected_items');

            if ($hasRejectionData) {
                // Process rejection data
                $rejectedItems = $request->input('rejected_items', []);
                $validRejectedItems = [];
                $totalRejectedQty = 0;

                foreach ($rejectedItems as $rejectedItem) {
                    $rejectedQty = (float) $rejectedItem['rejected_qty'];
                    
                    if ($rejectedQty > 0) {
                        $validRejectedItems[] = [
                            'material_code' => $rejectedItem['material_code'],
                            'rejected_qty' => $rejectedQty,
                            'reason' => $rejectedItem['reason']
                        ];
                        $totalRejectedQty += $rejectedQty;
                    }
                }

                if (!empty($validRejectedItems)) {
                    // Update supplier ticket with rejection data
                    $supplierTicket->update([
                        'status' => 'rejected',
                        'rejection_reason' => $request->input('rejection_reason'),
                        'rejection_date' => now(),
                        'rejected_item_json' => json_encode($validRejectedItems)
                    ]);

                    Log::info('GRD rejection processed', [
                        'ticket_id' => $supplierTicket->id,
                        'rejection_reason' => $request->input('rejection_reason'),
                        'rejected_quantity' => $totalRejectedQty,
                        'rejected_items_count' => count($validRejectedItems)
                    ]);

                    return redirect()->route('admin.supplier-tickets.show', $supplierTicket->id)
                        ->with('success', 'Item berhasil di-reject dan surat penolakan telah dibuat.');
                }
            }

            // Create GRD document in mysql6 database
            $grdData = [
                'DocNo' => $request->input('grd_number'),
                'Series' => 'GRD',
                'DocDate' => Carbon::parse($request->input('docdate'))->format('Y-m-d'),
                'SupplierCode' => $poItems->first()->SupplierCode ?? '',
                'PODocNo' => $supplierTicket->po_number,
                'Location' => $request->input('location'),
                'Zone' => '-',
                'SupplierDlvDocNo' => $request->input('supplier_delivery_no') ?? '-',
                'VehicleNo' => $request->input('vehicle_no') ?? '-',
                'Information' => $request->input('information') ?? '-',
                'Status' => 'OPEN',
                'PrintCounter' => 0,
                'PrintedBy' => '',
                'PrintedDate' => null,
                'CreatedBy' => Auth::user()->username,
                'CreatedDate' => now(),
                'ChangedBy' => Auth::user()->username,
                'ChangedDate' => now(),

            ];

            // Insert into mysql6.goodreceipth table
            DB::connection('mysql6')->table('goodsreceipth')->insert($grdData);

            // Insert detail items into goodsreceiptd table
            $grdDocNo = $request->input('grd_number');
            $itemNumber = 1;

            // Generate base TagNo once for this document
            $baseTagNo = $this->generateTagNoLogic($request->input('docdate'));
            $tagNoCounter = 0;

            foreach ($poItems as $index => $item) {
                // dd($item);
                $receivedQty = floatval($request->input("qty_received.{$index}", 0));
                $expiryDate = $request->input("expiry_date.{$index}", null);

                // Generate unique TagNo for this item (increment for each item)
                $tagNoCounter++;
                $tagNo = $baseTagNo + $tagNoCounter - 1;

                // Get MaterialCode from either Code or POMaterialCode
                $materialCode = $item->MaterialCode;
                // dd($materialCode);
                $unit = $item->unit;
                // dd($unit);

                // Skip if quantity is 0 or MaterialCode is empty
                if ($receivedQty <= 0 || empty($materialCode)) {
                    Log::warning('Skipping item due to invalid data', [
                        'index' => $index,
                        'receivedQty' => $receivedQty,
                        'materialCode' => $materialCode,
                        'item' => $item
                    ]);
                    continue;
                }

                $grdDetailData = [
                    'DocNo' => $grdDocNo,
                    'Number' => $itemNumber,
                    'MaterialCode' => $materialCode,
                    'Info' => '-',
                    'BatchNo' => '-',
                    'BatchInfo' => '-',
                    'ExpiryDate' => $expiryDate ? Carbon::parse($expiryDate)->format('Y-m-d') : '1900-01-01', // Default date if empty
                    'TagNo' => $tagNo ?? '-',
                    'Unit' => $unit,
                    'Qty' => $receivedQty
                ];

                // dd($grdDetailData);

                Log::info('Inserting GRD detail', [
                    'grdDetailData' => $grdDetailData
                ]);

                DB::connection('mysql6')->table('goodsreceiptd')->insert($grdDetailData);

                // Update PO Received Quantity
                $updatePOQty = "
                    UPDATE purchaseorderd 
                    SET QtyReceived = QtyReceived + ? 
                    WHERE DocNo = ? AND Number = ?
                ";
                DB::connection('mysql6')->update($updatePOQty, [
                    $receivedQty,
                    $supplierTicket->po_number,
                    $item->Number ?? $itemNumber
                ]);

                $itemNumber++;
            }

            // Update supplier ticket status
            $supplierTicket->update([
                'status' => 'completed',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'grd_number' => $request->input('docno'),
                'grd_created_by' => Auth::id(),
                'grd_created_at' => now()
            ]);

            Log::info('GRD document created successfully', [
                'ticket_id' => $supplierTicket->id,
                'grd_number' => $request->input('docno')
            ]);

            return redirect()->route('admin.supplier-tickets.show', $supplierTicket->id)
                ->with('success', 'GRD document berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error('Failed to create GRD document', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal membuat dokumen GRD: ' . $e->getMessage());
        }
    }

    /**
     * Combined method to handle both PQC creation and rejection
     */
    public function processCombined(Request $request, SupplierTicket $supplierTicket)
    {
        $action = $request->input('action'); // 'pqc', 'reject', or 'combined'

        if ($action === 'reject') {
            return $this->processReject($request, $supplierTicket);
        } else {
            // Handle both 'pqc' and 'combined' in createPQC
            return $this->createPQC($request, $supplierTicket);
        }
    }


    /**
     * Create PQC document
     */
    public function createPQC(Request $request, SupplierTicket $supplierTicket)
    {
        // dd($request->all());
        try {
            Log::info('Creating PQC document', [
                'ticket_id' => $supplierTicket->id,
                'user_id' => Auth::id(),
                'has_reject_data' => $request->has('rejection_reason')
            ]);

            $poItems = $this->getPODetailsForGRD($supplierTicket->po_number);

            if (!$poItems || $poItems->isEmpty()) {
                return redirect()->back()->with('error', 'PO items tidak ditemukan.');
            }


            DB::connection('mysql6')->beginTransaction();

            Log::info('PQC transaction started', [
                'ticket_id' => $supplierTicket->id,
                'docno' => $request->input('docno')
            ]);

            // 1. Insert PQC Header (goodsreceipth)
            $pqcData = [
                'DocNo' => $request->input('docno'),
                'Series' => 'PQC',
                'DocDate' => Carbon::parse($request->input('docdate'))->format('Y-m-d'),
                'SupplierCode' => $poItems->first()->SupplierCode ?? '',
                'PODocNo' => $supplierTicket->po_number,
                'Location' => $request->input('location'),
                'Zone' => '-',
                'SupplierDlvDocNo' => $supplierTicket->supplier_delivery_doc ?? '-',
                'VehicleNo' => $request->input('vehicle_no') ?? '-',
                'Information' => $request->input('information') ?? '-',
                'Status' => 'OPEN',
                'PrintCounter' => 0,
                'PrintedBy' => '',
                'PrintedDate' => null,
                'CreatedBy' => Auth::user()->username,
                'CreatedDate' => now(),
                'ChangedBy' => Auth::user()->username,
                'ChangedDate' => now()
            ];

            // dd($pqcData);

            Log::info('PQC Header Data', ['pqcData' => $pqcData]);

            try {
                DB::connection('mysql6')->table('goodsreceipth')->insert($pqcData);
                Log::info('PQC Header inserted successfully');
            } catch (\Exception $e) {
                Log::error('Failed to insert PQC header', [
                    'error' => $e->getMessage(),
                    'pqcData' => $pqcData
                ]);
                throw $e;
            }

            // 2. Insert PQC Details and process each item
            $pqcDocNo = $request->input('docno');
            $itemNumber = 1;

            // Generate base TagNo once for this document
            $baseTagNo = $this->generateTagNoLogic($request->input('docdate'));
            $tagNoCounter = 0;

            foreach ($poItems as $index => $item) {
                $receivedQty = floatval($request->input("qty_received.{$index}", 0));
                $expiryDate = $request->input("expiry_date.{$index}", null);

                // Get MaterialCode from either Code or POMaterialCode
                $materialCode = $item->Code ?? $item->POMaterialCode ?? '';
                $unit = $item->Unit ?? 'PCS';

                // Skip if quantity is 0 or MaterialCode is empty
                if ($receivedQty <= 0 || empty($materialCode)) {
                    Log::warning('Skipping PQC item due to invalid data', [
                        'index' => $index,
                        'receivedQty' => $receivedQty,
                        'materialCode' => $materialCode,
                        'item' => $item
                    ]);
                    continue;
                }

                // Generate unique TagNo for this item (increment for each item)
                $tagNoCounter++;
                $tagNo = $baseTagNo + $tagNoCounter - 1;

                // 2a. Insert PQC Detail (goodsreceiptd)
                $pqcDetailData = [
                    'DocNo' => $pqcDocNo,
                    'Number' => (int)$itemNumber,
                    'MaterialCode' => $materialCode,
                    'Info' => '-',
                    'TagNo' => $tagNo,
                    'BatchNo' => '-',
                    'BatchInfo' => '-',
                    'ExpiryDate' => $expiryDate ? Carbon::parse($expiryDate)->format('Y-m-d') : null,
                    'Unit' => $unit,
                    'Qty' => $receivedQty
                ];

                Log::info('PQC Detail Data', ['pqcDetailData' => $pqcDetailData]);

                try {
                    DB::connection('mysql6')->table('goodsreceiptd')->insert($pqcDetailData);
                    Log::info('PQC Detail inserted successfully', ['itemNumber' => $itemNumber]);
                } catch (\Exception $e) {
                    Log::error('Failed to insert PQC detail', [
                        'error' => $e->getMessage(),
                        'pqcDetailData' => $pqcDetailData
                    ]);
                    throw $e;
                }

                // 2b. Update PO Received Quantity
                $updatePOQty = "
					UPDATE purchaseorderd 
					SET QtyReceived = QtyReceived + ? 
					WHERE DocNo = ? AND Number = ?
				";
                try {
                    Log::info('Updating PO received quantity', [
                        'po_number' => $supplierTicket->po_number,
                        'po_item_number' => $item->Number,
                        'qty_add' => $receivedQty
                    ]);
                    $affected = DB::connection('mysql6')->update($updatePOQty, [
                        $receivedQty,
                        $supplierTicket->po_number,
                        $item->Number
                    ]);
                    Log::info('PO received quantity updated', [
                        'affected_rows' => $affected
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to update PO received quantity', [
                        'error' => $e->getMessage(),
                        'po_number' => $supplierTicket->po_number,
                        'po_item_number' => $item->Number,
                        'params' => [
                            $receivedQty,
                            $supplierTicket->po_number,
                            $item->Number
                        ]
                    ]);
                    throw $e;
                }

                // 2c. Insert to Stock
                $stockData = [
                    'TagNo' => $tagNo,
                    'MaterialCode' => $materialCode,
                    'DocNo' => $pqcDocNo,
                    'DocDate' => Carbon::parse($request->input('docdate'))->format('Ymd'),
                    'Location' => $request->input('location'),
                    'Zone' => $request->input('zone') ?? '-',
                    'Bin' => '',
                    'Number' => (int)$itemNumber,
                    'Qty' => $receivedQty,
                    'Price' => 0
                ];

                try {
                    Log::info('Inserting stock row', ['stockData' => $stockData]);
                    DB::connection('mysql6')->table('stock')->insert($stockData);
                    Log::info('Stock row inserted');
                } catch (\Exception $e) {
                    Log::error('Failed to insert stock row', [
                        'error' => $e->getMessage(),
                        'stockData' => $stockData
                    ]);
                    throw $e;
                }

                // 2d. Insert to Booking
                $bookingData = [
                    'TagNo' => $tagNo,
                    'MaterialCode' => $materialCode,
                    'DocNo' => $request->input('location') . '/' . $pqcDocNo,
                    'DocDate' => Carbon::parse($request->input('docdate'))->format('Ymd'),
                    'Location' => $request->input('location'),
                    'Zone' => $request->input('zone') ?? '-',
                    'Bin' => '',
                    'Number' => (int)$itemNumber,
                    'Qty' => $receivedQty
                ];

                try {
                    Log::info('Inserting booking row', ['bookingData' => $bookingData]);
                    DB::connection('mysql6')->table('booking')->insert($bookingData);
                    Log::info('Booking row inserted');
                } catch (\Exception $e) {
                    Log::error('Failed to insert booking row', [
                        'error' => $e->getMessage(),
                        'bookingData' => $bookingData
                    ]);
                    throw $e;
                }

                $itemNumber++;
            }

            // 3. Update PO Status if all items received
            $checkPOComplete = "
                UPDATE purchaseorderh 
                SET Status = 'PRINTED' 
                WHERE DocNo = ? 
                AND NOT EXISTS(
                    SELECT * FROM purchaseorderd 
                    WHERE DocNo = ? AND QtyReceived > 0
                )
            ";

            // dd($checkPOComplete);
            try {
                Log::info('Updating PO status if complete', [
                    'po_number' => $supplierTicket->po_number
                ]);
                $affectedPoH = DB::connection('mysql6')->update($checkPOComplete, [
                    $supplierTicket->po_number,
                    $supplierTicket->po_number
                ]);
                Log::info('PO header status update executed', [
                    'affected_rows' => $affectedPoH
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to update PO header status', [
                    'error' => $e->getMessage(),
                    'po_number' => $supplierTicket->po_number
                ]);
                throw $e;
            }

            // Commit transaction
            DB::connection('mysql6')->commit();
            Log::info('PQC transaction committed', [
                'ticket_id' => $supplierTicket->id,
                'pqc_number' => $request->input('docno')
            ]);

            // Update supplier ticket status
            $supplierTicket->update([
                'status' => 'completed',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
                'pqc_number' => $request->input('docno'),
                'pqc_created_by' => Auth::id(),
                'pqc_created_at' => now()
            ]);

            // Process rejection data if present
            if ($request->has('rejection_reason')) {
                $rejectedItems = $request->input('rejected_items', []);
                $totalRejectedQty = 0;
                $validRejectedItems = [];

                foreach ($rejectedItems as $item) {
                    $quantity_rejected = $item['rejected_qty'];
                    $totalRejectedQty += (float)($quantity_rejected);
                    
                    // Only include items with rejection quantity > 0
                    if ((float)$quantity_rejected > 0) {
                        $validRejectedItems[] = [
                            'material_code' => $item['material_code'] ?? '',
                            'rejected_qty' => (float)$quantity_rejected,
                            'reason' => $item['reason'] ?? 'Rejected by admin'
                        ];
                    }
                }

                if ($totalRejectedQty > 0) {
                    $supplierTicket->update([
                        'rejection_reason' => $request->input('rejection_reason'),
                        'rejected_quantity' => $totalRejectedQty,
                        'accepted_quantity' => 0,
                        'rejection_date' => now(),
                        'rejected_item_json' => json_encode($validRejectedItems) // Store rejected items as JSON
                    ]);

                    Log::info('Rejection data processed in PQC', [
                        'ticket_id' => $supplierTicket->id,
                        'rejection_reason' => $request->input('rejection_reason'),
                        'rejected_quantity' => $totalRejectedQty,
                        'rejected_items_count' => count($validRejectedItems)
                    ]);
                } else {
                    // dd('no rejected items');
                }
            }

            Log::info('PQC document created successfully', [
                'ticket_id' => $supplierTicket->id,
                'pqc_number' => $request->input('docno'),
                'has_reject_data' => $request->has('rejection_reason')
            ]);

            $successMessage = 'PQC document berhasil dibuat.';
            if ($request->has('rejection_reason')) {
                $successMessage = 'Dokumen PQC dan surat reject berhasil dibuat.';
            }

            return redirect()->route('admin.supplier-tickets.show', $supplierTicket->id)->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::connection('mysql6')->rollBack();

            Log::error('Failed to create PQC document', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal membuat dokumen PQC: ' . $e->getMessage());
        }
    }

    /**
     * Get statistics for admin dashboard
     */
    public function statistics()
    {
        $stats = [
            'total' => SupplierTicket::count(),
            'pending' => SupplierTicket::where('status', SupplierTicket::STATUS_PROCESSED)->count(),
            'approved' => SupplierTicket::where('status', SupplierTicket::STATUS_APPROVED)->count(),
            'rejected' => SupplierTicket::where('status', SupplierTicket::STATUS_REJECTED)->count(),
            'completed' => SupplierTicket::where('status', SupplierTicket::STATUS_COMPLETED)->count(),
            'today' => SupplierTicket::whereDate('created_at', today())->count(),
            'this_week' => SupplierTicket::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => SupplierTicket::whereMonth('created_at', now()->month)->count()
        ];

        return response()->json($stats);
    }

    /**
     * Generate document number based on series
     */
    public function generateDocNumber(Request $request)
    {
        try {
            $series = $request->input('series');
            
            if (!$series) {
                return response()->json([
                    'success' => false,
                    'message' => 'Series is required'
                ], 400);
            }

            // Generate document number based on series
            $currentDate = now()->format('ymd');
            $prefix = $series;
            
            // Get the last document number for this series today
            $lastDoc = DB::connection('mysql6')
                ->table('goodsreceipth')
                ->where('series', $series)
                ->whereDate('createdDate', today())
                ->orderBy('DocNo', 'desc')
                ->first();

            $sequence = 1;
            if ($lastDoc) {
                // Extract sequence from last document number
                $lastDocNumber = $lastDoc->docno;
                if (preg_match('/' . $prefix . $currentDate . '(\d{4})/', $lastDocNumber, $matches)) {
                    $sequence = (int)$matches[1] + 1;
                }
            }

            // Format: GRD2509260001, GRO2509260001, GRM2509260001
            $docNumber = $prefix . '-' . $currentDate . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'docNumber' => $docNumber
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate document number', [
                'series' => $request->input('series'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate document number: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process GRD rejection
     */
    public function processGRDReject(Request $request, SupplierTicket $supplierTicket)
    {
        try {
            DB::connection('mysql6')->beginTransaction();

            $request->validate([
                'rejection_reason' => 'required|string|max:1000',
                'rejected_items' => 'required|array|min:1',
                'rejected_items.*.material_code' => 'required|string',
                'rejected_items.*.rejected_qty' => 'required|numeric|min:0.0001',
                'rejected_items.*.reason' => 'required|string|max:500'
            ]);

            // Process rejected items
            $validRejectedItems = [];
            $totalRejectedQty = 0;

            foreach ($request->input('rejected_items') as $rejectedItem) {
                $rejectedQty = (float) $rejectedItem['rejected_qty'];
                
                if ($rejectedQty > 0) {
                    $validRejectedItems[] = [
                        'material_code' => $rejectedItem['material_code'],
                        'rejected_qty' => $rejectedQty,
                        'reason' => $rejectedItem['reason']
                    ];
                    $totalRejectedQty += $rejectedQty;
                }
            }

            if (empty($validRejectedItems)) {
                return redirect()->back()
                    ->with('error', 'Tidak ada item yang di-reject dengan quantity yang valid.');
            }

            // Update supplier ticket with rejection data
            $supplierTicket->update([
                'status' => 'rejected',
                'rejection_reason' => $request->input('rejection_reason'),
                'rejection_date' => now(),
                'rejected_item_json' => json_encode($validRejectedItems)
            ]);

            Log::info('GRD rejection processed', [
                'ticket_id' => $supplierTicket->id,
                'rejection_reason' => $request->input('rejection_reason'),
                'rejected_quantity' => $totalRejectedQty,
                'rejected_items_count' => count($validRejectedItems)
            ]);

            DB::connection('mysql6')->commit();

            return redirect()->route('admin.supplier-tickets.show', $supplierTicket->id)
                ->with('success', 'Item berhasil di-reject dan surat penolakan telah dibuat.');

        } catch (\Exception $e) {
            DB::connection('mysql6')->rollBack();

            Log::error('Failed to process GRD rejection', [
                'ticket_id' => $supplierTicket->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal memproses rejection: ' . $e->getMessage());
        }
    }

    /**
     * Show supplier arrival report
     */
    public function supplierArrivalReport()
    {
        return view('admin.supplier-tickets.supplier-arrival-report');
    }

    /**
     * Get supplier arrival data for report
     */
    public function getSupplierArrivalData(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

            // Get completed supplier tickets within date range
            $supplierTickets = SupplierTicket::where('status', 'completed')
                ->whereBetween('delivery_date', [$startDate, $endDate])
                ->with(['creator', 'processor'])
                ->orderBy('delivery_date', 'desc')
                ->get();

            // Format data for datatable
            $data = $supplierTickets->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'supplier_name' => $ticket->supplier_name,
                    'po_number' => $ticket->po_number,
                    'delivery_date' => $ticket->delivery_date ? Carbon::parse($ticket->delivery_date)->format('d M Y') : '-',
                    'delivery_time' => $ticket->delivery_date ? Carbon::parse($ticket->delivery_date)->format('H:i') : '-',
                    'vehicle_number' => $ticket->vehicle_number ?? '-',
                    'supplier_delivery_doc' => $ticket->supplier_delivery_doc ?? '-',
                    'status' => $ticket->status,
                    'created_by' => $ticket->creator ? $ticket->creator->name : '-',
                    'processed_by' => $ticket->processor ? $ticket->processor->name : '-',
                    'processed_at' => $ticket->processed_at ? Carbon::parse($ticket->processed_at)->format('d M Y H:i') : '-',
                    'grd_number' => $ticket->grd_number ?? '-',
                    'pqc_number' => $ticket->pqc_number ?? '-'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'total' => $data->count(),
                'start_date' => $startDate->format('d M Y'),
                'end_date' => $endDate->format('d M Y')
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get supplier arrival data', [
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export supplier arrival data to Excel
     */
    public function exportSupplierArrivalData(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();

            // Get completed supplier tickets within date range
            $supplierTickets = SupplierTicket::where('status', 'completed')
                ->whereBetween('delivery_date', [$startDate, $endDate])
                ->with(['creator', 'processor'])
                ->orderBy('delivery_date', 'desc')
                ->get();

            // Create CSV content
            $csvContent = "No,Ticket Number,Supplier,PO Number,Tanggal Kedatangan,Jam,No. Kendaraan,No. Surat Jalan,Status,GRD Number,PQC Number,Dibuat Oleh,Diproses Oleh,Tanggal Diproses\n";

            foreach ($supplierTickets as $index => $ticket) {
                $csvContent .= sprintf(
                    "%d,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                    $index + 1,
                    $ticket->ticket_number,
                    $ticket->supplier_name,
                    $ticket->po_number,
                    $ticket->delivery_date ? Carbon::parse($ticket->delivery_date)->format('d M Y') : '-',
                    $ticket->delivery_date ? Carbon::parse($ticket->delivery_date)->format('H:i') : '-',
                    $ticket->vehicle_number ?? '-',
                    $ticket->supplier_delivery_doc ?? '-',
                    $ticket->status,
                    $ticket->grd_number ?? '-',
                    $ticket->pqc_number ?? '-',
                    $ticket->creator ? $ticket->creator->name : '-',
                    $ticket->processor ? $ticket->processor->name : '-',
                    $ticket->processed_at ? Carbon::parse($ticket->processed_at)->format('d M Y H:i') : '-'
                );
            }

            $filename = 'supplier_arrival_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            Log::error('Failed to export supplier arrival data', [
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }
}
