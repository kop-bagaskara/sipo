# Update Meeting OPP Approval Fields

## Deskripsi
Update ini menambahkan kolom approval untuk flow Meeting OPP yang lebih lengkap.

## File yang Dibuat
1. `2025_09_05_084500_add_approval_fields_to_meeting_opps_table.php` - Migration untuk tambah kolom approval
2. `2025_09_05_084600_add_meeting_opp_status_to_job_developments.php` - Migration untuk update status job
3. `update_meeting_opps_approval_fields.sql` - SQL script untuk update manual
4. `rollback_meeting_opps_approval_fields.sql` - SQL script untuk rollback

## Kolom yang Ditambahkan ke `tb_meeting_opps`

### RnD Approval
- `rnd_approval` (ENUM: pending, approve, reject) - Status approval RnD
- `rnd_approval_notes` (TEXT) - Catatan approval RnD
- `rnd_approved_at` (TIMESTAMP) - Waktu approval RnD
- `rnd_approved_by` (BIGINT) - User yang approve (FK ke users)

### Marketing Approval
- `marketing_approval` (ENUM: pending, approve, reject) - Status approval Marketing
- `marketing_approval_notes` (TEXT) - Catatan approval Marketing
- `marketing_approved_at` (TIMESTAMP) - Waktu approval Marketing
- `marketing_approved_by` (BIGINT) - User yang approve (FK ke users)

## Status Job yang Ditambahkan
- `MEETING_OPP` - Setelah meeting OPP disimpan
- `READY_FOR_CUSTOMER` - Setelah marketing approve
- `REJECTED_BY_MARKETING` - Jika marketing reject

## Cara Update

### Opsi 1: Menggunakan Migration (Recommended)
```bash
php artisan migrate
```

### Opsi 2: Manual SQL (Jika migration gagal)
1. Backup database terlebih dahulu
2. Jalankan script `update_meeting_opps_approval_fields.sql`
3. Cek apakah semua kolom berhasil ditambahkan

### Opsi 3: Rollback (Jika ada masalah)
```bash
php artisan migrate:rollback
# atau
# Jalankan script rollback_meeting_opps_approval_fields.sql
```

## Testing
Setelah update, test fitur berikut:
1. Simpan Meeting OPP → Status job berubah jadi MEETING_OPP
2. RnD Approval → Form approval muncul
3. Marketing Approval → Form approval muncul setelah RnD approve
4. Timeline → Semua aktivitas tercatat

## Catatan
- Semua kolom approval default value = 'pending'
- Foreign key constraints akan set NULL jika user dihapus
- Data existing tidak akan terpengaruh
