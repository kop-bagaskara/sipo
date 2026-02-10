# Panduan Membuat Master Approval Setting untuk SPL

## Cara 1: Menggunakan Seeder (Recommended)

Jalankan seeder yang sudah dibuat:
```bash
php artisan db:seed --class=SplApprovalSettingSeeder
```

## Cara 2: Manual via SQL

Jika ingin membuat manual, gunakan query berikut (sesuaikan dengan kebutuhan):

```sql
-- Hapus approval setting SPL yang sudah ada (jika ada)
DELETE FROM tb_approval_hr_settings WHERE request_type = 'spl';

-- Order 1: SPV Division
INSERT INTO tb_approval_hr_settings (
    request_type, approval_level, approval_order, approver_type, role_key, 
    allowed_jabatan, user_id, user_name, user_position, is_active, description,
    created_at, updated_at
) VALUES (
    'spl', 'spv_division', 1, 'role', 'spv_division',
    '[5]', NULL, 'SPV per Divisi', 'SPV', true, 'SPV per Divisi yang menyetujui SPL',
    NOW(), NOW()
);

-- Order 2: HEAD Division
INSERT INTO tb_approval_hr_settings (
    request_type, approval_level, approval_order, approver_type, role_key, 
    allowed_jabatan, user_id, user_name, user_position, is_active, description,
    created_at, updated_at
) VALUES (
    'spl', 'head_division', 2, 'role', 'head_division',
    '[3,4,5]', NULL, 'HEAD per Divisi', 'HEAD/MANAGER/SPV', true, 'HEAD per Divisi yang menyetujui SPL',
    NOW(), NOW()
);

-- Order 3: Manager (opsional, set is_active = false jika tidak diperlukan)
INSERT INTO tb_approval_hr_settings (
    request_type, approval_level, approval_order, approver_type, role_key, 
    allowed_jabatan, user_id, user_name, user_position, is_active, description,
    created_at, updated_at
) VALUES (
    'spl', 'manager', 3, 'role', 'manager',
    '[3]', NULL, 'Manager', 'MANAGER', true, 'Manager yang menyetujui SPL',
    NOW(), NOW()
);

-- Order 4: HRD (final approval)
INSERT INTO tb_approval_hr_settings (
    request_type, approval_level, approval_order, approver_type, role_key, 
    allowed_jabatan, user_id, user_name, user_position, is_active, description,
    created_at, updated_at
) VALUES (
    'spl', 'hr', 4, 'role', 'hr',
    NULL, NULL, 'Semua HRD', 'HRD', true, 'HRD yang menyetujui SPL (final approval)',
    NOW(), NOW()
);
```

## Catatan Penting

1. **request_type**: Harus `'spl'` (lowercase)
2. **approval_order**: Urutan approval (1, 2, 3, 4, dst)
3. **approver_type**: `'role'` untuk role-based, `'user'` untuk user-specific
4. **role_key**: 
   - `'spv_division'` untuk SPV
   - `'head_division'` untuk HEAD
   - `'manager'` untuk Manager
   - `'hr'` untuk HRD
5. **allowed_jabatan**: Array JSON, contoh `[5]` untuk SPV, `[3,4,5]` untuk HEAD
6. **is_active**: `true` untuk aktif, `false` untuk nonaktif (skip level)

## Contoh Approval Flow

### Flow Sederhana (SPV → HRD):
- Order 1: SPV Division
- Order 2: HRD (skip HEAD dan Manager)

### Flow Lengkap (SPV → HEAD → Manager → HRD):
- Order 1: SPV Division
- Order 2: HEAD Division
- Order 3: Manager
- Order 4: HRD

### Flow Tanpa SPV (HEAD → Manager → HRD):
- Order 1: HEAD Division (is_active = true)
- Order 2: Manager (is_active = true)
- Order 3: HRD (is_active = true)
- SPV: is_active = false (akan di-skip)

