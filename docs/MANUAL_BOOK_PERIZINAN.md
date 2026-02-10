# Manual Book Sistem Perizinan HR

## Daftar Isi
1. [Pengenalan Sistem](#pengenalan-sistem)
2. [Input Perizinan (Tidak Masuk Kerja)](#input-perizinan-tidak-masuk-kerja)
3. [Proses Approval](#proses-approval)
4. [Setting Approval](#setting-approval)
5. [Auto-Approval untuk Supervisor dan Head](#auto-approval-untuk-supervisor-dan-head)
6. [FAQ (Frequently Asked Questions)](#faq)

---

## Pengenalan Sistem

Sistem perizinan HR digunakan untuk mengelola pengajuan tidak masuk kerja (absence) karyawan dengan alur approval yang dapat dikonfigurasi per divisi.

### Jenis Perizinan yang Tersedia:
- **DINAS** - Perjalanan dinas luar kota
- **CUTI TAHUNAN** - Cuti tahunan yang mengurangi jatah cuti
- **CUTI KHUSUS** - Cuti khusus (pernikahan, kematian, dll) yang tidak mengurangi jatah cuti
- **CUTI HAID** - Cuti haid khusus karyawan wanita
- **CUTI HAMIL** - Cuti hamil 90 hari (3 bulan)
- **IJIN** - Izin untuk keperluan mendesak
- **SAKIT** - Izin sakit dengan surat dokter

---

## Input Perizinan (Tidak Masuk Kerja)

### Cara Mengajukan Perizinan

1. **Akses Menu Perizinan**
   - Login ke sistem
   - Pilih menu **HR** → **Pengajuan** → **Buat Pengajuan Baru**
   - Pilih jenis pengajuan: **Tidak Masuk Kerja**

2. **Isi Form Perizinan**

   **Data Pemohon** (Otomatis terisi):
   - Nama
   - Jenis Kelamin
   - Bagian/Divisi

   **Detail Perizinan**:
   - **Jenis Izin**: Pilih jenis perizinan yang sesuai
   - **Durasi**: Masukkan jumlah hari izin
   - **Tanggal Awal**: Pilih tanggal mulai izin
   - **Sampai Tanggal**: Otomatis terhitung berdasarkan durasi
   - **Keperluan**: Alasan tidak masuk kerja

   **Field Tambahan** (Muncul sesuai jenis izin):
   - **Cuti Khusus**: Pilih kategori (Pernikahan, Kematian, dll)
   - **Dinas**: Isi kategori dan detail tujuan dinas
   - **Ijin**: Pilih kategori dan detail keperluan
   - **Cuti Hamil**: Isi HPL (Hari Perkiraan Lahir)
   - **Lampiran**: Upload surat dokter (jika diperlukan)

3. **Deadline Pengajuan**

   Setiap jenis perizinan memiliki deadline yang berbeda:
   - **DINAS**: H-1 (1 hari sebelum tanggal izin)
   - **CUTI TAHUNAN**: H-7 (7 hari sebelum tanggal izin)
   - **CUTI KHUSUS**: Sesuai kategori yang dipilih
   - **CUTI HAID**: Maksimal H+1 (1 hari setelah tanggal izin)
   - **CUTI HAMIL**: Minimal 45 hari sebelum HPL
   - **IJIN**: H-1 sampai H+1
   - **SAKIT**: Dapat diajukan saat sakit atau H+1

4. **Submit Pengajuan**
   - Klik tombol **Simpan** atau **Ajukan**
   - Sistem akan memvalidasi deadline dan data
   - Jika ada hari libur dalam rentang tanggal, sistem akan meminta konfirmasi
   - Pengajuan berhasil dibuat dengan nomor pengajuan

### Catatan Penting:
- Pastikan mengajukan sesuai deadline yang ditentukan
- Lampiran wajib untuk jenis perizinan tertentu (Sakit, Cuti Haid, dll)
- Cuti Tahunan akan mengurangi jatah cuti tahunan Anda
- Cuti Khusus, Dinas, dan Ijin tidak mengurangi jatah cuti tahunan

---

## Proses Approval

### Alur Approval Perizinan

Alur approval untuk perizinan (absence) menggunakan sistem **2 level konfigurasi**:

1. **Approval Setting Global** (`tb_approval_hr_settings`)
   - Menentukan **urutan** approval (approval_order)
   - Level yang tersedia: SPV Division, HEAD Division, Manager, HR

2. **Divisi Approval Setting** (`tb_divisi_approval_settings`)
   - Menentukan level mana yang **aktif** untuk setiap divisi
   - Dapat mengaktifkan/nonaktifkan: SPV, HEAD, Manager
   - HR selalu aktif (tidak bisa dinonaktifkan)

### Urutan Approval (Berdasarkan Approval Order):

1. **SPV Division** (jabatan 5) - Jika enabled
2. **HEAD Division** (jabatan 4) - Jika enabled
3. **Manager** (jabatan 3) - Jika enabled
4. **HR** (divisi 7) - Selalu aktif

### Cara Approve Pengajuan

#### Untuk Supervisor/HEAD/Manager:

1. **Akses Menu Approval**
   - Login dengan akun yang memiliki hak approve
   - Pilih menu **HR** → **Approval** → **Pending Approval**
   - Atau langsung dari dashboard: **Pengajuan Menunggu Approval**

2. **Lihat Detail Pengajuan**
   - Klik pada pengajuan yang ingin di-review
   - Sistem akan menampilkan:
     - Informasi pemohon
     - Detail perizinan
     - Alur approval (status setiap level)
     - Lampiran (jika ada)
     - Riwayat approval

3. **Proses Approval**
   - **Approve**: Klik tombol **Setujui**
     - Untuk absence pertama kali, isi **Pelaksana Tugas** (jika diperlukan)
     - Tambahkan catatan (opsional)
     - Klik **Konfirmasi Setujui**
   
   - **Reject**: Klik tombol **Tolak**
     - Isi alasan penolakan (wajib)
     - Klik **Konfirmasi Tolak**

4. **Status Setelah Approval**
   - Setelah approve, pengajuan akan otomatis lanjut ke level berikutnya
   - Status berubah sesuai level yang sudah approve:
     - `supervisor_approved` - Setelah SPV/HEAD approve
     - `manager_approved` - Setelah Manager approve
     - `hr_approved` - Setelah HR approve (final)

#### Untuk HR:

1. **Akses Menu HR Pending**
   - Login dengan akun HR (divisi 7)
   - Pilih menu **HR** → **Approval** → **HR Pending**

2. **Review Pengajuan**
   - Semua pengajuan yang sudah di-approve atasan akan muncul di sini
   - Review detail pengajuan

3. **Final Approval**
   - Setujui atau tolak pengajuan
   - Setelah HR approve, pengajuan selesai dan status menjadi `hr_approved`

### Status Pengajuan

- **pending** - Menunggu approval level pertama
- **supervisor_approved** - Sudah di-approve SPV/HEAD, menunggu Manager/HR
- **manager_approved** - Sudah di-approve Manager, menunggu HR
- **hr_approved** - Sudah di-approve HR (final)
- **supervisor_rejected** - Ditolak oleh SPV/HEAD
- **manager_rejected** - Ditolak oleh Manager
- **hr_rejected** - Ditolak oleh HR

---

## Setting Approval

### 1. Setting Approval Global (Approval Flow)

**Lokasi**: Menu **HR** → **Setting** → **Approval Settings** → **Approval Flow**

**Fungsi**: Mengatur urutan approval untuk setiap jenis pengajuan

**Cara Setting**:

1. **Akses Halaman Approval Settings**
   - Login sebagai Admin/HR
   - Pilih menu **HR** → **Setting** → **Approval Settings**

2. **Tambah/Edit Approval Level**
   - Klik tombol **Tambah Setting** atau edit setting yang ada
   - Isi form:
     - **Jenis Pengajuan**: Pilih `absence` untuk perizinan
     - **Level Approval**: 
       - `spv_division` - Supervisor Division
       - `head_division` - Head Division
       - `manager` - Manager
       - `hr` - HR
     - **Urutan Approval**: 1, 2, 3, dst (menentukan urutan)
     - **Tipe Approver**: 
       - `role` - Berdasarkan role/jabatan
       - `user` - User spesifik
     - **Role Key**: Kunci role (spv_division, head_division, manager, hr)
     - **Jabatan yang Diizinkan**: Array jabatan yang bisa approve
     - **Status Aktif**: Centang untuk mengaktifkan

3. **Urutan Approval untuk Absence** (Contoh):
   ```
   Order 1: SPV Division (spv_division)
   Order 2: HEAD Division (head_division)
   Order 3: Manager (manager)
   Order 4: HR (hr)
   ```

4. **Simpan Setting**
   - Klik **Simpan**
   - Cache akan otomatis di-clear

**Catatan**:
- Urutan approval sangat penting, pastikan `approval_order` berurutan
- HR harus selalu ada di urutan terakhir
- Setting ini berlaku **global** untuk semua divisi

### 2. Setting Approval Per Divisi

**Lokasi**: Menu **HR** → **Setting** → **Approval Settings** → **Divisi Approval Settings**

**Fungsi**: Mengaktifkan/nonaktifkan level approval untuk setiap divisi

**Cara Setting**:

1. **Akses Halaman Divisi Approval Settings**
   - Login sebagai Admin/HR
   - Pilih menu **HR** → **Setting** → **Approval Settings** → **Divisi**

2. **Pilih Divisi**
   - Pilih divisi yang ingin di-setting
   - Sistem akan menampilkan form setting untuk divisi tersebut

3. **Aktifkan/Nonaktifkan Level**
   - **SPV Enabled**: Centang untuk mengaktifkan level SPV
   - **HEAD Enabled**: Centang untuk mengaktifkan level HEAD
   - **Manager Enabled**: Centang untuk mengaktifkan level Manager
   - **HR**: Selalu aktif (tidak bisa dinonaktifkan)

4. **Preview Approval Chain**
   - Klik **Preview Chain** untuk melihat alur approval yang akan digunakan
   - Sistem akan menampilkan urutan approval berdasarkan:
     - Approval Setting Global (urutan)
     - Divisi Approval Setting (level yang aktif)

5. **Simpan Setting**
   - Klik **Simpan**
   - Cache akan otomatis di-clear

**Contoh Konfigurasi**:

**Divisi A** (Semua level aktif):
```
1. SPV Division ✓
2. HEAD Division ✓
3. Manager ✓
4. HR ✓
```

**Divisi B** (Skip SPV):
```
1. HEAD Division ✓
2. Manager ✓
3. HR ✓
```

**Divisi C** (Hanya HEAD dan HR):
```
1. HEAD Division ✓
2. HR ✓
```

### 3. Cara Kerja Approval Flow untuk Absence

1. **Sistem mengambil Approval Setting Global** untuk `absence`
   - Mendapatkan urutan approval (approval_order)
   - Urutan: SPV → HEAD → Manager → HR

2. **Sistem mengambil Divisi Approval Setting** untuk divisi pemohon
   - Cek level mana yang enabled
   - Skip level yang disabled

3. **Sistem membangun Approval Chain**
   - Hanya level yang enabled yang dimasukkan ke chain
   - Urutan tetap mengikuti approval_order dari global setting

4. **Saat Approval**
   - Sistem cek apakah level sebelumnya sudah approve
   - Jika belum, tidak bisa approve level berikutnya
   - Setelah approve, status dan current_approval_order di-update

---

## Auto-Approval untuk Supervisor dan Head

### Fitur Auto-Approval

Sistem memiliki fitur **auto-approval** untuk memudahkan proses approval:

### 1. Supervisor (Jabatan 5)

**Ketika Supervisor membuat pengajuan absence untuk dirinya sendiri:**
- Sistem otomatis **meng-approve** level supervisor
- Status langsung menjadi `supervisor_approved`
- Tidak perlu menunggu approval dari supervisor lain
- Catatan otomatis: "Auto-approved oleh supervisor"

### 2. Head (Jabatan 4)

**Ketika Head membuat pengajuan absence untuk dirinya sendiri:**
- Sistem otomatis **meng-approve** level head
- Status langsung menjadi `supervisor_approved`
- Tidak perlu menunggu approval dari head lain
- Catatan otomatis: "Auto-approved oleh head"

### 3. Manager (Jabatan 3)

**Ketika Manager membuat pengajuan absence:**
- Sistem **tidak auto-approve**
- Sistem otomatis **meng-set approver** dengan user dari **divisi 13** (hardcode)
- Prioritas: Manager (jabatan 3) dulu, baru Head (jabatan 4)
- Status tetap `pending` sampai di-approve oleh approver dari divisi 13

### Catatan Penting:
- Auto-approval hanya berlaku untuk **absence request**
- Auto-approval hanya untuk **pengajuan sendiri** (supervisor/head)
- Manager tetap harus melalui approval dari divisi 13
- Setelah auto-approve, pengajuan lanjut ke level berikutnya sesuai alur

---

## FAQ (Frequently Asked Questions)

### Q: Bagaimana cara melihat pengajuan saya?
**A**: Login → Menu **HR** → **Pengajuan** → **Daftar Pengajuan Saya**

### Q: Kenapa pengajuan saya ditolak otomatis?
**A**: Kemungkinan:
- Deadline pengajuan sudah lewat
- Data tidak lengkap
- Tidak memenuhi syarat jenis perizinan

### Q: Bagaimana cara membatalkan pengajuan?
**A**: 
- Hanya bisa dibatalkan jika status masih `pending`
- Klik tombol **Batal** pada detail pengajuan

### Q: Kenapa saya tidak bisa approve pengajuan tertentu?
**A**: Kemungkinan:
- Level sebelumnya belum approve
- Bukan approver yang ditentukan untuk pengajuan tersebut
- Pengajuan sudah di-approve/ditolak

### Q: Bagaimana cara mengubah setting approval?
**A**: 
- Login sebagai Admin/HR
- Menu **HR** → **Setting** → **Approval Settings**
- Edit setting yang diinginkan
- **Penting**: Perubahan setting tidak mempengaruhi pengajuan yang sudah dibuat

### Q: Apa bedanya Approval Setting Global dan Divisi?
**A**: 
- **Global**: Menentukan urutan approval (SPV → HEAD → Manager → HR)
- **Divisi**: Menentukan level mana yang aktif untuk divisi tersebut

### Q: Kenapa pengajuan saya stuck di status tertentu?
**A**: 
- Cek apakah approver untuk level tersebut sudah ditentukan
- Cek apakah level sebelumnya sudah approve
- Hubungi HR jika masalah berlanjut

### Q: Bagaimana cara melihat riwayat approval?
**A**: 
- Buka detail pengajuan
- Scroll ke bagian **Alur Approval** atau **Riwayat Approval**
- Sistem menampilkan semua level approval beserta statusnya

### Q: Apakah bisa mengubah approver setelah pengajuan dibuat?
**A**: 
- Tidak bisa diubah secara manual
- Sistem otomatis menentukan approver berdasarkan setting dan divisi

### Q: Bagaimana jika approver tidak tersedia?
**A**: 
- Sistem akan mencari approver lain dengan kriteria yang sama
- Jika tidak ada, pengajuan akan menunggu sampai ada approver yang tersedia
- Hubungi Admin untuk menambahkan approver

---

## Kontak Support

Jika ada pertanyaan atau masalah terkait sistem perizinan, hubungi:
- **Email**: hr@company.com
- **Telepon**: (021) 1234-5678
- **Ext**: 123

---

**Dokumen ini dibuat untuk memudahkan penggunaan sistem perizinan HR.**
**Terakhir di-update**: Januari 2025

