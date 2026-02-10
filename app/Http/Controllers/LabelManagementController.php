<?php

namespace App\Http\Controllers;

use App\Models\LabelCustomer;
use App\Models\LabelTemplate;
use App\Models\LabelGeneration;
use App\Models\MasterCustomer;
use App\Models\WorkOrderH;
use App\Models\MasterMaterial;
use App\Models\MasterCodeOperator;
use App\Models\MasterMachine;
use App\Models\MasterItemUnilever;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Barryvdh\DomPDF\Facade\Pdf;

class LabelManagementController extends Controller
{
    /**
     * Get dummy template data (hardcoded)
     * Based on templates from D:\MASTER LABEL folder
     */
    private function getDummyTemplates()
    {
        return collect([
            [
                'id' => 1,
                'template_name' => 'Template Label Besar CARTON COKLAT',
                'template_type' => 'besar',
                'brand_name' => null,
                'product_name' => 'COKLAT',
                'file_name' => 'Template Label Besar CARTON COKLAT.xlsx',
                'file_path' => 'D:/MASTER LABEL/Template Label Besar CARTON COKLAT.xlsx',
                'description' => 'Template label besar untuk carton produk coklat',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'template_name' => 'Template Label Besar CARTON PISANG',
                'template_type' => 'besar',
                'brand_name' => null,
                'product_name' => 'PISANG',
                'file_name' => 'Template Label Besar CARTON PISANG.xlsx',
                'file_path' => 'D:/MASTER LABEL/Template Label Besar CARTON PISANG.xlsx',
                'description' => 'Template label besar untuk carton produk pisang',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'template_name' => 'Template Label Besar-TSPM',
                'template_type' => 'besar',
                'brand_name' => 'TSPM',
                'product_name' => null,
                'file_name' => 'Template Label Besar-TSPM.xls',
                'file_path' => 'D:/MASTER LABEL/Template Label Besar-TSPM.xls',
                'description' => 'Template label besar untuk brand TSPM',
                'is_active' => true,
            ],
            [
                'id' => 4,
                'template_name' => 'Template Label Besar-Unilever',
                'template_type' => 'besar',
                'brand_name' => 'Unilever',
                'product_name' => null,
                'file_name' => 'Template Label Besar-Unilever.xls',
                'file_path' => 'D:/MASTER LABEL/Template Label Besar-Unilever.xls',
                'description' => 'Template label besar untuk brand Unilever',
                'is_active' => true,
            ],
            [
                'id' => 5,
                'template_name' => 'Template Label Kecil - NABATI',
                'template_type' => 'kecil',
                'brand_name' => 'NABATI',
                'product_name' => null,
                'file_name' => 'Template Label Kecil - NABATI.xls',
                'file_path' => 'D:/MASTER LABEL/Template Label Kecil - NABATI.xls',
                'description' => 'Template label kecil untuk brand NABATI',
                'is_active' => true,
            ],
            [
                'id' => 6,
                'template_name' => 'Template Label Kecil - VIDORAN',
                'template_type' => 'kecil',
                'brand_name' => 'VIDORAN',
                'product_name' => null,
                'file_name' => 'Template Label Kecil - VIDORAN.xls',
                'file_path' => 'D:/MASTER LABEL/Template Label Kecil - VIDORAN.xls',
                'description' => 'Template label kecil untuk brand VIDORAN',
                'is_active' => true,
            ],
            [
                'id' => 7,
                'template_name' => 'Template Label Kecil - FUKUMI',
                'template_type' => 'kecil',
                'brand_name' => 'FUKUMI',
                'product_name' => null,
                'file_name' => 'Template Label Kecil - FUKUMI.xls',
                'file_path' => 'D:/MASTER LABEL/Template Label Kecil - FUKUMI.xls',
                'description' => 'Template label kecil untuk brand FUKUMI',
                'is_active' => true,
            ],
            [
                'id' => 8,
                'template_name' => 'Template LABEL kecil-JUARA',
                'template_type' => 'kecil',
                'brand_name' => 'JUARA',
                'product_name' => null,
                'file_name' => 'Template LABEL kecil-JUARA.xls',
                'file_path' => 'D:/MASTER LABEL/Template LABEL kecil-JUARA.xls',
                'description' => 'Template label kecil untuk brand JUARA',
                'is_active' => true,
            ],
            [
                'id' => 9,
                'template_name' => 'TEMPLATE LABEL-MORINAGA-CHIL SCHOLL',
                'template_type' => 'besar',
                'brand_name' => 'MORINAGA',
                'product_name' => 'CHIL SCHOLL',
                'file_name' => 'TEMPLATE LABEL-MORINAGA-CHIL SCHOLL.xlsx',
                'file_path' => 'D:/MASTER LABEL/TEMPLATE LABEL-MORINAGA-CHIL SCHOLL.xlsx',
                'description' => 'Template label untuk brand Morinaga - Chil Scholl',
                'is_active' => true,
            ],
            [
                'id' => 10,
                'template_name' => 'Template Label Kecil',
                'template_type' => 'kecil',
                'brand_name' => null,
                'product_name' => null,
                'file_name' => 'Template Label Kecil.xls',
                'file_path' => 'D:/MASTER LABEL/Template Label Kecil.xls',
                'description' => 'Template label kecil umum',
                'is_active' => true,
            ],
        ]);
    }

    /**
     * Display the Label Management dashboard
     */
    public function index(Request $request)
    {
        // Get all active customers with their templates
        $query = LabelCustomer::with(['templates' => function ($q) {
            $q->where('is_active', true);
        }])->where('is_active', true);

        // Filter by customer name
        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->search . '%')
                    ->orWhere('brand_name', 'like', '%' . $request->search . '%')
                    ->orWhere('customer_code', 'like', '%' . $request->search . '%');
            });
        }

        // Get customers
        $customers = $query->orderBy('customer_name')->get();

        // Count statistics
        $totalCustomers = LabelCustomer::where('is_active', true)->count();
        $totalTemplates = LabelTemplate::where('is_active', true)->count();
        $countBesar = LabelTemplate::where('is_active', true)->where('template_type', 'besar')->count();
        $countKecil = LabelTemplate::where('is_active', true)->where('template_type', 'kecil')->count();

        return view('main.label-management.index', [
            'customers' => $customers,
            'totalCustomers' => $totalCustomers,
            'totalTemplates' => $totalTemplates,
            'countBesar' => $countBesar,
            'countKecil' => $countKecil,
            'search' => $request->search ?? '',
        ]);
    }

    /**
     * Search customer from mastercustomer
     */
    public function searchMasterCustomer(Request $request)
    {
        $search = $request->input('search', '');

        if (strlen($search) < 2) {
            return response()->json(['customers' => []]);
        }

        $customers = MasterCustomer::active()
            ->search($search)
            ->limit(20)
            ->get(['Code', 'Name', 'Address', 'City', 'Phone', 'Email', 'Contact']);

        return response()->json(['customers' => $customers]);
    }

    /**
     * Get customer detail by code
     */
    public function getMasterCustomer($code)
    {
        $customer = MasterCustomer::where('Code', $code)->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return response()->json([
            'code' => $customer->Code,
            'name' => $customer->Name,
            'address' => $customer->Address,
            'city' => $customer->City,
            'phone' => $customer->Phone,
            'email' => $customer->Email,
            'contact' => $customer->Contact,
        ]);
    }

    /**
     * Search work order by WOT (DocNo)
     */
    public function searchWorkOrder(Request $request)
    {
        $search = $request->input('search', '');

        if (strlen($search) < 2) {
            return response()->json(['work_orders' => []]);
        }

        $workOrders = WorkOrderH::search($search)
            ->limit(20)
            ->get(['DocNo', 'SODocNo', 'MaterialCode', 'DocDate', 'BatchNo', 'Qty']);

        return response()->json(['work_orders' => $workOrders]);
    }

    /**
     * Get work order detail by WOT (DocNo)
     */
    public function getWorkOrder(Request $request, $wot)
    {
        $workOrder = WorkOrderH::with('material')->where('DocNo', $wot)->first();

        if (!$workOrder) {
            return response()->json(['error' => 'Work Order not found'], 404);
        }

        // Get material name from mastermaterial
        $materialName = '';
        if ($workOrder->MaterialCode) {
            $material = MasterMaterial::getByCode($workOrder->MaterialCode);
            if ($material) {
                $materialName = $material->Name;
            }
        }

        // Get PC and MC from tb_master_item_unilever (PostgreSQL) based on MaterialCode (KodeDesign)
        // Only for Unilever customers (NOT for TSPM)
        $pcNo = '';
        $mcNo = '';
        $qty = '';

        // Check if this is for Unilever customer (from request template_id)
        $isUnilever = false;
        $isTSPM = false;
        if ($request->has('template_id')) {
            $template = LabelTemplate::with('customer')->find($request->template_id);
            if ($template && $template->customer) {
                $isUnilever = $this->isUnileverCustomer($template->customer);

                // Check if TSPM
                $customer = $template->customer;
                $customerName = $customer->customer_name ?? '';
                $customerCode = $customer->customer_code ?? '';
                $brandName = $customer->brand_name ?? '';
                $templateName = $template->template_name ?? '';

                $isTSPM = stripos($customerName, 'TSPM') !== false ||
                    stripos($customerCode, 'TSPM') !== false ||
                    stripos($brandName, 'TSPM') !== false ||
                    stripos($templateName, 'TSPM') !== false ||
                    stripos($templateName, 'tspm') !== false;
            }
        }

        // Only get PC/MC for Unilever, NOT for TSPM
        if ($isUnilever && !$isTSPM && $workOrder->MaterialCode) {
            $masterItem = MasterItemUnilever::where('KodeDesign', $workOrder->MaterialCode)->first();
            if ($masterItem) {
                $pcNo = $masterItem->PC ?? '';
                $mcNo = $masterItem->MC ?? '';
                $qty = $masterItem->QTY ?? '';
            }
        }

        // Format date untuk display
        $docDate = $workOrder->DocDate ? $workOrder->DocDate->format('d/m/Y') : '';
        $expiryDate = $workOrder->ExpiryDate ? $workOrder->ExpiryDate->format('d/m/Y') : '';

        // Base response data - common fields for all customers
        $responseData = [
            'WOT' => $workOrder->DocNo,
            'no_wo' => $workOrder->DocNo, // For TSPM (lowercase)
            'NO_WO' => $workOrder->DocNo, // For TSPM (uppercase - backward compatibility)
            'SODocNo' => $workOrder->SODocNo ?? '',
            'no_po' => $workOrder->SODocNo ?? '', // For TSPM (lowercase)
            'NO_PO' => $workOrder->SODocNo ?? '', // For TSPM (uppercase - backward compatibility)
            'MaterialCode' => $workOrder->MaterialCode ?? '',
            'kode_item' => $workOrder->MaterialCode ?? '', // For TSPM (lowercase)
            'KODE_DESIGN' => $workOrder->MaterialCode ?? '', // For TSPM (uppercase - backward compatibility)
            'ITEM' => $materialName, // Nama item dari mastermaterial
            'nama_produk' => $materialName, // For TSPM (lowercase)
            'NAMA_PRODUK' => $materialName, // For TSPM (uppercase - backward compatibility)
            'DocDate' => $docDate,
            'tgl_produksi' => $docDate, // For TSPM (lowercase)
            'TGL_PRODUKSI' => $docDate, // Mapping ke TGL_PRODUKSI
            'BatchNo' => $workOrder->BatchNo ?? '',
            'BATCH_NO' => $workOrder->BatchNo ?? '', // Mapping ke BATCH_NO
            'BatchInfo' => $workOrder->BatchInfo ?? '',
            'ExpiryDate' => $expiryDate,
            'tgl_expired' => $expiryDate, // For TSPM (lowercase)
            'TGL_EXPIRED' => $expiryDate, // For TSPM (uppercase - backward compatibility)
            'Qty' => $workOrder->Qty ?? 0,
            'isi' => $workOrder->Qty ?? 0, // For TSPM (lowercase)
            'ISI' => $workOrder->Qty ?? 0, // Mapping Qty ke ISI
            'Unit' => $workOrder->Unit ?? '',
            'Template' => $workOrder->Template ?? '',
            'Formula' => $workOrder->Formula ?? '',
            'Information' => $workOrder->Information ?? '',
            'Status' => $workOrder->Status ?? '',
        ];

        // Add TSPM specific fields (lowercase)
        if ($isTSPM) {
            $responseData['tgl_kirim'] = $expiryDate; // For TSPM
            $responseData['TGL_KIRIM'] = $expiryDate; // Backward compatibility
            $responseData['no_box'] = $workOrder->BatchInfo ?? ''; // For TSPM
            $responseData['NO_BOX'] = $workOrder->BatchInfo ?? ''; // Backward compatibility
        }

        // Only add PC_NO, MC_NO, Qty_PC for Unilever (NOT for TSPM)
        if ($isUnilever && !$isTSPM) {
            $responseData['PC_NO'] = $pcNo;
            $responseData['MC_NO'] = $mcNo;
            $responseData['Qty_PC'] = $qty;
        }

        return response()->json($responseData);
    }

    /**
     * Show the form for creating a new customer
     */
    public function createCustomer()
    {
        return view('main.label-management.customer-form', [
            'customer' => null,
            'mode' => 'create'
        ]);
    }

    /**
     * Store a newly created customer
     */
    public function storeCustomer(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'customer_code' => 'required|string|max:50|unique:tb_label_customers,customer_code',
                'customer_name' => 'required|string|max:255',
                'brand_name' => 'nullable|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'description' => 'nullable|string',
            ]);

            $customer = LabelCustomer::create([
                'customer_code' => $request->customer_code,
                'customer_name' => $request->customer_name,
                'brand_name' => $request->brand_name,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? true : false,
                'created_by' => auth()->id(),
            ]);

            // Return JSON for AJAX request, redirect for normal request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer berhasil ditambahkan.',
                    'redirect' => route('label-management.index')
                ]);
            }

            return redirect()->route('label-management.index')
                ->with('success', 'Customer berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the customer workspace (detail + templates)
     */
    public function showCustomer($id)
    {
        $customer = LabelCustomer::with([
            'templates' => function ($q) {
                $q->where('is_active', true)->orderBy('template_type');
            },
            'generations' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(20);
            }
        ])->findOrFail($id);

        // Ensure customer has 2 templates (Besar and Kecil)
        $templateTypes = ['besar', 'kecil'];
        foreach ($templateTypes as $type) {
            $template = $customer->templates->firstWhere('template_type', $type);
            if (!$template) {
                // Auto-create template if doesn't exist
                LabelTemplate::create([
                    'customer_id' => $customer->id,
                    'template_name' => ucfirst($type),
                    'template_type' => $type,
                    'is_active' => true,
                    'field_mapping' => [],
                    'created_by' => auth()->id(),
                ]);
            }
        }

        // Reload customer with templates
        $customer->refresh();
        $customer->load('templates');

        return view('main.label-management.customer-workspace', [
            'customer' => $customer
        ]);
    }

    /**
     * Show the form for editing the specified customer
     */
    public function editCustomer($id)
    {
        $customer = LabelCustomer::findOrFail($id);

        return view('main.label-management.customer-form', [
            'customer' => $customer,
            'mode' => 'edit'
        ]);
    }

    /**
     * Update the specified customer
     */
    public function updateCustomer(Request $request, $id)
    {
        try {
            $customer = LabelCustomer::findOrFail($id);

            $request->validate([
                'customer_code' => 'required|string|max:50|unique:tb_label_customers,customer_code,' . $id,
                'customer_name' => 'required|string|max:255',
                'brand_name' => 'nullable|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'description' => 'nullable|string',
            ]);

            $customer->update([
                'customer_code' => $request->customer_code,
                'customer_name' => $request->customer_name,
                'brand_name' => $request->brand_name,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? true : false,
                'updated_by' => auth()->id(),
            ]);

            // Return JSON for AJAX request, redirect for normal request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer berhasil diupdate.',
                    'redirect' => route('label-management.index')
                ]);
            }

            return redirect()->route('label-management.index')
                ->with('success', 'Customer berhasil diupdate.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified customer
     */
    public function destroyCustomer($id)
    {
        $customer = LabelCustomer::findOrFail($id);
        $customer->delete();

        return redirect()->route('label-management.index')
            ->with('success', 'Customer berhasil dihapus.');
    }

    /**
     * Show the form for creating a new template
     */
    public function createTemplate($customerId)
    {
        $customer = LabelCustomer::findOrFail($customerId);

        // Get default field mapping based on customer
        $defaultFields = $this->getDefaultFieldMappingByCustomer($customer);

        return view('main.label-management.template-form', [
            'customer' => $customer,
            'template' => null,
            'mode' => 'create',
            'defaultFields' => $defaultFields
        ]);
    }

    /**
     * Store a newly created template
     */
    public function storeTemplate(Request $request, $customerId)
    {
        try {
            $customer = LabelCustomer::findOrFail($customerId);

            $request->validate([
                'template_name' => 'required|string|max:255',
                'template_type' => 'required|string|max:255',
                'brand_name' => 'nullable|string|max:255',
                'product_name' => 'nullable|string|max:255',
                'template_file' => 'nullable|file|mimes:xlsx,xls|max:10240',
                'file_path' => 'nullable|string|max:500',
                'file_name' => 'nullable|string|max:255',
                'file_size' => 'nullable|integer',
                'description' => 'nullable|string',
                'field_mapping' => 'nullable|array',
                'field_mapping.*.excel_cell' => 'required_with:field_mapping.*.field_name|string',
                'field_mapping.*.field_name' => 'required_with:field_mapping.*.excel_cell|string',
                'field_mapping.*.label' => 'nullable|string',
                'field_mapping.*.required' => 'nullable|boolean',
            ]);

            // Process field mapping
            $fieldMapping = [];
            if ($request->has('field_mapping') && is_array($request->field_mapping)) {
                foreach ($request->field_mapping as $field) {
                    if (!empty($field['excel_cell']) && !empty($field['field_name'])) {
                        $fieldMapping[] = [
                            'excel_cell' => $field['excel_cell'],
                            'field_name' => $field['field_name'],
                            'label' => $field['label'] ?? $field['field_name'],
                            'required' => isset($field['required']) && $field['required'] == '1',
                        ];
                    }
                }
            }

            // Handle file upload
            $defaultDir = public_path('label');
            if (!is_dir($defaultDir)) {
                mkdir($defaultDir, 0755, true);
            }

            $filePath = null;
            $fileName = null;
            $fileSize = null;

            if ($request->hasFile('template_file')) {
                // Upload new file
                $file = $request->file('template_file');
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();

                // Generate unique filename if file exists
                $originalName = pathinfo($fileName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $counter = 1;
                $uniqueFileName = $fileName;

                while (file_exists($defaultDir . '/' . $uniqueFileName)) {
                    $uniqueFileName = $originalName . '_' . $counter . '.' . $extension;
                    $counter++;
                }

                // Move file to public/label
                $file->move($defaultDir, $uniqueFileName);
                $filePath = $defaultDir . '/' . $uniqueFileName;
            } else {
                // Use existing path or set default
                $filePath = $request->file_path;
                if (empty($filePath) || $filePath == $defaultDir . '/' || $filePath == str_replace('\\', '/', $defaultDir) . '/') {
                    $fileName = $request->file_name ?? str_replace(' ', '_', $request->template_name) . '.xlsx';
                    $filePath = $defaultDir . '/' . $fileName;
                } else {
                    $fileName = $request->file_name ?? basename($filePath);
                }
                $fileSize = $request->file_size;
            }

            // Handle custom template type
            $templateType = $request->template_type;
            if ($request->has('custom_template_type') && !empty($request->custom_template_type)) {
                $templateType = $request->custom_template_type;
            }

            // Normalize path (convert backslash to forward slash for Windows)
            $filePath = str_replace('\\', '/', $filePath);

            $template = LabelTemplate::create([
                'customer_id' => $customerId,
                'template_name' => $request->template_name,
                'template_type' => $templateType,
                'brand_name' => $request->brand_name,
                'product_name' => $request->product_name,
                'file_path' => $filePath,
                'file_name' => $fileName ?? basename($filePath),
                'file_size' => $fileSize,
                'description' => $request->description,
                'field_mapping' => !empty($fieldMapping) ? $fieldMapping : null,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);

            // Return JSON for AJAX request, redirect for normal request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template berhasil ditambahkan.',
                    'redirect' => route('label-management.customer.show', $customerId)
                ]);
            }

            return redirect()->route('label-management.customer.show', $customerId)
                ->with('success', 'Template berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the template with list of generated labels
     */
    public function showTemplate($id)
    {
        try {
            // Cari template dengan error handling
            $template = LabelTemplate::with([
                'customer',
                'generations' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ])->find($id);

            if (!$template) {
                abort(404, 'Template tidak ditemukan. ID template: ' . $id);
            }

            // Get customer dan validasi
            $customer = $template->customer;
            if (!$customer) {
                abort(404, 'Customer tidak ditemukan untuk template ini. ID template: ' . $id);
            }

            // FILTER DI AWAL: Cek tipe customer (Unilever atau TSPM)
            // Informasi ini bisa digunakan untuk menentukan view atau redirect yang sesuai jika diperlukan
            $isTSPM = $this->isTSPMCustomer($customer, $template);
            $isUnilever = $this->isUnileverCustomer($customer);

            return view('main.label-management.template-view', [
                'template' => $template,
                'customer' => $customer,
                'isTSPM' => $isTSPM,
                'isUnilever' => $isUnilever
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Template atau customer tidak ditemukan');
        } catch (\Exception $e) {
            abort(500, 'Error loading template: ' . $e->getMessage());
        }
    }

    /**
     * Preview template PDF as HTML for editing
     * Can be called with template ID or generation ID
     */
    public function previewTemplate($id = null, $generationId = null)
    {
        try {
            $generation = null;
            $template = null;
            $fieldMapping = [];
            $fieldValues = [];

            $route = request()->route();
            $routeParams = $route->parameters();

            if (isset($routeParams['generationId'])) {
                $generationId = $routeParams['generationId'];

                $generation = LabelGeneration::with('template.customer')->find($generationId);
                if (!$generation) {
                    abort(404, 'Generation tidak ditemukan. ID generation: ' . $generationId);
                }

                $template = $generation->template;
                if (!$template) {
                    abort(404, 'Template tidak ditemukan untuk generation ini. ID generation: ' . $generationId);
                }
                $fieldMapping = $template->field_mapping ?? [];
                $fieldValues = is_array($generation->field_values)
                    ? $generation->field_values
                    : json_decode($generation->field_values, true);
            } else {
                if (!$id) {
                    abort(404, 'Template ID is required');
                }

                $template = LabelTemplate::with('customer')->find($id);
                if (!$template) {
                    abort(404, 'Template tidak ditemukan. ID template: ' . $id);
                }

                $fieldMapping = $template->field_mapping ?? [];
                $fieldValues = [];

                if (!$generationId && request()->has('generationId')) {
                    $generationId = request()->query('generationId');
                }

                if ($generationId) {
                    $generation = LabelGeneration::where('template_id', $id)
                        ->where('id', $generationId)
                        ->first();

                    if (!$generation) {
                        abort(404, 'Generation tidak ditemukan. ID generation: ' . $generationId . ' untuk template ID: ' . $id);
                    }
                } else {
                    $generation = LabelGeneration::where('template_id', $id)
                        ->whereNotNull('field_values')
                        ->whereRaw("field_values::text != '{}'")
                        ->whereRaw("field_values::text != 'null'")
                        ->orderBy('created_at', 'desc')
                        ->first();
                }

                if ($generation && $generation->field_values && !empty($generation->field_values)) {
                    $fieldValues = is_array($generation->field_values)
                        ? $generation->field_values
                        : json_decode($generation->field_values, true);
                } else {
                    foreach ($fieldMapping as $field) {
                        $fieldValues[$field['field_name']] = '';
                    }
                }
            }

            // Get customer and validate
            $customer = $template->customer;
            if (!$customer) {
                abort(404, 'Customer tidak ditemukan untuk template ini');
            }

            $customerName = $customer->customer_name ?? '';
            $quantity = $generation ? $generation->quantity : 1;

            // FILTER DI AWAL: Cek tipe customer (Unilever atau TSPM)
            $isTSPM = $this->isTSPMCustomer($customer, $template);
            $isUnilever = $this->isUnileverCustomer($customer);

            // Ambil sample data
            $sampleData = $this->getSampleDataByCustomer($customer, $template);

            // Fill missing fields with sample data for preview based on field_mapping
            foreach ($fieldMapping as $field) {
                $fieldName = $field['field_name'] ?? '';
                if ($fieldName && (!isset($fieldValues[$fieldName]) || empty($fieldValues[$fieldName]))) {
                    // Use sample data if available, otherwise generate generic sample
                    $fieldValues[$fieldName] = $sampleData[$fieldName] ?? 'Sample ' . ($field['label'] ?? $fieldName);
                }
            }

            // Also fill common fields from sample data if not set
            foreach ($sampleData as $key => $value) {
                if (!isset($fieldValues[$key]) || empty($fieldValues[$key])) {
                    $fieldValues[$key] = $value;
                }
            }

            // PRIORITY 1: Filter TSPM Customer - langsung return template TSPM
            if ($isTSPM) {
                // For preview, always show 4 labels (2x2 grid) to demonstrate layout
                $previewQuantity = 4; // Always 4 for preview

                return view('main.label-management.label-pdf-tspm-template', [
                    'template' => $template,
                    'customer' => $customer,
                    'customerName' => $customerName,
                    'fieldValues' => $fieldValues,
                    'fieldMapping' => $fieldMapping,
                    'quantity' => $previewQuantity
                ]);
            }

            // PRIORITY 2: Filter Unilever Customer - langsung return template Unilever
            if ($isUnilever) {
                return view('main.label-management.label-pdf-template', [
                    'template' => $template,
                    'customer' => $customer,
                    'customerName' => $customerName,
                    'item' => $fieldValues['ITEM'] ?? $template->product_name ?? '',
                    'fieldValues' => $fieldValues,
                    'fieldMapping' => $fieldMapping,
                    'isi' => $fieldValues['ISI'] ?? '',
                    'quantity' => $quantity
                ]);
            }

            // PRIORITY 3: Check if file_name contains blade template name
            $fileName = $template->file_name ?? '';
            $bladeTemplateName = null;

            // Check if file_name is a blade template (contains .blade.php or just template name)
            if (!empty($fileName)) {
                // Remove .blade.php extension if exists
                $cleanFileName = str_replace('.blade.php', '', $fileName);
                $cleanFileName = str_replace('.php', '', $cleanFileName);

                // Check if it's a valid blade template path
                if (strpos($cleanFileName, 'label-pdf-') === 0 || strpos($cleanFileName, 'main.label-management.label-pdf-') === 0) {
                    // Extract template name
                    if (strpos($cleanFileName, 'main.label-management.') === 0) {
                        $bladeTemplateName = $cleanFileName;
                    } else {
                        $bladeTemplateName = 'main.label-management.' . $cleanFileName;
                    }

                    // Verify template exists
                    $viewPath = resource_path('views/' . str_replace('.', '/', $bladeTemplateName) . '.blade.php');
                    if (!file_exists($viewPath)) {
                        $bladeTemplateName = null; // Template doesn't exist, ignore
                    }
                }
            }

            // PRIORITY 4: Use blade template from file_name if specified
            if ($bladeTemplateName) {
                return view($bladeTemplateName, [
                    'template' => $template,
                    'customer' => $customer,
                    'customerName' => $customerName,
                    'fieldValues' => $fieldValues,
                    'fieldMapping' => $fieldMapping,
                    'quantity' => $quantity
                ]);
            }

            // Priority 4: Use custom HTML template if available
            if (!empty($template->html_template)) {
                // Return custom template view
                return view('main.label-management.label-pdf-custom-template', [
                    'template' => $template,
                    'customer' => $customer,
                    'customerName' => $customerName,
                    'fieldValues' => $fieldValues,
                    'quantity' => $quantity
                ]);
            }

            // Priority 5: Use default template view
            return view('main.label-management.label-pdf-template', [
                'template' => $template,
                'customer' => $customer,
                'customerName' => $customerName,
                'item' => $fieldValues['ITEM'] ?? '',
                'fieldValues' => $fieldValues,
                'fieldMapping' => $fieldMapping,
                'isi' => $fieldValues['ISI'] ?? '',
                'quantity' => $quantity
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Template or generation not found');
        } catch (\Exception $e) {
            abort(500, 'Error loading preview: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified template
     */
    public function editTemplate($id)
    {
        $template = LabelTemplate::with('customer')->findOrFail($id);

        // Get default field mapping based on customer (if template doesn't have field_mapping)
        $defaultFields = $this->getDefaultFieldMappingByCustomer($template->customer, $template);

        return view('main.label-management.template-form', [
            'customer' => $template->customer,
            'template' => $template,
            'mode' => 'edit',
            'defaultFields' => $defaultFields
        ]);
    }

    /**
     * Show visual workspace for template design
     */
    public function showWorkspace($id, Request $request)
    {
        $template = LabelTemplate::with('customer')->findOrFail($id);

        // Get available fields from field_mapping
        $availableFields = [];
        if ($template->field_mapping && is_array($template->field_mapping)) {
            foreach ($template->field_mapping as $field) {
                $availableFields[] = [
                    'name' => $field['field_name'] ?? '',
                    'label' => $field['label'] ?? $field['field_name'] ?? '',
                ];
            }
        }

        // Choose workspace type based on request parameter
        $workspaceType = $request->get('type', 'simple'); // 'word', 'grapesjs', or 'simple'

        if ($workspaceType === 'grapesjs') {
            return view('main.label-management.template-workspace-grapesjs', [
                'template' => $template,
                'customer' => $template->customer,
                'availableFields' => $availableFields,
            ]);
        }

        if ($workspaceType === 'simple') {
            return view('main.label-management.template-workspace-simple', [
                'template' => $template,
                'customer' => $template->customer,
                'availableFields' => $availableFields,
            ]);
        }

        // Use Word-like editor as default
        return view('main.label-management.template-workspace-word', [
            'template' => $template,
            'customer' => $template->customer,
            'availableFields' => $availableFields,
            'availableFields' => $availableFields,
        ]);
    }

    /**
     * Save workspace HTML template
     */
    public function saveWorkspace(Request $request, $id)
    {
        try {
            $template = LabelTemplate::findOrFail($id);

            $request->validate([
                'html_template' => 'nullable|string',
                'css_styles' => 'nullable|string',
            ]);

            $template->update([
                'html_template' => $request->html_template,
                'css_styles' => $request->css_styles,
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template workspace berhasil disimpan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick create template from workspace (simplified)
     */
    public function quickCreateTemplate(Request $request, $customerId)
    {
        try {
            $customer = LabelCustomer::findOrFail($customerId);

            $request->validate([
                'template_name' => 'required|string|max:255',
                'template_type' => 'required|string|max:255',
            ]);

            // Check if template with same type already exists
            $existingTemplate = LabelTemplate::where('customer_id', $customerId)
                ->where('template_type', $request->template_type)
                ->where('is_active', true)
                ->first();

            if ($existingTemplate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template dengan tipe ' . $request->template_type . ' sudah ada. Silakan edit template yang sudah ada.',
                    'template_id' => $existingTemplate->id
                ], 422);
            }

            // Get default field mapping based on customer
            $defaultFields = $this->getDefaultFieldMappingByCustomer($customer);

            $template = LabelTemplate::create([
                'customer_id' => $customerId,
                'template_name' => $request->template_name,
                'template_type' => $request->template_type,
                'brand_name' => $request->brand_name ?? $customer->brand_name,
                'product_name' => $request->product_name,
                'field_mapping' => $defaultFields,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil dibuat. Anda bisa langsung mulai mendesain di workspace.',
                'template_id' => $template->id,
                'redirect' => route('label-management.template.workspace', $template->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update field mapping from workspace
     */
    public function updateFieldMapping(Request $request, $id)
    {
        try {
            $template = LabelTemplate::findOrFail($id);

            $request->validate([
                'field_mapping' => 'required|array',
                'field_mapping.*.excel_cell' => 'required_with:field_mapping.*.field_name|string',
                'field_mapping.*.field_name' => 'required_with:field_mapping.*.excel_cell|string',
                'field_mapping.*.label' => 'nullable|string',
                'field_mapping.*.required' => 'nullable|boolean',
            ]);

            // Process field mapping
            $fieldMapping = [];
            foreach ($request->field_mapping as $field) {
                if (!empty($field['excel_cell']) && !empty($field['field_name'])) {
                    $fieldMapping[] = [
                        'excel_cell' => $field['excel_cell'],
                        'field_name' => $field['field_name'],
                        'label' => $field['label'] ?? $field['field_name'],
                        'required' => isset($field['required']) && $field['required'] == '1',
                    ];
                }
            }

            $template->update([
                'field_mapping' => $fieldMapping,
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Field mapping berhasil diupdate',
                'field_mapping' => $fieldMapping
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert Excel to HTML template
     */
    public function convertExcelToHtml(Request $request, $id)
    {
        try {
            $template = LabelTemplate::findOrFail($id);

            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            ]);

            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();

            // Get highest row and column
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            // Get merged cells first
            $mergedCells = $sheet->getMergeCells();
            $mergedRanges = [];
            foreach ($mergedCells as $mergedRange) {
                $range = Coordinate::rangeBoundaries($mergedRange);
                $mergedRanges[$mergedRange] = [
                    'start' => ['col' => $range[0][0], 'row' => $range[0][1]],
                    'end' => ['col' => $range[1][0], 'row' => $range[1][1]],
                ];
            }

            // Start building HTML
            $html = '<div style="position: relative; width: 100%; min-height: 500px; font-family: Arial, sans-serif;">';

            // Cell size mapping (approximate)
            $cellWidth = 80; // pixels per column
            $cellHeight = 20; // pixels per row

            // Track processed merged cells to avoid duplicates
            $processedMerged = [];

            // Process each cell
            for ($row = 1; $row <= $highestRow; $row++) {
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cellAddress = Coordinate::stringFromColumnIndex($col) . $row;

                    // Check if this cell is part of a merged range (and not the top-left cell)
                    $isInMerged = false;
                    $isTopLeftOfMerged = false;
                    foreach ($mergedRanges as $mergedRange => $rangeData) {
                        if (
                            $col >= $rangeData['start']['col'] && $col <= $rangeData['end']['col'] &&
                            $row >= $rangeData['start']['row'] && $row <= $rangeData['end']['row']
                        ) {
                            $isInMerged = true;
                            if ($col == $rangeData['start']['col'] && $row == $rangeData['start']['row']) {
                                $isTopLeftOfMerged = true;
                            } else {
                                // Skip this cell, it's part of merged but not the main cell
                                continue 2;
                            }
                            break;
                        }
                    }

                    $cell = $sheet->getCell($cellAddress);
                    $cellValue = $cell->getValue();

                    // Skip empty cells that are not merged and have no formatting
                    if (empty($cellValue) && $cellValue !== '0' && !$isInMerged) {
                        $style = $sheet->getStyle($cellAddress);
                        $borders = $style->getBorders();
                        $hasBorder = $borders->getTop()->getBorderStyle() !== \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE ||
                            $borders->getBottom()->getBorderStyle() !== \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE ||
                            $borders->getLeft()->getBorderStyle() !== \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE ||
                            $borders->getRight()->getBorderStyle() !== \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE;
                        if (!$hasBorder) {
                            continue;
                        }
                    }

                    // Get cell style
                    $style = $sheet->getStyle($cellAddress);
                    $font = $style->getFont();
                    $fill = $style->getFill();
                    $alignment = $style->getAlignment();

                    // Calculate position
                    $x = ($col - 1) * $cellWidth;
                    $y = ($row - 1) * $cellHeight;

                    // Get merged cell dimensions
                    $colspan = 1;
                    $rowspan = 1;
                    if ($isTopLeftOfMerged) {
                        foreach ($mergedRanges as $mergedRange => $rangeData) {
                            if ($col == $rangeData['start']['col'] && $row == $rangeData['start']['row']) {
                                $colspan = $rangeData['end']['col'] - $rangeData['start']['col'] + 1;
                                $rowspan = $rangeData['end']['row'] - $rangeData['start']['row'] + 1;
                                break;
                            }
                        }
                    }

                    // Build style string
                    $styleString = sprintf(
                        'position: absolute; left: %dpx; top: %dpx; ',
                        $x,
                        $y
                    );

                    // Font styles
                    $fontSize = $font->getSize() ?: 10;
                    $fontWeight = $font->getBold() ? 'bold' : 'normal';
                    $fontStyle = $font->getItalic() ? 'italic' : 'normal';
                    $fontColor = $font->getColor()->getRGB();
                    $fontName = $font->getName() ?: 'Arial';

                    $styleString .= sprintf(
                        'font-size: %dpt; font-weight: %s; font-style: %s; color: #%s; font-family: %s; ',
                        $fontSize,
                        $fontWeight,
                        $fontStyle,
                        $fontColor,
                        $fontName
                    );

                    // Background color
                    if ($fill->getFillType() !== \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE) {
                        $bgColor = $fill->getStartColor()->getRGB();
                        $styleString .= sprintf('background-color: #%s; ', $bgColor);
                    }

                    // Alignment
                    $horizontalAlign = $alignment->getHorizontal() ?: 'left';
                    $verticalAlign = $alignment->getVertical() ?: 'bottom';
                    $styleString .= sprintf('text-align: %s; vertical-align: %s; ', $horizontalAlign, $verticalAlign);

                    // Borders
                    $borders = $style->getBorders();
                    $borderStyle = '';
                    if ($borders->getTop()->getBorderStyle() !== \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE) {
                        $borderColor = $borders->getTop()->getColor()->getRGB();
                        $borderStyle .= sprintf('border-top: 1px solid #%s; ', $borderColor);
                    }
                    if ($borders->getBottom()->getBorderStyle() !== \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE) {
                        $borderColor = $borders->getBottom()->getColor()->getRGB();
                        $borderStyle .= sprintf('border-bottom: 1px solid #%s; ', $borderColor);
                    }
                    if ($borders->getLeft()->getBorderStyle() !== \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE) {
                        $borderColor = $borders->getLeft()->getColor()->getRGB();
                        $borderStyle .= sprintf('border-left: 1px solid #%s; ', $borderColor);
                    }
                    if ($borders->getRight()->getBorderStyle() !== \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE) {
                        $borderColor = $borders->getRight()->getColor()->getRGB();
                        $borderStyle .= sprintf('border-right: 1px solid #%s; ', $borderColor);
                    }
                    $styleString .= $borderStyle;

                    // Width and height for merged cells
                    if ($isTopLeftOfMerged && ($colspan > 1 || $rowspan > 1)) {
                        $styleString .= sprintf('width: %dpx; height: %dpx; ', $colspan * $cellWidth, $rowspan * $cellHeight);
                    } else {
                        $styleString .= sprintf('min-width: %dpx; min-height: %dpx; ', $cellWidth, $cellHeight);
                    }

                    // Padding
                    $styleString .= 'padding: 2px 4px; ';

                    // Process cell value - check if it contains field placeholder
                    $displayValue = $cellValue;
                    $isField = false;

                    // Check if value looks like a field placeholder (e.g., {{FIELD_NAME}} or FIELD_NAME)
                    if (preg_match('/\{\{?(\w+)\}?\}/', $cellValue, $matches)) {
                        $fieldName = $matches[1];
                        $displayValue = '{{' . $fieldName . '}}';
                        $isField = true;
                        $styleString .= 'background-color: #fff3cd; border: 1px dashed #ffc107; ';
                    }

                    // Determine HTML tag
                    $tag = ($isTopLeftOfMerged && ($colspan > 1 || $rowspan > 1)) ? 'div' : 'span';

                    $html .= sprintf(
                        '<%s style="%s">%s</%s>',
                        $tag,
                        trim($styleString),
                        htmlspecialchars($displayValue),
                        $tag
                    );
                }
            }

            $html .= '</div>';

            // Generate CSS
            $css = '@page { margin: 0; size: A4; } body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }';

            // Save to template
            $saveData = [
                'html' => $html,
                'css' => $css,
                'pageOrientation' => 'portrait',
                'source' => 'excel_import',
                'imported_at' => now()->toDateTimeString(),
            ];

            $template->update([
                'html_template' => json_encode($saveData),
                'css_styles' => $css,
                'updated_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Excel berhasil dikonversi ke HTML template',
                'html' => $html,
                'preview_url' => route('label-management.template.preview', $template->id),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified template
     */
    public function updateTemplate(Request $request, $id)
    {
        try {
            $template = LabelTemplate::findOrFail($id);

            $request->validate([
                'template_name' => 'required|string|max:255',
                'template_type' => 'required|string|max:255',
                'brand_name' => 'nullable|string|max:255',
                'product_name' => 'nullable|string|max:255',
                'template_file' => 'nullable|file|mimes:xlsx,xls|max:10240',
                'file_path' => 'nullable|string|max:500',
                'file_name' => 'nullable|string|max:255',
                'file_size' => 'nullable|integer',
                'description' => 'nullable|string',
                'field_mapping' => 'nullable|array',
                'field_mapping.*.excel_cell' => 'required_with:field_mapping.*.field_name|string',
                'field_mapping.*.field_name' => 'required_with:field_mapping.*.excel_cell|string',
                'field_mapping.*.label' => 'nullable|string',
                'field_mapping.*.required' => 'nullable|boolean',
            ]);

            // Process field mapping
            $fieldMapping = [];
            if ($request->has('field_mapping') && is_array($request->field_mapping)) {
                foreach ($request->field_mapping as $field) {
                    if (!empty($field['excel_cell']) && !empty($field['field_name'])) {
                        $fieldMapping[] = [
                            'excel_cell' => $field['excel_cell'],
                            'field_name' => $field['field_name'],
                            'label' => $field['label'] ?? $field['field_name'],
                            'required' => isset($field['required']) && $field['required'] == '1',
                        ];
                    }
                }
            }

            // Handle file upload
            $defaultDir = public_path('label');
            if (!is_dir($defaultDir)) {
                mkdir($defaultDir, 0755, true);
            }

            $filePath = $template->file_path;
            $fileName = $template->file_name;
            $fileSize = $template->file_size;

            if ($request->hasFile('template_file')) {
                // Delete old file if exists
                if ($template->file_path && file_exists($template->file_path)) {
                    @unlink($template->file_path);
                }

                // Upload new file
                $file = $request->file('template_file');
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();

                // Generate unique filename if file exists
                $originalName = pathinfo($fileName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $counter = 1;
                $uniqueFileName = $fileName;

                while (file_exists($defaultDir . '/' . $uniqueFileName)) {
                    $uniqueFileName = $originalName . '_' . $counter . '.' . $extension;
                    $counter++;
                }

                // Move file to public/label
                $file->move($defaultDir, $uniqueFileName);
                $filePath = $defaultDir . '/' . $uniqueFileName;
            } else {
                // Keep existing file or use provided path
                if ($request->file_path && $request->file_path != $template->file_path) {
                    $filePath = $request->file_path;
                    $fileName = $request->file_name ?? basename($filePath);
                }
            }

            // Handle custom template type
            $templateType = $request->template_type;
            if ($request->has('custom_template_type') && !empty($request->custom_template_type)) {
                $templateType = $request->custom_template_type;
            }

            // Normalize path (convert backslash to forward slash for Windows)
            $filePath = str_replace('\\', '/', $filePath);

            $template->update([
                'template_name' => $request->template_name,
                'template_type' => $templateType,
                'brand_name' => $request->brand_name,
                'product_name' => $request->product_name,
                'file_path' => $filePath,
                'file_name' => $fileName ?? basename($filePath),
                'file_size' => $fileSize,
                'description' => $request->description,
                'field_mapping' => !empty($fieldMapping) ? $fieldMapping : null,
                'updated_by' => auth()->id(),
            ]);

            // Return JSON for AJAX request, redirect for normal request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Template berhasil diupdate.',
                    'redirect' => route('label-management.customer.show', $template->customer_id)
                ]);
            }

            return redirect()->route('label-management.customer.show', $template->customer_id)
                ->with('success', 'Template berhasil diupdate.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified template
     */
    public function destroyTemplate($id)
    {
        $template = LabelTemplate::findOrFail($id);
        $customerId = $template->customer_id;
        $template->delete();

        return redirect()->route('label-management.customer.show', $customerId)
            ->with('success', 'Template berhasil dihapus.');
    }

    /**
     * Show form for generating label
     */
    public function showGenerateForm($id)
    {
        $template = LabelTemplate::with('customer')->findOrFail($id);

        // Check if customer is Unilever
        $isUnilever = $this->isUnileverCustomer($template->customer);

        // Check if customer is TSPM
        $isTSPM = $this->isTSPMCustomer($template->customer, $template);

        // Get machines for dropdown
        $machines = MasterMachine::active()
            ->orderBy('Code')
            ->get(['Code', 'Description']);

        // Get operators for dropdown
        $operators = MasterCodeOperator::orderBy('Nama')
            ->get(['id', 'Mesin', 'Nama', 'Kode']);

        return view('main.label-management.generate-label', [
            'template' => $template,
            'machines' => $machines,
            'operators' => $operators,
            'isUnilever' => $isUnilever,
            'isTSPM' => $isTSPM
        ]);
    }

    /**
     * Check if customer is Unilever
     * Based on customer_code (e.g., JKT.0021)
     */
    private function isUnileverCustomer($customer)
    {
        if (!$customer) {
            return false;
        }

        $customerCode = $customer->customer_code ?? '';

        // Unilever customer codes (add more if needed)
        $unileverCodes = [
            'JKT.0021',
            'JKT0021',
        ];

        // Check if customer_code matches Unilever codes
        foreach ($unileverCodes as $code) {
            if (strcasecmp($customerCode, $code) === 0) {
                return true;
            }
        }

        // Also check if customer_code starts with JKT (for flexibility)
        if (!empty($customerCode) && stripos($customerCode, 'JKT') === 0) {
            return true;
        }

        // Backward compatibility: check name/brand
        $customerName = strtolower($customer->customer_name ?? '');
        $brandName = strtolower($customer->brand_name ?? '');

        return str_contains($customerName, 'unilever')
            || str_contains($brandName, 'unilever');
    }

    /**
     * Check if customer is TSPM
     * Based on customer_code (e.g., PSR.0216)
     */
    private function isTSPMCustomer($customer, $template = null)
    {
        if (!$customer) {
            return false;
        }

        $customerCode = $customer->customer_code ?? '';

        // TSPM customer codes (add more if needed)
        $tspmCodes = [
            'PSR.0216',
            'PSR0216',
        ];

        // Check if customer_code matches TSPM codes
        foreach ($tspmCodes as $code) {
            if (strcasecmp($customerCode, $code) === 0) {
                return true;
            }
        }

        // Also check if customer_code starts with PSR (for flexibility)
        if (!empty($customerCode) && stripos($customerCode, 'PSR') === 0) {
            return true;
        }

        return false;
    }

    /**
     * Get sample data based on customer
     * Each customer can have different sample data for preview
     */
    private function getSampleDataByCustomer($customer, $template = null)
    {
        if (!$customer) {
            // Default Unilever sample data (backward compatibility)
            return [
                'ITEM' => 'Sample Item',
                'PC_NO' => '12345678',
                'MC_NO' => '87654321',
                'WOT' => 'WOT-240306-0001',
                'TGL_PRODUKSI' => date('d/m/Y'),
                'MESIN_SHIFT' => 'B3/2',
                'OPERATOR' => '900047',
                'BATCH_NO' => '1/2/1',
                'NO_BOX' => 'BOX-001',
                'TANGGAL_KIRIM' => date('d/m/Y', strtotime('+1 day')),
                'ISI' => '1200',
            ];
        }

        // Check customer type
        $isTSPM = $this->isTSPMCustomer($customer, $template);
        $isUnilever = $this->isUnileverCustomer($customer);

        // TSPM Sample Data (menggunakan lowercase sesuai format database)
        if ($isTSPM) {
            return [
                'nama_produk' => 'Sample Product Name',
                'kode_item' => 'KD-12345',
                'no_wo' => 'WOT-240306-0001',
                'no_po' => 'PO-2024-001',
                'tgl_produksi' => date('d/m/Y'),
                'shift' => 'B3',
                'tgl_expired' => date('d/m/Y', strtotime('+1 year')),
                'operator' => '900047',
                'mesin' => 'MESIN-001',
                'isi' => '1200',
                'tgl_kirim' => date('d/m/Y', strtotime('+1 day')),
                'no_box' => 'BOX-001',
            ];
        }

        // Unilever Sample Data (default)
        if ($isUnilever) {
            return [
                'ITEM' => 'Sample Item',
                'PC_NO' => '12345678',
                'MC_NO' => '87654321',
                'WOT' => 'WOT-240306-0001',
                'TGL_PRODUKSI' => date('d/m/Y'),
                'MESIN_SHIFT' => 'B3/2',
                'OPERATOR' => '900047',
                'BATCH_NO' => '1/2/1',
                'NO_BOX' => 'BOX-001',
                'TANGGAL_KIRIM' => date('d/m/Y', strtotime('+1 day')),
                'ISI' => '1200',
            ];
        }

        // Default/Generic Sample Data (for other customers)
        return [
            'ITEM' => 'Sample Item',
            'WOT' => 'WOT-240306-0001',
            'TGL_PRODUKSI' => date('d/m/Y'),
            'OPERATOR' => '900047',
            'BATCH_NO' => '1/2/1',
            'NO_BOX' => 'BOX-001',
            'ISI' => '1200',
        ];
    }

    /**
     * Get default field mapping based on customer
     * Each customer can have different field mappings
     */
    private function getDefaultFieldMappingByCustomer($customer, $template = null)
    {
        if (!$customer) {
            // Default Unilever fields (backward compatibility)
            return [
                ['excel_cell' => 'D20', 'field_name' => 'ITEM', 'label' => 'Item', 'required' => true],
                ['excel_cell' => 'D5', 'field_name' => 'PC_NO', 'label' => 'PC NO.', 'required' => true],
                ['excel_cell' => 'D7', 'field_name' => 'MC_NO', 'label' => 'MC NO.', 'required' => true],
                ['excel_cell' => 'D9', 'field_name' => 'WOT', 'label' => 'No.WOT', 'required' => true],
                ['excel_cell' => 'D10', 'field_name' => 'TGL_PRODUKSI', 'label' => 'TGL.PRODUKSI', 'required' => true],
                ['excel_cell' => 'D11', 'field_name' => 'MESIN_SHIFT', 'label' => 'MESIN/SHIFT', 'required' => false],
                ['excel_cell' => 'D12', 'field_name' => 'OPERATOR', 'label' => 'OPERATOR', 'required' => false],
                ['excel_cell' => 'D13', 'field_name' => 'BATCH_NO', 'label' => 'BATCH NO.', 'required' => false],
                ['excel_cell' => 'D14', 'field_name' => 'NO_BOX', 'label' => 'NO.BOX', 'required' => false],
                ['excel_cell' => 'D15', 'field_name' => 'TANGGAL_KIRIM', 'label' => 'TANGGAL KIRIM', 'required' => false],
                ['excel_cell' => 'F5', 'field_name' => 'ISI', 'label' => 'isi (pcs)', 'required' => false],
            ];
        }

        // Check customer type
        $isTSPM = $this->isTSPMCustomer($customer, $template);
        $isUnilever = $this->isUnileverCustomer($customer);

        // TSPM Field Mapping (menggunakan lowercase sesuai format database)
        if ($isTSPM) {
            return [
                ['excel_cell' => '', 'field_name' => 'nama_produk', 'label' => 'NAMA PRODUK', 'required' => true],
                ['excel_cell' => '', 'field_name' => 'kode_item', 'label' => 'KODE ITEM', 'required' => true],
                ['excel_cell' => '', 'field_name' => 'no_wo', 'label' => 'NO.WO', 'required' => true],
                ['excel_cell' => '', 'field_name' => 'no_po', 'label' => 'NO.PO', 'required' => false],
                ['excel_cell' => '', 'field_name' => 'tgl_produksi', 'label' => 'TGL.PRODUKSI', 'required' => true],
                ['excel_cell' => '', 'field_name' => 'shift', 'label' => 'SHIFT', 'required' => false],
                ['excel_cell' => '', 'field_name' => 'tgl_expired', 'label' => 'TGL.EXPIRED', 'required' => false],
                ['excel_cell' => '', 'field_name' => 'operator', 'label' => 'OPERATOR', 'required' => false],
                ['excel_cell' => '', 'field_name' => 'mesin', 'label' => 'MESIN', 'required' => false],
                ['excel_cell' => '', 'field_name' => 'isi', 'label' => 'ISI', 'required' => false],
                ['excel_cell' => '', 'field_name' => 'tgl_kirim', 'label' => 'TGL.KIRIM', 'required' => false],
                ['excel_cell' => '', 'field_name' => 'no_box', 'label' => 'NO.BOX', 'required' => false],
            ];
        }

        // Unilever Field Mapping (default)
        if ($isUnilever) {
            return [
                ['excel_cell' => 'D20', 'field_name' => 'ITEM', 'label' => 'Item', 'required' => true],
                ['excel_cell' => 'D5', 'field_name' => 'PC_NO', 'label' => 'PC NO.', 'required' => true],
                ['excel_cell' => 'D7', 'field_name' => 'MC_NO', 'label' => 'MC NO.', 'required' => true],
                ['excel_cell' => 'D9', 'field_name' => 'WOT', 'label' => 'No.WOT', 'required' => true],
                ['excel_cell' => 'D10', 'field_name' => 'TGL_PRODUKSI', 'label' => 'TGL.PRODUKSI', 'required' => true],
                ['excel_cell' => 'D11', 'field_name' => 'MESIN_SHIFT', 'label' => 'MESIN/SHIFT', 'required' => false],
                ['excel_cell' => 'D12', 'field_name' => 'OPERATOR', 'label' => 'OPERATOR', 'required' => false],
                ['excel_cell' => 'D13', 'field_name' => 'BATCH_NO', 'label' => 'BATCH NO.', 'required' => false],
                ['excel_cell' => 'D14', 'field_name' => 'NO_BOX', 'label' => 'NO.BOX', 'required' => false],
                ['excel_cell' => 'D15', 'field_name' => 'TANGGAL_KIRIM', 'label' => 'TANGGAL KIRIM', 'required' => false],
                ['excel_cell' => 'F5', 'field_name' => 'ISI', 'label' => 'isi (pcs)', 'required' => false],
            ];
        }

        // Default/Generic Field Mapping (for other customers)
        return [
            ['excel_cell' => '', 'field_name' => 'ITEM', 'label' => 'Item', 'required' => true],
            ['excel_cell' => '', 'field_name' => 'WOT', 'label' => 'No.WOT', 'required' => true],
            ['excel_cell' => '', 'field_name' => 'TGL_PRODUKSI', 'label' => 'TGL.PRODUKSI', 'required' => true],
            ['excel_cell' => '', 'field_name' => 'OPERATOR', 'label' => 'OPERATOR', 'required' => false],
            ['excel_cell' => '', 'field_name' => 'BATCH_NO', 'label' => 'BATCH NO.', 'required' => false],
            ['excel_cell' => '', 'field_name' => 'NO_BOX', 'label' => 'NO.BOX', 'required' => false],
            ['excel_cell' => '', 'field_name' => 'ISI', 'label' => 'isi (pcs)', 'required' => false],
        ];
    }

    /**
     * Generate label from template
     */
    public function generateLabel(Request $request, $id)
    {
        try {
            $template = LabelTemplate::with('customer')->findOrFail($id);

            // Validate field mapping
            $fieldMapping = $template->field_mapping ?? [];
            if (empty($fieldMapping)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template belum memiliki field mapping. Silakan edit template terlebih dahulu.'
                ], 400);
            }

            // Validate required fields
            $validationRules = [];
            foreach ($fieldMapping as $field) {
                if (isset($field['required']) && $field['required']) {
                    $validationRules[$field['field_name']] = 'required|string';
                } else {
                    $validationRules[$field['field_name']] = 'nullable|string';
                }
            }

            $request->validate($validationRules);

            // Generate PDF langsung dari HTML template (tanpa Excel)
            // Collect all field values
            $fieldValues = [];
            foreach ($fieldMapping as $field) {
                $fieldName = $field['field_name'];
                $fieldValues[$fieldName] = $request->input($fieldName, '');
            }

            // Generate output filename
            $outputDir = storage_path('app/public/labels');
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            $timestamp = date('Ymd_His');
            $baseFilename = str_replace(' ', '_', $template->template_name) . '_' . $timestamp;
            $pdfPath = $outputDir . '/' . $baseFilename . '.pdf';

            // Generate PDF from HTML template
            try {
                $quantity = $request->input('quantity', 1); // Default 1 label per page

                $customer = $template->customer;
                $customerName = $customer->customer_name ?? '';

                // Priority 1: Check if file_name contains blade template name
                $fileName = $template->file_name ?? '';
                $bladeTemplateName = null;

                // Check if file_name is a blade template (contains .blade.php or just template name)
                if (!empty($fileName)) {
                    // Remove .blade.php extension if exists
                    $cleanFileName = str_replace('.blade.php', '', $fileName);
                    $cleanFileName = str_replace('.php', '', $cleanFileName);

                    // Check if it's a valid blade template path
                    if (strpos($cleanFileName, 'label-pdf-') === 0 || strpos($cleanFileName, 'main.label-management.label-pdf-') === 0) {
                        // Extract template name
                        if (strpos($cleanFileName, 'main.label-management.') === 0) {
                            $bladeTemplateName = $cleanFileName;
                        } else {
                            $bladeTemplateName = 'main.label-management.' . $cleanFileName;
                        }

                        // Verify template exists
                        $viewPath = resource_path('views/' . str_replace('.', '/', $bladeTemplateName) . '.blade.php');
                        if (!file_exists($viewPath)) {
                            $bladeTemplateName = null; // Template doesn't exist, ignore
                        }
                    }
                }

                // Priority 2: Use blade template from file_name if specified
                if ($bladeTemplateName) {
                    // dd('1');
                    $pdf = Pdf::loadView($bladeTemplateName, [
                        'template' => $template,
                        'customer' => $customer,
                        'customerName' => $customerName,
                        'fieldValues' => $fieldValues,
                        'fieldMapping' => $fieldMapping,
                        'quantity' => $quantity
                    ]);
                } else {
                    // dd('2');

                    // Priority 3: Check if this is TSPM template (backward compatibility)
                    $isTSPM = $this->isTSPMCustomer($customer, $template);

                    // Check if this is Unilever template
                    $isUnilever = $this->isUnileverCustomer($customer);

                    // For TSPM, always use the dedicated blade template
                    if ($isTSPM) {
                        // For TSPM, round up quantity to nearest multiple of 4 (4 labels per page)
                        $tspmQuantity = max(4, ceil($quantity / 4) * 4);

                        $pdf = Pdf::loadView('main.label-management.label-pdf-tspm-template', [
                            'template' => $template,
                            'customer' => $customer,
                            'customerName' => $customerName,
                            'fieldValues' => $fieldValues,
                            'fieldMapping' => $fieldMapping,
                            'quantity' => $tspmQuantity
                        ]);
                    } elseif ($isUnilever) {
                        // For Unilever, use dedicated blade template
                        $pdf = Pdf::loadView('main.label-management.label-pdf-template', [
                            'template' => $template,
                            'customer' => $customer,
                            'customerName' => $customerName,
                            'item' => $fieldValues['ITEM'] ?? $template->product_name ?? '',
                            'fieldValues' => $fieldValues,
                            'fieldMapping' => $fieldMapping,
                            'isi' => $fieldValues['ISI'] ?? '',
                            'quantity' => $quantity
                        ]);
                    } elseif (!empty($template->html_template)) {
                        // Use custom HTML template if available (for non-TSPM)
                        // Check if html_template is JSON (from visual builder) or plain HTML
                        $templateData = json_decode($template->html_template, true);

                        if ($templateData && isset($templateData['html'])) {
                            // Use HTML from visual builder
                            $htmlContent = $templateData['html'];
                            $pageSize = $templateData['pageSize'] ?? 'A4';
                            $labelsPerPage = $templateData['labelsPerPage'] ?? 1;
                        } else {
                            // Use plain HTML template
                            $htmlContent = $template->html_template;
                            $pageSize = 'A4';
                            $labelsPerPage = 1;
                        }

                        $cssContent = $template->css_styles ?? '';

                        // Replace placeholders with actual values
                        $htmlContent = str_replace('{{CUSTOMER}}', $customerName, $htmlContent);
                        $htmlContent = str_replace('{{QUANTITY}}', $quantity, $htmlContent);

                        // Replace all field placeholders
                        foreach ($fieldValues as $fieldName => $fieldValue) {
                            $htmlContent = str_replace('{{' . $fieldName . '}}', $fieldValue, $htmlContent);
                        }

                        // Inject CSS
                        $fullHtml = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>' . $cssContent . '</style></head><body>' . $htmlContent . '</body></html>';

                        $pdf = Pdf::loadHTML($fullHtml);

                        // Set paper size
                        $pdf->setPaper(strtolower($pageSize), 'portrait');
                    } else {
                        // Use default template view
                        $pdf = Pdf::loadView('main.label-management.label-pdf-template', [
                            'template' => $template,
                            'customer' => $customer,
                            'customerName' => $customerName,
                            'item' => $fieldValues['ITEM'] ?? $template->product_name ?? '',
                            'fieldValues' => $fieldValues,
                            'fieldMapping' => $fieldMapping,
                            'isi' => $fieldValues['ISI'] ?? '',
                            'quantity' => $quantity
                        ]);
                    }
                }

                // Check if TSPM template (use landscape for TSPM)
                $customer = $template->customer;
                $isTSPM = $this->isTSPMCustomer($customer, $template);
                $fileName = $template->file_name ?? '';
                $isTSPMFileName = stripos(strtolower($fileName), 'tspm') !== false;

                if ($isTSPM || $isTSPMFileName || $bladeTemplateName) {
                    $pdf->setPaper('a4', 'landscape');
                } else {
                    $pdf->setPaper('a4', 'portrait');
                }
                $pdf->save($pdfPath);

                // Save generation history to database
                // Store relative path for easier access
                $relativePath = 'labels/' . $baseFilename . '.pdf';
                $generation = LabelGeneration::create([
                    'template_id' => $template->id,
                    'customer_id' => $template->customer_id,
                    'field_values' => $fieldValues,
                    'pdf_file_path' => $relativePath, // Store relative path
                    'pdf_file_name' => $baseFilename . '.pdf',
                    'quantity' => $quantity,
                    'created_by' => auth()->id(),
                ]);

                // Return download response
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Label PDF berhasil digenerate',
                        'download_url' => '/sipo_krisan/public/storage/labels/' . $baseFilename . '.pdf',
                        'filename' => $baseFilename . '.pdf',
                        'generation_id' => $generation->id
                    ]);
                }

                return response()->download($pdfPath, $baseFilename . '.pdf')->deleteFileAfterSend(false);
            } catch (\Exception $pdfError) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat generate PDF: ' . $pdfError->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate label for packaging (old method - keep for backward compatibility)
     */
    public function generate(Request $request)
    {
        $request->validate([
            'product_code' => 'required|string',
            'product_name' => 'required|string',
            'quantity' => 'required|integer|min:1|max:10000',
            // Tambahkan field lain sesuai kebutuhan
        ]);

        $productCode = $request->input('product_code');
        $productName = $request->input('product_name');
        $quantity = $request->input('quantity', 1);

        $labels = [];
        for ($i = 0; $i < $quantity; $i++) {
            $labels[] = [
                'product_code' => $productCode,
                'product_name' => $productName,
                'batch_number' => $this->generateBatchNumber(),
                'production_date' => date('Y-m-d'),
                'expiry_date' => $request->input('expiry_date', null),
                'serial_number' => $this->generateSerialNumber($i + 1),
            ];
        }

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'count' => count($labels)
        ]);
    }

    /**
     * Export labels to Excel
     */
    public function export(Request $request)
    {
        $request->validate([
            'labels' => 'required|array',
        ]);

        $labels = $request->input('labels', []);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'Label Packaging Produk');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set headers
        $sheet->setCellValue('A3', 'No');
        $sheet->setCellValue('B3', 'Kode Produk');
        $sheet->setCellValue('C3', 'Nama Produk');
        $sheet->setCellValue('D3', 'Batch Number');
        $sheet->setCellValue('E3', 'Tanggal Produksi');
        $sheet->setCellValue('F3', 'Tanggal Kadaluarsa');
        $sheet->setCellValue('G3', 'Serial Number');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $sheet->getStyle('A3:G3')->applyFromArray($headerStyle);

        // Set data
        $row = 4;
        foreach ($labels as $index => $label) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $label['product_code'] ?? '');
            $sheet->setCellValue('C' . $row, $label['product_name'] ?? '');
            $sheet->setCellValue('D' . $row, $label['batch_number'] ?? '');
            $sheet->setCellValue('E' . $row, $label['production_date'] ?? '');
            $sheet->setCellValue('F' . $row, $label['expiry_date'] ?? '');
            $sheet->setCellValue('G' . $row, $label['serial_number'] ?? '');
            $row++;
        }

        // Auto-size columns
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(15);

        // Center align number column
        $sheet->getStyle('A4:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Create writer and save
        $writer = new Xlsx($spreadsheet);
        $filename = 'label_packaging_' . date('Y-m-d_His') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Generate batch number
     */
    private function generateBatchNumber()
    {
        return 'BATCH-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Generate serial number
     */
    private function generateSerialNumber($index)
    {
        return 'SN-' . date('Ymd') . '-' . str_pad($index, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Master Item Unilever Index
     */
    public function masterItemUnileverIndex(Request $request)
    {
        $search = $request->input('search', '');

        $query = MasterItemUnilever::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('KodeDesign', 'like', '%' . $search . '%')
                    ->orWhere('NamaItem', 'like', '%' . $search . '%')
                    ->orWhere('PC', 'like', '%' . $search . '%')
                    ->orWhere('MC', 'like', '%' . $search . '%')
                    ->orWhere('QTY', 'like', '%' . $search . '%');
            });
        }

        $items = $query->orderBy('id', 'desc')->paginate(20);

        // Get materials for dropdown (limited to 500 for performance)
        $materials = MasterMaterial::orderBy('Code')
            ->limit(500)
            ->get(['Code', 'Name']);

        return view('main.label-management.master-item-unilever.index', [
            'title' => 'Master Item Unilever',
            'items' => $items,
            'search' => $search,
            'materials' => $materials
        ]);
    }

    /**
     * Search Master Material for Select2
     */
    public function searchMasterMaterial(Request $request)
    {
        $search = $request->input('search', '');

        if (empty($search)) {
            return response()->json([
                'materials' => []
            ]);
        }

        $materials = MasterMaterial::where(function ($q) use ($search) {
            $q->where('Code', 'like', '%' . $search . '%')
                ->orWhere('Name', 'like', '%' . $search . '%');
        })
            ->orderBy('Code')
            ->limit(50)
            ->get(['Code', 'Name']);

        return response()->json([
            'materials' => $materials->map(function ($material) {
                return [
                    'id' => $material->Code,
                    'text' => $material->Code . ' - ' . $material->Name,
                    'Code' => $material->Code,
                    'Name' => $material->Name
                ];
            })
        ]);
    }

    /**
     * Store Master Item Unilever
     */
    public function storeMasterItemUnilever(Request $request)
    {
        $request->validate([
            'KodeDesign' => 'nullable|string|max:255',
            'NamaItem' => 'nullable|string|max:500',
            'PC' => 'nullable|string|max:255',
            'MC' => 'nullable|string|max:255',
            'QTY' => 'nullable|string|max:255',
        ]);

        MasterItemUnilever::create($request->only(['KodeDesign', 'NamaItem', 'PC', 'MC', 'QTY']));

        return response()->json([
            'success' => true,
            'message' => 'Master Item Unilever berhasil ditambahkan'
        ]);
    }

    /**
     * Update Master Item Unilever
     */
    public function updateMasterItemUnilever(Request $request, $id)
    {
        $request->validate([
            'KodeDesign' => 'nullable|string|max:255',
            'NamaItem' => 'nullable|string|max:500',
            'PC' => 'nullable|string|max:255',
            'MC' => 'nullable|string|max:255',
            'QTY' => 'nullable|string|max:255',
        ]);

        $item = MasterItemUnilever::findOrFail($id);
        $item->update($request->only(['KodeDesign', 'NamaItem', 'PC', 'MC', 'QTY']));

        return response()->json([
            'success' => true,
            'message' => 'Master Item Unilever berhasil diupdate'
        ]);
    }

    /**
     * Delete Master Item Unilever
     */
    public function destroyMasterItemUnilever($id)
    {
        $item = MasterItemUnilever::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Master Item Unilever berhasil dihapus'
        ]);
    }

    /**
     * Master Code Operator Index
     */
    public function masterCodeOperatorIndex(Request $request)
    {
        $search = $request->input('search', '');

        $query = MasterCodeOperator::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('Mesin', 'like', '%' . $search . '%')
                    ->orWhere('Nama', 'like', '%' . $search . '%')
                    ->orWhere('Kode', 'like', '%' . $search . '%');
            });
        }

        $operators = $query->orderBy('id', 'desc')->paginate(20);

        // Get machines for dropdown
        $machines = MasterMachine::active()
            ->orderBy('Code')
            ->get(['Code', 'Description']);

        return view('main.label-management.master-code-operator.index', [
            'title' => 'Master Code Operator',
            'operators' => $operators,
            'search' => $search,
            'machines' => $machines
        ]);
    }

    /**
     * Store Master Code Operator
     */
    public function storeMasterCodeOperator(Request $request)
    {
        $request->validate([
            'Mesin' => 'nullable|string|max:255',
            'Nama' => 'nullable|string|max:255',
            'Kode' => 'nullable|string|max:255',
        ]);

        MasterCodeOperator::create($request->only(['Mesin', 'Nama', 'Kode']));

        return response()->json([
            'success' => true,
            'message' => 'Master Code Operator berhasil ditambahkan'
        ]);
    }

    /**
     * Update Master Code Operator
     */
    public function updateMasterCodeOperator(Request $request, $id)
    {
        $request->validate([
            'Mesin' => 'nullable|string|max:255',
            'Nama' => 'nullable|string|max:255',
            'Kode' => 'nullable|string|max:255',
        ]);

        $operator = MasterCodeOperator::findOrFail($id);
        $operator->update($request->only(['Mesin', 'Nama', 'Kode']));

        return response()->json([
            'success' => true,
            'message' => 'Master Code Operator berhasil diupdate'
        ]);
    }

    /**
     * Delete Master Code Operator
     */
    public function destroyMasterCodeOperator($id)
    {
        $operator = MasterCodeOperator::findOrFail($id);
        $operator->delete();

        return response()->json([
            'success' => true,
            'message' => 'Master Code Operator berhasil dihapus'
        ]);
    }

    /**
     * Import example from image (AI-powered)
     */
    public function importExample(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120' // 5MB max
        ]);

        $template = LabelTemplate::findOrFail($id);
        $image = $request->file('image');

        try {
            // Get image dimensions
            $imageInfo = getimagesize($image->getPathname());
            $imageWidth = $imageInfo[0];
            $imageHeight = $imageInfo[1];

            // Convert image to base64 for AI API
            $imageData = base64_encode(file_get_contents($image->getPathname()));
            $imageMime = $image->getMimeType();
            $imageBase64 = 'data:' . $imageMime . ';base64,' . $imageData;

            // Analyze image with AI
            $elements = $this->analyzeImageWithAI($imageBase64, $imageWidth, $imageHeight, $template);

            // Check if AI returned HTML/CSS or elements
            if (isset($elements['html']) && isset($elements['css'])) {
                return response()->json([
                    'success' => true,
                    'html' => $elements['html'],
                    'css' => $elements['css'],
                    'imageWidth' => $imageWidth,
                    'imageHeight' => $imageHeight,
                    'message' => 'Gambar berhasil dianalisis'
                ]);
            } else {
                // Legacy format: elements array
                return response()->json([
                    'success' => true,
                    'elements' => $elements,
                    'imageWidth' => $imageWidth,
                    'imageHeight' => $imageHeight,
                    'message' => 'Gambar berhasil dianalisis'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error importing example: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menganalisis gambar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyze image with AI (OpenAI Vision API or fallback)
     */
    private function analyzeImageWithAI($imageBase64, $imageWidth, $imageHeight, $template)
    {
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            // Fallback: Manual analysis using basic image processing
            return $this->analyzeImageManually($imageBase64, $imageWidth, $imageHeight, $template);
        }

        try {
            // Use OpenAI Vision API
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o', // or 'gpt-4-vision-preview'
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $this->getAIPrompt($template)
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => $imageBase64
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'max_tokens' => 2000
                ]);

            if (!$response->successful()) {
                throw new \Exception('OpenAI API error: ' . $response->body());
            }

            $result = $response->json();

            if (isset($result['choices'][0]['message']['content'])) {
                $content = $result['choices'][0]['message']['content'];

                // Parse JSON from AI response
                $jsonMatch = [];
                if (preg_match('/```json\s*([\s\S]*?)\s*```/', $content, $jsonMatch)) {
                    $content = $jsonMatch[1];
                } elseif (preg_match('/\{[\s\S]*\}/', $content, $jsonMatch)) {
                    $content = $jsonMatch[0];
                }

                $parsed = json_decode($content, true);

                // Return HTML and CSS directly
                if (isset($parsed['html']) && isset($parsed['css'])) {
                    return [
                        'html' => $parsed['html'],
                        'css' => $parsed['css']
                    ];
                }

                // Legacy: if elements format is returned, convert to HTML/CSS
                if (isset($parsed['elements']) && is_array($parsed['elements'])) {
                    $html = $this->convertElementsToHTML($parsed['elements']);
                    $css = $this->generateCSSFromElements($parsed['elements']);
                    return [
                        'html' => $html,
                        'css' => $css
                    ];
                }
            }

            // If AI response parsing fails, fallback to manual
            return $this->analyzeImageManually($imageBase64, $imageWidth, $imageHeight, $template);
        } catch (\Exception $e) {
            Log::warning('OpenAI API error, using fallback: ' . $e->getMessage());
            // Fallback to manual analysis
            return $this->analyzeImageManually($imageBase64, $imageWidth, $imageHeight, $template);
        }
    }

    /**
     * Get AI prompt for image analysis
     */
    private function getAIPrompt($template)
    {
        $availableFields = [];
        if ($template->field_mapping) {
            $mapping = is_string($template->field_mapping) ? json_decode($template->field_mapping, true) : $template->field_mapping;
            if (is_array($mapping)) {
                foreach ($mapping as $key => $value) {
                    if ($value) {
                        $availableFields[] = [
                            'name' => $key,
                            'label' => $value
                        ];
                    }
                }
            }
        }

        $fieldsList = '';
        if (!empty($availableFields)) {
            $fieldsList = "\n\nAvailable fields:\n";
            foreach ($availableFields as $field) {
                $fieldsList .= "- {$field['name']} ({$field['label']})\n";
            }
        }

        return "Analisis gambar label ini dan buat HTML dan CSS untuk label template.

Gambar ini adalah screenshot dari label Excel yang perlu dikonversi menjadi HTML/CSS.

Tugas Anda:
1. Identifikasi semua teks, field data, header, tabel, dan garis (divider) dalam gambar
2. Buat struktur HTML yang merepresentasikan layout label
3. Untuk field data yang bisa diisi (seperti ITEM, WOT, dll), gunakan placeholder {{FIELD_NAME}}
4. Buat CSS untuk styling yang sesuai dengan gambar (posisi, ukuran, font, border, alignment)
5. Gunakan position: absolute untuk elemen yang perlu posisi spesifik
6. Pastikan layout sesuai dengan gambar

Format output JSON:
{
  \"html\": \"<div class=\\\"label-container\\\">...</div>\",
  \"css\": \"@page { margin: 0; size: A4; } body { ... } .label-container { ... }\"
}

Aturan:
- HTML harus valid dan bisa langsung digunakan
- CSS harus lengkap termasuk @page untuk PDF
- Gunakan {{FIELD_NAME}} untuk placeholder field data
- Posisi elemen harus sesuai dengan gambar
- Font size, weight, dan alignment harus sesuai gambar
- Border dan spacing harus sesuai gambar
{$fieldsList}

Return hanya JSON dengan format di atas, tanpa penjelasan tambahan.";
    }

    /**
     * Fallback: Manual image analysis (basic)
     */
    private function analyzeImageManually($imageBase64, $imageWidth, $imageHeight, $template)
    {
        // Basic fallback: Create a simple HTML/CSS structure
        $html = '<div class="label-container">
    <div class="header">
        <h1>Label Header</h1>
    </div>
    <div class="content">
        <div class="field-row">
            <span class="label">Field 1:</span>
            <span class="value">{{FIELD1}}</span>
        </div>
        <div class="field-row">
            <span class="label">Field 2:</span>
            <span class="value">{{FIELD2}}</span>
        </div>
    </div>
</div>';

        $css = '@page {
    margin: 0;
    size: A4;
}

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    font-size: 12px;
}

.label-container {
    position: relative;
    width: 100%;
    min-height: 500px;
}

.header {
    text-align: center;
    margin-bottom: 20px;
}

.header h1 {
    font-size: 18px;
    font-weight: bold;
    margin: 0;
}

.content {
    margin-top: 20px;
}

.field-row {
    margin-bottom: 10px;
    display: flex;
}

.field-row .label {
    font-weight: bold;
    margin-right: 10px;
    min-width: 100px;
}

.field-row .value {
    flex: 1;
}';

        Log::info('Using manual fallback for image analysis. User should manually adjust HTML/CSS.');

        return [
            'html' => $html,
            'css' => $css
        ];
    }

    /**
     * Convert elements array to HTML (legacy support)
     */
    private function convertElementsToHTML($elements)
    {
        $html = '<div class="label-container" style="position: relative; width: 100%; min-height: 500px;">' . "\n";

        foreach ($elements as $elem) {
            $style = sprintf(
                'position: absolute; left: %dpx; top: %dpx; width: %dpx; height: %dpx; font-size: %dpx; font-weight: %s;',
                $elem['x'] ?? 0,
                $elem['y'] ?? 0,
                $elem['width'] ?? 200,
                $elem['height'] ?? 30,
                $elem['fontSize'] ?? 12,
                $elem['fontWeight'] ?? 'normal'
            );

            if ($elem['border'] ?? false) {
                $style .= ' border: 1px solid #000; padding: 5px;';
            }

            $content = '';
            if (($elem['type'] ?? '') === 'field' && isset($elem['field'])) {
                $content = '{{' . $elem['field'] . '}}';
            } elseif (($elem['type'] ?? '') === 'line') {
                $content = '<hr style="margin:0; border: 1px solid #000;">';
            } else {
                $content = htmlspecialchars($elem['text'] ?? '');
            }

            $html .= sprintf(
                '    <div class="element-%s" style="%s">%s</div>' . "\n",
                $elem['type'] ?? 'text',
                $style,
                $content
            );
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Generate CSS from elements array (legacy support)
     */
    private function generateCSSFromElements($elements)
    {
        return '@page {
    margin: 0;
    size: A4;
}

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
}

.label-container {
    position: relative;
    width: 100%;
    min-height: 500px;
}';
    }
}
