# Dokumentasi Validasi Tukar Shift

## Overview
Dokumentasi ini menjelaskan semua validasi yang dilakukan saat pengajuan tukar shift, termasuk validasi basic field, validasi business rule, dan validasi khusus untuk setiap skenario.

---

## Lokasi Kode Validasi

File: [HRRequestController.php](../app/Http/Controllers/HRRequestController.php)
Method: `validateRequestData()` (line 1708-2078)

---

## Validasi Dasar (All Scenarios)

### 1. Request Type Validation
```php
'request_type' => 'required|in:shift_change,absence,overtime,vehicle_asset'
```

**Validasi:**
- Field wajib diisi
- Nilai harus salah satu dari: `shift_change`, `absence`, `overtime`, `vehicle_asset`

### 2. Scenario Type Validation
```php
'scenario_type' => 'required|in:self,exchange,holiday'
```

**Validasi:**
- Field wajib diisi
- Nilai harus salah satu dari: `self`, `exchange`, `holiday`

### 3. Optional Fields Validation
```php
'notes' => 'nullable|string|max:1000',
'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
```

**Validasi:**
- `notes`: Maksimal 1000 karakter
- `attachment`: File PDF/JPG/JPEG/PNG, maksimal 2MB (2048 KB)

---

## Skenario 1: Self (Tukar Shift Diri Sendiri)

### Field Validations

| Field | Rules |
|-------|-------|
| `date` | `required\|date_format:Y-m-d` |
| `original_start_time` | `required\|date_format:H:i` |
| `original_end_time` | `required\|date_format:H:i\|after:original_start_time` |
| `new_start_time` | `required\|date_format:H:i` |
| `new_end_time` | `required\|date_format:H:i\|after:new_start_time` |
| `purpose` | `nullable\|string\|max:500` |

### Custom Validations

#### 1. Tidak Boleh Tanggal di Masa Lalu
```php
// HRRequestController.php:1936-1943
$requestDateObj = Carbon::parse($requestDate)->startOfDay();
$today = Carbon::today();

if ($requestDateObj->lt($today)) {
    $errors[$dateField] = ['Tidak bisa mengajukan tukar shift untuk tanggal yang sudah lewat'];
}
```

**Trigger:** Field `date` < hari ini
**Error Message:** "Tidak bisa mengajukan tukar shift untuk tanggal yang sudah lewat"

#### 2. Minimal H-1 (1 Hari Sebelumnya)
```php
// HRRequestController.php:1945-1949
$daysUntilStart = $today->diffInDays($requestDateObj, false);
if ($daysUntilStart !== null && $daysUntilStart < 1) {
    $errors[$dateField] = ['Pengajuan tukar shift harus minimal H-1 (1 hari sebelum tanggal tukar shift). Tanggal tukar shift minimal ' . $today->copy()->addDay()->format('d/m/Y')];
}
```

**Trigger:** Field `date` < hari ini + 1 hari
**Error Message:** "Pengajuan tukar shift harus minimal H-1 (1 hari sebelum tanggal tukar shift). Tanggal tukar shift minimal [tanggal]"

**Contoh:**
- Hari ini: 28 Januari 2025
- Minimal tanggal tukar shift: 29 Januari 2025
- Jika input 28 Januari → ERROR

#### 3. Cek Pengajuan Ganda (Double Request)
```php
// HRRequestController.php:2018-2031
$existingApplicantRequest = EmployeeRequest::where('employee_id', $user->id)
    ->whereIn('request_type', ['shift_change', 'absence'])
    ->where('status', '!=', 'cancelled')
    ->whereDate('created_at', '>=', $dateToCheck)
    ->where(function($query) use ($dateToCheck) {
        $query->whereRaw("request_data->>'date' = ?", [$dateToCheck])
              ->orWhereRaw("request_data->>'holiday_work_date' = ?", [$dateToCheck]);
    })
    ->first();
```

**Trigger:** Ada pengajuan lain (shift_change atau absence) pada tanggal yang sama
**Error Message:** "Anda sudah memiliki pengajuan (tukar shift/tidak masuk kerja) pada tanggal tersebut"

---

## Skenario 2: Exchange (Tukar Shift dengan Teman)

### Field Validations

| Field | Rules |
|-------|-------|
| `date` | `required\|date_format:Y-m-d` |
| `applicant_start_time` | `required\|date_format:H:i` |
| `applicant_end_time` | `required\|date_format:H:i\|after:applicant_start_time` |
| `purpose` | `required\|string\|max:500` |
| `substitute_id` | `required\|integer\|exists:users,id` |
| `substitute_name` | `required\|string\|max:255` |
| `substitute_start_time` | `required\|date_format:H:i` |
| `substitute_end_time` | `required\|date_format:H:i\|after:substitute_start_time` |
| `substitute_purpose` | `required\|string\|max:500` |

### Custom Validations

#### 1-3. Sama seperti Skenario 1 (Tanggal, H-1, Double Request)

#### 4. Pengganti Tidak Boleh Sama dengan Pemohon
```php
// HRRequestController.php:1958-1962
if ($validatedData['substitute_id'] == $user->id) {
    $errors['substitute_name'] = ['Pengganti tidak boleh sama dengan pemohon'];
}
```

**Trigger:** `substitute_id` == ID user yang sedang login
**Error Message:** "Pengganti tidak boleh sama dengan pemohon"

#### 5. Pengganti Harus dari Departemen yang Sama
```php
// HRRequestController.php:1964-1969
if ($validatedData['substitute_department'] !== $validatedData['applicant_department']) {
    $errors['substitute_name'] = ['Pengganti harus dari departemen yang sama dengan pemohon'];
}
```

**Trigger:** `substitute_department` != `applicant_department`
**Error Message:** "Pengganti harus dari departemen yang sama dengan pemohon"

#### 6. Cek Pengganti Sedang Cuti/Izin
```php
// HRRequestController.php:1988-1999
$substituteAbsence = EmployeeRequest::where('employee_id', $validatedData['substitute_id'])
    ->where('request_type', 'absence')
    ->whereIn('status', ['pending', 'supervisor_approved', 'manager_approved', 'hr_approved'])
    ->whereRaw("request_data->>'date_start' <= ?", [$dateToCheck])
    ->whereRaw("request_data->>'date_end' >= ?", [$dateToCheck])
    ->first();
```

**Trigger:** Pengganti memiliki pengajuan absence yang tumpang tindih dengan tanggal tukar shift
**Error Message:** "Pengganti sedang mengajukan cuti/izin pada tanggal tersebut"

#### 7. Cek Pengganti Sudah Ada Tukar Shift
```php
// HRRequestController.php:2001-2010
$substituteShiftChange = EmployeeRequest::where('employee_id', $validatedData['substitute_id'])
    ->where('request_type', 'shift_change')
    ->where('status', '!=', 'cancelled')
    ->whereRaw("request_data->>'date' = ?", [$dateToCheck])
    ->first();
```

**Trigger:** Pengganti sudah memiliki pengajuan tukar shift pada tanggal yang sama
**Error Message:** "Pengganti sudah memiliki pengajuan tukar shift pada tanggal tersebut"

---

## Skenario 3: Holiday (Tukar Shift Hari Merah/Lembur)

### Field Validations

| Field | Rules |
|-------|-------|
| `holiday_work_date` | `required\|date_format:Y-m-d` |
| `compensatory_date` | `required\|date_format:Y-m-d\|after:holiday_work_date` |
| `applicant_start_time` | `required\|date_format:H:i` |
| `applicant_end_time` | `required\|date_format:H:i\|after:applicant_start_time` |
| `purpose` | `required\|string\|max:500` |
| `work_hours` | `required\|numeric\|min:0.5\|max:24` |

### Custom Validations

#### 1-2. Tanggal Kerja Tidak Boleh di Masa Lalu & H-1
Sama seperti skenario sebelumnya, tetapi menggunakan field `holiday_work_date`

#### 3. Cek Pengajuan Ganda pada Tanggal Kerja
```php
// HRRequestController.php:2018-2031
$existingApplicantRequest = EmployeeRequest::where('employee_id', $user->id)
    ->whereIn('request_type', ['shift_change', 'absence'])
    ->where('status', '!=', 'cancelled')
    ->where(function($query) use ($dateToCheck) {
        $query->whereRaw("request_data->>'date' = ?", [$dateToCheck])
              ->orWhereRaw("request_data->>'holiday_work_date' = ?", [$dateToCheck]);
    })
    ->first();
```

**Trigger:** Ada pengajuan lain pada `holiday_work_date`
**Error Message:** "Anda sudah memiliki pengajuan (tukar shift/tidak masuk kerja) pada tanggal tersebut"

#### 4. Cek Pengajuan Ganda pada Tanggal Pengganti (OFF)
```php
// HRRequestController.php:2034-2051
$existingCompensatoryRequest = EmployeeRequest::where('employee_id', $user->id)
    ->whereIn('request_type', ['shift_change', 'absence'])
    ->where('status', '!=', 'cancelled')
    ->whereDate('created_at', '>=', $compensatoryDate)
    ->where(function($query) use ($compensatoryDate) {
        $query->whereRaw("request_data->>'date' = ?", [$compensatoryDate])
              ->orWhereRaw("request_data->>'compensatory_date' = ?", [$compensatoryDate]);
    })
    ->first();
```

**Trigger:** Ada pengajuan lain pada `compensatory_date`
**Error Message:** "Anda sudah memiliki pengajuan pada tanggal pengganti (OFF) tersebut"

---

## Flow Validasi Lengkap

```
1. Basic Field Validation (Laravel Validator)
   ├─ Check required fields
   ├─ Check format (date, time, email, etc.)
   └─ Check basic rules (max, min, after, etc.)

2. Scenario Type Detection
   ├─ self
   ├─ exchange
   └─ holiday

3. Custom Business Validation (per scenario)
   ├─ Date validation (not in past)
   ├─ H-1 deadline validation
   ├─ Double request check
   ├─ Substitute validation (exchange only)
   │  ├─ Not same as applicant
   │  ├─ Same department
   │  ├─ Not on absence
   │  └─ No existing shift change
   └─ Compensatory date validation (holiday only)
      └─ No existing request on compensatory date

4. File Upload Validation
   ├─ Check file type
   ├─ Check file size
   └─ Store file

5. Return validated data or throw ValidationException
```

---

## Contoh Implementasi Validasi di Frontend

### JavaScript Example
```javascript
function validateShiftChange(data) {
    const errors = [];
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const requestDate = new Date(data.date);
    const minDate = new Date(today);
    minDate.setDate(minDate.getDate() + 1); // H-1

    // Validasi tanggal
    if (requestDate < today) {
        errors.push('Tidak bisa mengajukan tukar shift untuk tanggal yang sudah lewat');
    }

    if (requestDate < minDate) {
        const minDateStr = minDate.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        errors.push(`Pengajuan tukar shift harus minimal H-1. Tanggal tukar shift minimal ${minDateStr}`);
    }

    // Validasi skenario exchange
    if (data.scenario_type === 'exchange') {
        if (data.substitute_id === currentUserId) {
            errors.push('Pengganti tidak boleh sama dengan pemohon');
        }

        if (data.substitute_department !== data.applicant_department) {
            errors.push('Pengganti harus dari departemen yang sama dengan pemohon');
        }
    }

    // Validasi skenario holiday
    if (data.scenario_type === 'holiday') {
        const workDate = new Date(data.holiday_work_date);
        const compDate = new Date(data.compensatory_date);

        if (compDate <= workDate) {
            errors.push('Tanggal pengganti harus setelah tanggal kerja di hari merah');
        }
    }

    return errors;
}
```

### Vue.js/React Example
```javascript
const shiftChangeSchema = {
    scenario_type: {
        required: true,
        enum: ['self', 'exchange', 'holiday']
    },
    date: {
        required: true,
        format: 'Y-m-d',
        validator: (value) => {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const requestDate = new Date(value);

            if (requestDate < today) {
                return 'Tidak bisa mengajukan tukar shift untuk tanggal yang sudah lewat';
            }

            const minDate = new Date(today);
            minDate.setDate(minDate.getDate() + 1);

            if (requestDate < minDate) {
                const minDateStr = minDate.toLocaleDateString('id-ID');
                return `Pengajuan tukar shift harus minimal H-1. Tanggal tukar shift minimal ${minDateStr}`;
            }

            return true;
        }
    }
    // ... other fields
};
```

---

## Testing Scenarios

### Test Case 1: Self Shift Change - Valid
```json
{
  "scenario_type": "self",
  "date": "2025-02-05",
  "original_start_time": "08:00",
  "original_end_time": "17:00",
  "new_start_time": "13:00",
  "new_end_time": "22:00"
}
```
**Expected:** PASS ✓

### Test Case 2: Self Shift Change - Past Date
```json
{
  "scenario_type": "self",
  "date": "2025-01-25",
  "original_start_time": "08:00",
  "original_end_time": "17:00",
  "new_start_time": "13:00",
  "new_end_time": "22:00"
}
```
**Expected:** FAIL - "Tidak bisa mengajukan tukar shift untuk tanggal yang sudah lewat"

### Test Case 3: Exchange - Different Department
```json
{
  "scenario_type": "exchange",
  "date": "2025-02-05",
  "substitute_id": 456,
  "substitute_department": "Finance",
  "applicant_department": "IT"
}
```
**Expected:** FAIL - "Pengganti harus dari departemen yang sama dengan pemohon"

### Test Case 4: Holiday - Invalid Compensatory Date
```json
{
  "scenario_type": "holiday",
  "holiday_work_date": "2025-02-01",
  "compensatory_date": "2025-01-30"
}
```
**Expected:** FAIL - "The compensatory date field must be a date after holiday work date"

---

## Error Handling

### Format Response Error
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "date": [
            "Pengajuan tukar shift harus minimal H-1 (1 hari sebelum tanggal tukar shift). Tanggal tukar shift minimal 29/01/2025"
        ],
        "substitute_name": [
            "Pengganti harus dari departemen yang sama dengan pemohon"
        ]
    }
}
```

### HTTP Status Code
- `422 Unprocessable Entity` - Validation failed
- `200 OK` - Validation passed

---

## Best Practices

1. **Validasi di Frontend**: Lakukan validasi dasar di frontend untuk UX yang lebih baik
2. **Validasi di Backend**: Jangan mengandalkan frontend, backend harus tetap validasi
3. **User-Friendly Messages**: Pesan error harus jelas dan dapat dipahami user
4. **Early Validation**: Validasi secepat mungkin (onBlur atau onChange) untuk feedback yang lebih cepat
5. **Display Minimum Date**: Di date picker, set `min-date` ke H+1 untuk mencegah user memilih tanggal yang salah

---

## Referensi

- **Laravel Validation**: [https://laravel.com/docs/validation](https://laravel.com/docs/validation)
- **Carbon Documentation**: [https://carbon.nesbot.com/docs/](https://carbon.nesbot.com/docs/)
- **Controller Code**: [HRRequestController.php:1708-2078](../app/Http/Controllers/HRRequestController.php#L1708-L2078)
