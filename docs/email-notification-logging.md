# 📧 Log Notifikasi Email

## Format Log

Semua log notifikasi email sekarang memiliki format yang jelas dan detail dengan separator:

```
========== EMAIL NOTIFICATION START ==========
--- Sending Email to Approver ---
✅ Email SENT Successfully
========== EMAIL NOTIFICATION COMPLETED ==========
```

## Contoh Log Lengkap

### 1. Notifikasi ke Approver Pertama (Request Baru)

```log
[2025-02-05 10:30:15] local.INFO: ========== EMAIL NOTIFICATION START ==========
[2025-02-05 10:30:15] local.INFO: {
    "type": "FIRST_APPROVER_NOTIFICATION",
    "request_id": 123,
    "request_number": "HRD-251105-0001",
    "request_type": "absence",
    "employee_name": "Budi Santoso",
    "employee_email": "budi@krisanthium.com"
}
[2025-02-05 10:30:15] local.INFO: --- Sending Email to Approver ---
[2025-02-05 10:30:15] local.INFO: {
    "to_email": "spv@krisanthium.com",
    "to_name": "Joko SPV",
    "from_email": "sip.krisanthium@gmail.com",
    "from_name": "SIPO - Krisanthium",
    "subject": "🔔 [SIPO] Permohonan Baru Menunggu Approval - HRD-251105-0001 - Budi Santoso"
}
[2025-02-05 10:30:16] local.INFO: ✅ Email SENT Successfully
[2025-02-05 10:30:16] local.INFO: {
    "to": "spv@krisanthium.com",
    "to_name": "Joko SPV",
    "from": "sip.krisanthium@gmail.com",
    "approver_id": 45
}
[2025-02-05 10:30:16] local.INFO: ========== EMAIL NOTIFICATION COMPLETED ==========
[2025-02-05 10:30:16] local.INFO: {
    "request_id": 123,
    "request_number": "HRD-251105-0001",
    "total_approvers": 2,
    "emails_sent": 2,
    "emails_failed": 0,
    "success_rate": "100%"
}
```

### 2. Notifikasi Approved

```log
[2025-02-05 11:00:00] local.INFO: ========== APPROVAL EMAIL NOTIFICATION START ==========
[2025-02-05 11:00:00] local.INFO: {
    "type": "APPROVAL_NOTIFICATION",
    "request_id": 123,
    "request_number": "HRD-251105-0001",
    "approved_by": "Joko SPV",
    "approved_by_id": 45,
    "current_level": "spv_division"
}
[2025-02-05 11:00:00] local.INFO: --- Sending Approval Email to Requester ---
[2025-02-05 11:00:00] local.INFO: {
    "to_email": "budi@krisanthium.com",
    "to_name": "Budi Santoso",
    "from_email": "sip.krisanthium@gmail.com",
    "from_name": "SIPO - Krisanthium",
    "subject": "✅ [SIPO] Permohonan Disetujui - HRD-251105-0001",
    "is_final_approval": false
}
[2025-02-05 11:00:01] local.INFO: ✅ Approval Email SENT to Requester
[2025-02-05 11:00:01] local.INFO: --- Sending Pending Emails to Next Approvers ---
[2025-02-05 11:00:01] local.INFO: {
    "next_approvers_count": 1
}
[2025-02-05 11:00:01] local.INFO: --- Sending Email to Next Approver ---
[2025-02-05 11:00:01] local.INFO: {
    "to_email": "manager@krisanthium.com",
    "to_name": "Joko Manager",
    "from_email": "sip.krisanthium@gmail.com",
    "from_name": "SIPO - Krisanthium",
    "subject": "🔔 [SIPO] Permohonan Baru Menunggu Approval - HRD-251105-0001"
}
[2025-02-05 11:00:02] local.INFO: ✅ Pending Email SENT to Next Approver
[2025-02-05 11:00:02] local.INFO: ========== APPROVAL EMAIL NOTIFICATION COMPLETED ==========
```

### 3. Notifikasi Rejected

```log
[2025-02-05 11:30:00] local.INFO: ========== REJECTION EMAIL NOTIFICATION START ==========
[2025-02-05 11:30:00] local.INFO: {
    "type": "REJECTION_NOTIFICATION",
    "request_id": 123,
    "request_number": "HRD-251105-0001",
    "rejected_by": "Joko SPV",
    "rejected_by_id": 45,
    "current_level": "spv_division",
    "rejection_reason": "Tidak ada pengganti tugas"
}
[2025-02-05 11:30:00] local.INFO: --- Sending Rejection Email to Requester ---
[2025-02-05 11:30:00] local.INFO: {
    "to_email": "budi@krisanthium.com",
    "to_name": "Budi Santoso",
    "from_email": "sip.krisanthium@gmail.com",
    "from_name": "SIPO - Krisanthium",
    "subject": "❌ [SIPO] Permohonan Ditolak - HRD-251105-0001",
    "rejection_reason": "Tidak ada pengganti tugas"
}
[2025-02-05 11:30:01] local.INFO: ✅ Rejection Email SENT to Requester
[2025-02-05 11:30:01] local.INFO: ========== REJECTION EMAIL NOTIFICATION COMPLETED ==========
```

### 4. Error / Gagal Kirim Email

```log
[2025-02-05 12:00:00] local.INFO: ========== EMAIL NOTIFICATION START ==========
[2025-02-05 12:00:00] local.INFO: --- Sending Email to Approver ---
[2025-02-05 12:00:01] local.ERROR: ❌ Email FAILED
[2025-02-05 12:00:01] local.ERROR: {
    "to": "spv@krisanthium.com",
    "to_name": "Joko SPV",
    "from": "sip.krisanthium@gmail.com",
    "error": "Swift_TransportException: Connection could not be established with host smtp.gmail.com [Connection timed out #110]",
    "trace": "..."
}
[2025-02-05 12:00:01] local.INFO: ========== EMAIL NOTIFICATION COMPLETED ==========
[2025-02-05 12:00:01] local.INFO: {
    "request_id": 123,
    "request_number": "HRD-251105-0001",
    "total_approvers": 2,
    "emails_sent": 0,
    "emails_failed": 2,
    "success_rate": "0%"
}
```

## Cara Membaca Log

### ✅ Status Sukses
```
✅ Email SENT Successfully
```
Artinya: Email berhasil dikirim ke tujuan

### ❌ Status Gagal
```
❌ Email FAILED
```
Artinya: Email gagal dikirim, ada error message di bawahnya

### ⚠️ Warning
```
⚠️ Approver has no email
```
Artinya: Approver/requester tidak memiliki alamat email

## Informasi yang Tercatat

Setiap log mencatat:
1. **FROM**: Email pengirim (`sip.krisanthium@gmail.com`)
2. **TO**: Email penerima (approver/requester)
3. **TO_NAME**: Nama penerima
4. **FROM_NAME**: Nama pengirim (`SIPO - Krisanthium`)
5. **SUBJECT**: Subject email lengkap
6. **STATUS**: ✅ Sent / ❌ Failed / ⚠️ Warning
7. **REQUEST_ID**: ID request di database
8. **REQUEST_NUMBER**: Nomor request (contoh: `HRD-251105-0001`)
9. **EMPLOYEE_NAME**: Nama karyawan yang mengajukan
10. **APPROVER_NAME**: Nama approver (untuk approved/rejected)
11. **REJECTION_REASON**: Alasan penolakan (jika direject)
12. **SUCCESS_RATE**: Persentase keberhasilan pengiriman
13. **TOTAL_APPROVERS**: Jumlah approver yang harus menerima email
14. **EMAILS_SENT**: Jumlah email yang berhasil dikirim
15. **EMAILS_FAILED**: Jumlah email yang gagal dikirim

## Cara Melihat Log

### 1. Live Log (Real-time)
```bash
tail -f storage/logs/laravel.log
```

### 2. Filter Hanya Email Notification
```bash
tail -f storage/logs/laravel.log | grep "EMAIL NOTIFICATION"
```

### 3. Filter Hanya Error
```bash
tail -f storage/logs/laravel.log | grep "❌"
```

### 4. Filter Hanya Success
```bash
tail -f storage/logs/laravel.log | grep "✅"
```

### 5. Filter Berdasarkan Request Number
```bash
tail -f storage/logs/laravel.log | grep "HRD-251105-0001"
```

### 6. Cari Log Spesifik
```bash
grep "request_id": 123 storage/logs/laravel.log
```

## Troubleshooting

### Email Tidak Terkirim

Cek error message di log:

1. **Connection timeout**
   - Cek koneksi internet
   - Cek firewall settings
   - Cek apakah SMTP host benar

2. **Authentication failed**
   - Cek MAIL_USERNAME dan MAIL_PASSWORD di .env
   - Untuk Gmail, pastikan menggunakan App Password (bukan password biasa)

3. **Email not valid**
   - Cek apakah alamat email penerima valid
   - Cek apakah email pengirim valid

### Cek Konfigurasi Email

```bash
php artisan config:cache
php artisan config:clear
```

Lihat config yang aktif:
```bash
php artisan config:show mail
```

## Debugging

### Enable Debug Mode untuk Detail Error
Di file `.env`:
```
APP_DEBUG=true
LOG_LEVEL=debug
```

### Test Kirim Email Manual

Buat route test di `routes/web.php`:
```php
Route::get('/test-email', function() {
    $notificationService = new \App\Services\EmployeeRequestNotificationService(new \App\Services\ApprovalService());

    $request = \App\Models\EmployeeRequest::first();
    if ($request) {
        $notificationService->notifyFirstApprovers($request);
        return 'Email sent! Check log.';
    }

    return 'No request found';
});
```

Akses: `http://your-domain.test/test-email`

---

**Tips:**
- Gunakan `tail -f` untuk monitoring real-time
- Filter dengan `grep` untuk fokus ke informasi spesifik
- Cek `success_rate` untuk melihat persentase keberhasilan pengiriman
- Selalu cek log error (❌) jika ada email yang tidak terkirim
