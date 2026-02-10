-- Update script untuk menambah kolom approval di tabel tb_meeting_opps
-- Jalankan script ini jika migration tidak bisa dijalankan

-- Tambah kolom RnD Approval
ALTER TABLE `tb_meeting_opps`
ADD COLUMN `rnd_approval` ENUM('pending', 'approve', 'reject') DEFAULT 'pending' AFTER `rnd_notes`,
ADD COLUMN `rnd_approval_notes` TEXT NULL AFTER `rnd_approval`,
ADD COLUMN `rnd_approved_at` TIMESTAMP NULL AFTER `rnd_approval_notes`,
ADD COLUMN `rnd_approved_by` BIGINT UNSIGNED NULL AFTER `rnd_approved_at`;

-- Tambah kolom Marketing Approval
ALTER TABLE `tb_meeting_opps`
ADD COLUMN `marketing_approval` ENUM('pending', 'approve', 'reject') DEFAULT 'pending' AFTER `rnd_approved_by`,
ADD COLUMN `marketing_approval_notes` TEXT NULL AFTER `marketing_approval`,
ADD COLUMN `marketing_approved_at` TIMESTAMP NULL AFTER `marketing_approval_notes`,
ADD COLUMN `marketing_approved_by` BIGINT UNSIGNED NULL AFTER `marketing_approved_at`;

-- Tambah foreign key constraints
ALTER TABLE `tb_meeting_opps`
ADD CONSTRAINT `fk_meeting_opps_rnd_approved_by`
FOREIGN KEY (`rnd_approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

ALTER TABLE `tb_meeting_opps`
ADD CONSTRAINT `fk_meeting_opps_marketing_approved_by`
FOREIGN KEY (`marketing_approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- Update status_job di tb_job_order_developments (jika diperlukan)
-- Uncomment jika perlu update status enum
-- ALTER TABLE `tb_job_order_developments`
-- MODIFY COLUMN `status_job` ENUM(
--     'DRAFT',
--     'PLANNING',
--     'OPEN',
--     'IN_PROGRESS',
--     'FINISH_PREPRESS',
--     'MEETING_OPP',
--     'READY_FOR_CUSTOMER',
--     'REJECTED_BY_MARKETING',
--     'COMPLETED',
--     'SALES_ORDER_CREATED'
-- ) DEFAULT 'DRAFT';
