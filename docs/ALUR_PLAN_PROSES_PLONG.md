# Alur Plan Proses PLONG

## 📋 Overview
Dokumen ini menjelaskan alur lengkap untuk membuat planning proses PLONG di sistem SIPO KRISAN.

## 🔄 Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│  1. Pilih Proses Rencana Plan                               │
│     Route: /process/planning                                │
│     View: pilih-rencana-plan.blade.php                       │
│     - User memilih radio button "PLONG"                      │
│     - Klik tombol "Lanjutkan ke Planning"                    │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│  2. Halaman Planning Produksi                               │
│     Route: /process/planning/lanjut?processes=PLONG         │
│     View: planning.blade.php                                 │
│     - Menampilkan badge "PLONG"                              │
│     - Menampilkan tabel Work Order yang tersedia             │
│     - User memilih Work Order dengan checkbox                │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│  3. Buat Planning                                            │
│     - User klik tombol "Buat Planning"                       │
│     - Modal muncul untuk input Start Date                    │
│     - User pilih tanggal mulai                               │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│  4. Generate Plan Preview                                    │
│     POST: /process/submit-plan-first                         │
│     Controller: ProcessController@submitPlanFirst            │
│     - Validasi data Work Order                               │
│     - Generate code_plan (format: P-PPIC-YYYYMMDD-XXX)      │
│     - Untuk setiap item:                                     │
│       * generateSimpleProcessPlan(item, 'PLONG', startTime)  │
│       * Ambil mesin dari tb_mapping_items (field: m_plg)    │
│       * Hitung estimasi waktu                                │
│       * Generate StartJam & EndJam                           │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│  5. Tampilkan Preview                                        │
│     - Menampilkan tabel preview planning                     │
│     - Menampilkan timeline per mesin                         │
│     - User dapat review sebelum save                         │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│  6. Save ke Database                                         │
│     POST: /process/save-plan-from-preview                   │
│     Controller: ProcessController@savePlanFromPreview        │
│     - Save ke tabel: tb_plan_first_productions              │
│     - Process code: 'PLG'                                    │
│     - Department: dari mastermachine berdasarkan mesin       │
│     - Status: 'PLANNED'                                      │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│  7. Tampil di Timeline                                       │
│     Route: /process/plan-first-table                         │
│     View: timeline-table.blade.php                           │
│     - Plan PLONG muncul di timeline sesuai mesin            │
│     - Dapat di-edit, dihapus, atau diubah prioritas          │
└─────────────────────────────────────────────────────────────┘
```

## 📝 Detail Setiap Langkah

### 1. Pilih Proses Rencana Plan

**File:** `resources/views/main/process/pilih-rencana-plan.blade.php`

- User melihat tabel daftar proses
- PLONG ada di baris ke-3 dengan deskripsi: "Proses pembuatan lubang atau bentuk khusus dengan die cutting atau punching tools"
- User memilih radio button dengan value `PLONG`
- Tombol "Lanjutkan ke Planning" menjadi aktif
- JavaScript mengirim parameter `processes=PLONG` ke halaman planning

**Code Reference:**
```482:488:resources/views/main/process/pilih-rencana-plan.blade.php
<input type="radio" name="process_choice" class="check-process" id="proc_plong"
    value="PLONG">
<label for="proc_plong" class="sr-only">Pilih proses PLONG</label>
</td>
<td><strong>PLONG</strong></td>
<td>Proses pembuatan lubang atau bentuk khusus dengan die cutting atau punching tools.
</td>
```

### 2. Halaman Planning Produksi

**File:** `resources/views/main/process/planning.blade.php`

- Sistem membaca parameter `processes` dari URL
- Menampilkan badge PLONG dengan warna kuning (`#ffc107`)
- Menampilkan tabel DataTable dengan Work Order yang tersedia
- User dapat memilih multiple Work Order dengan checkbox
- Setiap Work Order memiliki: MaterialCode, WODocNo, Quantity, DeliveryDate, dll

**Code Reference:**
```132:149:resources/views/main/process/planning.blade.php
@php
    $processes = request()->get('processes', '');
    $processList = $processes ? explode(',', $processes) : [];
@endphp


<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h4 class="card-title">Pilih Work Order untuk Planning</h4>
                        <p class="text-muted">Pilih item yang akan direncanakan untuk proses:
                            @foreach ($processList as $process)
                                <span class="process-badge {{ strtolower($process) }}">{{ $process }}</span>
                            @endforeach
                        </p>
```

### 3. Generate Plan Preview

**File:** `app/Http/Controllers/ProcessController.php`
**Method:** `submitPlanFirst()`

Proses yang terjadi:
1. Validasi data Work Order yang dipilih
2. Generate `code_plan` dengan format: `P-PPIC-YYYYMMDD-XXX`
3. Untuk setiap item, panggil `generateSimpleProcessPlan(item, 'PLONG', startTime)`

**Code Reference:**
```221:303:app/Http/Controllers/ProcessController.php
public function submitPlanFirst(Request $request)
{
    // dd($request->all());
    // Log request data untuk debugging
    Log::info('submitPlanFirst called with data:', [
        'request_data' => $request->all(),
        'data_count' => $request->data ? count($request->data) : 0,
        'start_date' => $request->start_date,
        'processes' => $request->processes
    ]);

    $data = $request->data;
    $startDate = $request->start_date;
    $selectedProcess = $request->processes; // Proses yang dipilih user (CETAK, PTG, dll)

    // Validasi data yang diterima
    if (!$data || !is_array($data) || empty($data)) {
        Log::error('Invalid data received in submitPlanFirst');
        return response()->json([
            'success' => false,
            'message' => 'Data tidak valid atau kosong'
        ], 400);
    }

    if (!$startDate) {
        Log::error('Start date is required');
        return response()->json([
            'success' => false,
            'message' => 'Start date diperlukan'
        ], 400);
    }

    if (!$selectedProcess) {
        Log::error('Process selection is required');
        return response()->json([
            'success' => false,
            'message' => 'Pemilihan proses diperlukan'
        ], 400);
    }

    // Generate code plan
    $prefix = 'P-PPIC';
    $currentDate = Carbon::now()->format('Ymd');
    $lastPlan = PlanFirstProduction::whereDate('created_at', now()->toDateString())
        ->where('code_plan', 'like', $prefix . '-' . $currentDate . '-%')
        ->orderBy('code_plan', 'desc')
        ->first();

    $codeNumber = 1;
    if ($lastPlan) {
        $lastCode = $lastPlan->code_plan;
        $lastNum = intval(substr($lastCode, strrpos($lastCode, '-') + 1));
        $codeNumber = $lastNum + 1;
    }
    $codeNumberPadded = str_pad($codeNumber, 3, '0', STR_PAD_LEFT);
    $code_plan = $prefix . '-' . $currentDate . '-' . $codeNumberPadded;

    Log::info('Generated code plan:', ['code_plan' => $code_plan]);

    // Proses data untuk preview
    $planPerItem = [];
    $startTime = Carbon::parse($startDate)->setTime(8, 0, 0); // Mulai jam 8 pagi

    foreach ($data as $item) {
        // Validasi item data
        if (!isset($item['MaterialCode']) || !isset($item['WODocNo']) || !isset($item['Quantity'])) {
            Log::warning('Invalid item data:', $item);
            continue;
        }

        // Buat unique key untuk item
        $uniqueKey = $item['MaterialCode'] . '_' . $item['WODocNo'];

        // Generate planning data untuk proses yang dipilih
        $planData = $this->generateSimpleProcessPlan($item, $selectedProcess, $startTime);
        // dd($planData);

        if ($planData) {
            $planPerItem[$uniqueKey] = $planData;
            // Update start time untuk item berikutnya (tambahkan 1 jam setup)
            $startTime = Carbon::parse($planData['EndJam'])->addHour();
        }
    }
```

### 4. Generate Simple Process Plan untuk PLONG

**File:** `app/Http/Controllers/ProcessController.php`
**Method:** `generateSimpleProcessPlan()`

Untuk proses PLONG:
1. **Ambil Mesin:**
   - Cari di `tb_mapping_items` dengan field `m_plg` berdasarkan `MaterialCode`
   - Jika tidak ada, gunakan default: `PLG-001`

2. **Hitung Estimasi:**
   - Ambil `CapacityPerHour` dari tabel `machines`
   - Konversi quantity jika unit berbeda
   - Estimasi = Quantity / CapacityPerHour
   - Base estimation untuk PLONG: 0.4 jam

3. **Generate Timeline:**
   - StartJam: dari startTime yang diberikan
   - EndJam: StartJam + Estimasi (dalam jam)

**Code Reference:**
```752:762:app/Http/Controllers/ProcessController.php
} elseif (in_array(strtoupper($process), ['PLONG', 'PLG'])) {
    // Untuk proses PLONG, cari field m_plg
    $mapping = DB::table('tb_mapping_items')
        ->where('kode', $materialCode)
        ->whereNotNull('m_plg')
        ->where('m_plg', '!=', '')
        ->first();

    if ($mapping) {
        return $mapping->m_plg;
    }
```

**Code Reference:**
```810:811:app/Http/Controllers/ProcessController.php
'PLONG' => 'PLG-001',
'PLG' => 'PLG-001',
```

**Code Reference:**
```835:836:app/Http/Controllers/ProcessController.php
'PLONG' => 0.4,
'PLG' => 0.4,
```

### 5. Save ke Database

**File:** `app/Http/Controllers/ProcessController.php`
**Method:** `savePlanningToDatabase()`

Data yang disimpan ke `tb_plan_first_productions`:
- `code_plan`: Kode plan yang di-generate
- `code_item`: MaterialCode
- `code_machine`: Kode mesin dari mapping
- `process`: 'PLG' (kode untuk PLONG)
- `department`: Diambil dari tabel machines berdasarkan code_machine
- `quantity`: Quantity yang sudah dikonversi
- `est_jam`: Estimasi dalam jam
- `start_jam`: Waktu mulai
- `end_jam`: Waktu selesai
- `flag_status`: 'PLANNED'
- `wo_docno`: Nomor Work Order
- `so_docno`: Nomor Sales Order
- `delivery_date`: Tanggal delivery

**Code Reference:**
```436:437:app/Http/Controllers/ProcessController.php
} elseif ($selectedProcess == 'PLONG') {
    $process = 'PLG';
```

**Code Reference:**
```464:484:app/Http/Controllers/ProcessController.php
$planningId = DB::table('tb_plan_first_productions')->insertGetId([
    'code_plan' => $code_plan,
    'code_item' => $planData['MaterialCode'],
    'code_machine' => $planData['Machine'],
    'quantity' => $planData['Quantity'],
    'up_cetak' => $planData['UP'] ?? 1,
    'capacity' => $planData['MachineCapacity'] ?? 1000,
    'est_jam' => $planData['Estimation'],
    'est_day' => $planData['Estimation'] / 24, // Konversi jam ke hari
    'start_jam' => $planData['StartJam'],
    'end_jam' => $planData['EndJam'],
    'flag_status' => 'PLANNED',
    'wo_docno' => $planData['WODocNo'],
    'so_docno' => $planData['SODocNo'],
    'delivery_date' => $planData['DeliveryDate'],
    'created_by' => auth()->user()->name ?? 'SYSTEM',
    'created_at' => now(),
    'material_name' => $planData['MaterialName'],
    'process' => $processType,
    'department' => $department,
```

### 6. Tampil di Timeline

**File:** `resources/views/main/process/timeline-table.blade.php`

- Plan PLONG muncul di timeline sesuai dengan mesin yang digunakan
- Dapat dilihat per mesin dengan grouping
- User dapat:
  - Edit plan
  - Hapus plan
  - Ubah prioritas
  - Tambah plan manual
  - Atur shift (2 shift atau 3 shift)

## 🔧 Konfigurasi yang Diperlukan

### 1. Mapping Item ke Mesin PLONG

Pastikan di tabel `tb_mapping_items` sudah ada mapping:
- Field `m_plg` harus diisi dengan kode mesin PLONG untuk setiap material yang akan di-plan

**Contoh:**
```sql
UPDATE tb_mapping_items 
SET m_plg = 'PLG-001' 
WHERE kode = 'MATERIAL_CODE';
```

### 2. Master Machine PLONG

Pastikan mesin PLONG sudah terdaftar di tabel `machines`:
- `Code`: Kode mesin (contoh: PLG-001)
- `CapacityPerHour`: Kapasitas per jam
- `Unit`: Unit kapasitas (PCS, SET, dll)
- `Department`: Department yang menangani mesin ini

### 3. Default Machine

Jika tidak ada mapping, sistem akan menggunakan default:
- Default machine: `PLG-001`
- Base estimation: `0.4` jam

## 📊 Data Flow

```
User Input
    │
    ├─> Work Order Selection
    │   └─> MaterialCode, WODocNo, Quantity, DeliveryDate
    │
    ├─> Start Date Selection
    │   └─> YYYY-MM-DD format
    │
    └─> Process Selection
        └─> 'PLONG'

    ▼

Backend Processing
    │
    ├─> Generate Code Plan
    │   └─> P-PPIC-YYYYMMDD-XXX
    │
    ├─> For Each Item:
    │   ├─> Get Machine (from tb_mapping_items.m_plg)
    │   ├─> Get Machine Capacity (from machines.CapacityPerHour)
    │   ├─> Convert Quantity (if unit different)
    │   ├─> Calculate Estimation (Quantity / Capacity)
    │   └─> Generate Timeline (StartJam, EndJam)
    │
    └─> Return Preview Data

    ▼

Database Storage
    │
    └─> tb_plan_first_productions
        ├─> code_plan
        ├─> code_item
        ├─> code_machine
        ├─> process = 'PLG'
        ├─> quantity
        ├─> est_jam
        ├─> start_jam
        ├─> end_jam
        ├─> flag_status = 'PLANNED'
        └─> ... (other fields)

    ▼

Timeline Display
    │
    └─> Grouped by Machine
        └─> Visual Timeline with Gantt Chart
```

## ⚠️ Catatan Penting

1. **Waktu Mulai:** Default mulai jam 8:00 pagi
2. **Setup Time:** Setiap item berikutnya ditambahkan 1 jam setup time
3. **Unit Conversion:** Sistem otomatis konversi jika unit WO berbeda dengan unit mesin
4. **WOP Handling:** Jika WODocNo dimulai dengan 'WOP', quantity di-set 0 dan durasi 8 jam
5. **Process Code:** PLONG disimpan sebagai 'PLG' di database
6. **Department:** Diambil otomatis dari tabel machines berdasarkan code_machine

## 🐛 Troubleshooting

### Problem: Mesin tidak ditemukan
**Solusi:** Pastikan mapping di `tb_mapping_items` sudah ada untuk material tersebut, atau pastikan default machine `PLG-001` ada di tabel `machines`

### Problem: Estimasi waktu tidak akurat
**Solusi:** Periksa `CapacityPerHour` di tabel `machines` untuk mesin PLONG yang digunakan

### Problem: Plan tidak muncul di timeline
**Solusi:** 
- Periksa `flag_status` harus 'PLANNED'
- Periksa `code_machine` sesuai dengan mesin yang ditampilkan
- Periksa filter tanggal di timeline view

## 📚 File-file Terkait

1. **Controller:**
   - `app/Http/Controllers/ProcessController.php`
     - `pilihRencanaPlan()` - Halaman pilih proses
     - `submitPlanFirst()` - Generate preview
     - `generateSimpleProcessPlan()` - Generate plan per item
     - `getDefaultMachineForProcess()` - Ambil mesin default
     - `savePlanningToDatabase()` - Save ke database
     - `savePlanFromPreview()` - Save dari preview

2. **Views:**
   - `resources/views/main/process/pilih-rencana-plan.blade.php` - Pilih proses
   - `resources/views/main/process/planning.blade.php` - Planning page
   - `resources/views/main/process/timeline-table.blade.php` - Timeline view

3. **Models:**
   - `app/Models/PlanFirstProduction.php`
   - `app/Models/Machine.php`
   - `app/Models/MappingItem.php`

4. **Database Tables:**
   - `tb_plan_first_productions` - Data planning
   - `tb_mapping_items` - Mapping material ke mesin
   - `machines` - Master data mesin

## ✅ Checklist Implementasi

- [x] PLONG tersedia di halaman pilih proses
- [x] Planning page menerima parameter PLONG
- [x] Generate plan untuk PLONG sudah ada
- [x] Mapping mesin PLONG (m_plg) sudah ada
- [x] Save ke database dengan process code 'PLG'
- [x] Timeline menampilkan plan PLONG
- [ ] (Optional) Validasi khusus untuk PLONG
- [ ] (Optional) Report khusus untuk PLONG
- [ ] (Optional) Dashboard khusus untuk PLONG

---

**Last Updated:** {{ date('Y-m-d') }}
**Version:** 1.0

