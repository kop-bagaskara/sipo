# Manual Book Sistem Approval HR
## Panduan Lengkap untuk Pengguna

---

## Daftar Isi

1. [Pengenalan Sistem Approval](#pengenalan-sistem-approval)
2. [Jenis-Jenis Pengajuan](#jenis-jenis-pengajuan)
3. [Level-Level Approval](#level-level-approval)
4. [Alur Approval untuk Setiap Jenis Pengajuan](#alur-approval-untuk-setiap-jenis-pengajuan)
5. [Custom Approval Flow](#custom-approval-flow)
6. [Cara Menggunakan Sistem Approval](#cara-menggunakan-sistem-approval)
7. [Status Pengajuan](#status-pengajuan)
8. [Frequently Asked Questions (FAQ)](#frequently-asked-questions-faq)

---

## 1. Pengenalan Sistem Approval

Sistem Approval HR adalah sistem yang digunakan untuk mengelola proses persetujuan berbagai jenis pengajuan karyawan. Sistem ini memastikan bahwa setiap pengajuan melewati tahap persetujuan yang sesuai dengan hierarki organisasi dan kebijakan perusahaan.

### Fitur Utama:
- ✅ Multi-level approval berdasarkan hierarki organisasi
- ✅ Custom approval flow untuk divisi tertentu
- ✅ Tracking status pengajuan secara real-time
- ✅ Notifikasi otomatis untuk approver
- ✅ Riwayat approval yang lengkap
- ✅ Fitur pembatalan approval (disapprove)

---

## 2. Jenis-Jenis Pengajuan

Sistem ini mendukung beberapa jenis pengajuan:

### 2.1. Permohonan Tidak Masuk Kerja (Absence)
Pengajuan untuk izin tidak masuk kerja, termasuk:
- Cuti Tahunan
- Ijin Sakit
- Ijin Lainnya

**Catatan Khusus:**
- Untuk **Cuti Tahunan**, diperlukan pengisian "Pelaksana Tugas" (pengganti)
- Pengisian "Pelaksana Tugas" tidak diperlukan jika pengajuan dilakukan oleh **Manager**

### 2.2. Permohonan Tukar Shift (Shift Change)
Pengajuan untuk menukar jadwal shift kerja.

### 2.3. Permintaan Membawa Kendaraan/Inventaris (Vehicle/Asset)
Pengajuan untuk meminjam kendaraan atau inventaris perusahaan.

### 2.4. Surat Perintah Lembur (Overtime)
Pengajuan untuk lembur (jika tersedia).

---

## 3. Level-Level Approval

Sistem approval memiliki beberapa level persetujuan:

### 3.1. SPV (Supervisor) - Jabatan 5
- Level approval pertama untuk sebagian besar pengajuan
- Dapat diaktifkan/nonaktifkan per divisi untuk pengajuan absence
- Bertanggung jawab untuk menyetujui pengajuan dari karyawan di divisinya

### 3.2. HEAD DIVISI - Jabatan 4
- Level approval setelah SPV (jika SPV diaktifkan)
- Dapat diaktifkan/nonaktifkan per divisi untuk pengajuan absence
- Bertanggung jawab untuk menyetujui pengajuan dari divisinya

### 3.3. MANAGER - Jabatan 3
- Level approval setelah HEAD (jika HEAD diaktifkan)
- Dapat diaktifkan/nonaktifkan per divisi untuk pengajuan absence
- **Catatan Khusus:** Untuk pengajuan yang dibuat oleh Manager sendiri, approval langsung ke General Manager (melewati SPV dan HEAD)

### 3.4. GENERAL MANAGER - Divisi 13
- Level approval khusus untuk:
  - Pengajuan dari **Manager** (jabatan 3)
  - Pengajuan dari **HEAD PRODUKSI** (jabatan 4, divisi 4)
- Bertanggung jawab untuk menyetujui pengajuan strategis

### 3.5. HRD (Human Resources) - Divisi 7
- Level approval terakhir untuk semua jenis pengajuan
- HRD dapat menyetujui pengajuan dari semua divisi
- Bertanggung jawab untuk finalisasi dan pencatatan

---

## 4. Alur Approval untuk Setiap Jenis Pengajuan

### 4.1. Permohonan Tidak Masuk Kerja (Absence)

Alur approval untuk absence **dapat berbeda per divisi** tergantung konfigurasi:

#### Alur Standar (jika semua level diaktifkan):
```
Karyawan → SPV → HEAD DIVISI → MANAGER → HRD
```

#### Alur Tanpa SPV (jika SPV dinonaktifkan):
```
Karyawan → HEAD DIVISI → MANAGER → HRD
```

#### Alur Tanpa HEAD (jika HEAD dinonaktifkan):
```
Karyawan → SPV → MANAGER → HRD
```

#### Alur Tanpa MANAGER (jika MANAGER dinonaktifkan):
```
Karyawan → SPV → HEAD DIVISI → HRD
```

**Catatan:**
- Konfigurasi aktivasi/nonaktivasi level approval untuk absence dapat diatur per divisi
- HRD selalu menjadi level approval terakhir

### 4.2. Permohonan Tukar Shift (Shift Change)

Alur approval untuk shift change **mengikuti konfigurasi global**:

#### Alur Standar:
```
Karyawan → SPV/HEAD/MANAGER (sesuai urutan) → HRD
```

**Catatan:**
- Urutan approval untuk shift change ditentukan oleh konfigurasi di `ApprovalSetting`
- Beberapa level dapat memiliki `allowed_jabatan` yang sama (misalnya: SPV, HEAD, atau MANAGER dapat approve di urutan yang sama)
- HRD selalu menjadi level approval terakhir

### 4.3. Permintaan Membawa Kendaraan/Inventaris

Alur approval untuk vehicle/asset:

#### Untuk Karyawan Biasa:
```
Karyawan → MANAGER → GENERAL MANAGER → HRGA
```

#### Untuk Manager (jabatan 3):
```
Manager → GENERAL MANAGER → HRGA
```

**Catatan:**
- HRGA adalah level approval terakhir untuk vehicle/asset
- General Manager dapat menyetujui pengajuan dari Manager

---

## 5. Custom Approval Flow

Sistem memiliki beberapa custom approval flow untuk kasus khusus:

### 5.1. Custom Flow untuk Manager (Jabatan 3)

**Ketentuan:**
- Jika pengajuan dibuat oleh user dengan **jabatan 3 (Manager)**
- Berlaku untuk semua jenis pengajuan (absence, shift_change, dll)

**Alur Approval:**
```
Manager → GENERAL MANAGER → HRD
```

**Penjelasan:**
- Manager melewati SPV dan HEAD DIVISI
- Approval langsung ke General Manager
- Setelah General Manager approve, baru ke HRD

### 5.2. Custom Flow untuk HEAD PRODUKSI (Jabatan 4, Divisi 4)

**Ketentuan:**
- Jika pengajuan dibuat oleh user dengan **jabatan 4 (HEAD)** dan **divisi 4 (PRODUKSI)**
- Berlaku untuk semua jenis pengajuan (absence, shift_change, dll)

**Alur Approval:**
```
HEAD PRODUKSI → GENERAL MANAGER → HRD
```

**Penjelasan:**
- HEAD PRODUKSI melewati SPV dan MANAGER (karena Manager Produksi tidak ada)
- Approval langsung ke General Manager
- Setelah General Manager approve, baru ke HRD

---

## 6. Cara Menggunakan Sistem Approval

### 6.1. Sebagai Pemohon (Karyawan)

#### Membuat Pengajuan:
1. Login ke sistem
2. Pilih menu **"Form Perizinan"**
3. Pilih jenis pengajuan yang diinginkan:
   - **Permohonan Tidak Masuk Kerja** (untuk absence)
   - **Permohonan Tukar Shift** (untuk shift change)
   - **Pinjam Kendaraan/Inventaris** (untuk vehicle/asset)
4. Isi form pengajuan dengan lengkap
5. **Khusus untuk Cuti Tahunan:** Isi "Pelaksana Tugas" (pengganti)
6. Klik **"Kirim"** atau **"Kirim dengan Konfirmasi"**

#### Melihat Status Pengajuan:
1. Pilih menu **"Form Perizinan"** → **"Riwayat Pengajuan"**
2. Lihat status pengajuan Anda:
   - **Pending:** Menunggu approval
   - **Disetujui:** Sudah disetujui
   - **Ditolak:** Ditolak oleh approver
   - **Dibatalkan:** Dibatalkan oleh Anda

#### Membatalkan Pengajuan:
- Pengajuan hanya dapat dibatalkan jika **belum ada yang approve**
- Klik tombol **"Batal"** pada pengajuan yang ingin dibatalkan

### 6.2. Sebagai Approver

#### Melihat Daftar Pengajuan yang Perlu Approval:
1. Login ke sistem
2. Pilih menu sesuai level approval Anda:
   - **SPV:** "Form Perizinan" → "SPV Pending Approval"
   - **HEAD:** "Form Perizinan" → "HEAD Pending Approval"
   - **Manager:** "Form Perizinan" → "Manager Pending Approval"
   - **General Manager:** "Form Perizinan" → "General Manager Pending Approval"
   - **HRD:** "Form Perizinan" → "HRD Pending Approval"

#### Menyetujui Pengajuan:
1. Klik tombol **"Lihat"** pada pengajuan yang ingin Anda approve
2. Baca detail pengajuan dengan teliti
3. **Khusus untuk Cuti Tahunan (jika Anda adalah HEAD DIVISI pertama yang approve):**
   - Isi "Pelaksana Tugas" (pengganti) jika belum diisi
4. Klik tombol **"Setujui"**
5. (Opsional) Tambahkan catatan jika diperlukan
6. Klik **"Konfirmasi"**

#### Menolak Pengajuan:
1. Klik tombol **"Lihat"** pada pengajuan yang ingin Anda tolak
2. Klik tombol **"Tolak"**
3. **WAJIB** isi alasan penolakan
4. Klik **"Konfirmasi"**

#### Membatalkan Approval (Disapprove):
1. Klik tombol **"Lihat"** pada pengajuan yang sudah Anda approve
2. Klik tombol **"Batalkan"** di bagian "Alur Approval"
3. Konfirmasi pembatalan
4. **Catatan:** Approval hanya dapat dibatalkan jika:
   - Belum ada level approval berikutnya yang approve
   - Pengajuan belum selesai (belum di-approve HRD)

---

## 7. Status Pengajuan

### 7.1. Status yang Tersedia:

| Status | Deskripsi | Dapat Diubah? |
|--------|-----------|---------------|
| **Pending** | Pengajuan baru dibuat, menunggu approval | ✅ Ya (dapat dibatalkan) |
| **Supervisor Approved** | Sudah disetujui oleh SPV | ❌ Tidak (kecuali SPV batalkan) |
| **Head Approved** | Sudah disetujui oleh HEAD | ❌ Tidak (kecuali HEAD batalkan) |
| **Manager Approved** | Sudah disetujui oleh Manager | ❌ Tidak (kecuali Manager batalkan) |
| **HR Approved** | Sudah disetujui oleh HRD (Final) | ❌ Tidak |
| **Supervisor Rejected** | Ditolak oleh SPV | ❌ Tidak |
| **Head Rejected** | Ditolak oleh HEAD | ❌ Tidak |
| **Manager Rejected** | Ditolak oleh Manager | ❌ Tidak |
| **HR Rejected** | Ditolak oleh HRD | ❌ Tidak |
| **Cancelled** | Dibatalkan oleh pemohon | ❌ Tidak |

### 7.2. Badge Status di Tampilan:

- 🟡 **Menunggu [Level]:** Pengajuan menunggu approval dari level tertentu
- 🟢 **Disetujui [Level]:** Sudah disetujui oleh level tertentu
- 🔴 **Ditolak [Level]:** Ditolak oleh level tertentu
- ⚪ **Dibatalkan:** Pengajuan dibatalkan

---

## 8. Frequently Asked Questions (FAQ)

### Q1: Kenapa pengajuan saya tidak muncul di daftar pending approval?
**A:** Beberapa kemungkinan:
- Pengajuan sudah di-approve atau ditolak
- Anda bukan approver yang ditentukan untuk pengajuan tersebut
- Level approval sebelumnya belum approve
- Untuk absence: Level approval Anda dinonaktifkan di konfigurasi divisi

### Q2: Kenapa saya tidak bisa approve pengajuan tertentu?
**A:** Beberapa kemungkinan:
- Level approval sebelumnya belum approve
- Anda bukan approver yang ditentukan untuk pengajuan tersebut
- Pengajuan sudah melewati tahap approval Anda
- Untuk absence: Level approval Anda dinonaktifkan di konfigurasi divisi

### Q3: Apakah saya bisa membatalkan approval yang sudah saya berikan?
**A:** Ya, dengan ketentuan:
- Approval hanya dapat dibatalkan jika belum ada level approval berikutnya yang approve
- Pengajuan belum selesai (belum di-approve HRD)
- Klik tombol **"Batalkan"** di halaman detail pengajuan

### Q4: Kenapa untuk Cuti Tahunan harus mengisi "Pelaksana Tugas"?
**A:** "Pelaksana Tugas" adalah karyawan yang akan menggantikan tugas Anda selama cuti. Ini diperlukan untuk memastikan kontinuitas pekerjaan.

### Q5: Kenapa pengajuan dari Manager langsung ke General Manager?
**A:** Ini adalah custom approval flow khusus untuk Manager. Karena Manager adalah level yang tinggi dalam hierarki, pengajuan mereka langsung ke General Manager tanpa melalui SPV dan HEAD.

### Q6: Kenapa pengajuan dari HEAD PRODUKSI langsung ke General Manager?
**A:** Ini adalah custom approval flow khusus untuk HEAD PRODUKSI. Karena Manager Produksi tidak ada, pengajuan dari HEAD PRODUKSI langsung ke General Manager tanpa melalui SPV dan MANAGER.

### Q7: Apakah saya bisa melihat riwayat approval?
**A:** Ya, di halaman detail pengajuan, terdapat bagian **"Riwayat Approval"** yang menampilkan:
- Siapa yang sudah approve
- Kapan waktu approval
- Catatan dari approver (jika ada)

### Q8: Bagaimana cara mengetahui urutan approval untuk pengajuan saya?
**A:** Di halaman detail pengajuan, terdapat bagian **"Alur Approval"** yang menampilkan:
- Urutan approval (1, 2, 3, dst)
- Level approval (SPV, HEAD, Manager, General Manager, HRD)
- Status setiap level (Pending, Menunggu, Selesai)

### Q9: Apakah ada notifikasi untuk approver?
**A:** Ya, sistem mengirimkan notifikasi email (jika diaktifkan) kepada approver ketika ada pengajuan yang menunggu approval mereka.

### Q10: Bagaimana jika saya salah mengisi form pengajuan?
**A:** Jika pengajuan **belum ada yang approve**, Anda dapat:
- **Edit pengajuan:** Klik tombol "Edit" pada pengajuan
- **Hapus pengajuan:** Klik tombol "Hapus" pada pengajuan

Jika pengajuan **sudah ada yang approve**, Anda tidak dapat mengedit atau menghapus. Hubungi HR untuk bantuan.

---

## 9. Tips dan Best Practices

### Untuk Pemohon:
1. ✅ Isi form pengajuan dengan lengkap dan benar
2. ✅ Ajukan pengajuan **minimal 3 hari** sebelum tanggal yang diminta (untuk cuti)
3. ✅ Pastikan "Pelaksana Tugas" sudah disetujui (untuk Cuti Tahunan)
4. ✅ Periksa status pengajuan secara berkala
5. ✅ Siapkan dokumen pendukung jika diperlukan

### Untuk Approver:
1. ✅ Periksa daftar pending approval secara berkala
2. ✅ Baca detail pengajuan dengan teliti sebelum approve/tolak
3. ✅ Berikan catatan yang jelas jika menolak pengajuan
4. ✅ Approve/tolak pengajuan dalam waktu yang wajar (maksimal 2-3 hari kerja)
5. ✅ Periksa konflik jadwal (untuk shift change) atau beban kerja (untuk cuti)

---

## 10. Kontak Support

Jika Anda mengalami masalah atau memiliki pertanyaan tentang sistem approval, silakan hubungi:

- **Email:** [email HR]
- **Telepon:** [nomor telepon HR]
- **Jam Operasional:** Senin - Jumat, 08:00 - 17:00 WIB

---

## 11. Changelog

### Versi 1.0 (2026)
- ✅ Implementasi sistem approval multi-level
- ✅ Custom approval flow untuk Manager
- ✅ Custom approval flow untuk HEAD PRODUKSI
- ✅ Fitur pembatalan approval (disapprove)
- ✅ Notifikasi email untuk approver
- ✅ Tracking riwayat approval

---

**Dokumen ini dibuat untuk membantu pengguna memahami dan menggunakan sistem approval dengan baik. Jika ada pertanyaan atau saran, silakan hubungi tim HR.**

---

*Terakhir diperbarui: Februari 2026*

