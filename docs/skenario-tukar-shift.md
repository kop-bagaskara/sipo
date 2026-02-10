# Dokumentasi Skenio Tukar Shift

## Overview
Fitur Tukar Shift memungkinkan karyawan untuk mengajukan permohonan pertukaran shift kerja. Terdapat 3 skenario yang dapat dipilih oleh karyawan.

---

## Skenario 1: Tukar Shift Diri Sendiri (`self`)

### Deskripsi
Karyawan mengubah jam kerjanya sendiri pada tanggal yang sama tanpa melibatkan orang lain. Contoh: Biasanya masuk pagi (08:00-17:00), ingin diganti menjadi siang (13:00-22:00).

### Field Inputan

| Field | Tipe | Required | Deskripsi |
|-------|------|----------|-----------|
| `scenario_type` | string | Yes | Value: `"self"` |
| `date` | date (Y-m-d) | Yes | Tanggal tukar shift |
| `original_start_time` | time (H:i) | Yes | Jam kerja saat ini (jam mulai) |
| `original_end_time` | time (H:i) | Yes | Jam kerja saat ini (jam selesai) |
| `new_start_time` | time (H:i) | Yes | Jam kerja baru (jam mulai) |
| `new_end_time` | time (H:i) | Yes | Jam kerja baru (jam selesai) |
| `purpose` | string | No | Alasan tukar shift (maks. 500 karakter) |
| `applicant_name` | string | Auto | Nama pemohon (auto dari user login) |
| `applicant_department` | string | Auto | Departemen pemohon (auto dari database) |
| `applicant_nip` | string | Auto | NIP pemohon (auto dari database) |
| `notes` | string | No | Catatan tambahan (maks. 1000 karakter) |
| `attachment` | file | No | Lampiran (PDF/JPG/JPEG/PNG, max 2MB) |

### Contoh Data Input
```json
{
  "scenario_type": "self",
  "date": "2025-01-30",
  "original_start_time": "08:00",
  "original_end_time": "17:00",
  "new_start_time": "13:00",
  "new_end_time": "22:00",
  "purpose": "Ada keperluan keluarga di pagi hari",
  "applicant_name": "Ahmad Sudrajat",
  "applicant_department": "IT Division",
  "applicant_nip": "EMP001"
}
```

### Validasi

| # | Validasi | Pesan Error |
|---|----------|-------------|
| 1 | Tanggal tidak boleh di masa lalu | "Tidak bisa mengajukan tukar shift untuk tanggal yang sudah lewat" |
| 2 | Minimal H-1 (1 hari sebelum tanggal tukar shift) | "Pengajuan tukar shift harus minimal H-1. Tanggal tukar shift minimal [tanggal]" |
| 3 | `original_end_time` harus setelah `original_start_time` | "The original end time field must be a time after original start time" |
| 4 | `new_end_time` harus setelah `new_start_time` | "The new end time field must be a time after new start time" |
| 5 | Tidak boleh ada pengajuan lain (tukar shift/absence) pada tanggal tersebut | "Anda sudah memiliki pengajuan (tukar shift/tidak masuk kerja) pada tanggal tersebut" |

---

## Skenario 2: Tukar Shift dengan Teman (`exchange`)

### Deskripsi
Karyawan menukar shift kerjanya dengan teman di departemen yang sama. Contoh: A masuk pagi, B masuk siang. A ingin tukar, jadi A yang masuk siang dan B yang masuk pagi.

### Field Inputan

| Field | Tipe | Required | Deskripsi |
|-------|------|----------|-----------|
| `scenario_type` | string | Yes | Value: `"exchange"` |
| `date` | date (Y-m-d) | Yes | Tanggal tukar shift |
| `applicant_start_time` | time (H:i) | Yes | Jam kerja pemohon |
| `applicant_end_time` | time (H:i) | Yes | Jam kerja pemohon |
| `purpose` | string | Yes | Alasan tukar shift pemohon (maks. 500 karakter) |
| `substitute_id` | integer | Yes | ID user pengganti (dari tabel users) |
| `substitute_name` | string | Yes | Nama pengganti |
| `substitute_department` | string | No | Departemen pengganti (auto dari database) |
| `substitute_nip` | string | No | NIP pengganti (auto dari database) |
| `substitute_start_time` | time (H:i) | Yes | Jam kerja pengganti |
| `substitute_end_time` | time (H:i) | Yes | Jam kerja pengganti |
| `substitute_purpose` | string | Yes | Alasan tukar shift pengganti (maks. 500 karakter) |
| `applicant_name` | string | Auto | Nama pemohon (auto dari user login) |
| `applicant_department` | string | Auto | Departemen pemohon (auto dari database) |
| `applicant_nip` | string | Auto | NIP pemohon (auto dari database) |
| `notes` | string | No | Catatan tambahan (maks. 1000 karakter) |
| `attachment` | file | No | Lampiran (PDF/JPG/JPEG/PNG, max 2MB) |

### Contoh Data Input
```json
{
  "scenario_type": "exchange",
  "date": "2025-01-30",
  "applicant_start_time": "08:00",
  "applicant_end_time": "17:00",
  "purpose": "Ada keperluan keluarga di pagi hari",
  "substitute_id": 123,
  "substitute_name": "Budi Santoso",
  "substitute_department": "IT Division",
  "substitute_nip": "EMP002",
  "substitute_start_time": "13:00",
  "substitute_end_time": "22:00",
  "substitute_purpose": "Menggantikan Ahmad yang ada keperluan",
  "applicant_name": "Ahmad Sudrajat",
  "applicant_department": "IT Division",
  "applicant_nip": "EMP001"
}
```

### Validasi

| # | Validasi | Pesan Error |
|---|----------|-------------|
| 1 | Tanggal tidak boleh di masa lalu | "Tidak bisa mengajukan tukar shift untuk tanggal yang sudah lewat" |
| 2 | Minimal H-1 (1 hari sebelum tanggal tukar shift) | "Pengajuan tukar shift harus minimal H-1. Tanggal tukar shift minimal [tanggal]" |
| 3 | Pengganti tidak boleh sama dengan pemohon | "Pengganti tidak boleh sama dengan pemohon" |
| 4 | Pengganti harus dari departemen yang sama | "Pengganti harus dari departemen yang sama dengan pemohon" |
| 5 | `applicant_end_time` harus setelah `applicant_start_time` | "The applicant end time field must be a time after applicant start time" |
| 6 | `substitute_end_time` harus setelah `substitute_start_time` | "The substitute end time field must be a time after substitute start time" |
| 7 | Pemohon tidak boleh ada pengajuan lain (tukar shift/absence) pada tanggal tersebut | "Anda sudah memiliki pengajuan (tukar shift/tidak masuk kerja) pada tanggal tersebut" |
| 8 | Pengganti sedang mengajukan cuti/izin pada tanggal tersebut | "Pengganti sedang mengajukan cuti/izin pada tanggal tersebut" |
| 9 | Pengganti sudah memiliki pengajuan tukar shift pada tanggal tersebut | "Pengganti sudah memiliki pengajuan tukar shift pada tanggal tersebut" |

---

## Skenario 3: Tukar Shift karena Hari Merah/Lembur (`holiday`)

### Deskripsi
Karyawan bekerja pada hari libur merah (lembur) dan mendapatkan tanggal pengganti untuk libur (compensatory leave/off).

### Field Inputan

| Field | Tipe | Required | Deskripsi |
|-------|------|----------|-----------|
| `scenario_type` | string | Yes | Value: `"holiday"` |
| `holiday_work_date` | date (Y-m-d) | Yes | Tanggal kerja (hari merah) |
| `applicant_start_time` | time (H:i) | Yes | Jam kerja saat hari merah |
| `applicant_end_time` | time (H:i) | Yes | Jam kerja saat hari merah |
| `work_hours` | decimal | Yes | Total jam kerja (0.5 - 24 jam) |
| `compensatory_date` | date (Y-m-d) | Yes | Tanggal pengganti (OFF) |
| `purpose` | string | Yes | Alasan kerja di hari merah (maks. 500 karakter) |
| `applicant_name` | string | Auto | Nama pemohon (auto dari user login) |
| `applicant_department` | string | Auto | Departemen pemohon (auto dari database) |
| `applicant_nip` | string | Auto | NIP pemohon (auto dari database) |
| `notes` | string | No | Catatan tambahan (maks. 1000 karakter) |
| `attachment` | file | No | Lampiran (PDF/JPG/JPEG/PNG, max 2MB) |

### Contoh Data Input
```json
{
  "scenario_type": "holiday",
  "holiday_work_date": "2025-02-01",
  "applicant_start_time": "08:00",
  "applicant_end_time": "17:00",
  "work_hours": 9,
  "compensatory_date": "2025-02-05",
  "purpose": "Lembur proyek urgent",
  "applicant_name": "Ahmad Sudrajat",
  "applicant_department": "IT Division",
  "applicant_nip": "EMP001"
}
```

### Validasi

| # | Validasi | Pesan Error |
|---|----------|-------------|
| 1 | Tanggal kerja (`holiday_work_date`) tidak boleh di masa lalu | "Tidak bisa mengajukan tukar shift untuk tanggal yang sudah lewat" |
| 2 | Minimal H-1 (1 hari sebelum tanggal kerja) | "Pengajuan tukar shift harus minimal H-1. Tanggal tukar shift minimal [tanggal]" |
| 3 | `applicant_end_time` harus setelah `applicant_start_time` | "The applicant end time field must be a time after applicant start time" |
| 4 | `compensatory_date` harus setelah `holiday_work_date` | "The compensatory date field must be a date after holiday work date" |
| 5 | `work_hours` harus antara 0.5 - 24 jam | "The work hours field must be a number between 0.5 and 24" |
| 6 | Tidak boleh ada pengajuan lain pada `holiday_work_date` | "Anda sudah memiliki pengajuan (tukar shift/tidak masuk kerja) pada tanggal tersebut" |
| 7 | Tidak boleh ada pengajuan lain pada `compensatory_date` | "Anda sudah memiliki pengajuan pada tanggal pengganti (OFF) tersebut" |

---

## Alur Approval

Berdasarkan konfigurasi `ApprovalSetting`, alur approval untuk tukar shift adalah:

1. **Pending** → Menunggu approval pertama (sesuai approval flow)
2. **Supervisor/Manager Approved** → Menunggu approval berikutnya
3. **HR Approved** → Disetujui final

Status yang mungkin:
- `pending` - Menunggu approval atasan
- `supervisor_approved` - Disetujui supervisor
- `supervisor_rejected` - Ditolak supervisor
- `manager_approved` - Disetujui manager
- `manager_rejected` - Ditolak manager
- `hr_approved` - Disetujui HR (final)
- `hr_rejected` - Ditolak HR (final)
- `cancelled` - Dibatalkan

---

## Format Request Number

Format: `HRD-YYMMDD-XXXX`

Contoh: `HRD-250130-0001`
- `HRD` - Prefix
- `250130` - Tanggal (25 = 2025, 01 = Januari, 30 = Tanggal 30)
- `0001` - Counter (4 digit)

---

## API Endpoint

### Membuat Pengajuan Tukar Shift
```
POST /api/hr-requests
Content-Type: multipart/form-data

{
  "request_type": "shift_change",
  "scenario_type": "self|exchange|holiday",
  ... (field lain sesuai skenario)
}
```

### Mendapatkan Daftar Pengajuan
```
GET /api/hr-requests?type=shift_change&status=pending
```

### Approval Pengajuan
```
POST /api/hr-requests/{id}/approve
{
  "notes": "Disetujui untuk keperluan mendesak"
}
```

### Rejection Pengajuan
```
POST /api/hr-requests/{id}/reject
{
  "notes": "Tidak dapat disetujui karena..."
}
```

---

## Referensi

- **Model**: [EmployeeRequest.php](../app/Models/EmployeeRequest.php)
- **Controller**: [HRRequestController.php](../app/Http/Controllers/HRRequestController.php)
- **Database Table**: `tb_employee_requests` (PostgreSQL)

---

## Catatan Penting

1. **Auto-filled fields**: Field seperti `applicant_name`, `applicant_department`, `applicant_nip` akan otomatis diisi dari data user login dan database.
2. **Deadline H-1**: Semua skenario tukar shift wajib diajukan minimal 1 hari sebelum tanggal tukar shift.
3. **Tidak boleh tumpang tindih**: Satu tanggal tidak boleh memiliki lebih dari satu pengajuan (tukar shift atau absence).
4. **Exchange scenario**: Pengganti harus dari departemen yang sama dengan pemohon.
5. **Holiday scenario**: Tanggal pengganti (compensatory date) harus setelah tanggal kerja di hari merah.
