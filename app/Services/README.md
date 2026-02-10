# JobOrderNumberService

Service ini digunakan untuk generate nomor job order otomatis dengan format yang konsisten.

## Format Nomor Job Order

Format: `PRP-YYMMDD-XXXX`

- **PRP**: Prefix tetap untuk Prepress
- **YY**: 2 digit tahun (contoh: 25 untuk 2025)
- **MM**: 2 digit bulan (contoh: 08 untuk Agustus)
- **DD**: 2 digit tanggal (contoh: 20 untuk tanggal 20)
- **XXXX**: Counter 4 digit dengan leading zeros (contoh: 0001, 0002, dst)

## Contoh Nomor

- `PRP-250820-0001` - Job order pertama pada 20 Agustus 2025
- `PRP-250820-0002` - Job order kedua pada 20 Agustus 2025
- `PRP-250821-0001` - Job order pertama pada 21 Agustus 2025

## Cara Penggunaan

### 1. Generate Nomor Otomatis

```php
use App\Services\JobOrderNumberService;

$service = new JobOrderNumberService();

// Generate untuk tanggal hari ini
$nomor = $service->generateJobOrderNumber();

// Generate untuk tanggal tertentu
$nomor = $service->generateJobOrderNumber('2025-08-20');
```

### 2. Di Controller

```php
public function submitJobOrderPrepress(Request $request)
{
    $data = $request->all();
    
    // Generate nomor job order otomatis jika create baru
    if (!$data['id_job']) {
        $jobOrderNumberService = new JobOrderNumberService();
        $data['nomor_job_order'] = $jobOrderNumberService->generateJobOrderNumber($data['tanggal']);
    }
    
    // ... proses lainnya
}
```

### 3. API Endpoint

```
GET /sipo/prepress/job-order/next-number?tanggal=2025-08-20
```

Response:
```json
{
    "success": true,
    "next_number": "PRP-250820-0003"
}
```

## Fitur

- **Auto-increment**: Counter otomatis bertambah untuk setiap tanggal
- **Reset harian**: Counter dimulai dari 0001 setiap hari baru
- **Thread-safe**: Menggunakan database query untuk mendapatkan counter terakhir
- **Validasi**: Bisa dicek apakah nomor sudah ada atau belum

## Dependencies

- `App\Models\JobPrepress`
- `Carbon\Carbon`
