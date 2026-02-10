# Notifikasi Email Employee Request

## 📧 Alur Notifikasi

### 1. Ketika Request Baru Dibuat
- **Penerima:** Approver pertama di approval chain
- **Email:** `EmployeeRequestPendingMail`
- **Template:** `resources/views/emails/employee-request-pending.blade.php`

### 2. Ketika Request Disetujui (Approved)
- **Penerima:**
  - Requester (user yang mengajukan)
  - Approver berikutnya (jika belum final approval)
- **Email:** `EmployeeRequestApprovedMail`
- **Template:** `resources/views/emails/employee-request-approved.blade.php`

### 3. Ketika Request Ditolak (Rejected)
- **Penerima:** Requester (user yang mengajukan)
- **Email:** `EmployeeRequestRejectedMail`
- **Template:** `resources/views/emails/employee-request-rejected.blade.php`

## 📁 File yang Dibuat

### Mail Classes
1. `app/Mail/EmployeeRequestPendingMail.php`
2. `app/Mail/EmployeeRequestApprovedMail.php`
3. `app/Mail/EmployeeRequestRejectedMail.php`

### Service
4. `app/Services/EmployeeRequestNotificationService.php`

### Email Templates
5. `resources/views/emails/employee-request-pending.blade.php`
6. `resources/views/emails/employee-request-approved.blade.php`
7. `resources/views/emails/employee-request-rejected.blade.php`

## 🔧 File yang Diupdate

### Controllers
- `app/Http/Controllers/HRRequestController.php`
  - Method `store()` - Menambahkan notifikasi ke approver pertama
  - Method `storeWithConfirmation()` - Menambahkan notifikasi ke approver pertama

### Services
- `app/Services/ApprovalService.php`
  - Method `processApproval()` - Menambahkan notifikasi approved/rejected
  - Method `sendApprovalNotification()` - Method baru untuk mengirim notifikasi

## ⚙️ Konfigurasi Email

Email sudah terkonfigurasi dengan:
- **Driver:** SMTP
- **Host:** smtp.gmail.com
- **Port:** 587
- **Username:** sip.krisanthium@gmail.com
- **Encryption:** TLS
- **From Address:** sip.krisanthium@gmail.com
- **From Name:** SIP - Krisanthium

## 🚀 Penggunaan

Notifikasi akan dikirim secara otomatis:

1. **Otomatis saat request dibuat:**
   ```php
   // Di HRRequestController.php
   $notificationService = new EmployeeRequestNotificationService($approvalService);
   $notificationService->notifyFirstApprovers($employeeRequest);
   ```

2. **Otomatis saat approve/reject:**
   ```php
   // Di ApprovalService.php
   $this->sendApprovalNotification($request, $roleKey, $status, $approver, $notes);
   ```

## 📝 Log

Semua aktivitas notifikasi di-log ke `storage/logs/laravel.log`:
- Pengiriman notifikasi ke approver
- Notifikasi approval
- Notifikasi rejection
- Error jika gagal mengirim

## 🎨 Fitur Email Template

### Desain Responsif
- Mobile-friendly design
- Warna dan branding yang konsisten
- Layout yang jelas dan mudah dibaca

### Konten
- Header dengan ikon yang sesuai (🔔 pending, ✅ approved, ❌ rejected, 🎉 final approval)
- Informasi lengkap request (nomor, tipe, pemohon, detail)
- Alur approval dengan visualisasi step
- Informasi approver (nama, jabatan, waktu)
- Alasan penolakan (jika direject)

### Warna Theme
- **Pending:** Ungu (#667eea)
- **Approved:** Hijau (#28a745)
- **Final Approval:** Kuning Emas (#ffc107)
- **Rejected:** Merah (#dc3545)

## 🔍 Troubleshooting

### Cek Log Email
```bash
tail -f storage/logs/laravel.log | grep -i "notification\|mail"
```

### Test Kirim Email
```php
$notificationService = new EmployeeRequestNotificationService($approvalService);
$notificationService->notifyFirstApprovers($employeeRequest);
```

### Queue Worker (jika menggunakan queue)
```bash
php artisan queue:work
```

## ⚠️ Catatan Penting

1. **Error Handling:** Jika email gagal dikirim, proses approval tetap berjalan (tidak mengganggu flow)
2. **Multiple Approvers:** Semua approver di level yang sama akan menerima email
3. **Auto-approval:** Untuk SPV/Head yang create request, notifikasi akan ke approver berikutnya
4. **Final Approval:** Pada final approval (HRD), requester mendapatkan email "🎉 Selamat!"

## 📊 Contoh Email

### Pending (ke Approver)
Subject: `🔔 [SIPO] Permohonan Baru Menunggu Approval - HRD-251105-0001 - Budi Santoso`

### Approved (ke Requester)
Subject: `✅ [SIPO] Permohonan Disetujui - HRD-251105-0001`

### Final Approved (ke Requester)
Subject: `🎉 [SIPO] Permohonan Disetujui Semua - HRD-251105-0001`

### Rejected (ke Requester)
Subject: `❌ [SIPO] Permohonan Ditolak - HRD-251105-0001`

---

**Dibuat:** 5 Februari 2026
**Version:** 1.0
