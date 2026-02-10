-- Rollback script untuk menghapus kolom approval di tabel tb_meeting_opps
-- Jalankan script ini jika perlu rollback perubahan

-- Hapus foreign key constraints terlebih dahulu
ALTER TABLE `tb_meeting_opps`
DROP FOREIGN KEY `fk_meeting_opps_rnd_approved_by`;

ALTER TABLE `tb_meeting_opps`
DROP FOREIGN KEY `fk_meeting_opps_marketing_approved_by`;

-- Hapus kolom RnD Approval
ALTER TABLE `tb_meeting_opps`
DROP COLUMN `rnd_approval`,
DROP COLUMN `rnd_approval_notes`,
DROP COLUMN `rnd_approved_at`,
DROP COLUMN `rnd_approved_by`;

-- Hapus kolom Marketing Approval
ALTER TABLE `tb_meeting_opps`
DROP COLUMN `marketing_approval`,
DROP COLUMN `marketing_approval_notes`,
DROP COLUMN `marketing_approved_at`,
DROP COLUMN `marketing_approved_by`;

-- Rollback status_job di tb_job_order_developments (jika diperlukan)
-- Uncomment jika perlu rollback status enum
-- ALTER TABLE `tb_job_order_developments`
-- MODIFY COLUMN `status_job` ENUM(
--     'DRAFT',
--     'PLANNING',
--     'OPEN',
--     'IN_PROGRESS',
--     'COMPLETED'
-- ) DEFAULT 'DRAFT';
