@extends('main.layouts.main')
@section('title')
    Panduan Penggunaan Sistem Perizinan
@endsection
@section('css')
    <style>
        .guide-section {
            margin-bottom: 2rem;
        }
        .guide-card {
            border-left: 4px solid #667eea;
            margin-bottom: 1.5rem;
        }
        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            font-weight: bold;
            margin-right: 1rem;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #17a2b8;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
        }
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
        }
        .example-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
        }
        .comparison-table {
            width: 100%;
            margin: 1rem 0;
        }
        .comparison-table th {
            background: #667eea;
            color: white;
            padding: 0.75rem;
            text-align: left;
        }
        .comparison-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #dee2e6;
        }
        .comparison-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .timeline-box {
            background: #fff;
            border: 2px dashed #667eea;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 4px;
        }
    </style>
@endsection
@section('page-title')
    Panduan Penggunaan Sistem Perizinan
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Panduan Penggunaan Sistem Perizinan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hr.requests.index') }}">Pengajuan</a></li>
                    <li class="breadcrumb-item active">Panduan</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h4 class="card-title mb-0 text-white">
                            <i class="mdi mdi-book-open-page-variant me-2"></i>
                            Panduan Lengkap Sistem Perizinan HR
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Pengenalan -->
                        <div class="guide-section">
                            <h5 class="text-primary mb-3">
                                <i class="mdi mdi-information-outline me-2"></i>Pengenalan Sistem
                            </h5>
                            <div class="guide-card card">
                                <div class="card-body">
                                    <p class="lead">Selamat datang di Sistem Perizinan HR! Sistem ini dibuat untuk memudahkan kamu mengajukan izin tidak masuk kerja secara online. Semua pengajuan akan melalui proses persetujuan (approval) yang sudah diatur sesuai divisi kamu.</p>

                                    <div class="info-box mt-3">
                                        <strong><i class="mdi mdi-information me-2"></i>Penting untuk Diketahui:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Semua pengajuan akan tercatat dan terpantau secara real-time</li>
                                            <li>Status pengajuan bisa kamu cek kapan saja</li>
                                            <li>Notifikasi akan dikirim ke email kamu setiap ada update</li>
                                        </ul>
                                    </div>

                                    <h6 class="mt-4 mb-3">📋 Tabel Perbandingan Jenis Izin</h6>
                                    <div class="table-responsive">
                                        <table class="comparison-table">
                                            <thead>
                                                <tr>
                                                    <th>Jenis Izin</th>
                                                    <th>Keterangan</th>
                                                    <th>Deadline Pengajuan</th>
                                                    <th>Mengurangi Cuti Tahunan?</th>
                                                    <th>File Wajib?</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong>DINAS</strong></td>
                                                    <td>Perjalanan dinas ke luar kota</td>
                                                    <td>Minimal 1 hari sebelum</td>
                                                    <td>❌ Tidak</td>
                                                    <td>❌ Tidak</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>CUTI TAHUNAN</strong></td>
                                                    <td>Cuti tahunan sesuai jatah</td>
                                                    <td>Minimal 7 hari sebelumnya</td>
                                                    <td>✅ Ya</td>
                                                    <td>❌ Tidak</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>CUTI KHUSUS</strong></td>
                                                    <td>Pernikahan, kematian, dll</td>
                                                    <td>Minimal 3 hari sebelumnya</td>
                                                    <td>❌ Tidak</td>
                                                    <td>❌ Tidak</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>CUTI HAID</strong></td>
                                                    <td>Khusus karyawan wanita</td>
                                                    <td>Maksimal 1 hari setelah</td>
                                                    <td>❌ Tidak</td>
                                                    <td>✅ Surat dokter</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>CUTI HAMIL</strong></td>
                                                    <td>Cuti hamil 90 hari (3 bulan)</td>
                                                    <td>Minimal 30 hari sebelumnya</td>
                                                    <td>❌ Tidak</td>
                                                    <td>✅ Surat dokter</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>IJIN</strong></td>
                                                    <td>Izin keperluan mendesak</td>
                                                    <td>1 hari sebelum - 1 hari setelah</td>
                                                    <td>❌ Tidak</td>
                                                    <td>❌ Tidak</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>SAKIT</strong></td>
                                                    <td>Izin sakit</td>
                                                    <td>Bisa kapan saja</td>
                                                    <td>❌ Tidak</td>
                                                    <td>✅ Surat dokter (wajib!)</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="example-box mt-3">
                                        <strong><i class="mdi mdi-lightbulb-on me-2"></i>Contoh Deadline:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li><strong>Cuti Tahunan:</strong> Mau cuti tanggal 15 Januari? Ajukan paling lambat tanggal 8 Januari (7 hari sebelumnya)</li>
                                            <li><strong>Dinas:</strong> Mau dinas tanggal 20 Januari? Ajukan paling lambat tanggal 19 Januari (1 hari sebelumnya)</li>
                                            <li><strong>Ijin:</strong> Mau izin tanggal 10 Januari? Bisa ajukan mulai 9 Januari sampai 11 Januari</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cara Mengajukan Perizinan -->
                        <div class="guide-section">
                            <h5 class="text-primary mb-3">
                                <i class="mdi mdi-file-document-edit me-2"></i>Cara Mengajukan Perizinan
                            </h5>

                            <div class="guide-card card">
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-4">
                                        <span class="step-number">1</span>
                                        <div>
                                            <h6>Buka Halaman Pengajuan</h6>
                                            <p class="mb-2">Setelah login, ikuti langkah berikut:</p>
                                            <ol class="mb-0">
                                                <li>Klik menu <strong>HR</strong> di sidebar</li>
                                                <li>Pilih <strong>Pengajuan</strong></li>
                                                <li>Klik tombol <strong>Buat Pengajuan Baru</strong></li>
                                                <li>Pilih <strong>Tidak Masuk Kerja</strong></li>
                                            </ol>
                                            <div class="example-box mt-2">
                                                <small><i class="mdi mdi-information me-1"></i><strong>Tips:</strong> Pastikan kamu sudah login dengan akun yang benar sebelum membuat pengajuan.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start mb-4">
                                        <span class="step-number">2</span>
                                        <div>
                                            <h6>Isi Data Pengajuan</h6>
                                            <p class="mb-2">Data kamu (nama, divisi, NIK, dll) sudah otomatis terisi dari profil. Yang perlu kamu isi manual:</p>
                                            <ul class="mb-2">
                                                <li><strong>Jenis Izin</strong>: Pilih dari dropdown (Dinas, Cuti Tahunan, Cuti Khusus, dll)</li>
                                                <li><strong>Berapa Hari</strong>: Masukkan angka jumlah hari izin yang dibutuhkan</li>
                                                <li><strong>Dari Tanggal</strong>: Klik kalender dan pilih tanggal mulai izin</li>
                                                <li><strong>Sampai Tanggal</strong>: Akan otomatis terisi sesuai jumlah hari yang kamu masukkan</li>
                                                <li><strong>Alasan</strong>: Tuliskan alasan detail kenapa kamu tidak masuk kerja (wajib diisi!)</li>
                                            </ul>
                                            <div class="example-box mt-2">
                                                <small><i class="mdi mdi-lightbulb me-1"></i><strong>Contoh:</strong> "Saya akan mengikuti acara pernikahan adik di Bandung pada tanggal tersebut"</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start mb-4">
                                        <span class="step-number">3</span>
                                        <div>
                                            <h6>Isi Data Tambahan (Jika Diperlukan)</h6>
                                            <p class="mb-2">Tergantung jenis izin yang dipilih, mungkin ada kolom tambahan yang wajib diisi:</p>
                                            <ul class="mb-2">
                                                <li><strong>Cuti Khusus</strong>: Pilih kategorinya dari dropdown (Pernikahan, Kematian, dll)</li>
                                                <li><strong>Dinas</strong>: Isi tujuan dinas (kota/tempat) dan detail keperluannya</li>
                                                <li><strong>Ijin</strong>: Pilih kategori (Keperluan Pribadi, Urusan Keluarga, dll) dan tulis detail keperluannya</li>
                                                <li><strong>Cuti Hamil</strong>: Isi tanggal perkiraan lahir (HPL) sesuai surat dokter</li>
                                                <li><strong>Upload File</strong>:
                                                    <ul>
                                                        <li>Izin Sakit: <strong>WAJIB</strong> upload surat dokter (format PDF/JPG, max 2MB)</li>
                                                        <li>Cuti Haid: <strong>WAJIB</strong> upload surat dokter</li>
                                                        <li>Cuti Hamil: <strong>WAJIB</strong> upload surat dokter kandungan</li>
                                                    </ul>
                                                </li>
                                            </ul>
                                            <div class="warning-box mt-2">
                                                <small><i class="mdi mdi-alert me-1"></i><strong>Peringatan:</strong> Jika file tidak diupload untuk izin yang mewajibkan, pengajuan akan langsung ditolak oleh sistem!</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start mb-4">
                                        <span class="step-number">4</span>
                                        <div>
                                            <h6>Review dan Kirim Pengajuan</h6>
                                            <p class="mb-2">Sebelum mengirim, pastikan:</p>
                                            <ol class="mb-2">
                                                <li>Semua data sudah terisi dengan benar</li>
                                                <li>Deadline pengajuan sudah sesuai (cek tabel di atas)</li>
                                                <li>File sudah diupload (jika wajib)</li>
                                            </ol>
                                            <p class="mb-2">Setelah yakin, klik tombol <strong>Simpan</strong> atau <strong>Ajukan</strong>.</p>
                                            <div class="info-box mt-2">
                                                <small><i class="mdi mdi-information me-1"></i><strong>Yang Terjadi Setelah Klik Ajukan:</strong></small>
                                                <ul class="mb-0 mt-1">
                                                    <li>Sistem akan cek apakah deadline sudah sesuai</li>
                                                    <li>Jika ada hari libur di tanggal yang dipilih, sistem akan menampilkan konfirmasi</li>
                                                    <li>Setelah berhasil, kamu akan mendapat <strong>nomor pengajuan</strong> (simpan nomor ini!)</li>
                                                    <li>Notifikasi akan dikirim ke email kamu</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-box mt-4">
                                        <strong><i class="mdi mdi-lightbulb-on-outline me-2"></i>💡 Tips Penting Buat Kamu:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li><strong>Cek Deadline!</strong> Jangan lupa cek deadline untuk setiap jenis izin sebelum mengajukan</li>
                                            <li><strong>Upload File Wajib:</strong> Kalau izin Sakit, Cuti Haid, atau Cuti Hamil, wajib upload surat dokter (format PDF/JPG, max 2MB)</li>
                                            <li><strong>Cuti Tahunan:</strong> Akan mengurangi jatah cuti tahunan kamu, jadi hitung dengan baik</li>
                                            <li><strong>Tidak Mengurangi Cuti:</strong> Cuti Khusus, Dinas, dan Ijin <strong>tidak</strong> mengurangi jatah cuti tahunan</li>
                                            <li><strong>Hari Libur:</strong> Sistem akan menanyakan konfirmasi jika ada hari libur di tanggal yang dipilih</li>
                                            <li><strong>Simpan Nomor Pengajuan:</strong> Setelah submit, simpan nomor pengajuan untuk tracking</li>
                                        </ul>
                                    </div>

                                    <div class="warning-box mt-3">
                                        <strong><i class="mdi mdi-alert me-2"></i>⚠️ Hal yang Harus Diperhatikan:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Pastikan semua data yang diisi sudah benar sebelum submit (tidak bisa edit setelah submit)</li>
                                            <li>Jika ada kesalahan dan status masih <code>pending</code>, batalkan dan buat pengajuan baru</li>
                                            <li>Jangan lupa cek email untuk notifikasi update status pengajuan</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Proses Approval -->
                        <div class="guide-section">
                            <h5 class="text-primary mb-3">
                                <i class="mdi mdi-check-circle me-2"></i>Proses Approval
                            </h5>

                            <div class="guide-card card">
                                <div class="card-body">
                                    <h6 class="mb-3">📊 Gimana Alur Persetujuannya?</h6>
                                    <p>Setiap pengajuan akan melalui beberapa tahap persetujuan secara berurutan. Urutannya bisa berbeda-beda tergantung divisi kamu, tapi biasanya seperti ini:</p>

                                    <div class="timeline-box">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="step-number" style="width: 35px; height: 35px; font-size: 0.9rem;">1</span>
                                            <div class="ms-2">
                                                <strong>Supervisor (SPV)</strong> - Atasan langsung di divisi kamu
                                                <br><small class="text-muted">Level pertama yang akan meninjau pengajuan</small>
                                            </div>
                                        </div>
                                        <div class="text-center my-2">
                                            <i class="mdi mdi-arrow-down text-primary"></i>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="step-number" style="width: 35px; height: 35px; font-size: 0.9rem;">2</span>
                                            <div class="ms-2">
                                                <strong>Head</strong> - Kepala divisi
                                                <br><small class="text-muted">Setelah SPV approve, lanjut ke Head</small>
                                            </div>
                                        </div>
                                        <div class="text-center my-2">
                                            <i class="mdi mdi-arrow-down text-primary"></i>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="step-number" style="width: 35px; height: 35px; font-size: 0.9rem;">3</span>
                                            <div class="ms-2">
                                                <strong>Manager</strong> - Manager yang bertanggung jawab
                                                <br><small class="text-muted">Setelah Head approve, lanjut ke Manager</small>
                                            </div>
                                        </div>
                                        <div class="text-center my-2">
                                            <i class="mdi mdi-arrow-down text-primary"></i>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="step-number" style="width: 35px; height: 35px; font-size: 0.9rem;">4</span>
                                            <div class="ms-2">
                                                <strong>HR</strong> - Bagian HR (Final Approval)
                                                <br><small class="text-muted"><strong>Ini yang terakhir, selalu ada di semua divisi</strong></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="warning-box mt-3">
                                        <strong><i class="mdi mdi-alert-circle me-2"></i>Penting untuk Diketahui:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Tidak semua level harus approve - tergantung setting divisi kamu, mungkin ada yang dilewati</li>
                                            <li>Jika salah satu level <strong>menolak</strong>, pengajuan langsung ditolak dan tidak lanjut ke level berikutnya</li>
                                            <li>Setiap level punya waktu maksimal untuk approve (biasanya 2-3 hari kerja)</li>
                                            <li>Kamu akan dapat notifikasi email setiap ada update status</li>
                                        </ul>
                                    </div>

                                    <h6 class="mt-4 mb-3">✅ Cara Approve Pengajuan (Untuk Atasan/Approver)</h6>
                                    <p class="mb-3">Jika kamu adalah atasan yang punya hak untuk approve pengajuan, ikuti langkah-langkah berikut:</p>

                                    <div class="d-flex align-items-start mb-4">
                                        <span class="step-number">1</span>
                                        <div>
                                            <h6>Buka Halaman Approval</h6>
                                            <p class="mb-2">Langkah-langkahnya:</p>
                                            <ol class="mb-0">
                                                <li>Login dengan akun yang punya hak approve</li>
                                                <li>Klik menu <strong>HR</strong> di sidebar</li>
                                                <li>Pilih <strong>Approval</strong></li>
                                                <li>Klik <strong>Pending Approval</strong></li>
                                            </ol>
                                            <div class="info-box mt-2">
                                                <small><i class="mdi mdi-information me-1"></i>Di halaman ini, kamu akan melihat daftar semua pengajuan yang menunggu persetujuan kamu.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start mb-4">
                                        <span class="step-number">2</span>
                                        <div>
                                            <h6>Baca Detail Pengajuan dengan Teliti</h6>
                                            <p class="mb-2">Klik pengajuan yang mau di-review. Di halaman detail, kamu akan melihat:</p>
                                            <ul class="mb-2">
                                                <li><strong>Data Pemohon:</strong> Nama, NIK, Divisi, Jabatan</li>
                                                <li><strong>Detail Izin:</strong> Jenis izin, tanggal, jumlah hari, alasan</li>
                                                <li><strong>Alur Approval:</strong> Siapa saja yang harus approve dan statusnya</li>
                                                <li><strong>File yang Diupload:</strong> Surat dokter, dokumen pendukung (jika ada)</li>
                                                <li><strong>Riwayat Approval:</strong> Siapa yang sudah approve/tolak sebelumnya</li>
                                            </ul>
                                            <div class="warning-box mt-2">
                                                <small><i class="mdi mdi-alert me-1"></i><strong>Penting:</strong> Baca semua informasi dengan teliti sebelum memutuskan approve atau tolak!</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-start mb-4">
                                        <span class="step-number">3</span>
                                        <div>
                                            <h6>Setujui atau Tolak Pengajuan</h6>

                                            <div class="success-box mb-3">
                                                <h6 class="mb-2"><i class="mdi mdi-check-circle me-2"></i><strong>Kalau Setuju:</strong></h6>
                                                <ol class="mb-0">
                                                    <li>Klik tombol <strong>Setujui</strong> (biasanya berwarna hijau)</li>
                                                    <li>Isi <strong>siapa yang menggantikan tugas</strong> (jika diperlukan) - ini penting untuk memastikan pekerjaan tetap berjalan</li>
                                                    <li>Tambahkan <strong>catatan</strong> (opsional) - misalnya: "Setuju, pastikan laporan selesai sebelum cuti"</li>
                                                    <li>Klik <strong>Konfirmasi Setujui</strong></li>
                                                </ol>
                                            </div>

                                            <div class="warning-box">
                                                <h6 class="mb-2"><i class="mdi mdi-close-circle me-2"></i><strong>Kalau Tolak:</strong></h6>
                                                <ol class="mb-0">
                                                    <li>Klik tombol <strong>Tolak</strong> (biasanya berwarna merah)</li>
                                                    <li><strong>WAJIB</strong> isi alasan kenapa ditolak - ini penting untuk feedback ke pemohon</li>
                                                    <li>Klik <strong>Konfirmasi Tolak</strong></li>
                                                </ol>
                                                <p class="mb-0 mt-2"><small><strong>Catatan:</strong> Alasan penolakan akan dikirim ke email pemohon, jadi tulis dengan jelas dan profesional.</small></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="success-box mt-4">
                                        <strong><i class="mdi mdi-check-circle me-2"></i>📊 Status Pengajuan Setelah Di-Approve:</strong>
                                        <p class="mb-2 mt-2">Berikut adalah status-status yang akan muncul setelah pengajuan di-approve:</p>
                                        <ul class="mb-2">
                                            <li><strong><code>pending</code></strong> - Pengajuan baru dibuat, menunggu approval</li>
                                            <li><strong><code>supervisor_approved</strong></code> - Sudah disetujui SPV atau Head, lanjut ke level berikutnya</li>
                                            <li><strong><code>manager_approved</code></strong> - Sudah disetujui Manager, lanjut ke HR</li>
                                            <li><strong><code>hr_approved</code></strong> - Sudah disetujui HR (selesai, final! Pengajuan resmi disetujui)</li>
                                            <li><strong><code>rejected</code></strong> - Pengajuan ditolak oleh salah satu approver</li>
                                        </ul>
                                        <div class="example-box mt-2">
                                            <small><i class="mdi mdi-information me-1"></i><strong>Catatan:</strong> Status akan berubah secara otomatis setiap kali ada yang approve. Kamu bisa cek status kapan saja di halaman detail pengajuan.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Auto-Approval -->
                        <div class="guide-section">
                            <h5 class="text-primary mb-3">
                                <i class="mdi mdi-auto-fix me-2"></i>Auto-Approval untuk Supervisor dan Head
                            </h5>

                            <div class="guide-card card">
                                <div class="card-body">
                                    <p class="lead">Ada fitur khusus yang membuat proses approval lebih cepat dan mudah, namanya <strong>Auto-Approval</strong>. Fitur ini akan otomatis menyetujui pengajuan kamu di level tertentu tanpa perlu menunggu approval manual.</p>

                                    <div class="info-box">
                                        <h6><i class="mdi mdi-account-tie me-2"></i>👔 Kalau Kamu Supervisor (Jabatan 5)</h6>
                                        <p class="mb-2">Kalau kamu membuat pengajuan izin untuk diri sendiri, sistem akan <strong>otomatis approve</strong> di level supervisor. Jadi:</p>
                                        <ul class="mb-0">
                                            <li>Status langsung berubah menjadi <code>supervisor_approved</code></li>
                                            <li>Tidak perlu menunggu orang lain approve dulu</li>
                                            <li>Pengajuan langsung lanjut ke level berikutnya (Head/Manager)</li>
                                        </ul>
                                        <div class="example-box mt-2">
                                            <small><i class="mdi mdi-lightbulb me-1"></i><strong>Contoh:</strong> Kamu Supervisor, ajukan cuti tanggal 15 Januari → Status langsung <code>supervisor_approved</code> → Lanjut ke Head untuk approval.</small>
                                        </div>
                                    </div>

                                    <div class="info-box">
                                        <h6><i class="mdi mdi-account-star me-2"></i>⭐ Kalau Kamu Head (Jabatan 4)</h6>
                                        <p class="mb-2">Sama seperti Supervisor, kalau kamu membuat pengajuan untuk diri sendiri, sistem akan <strong>otomatis approve</strong> di level head. Jadi:</p>
                                        <ul class="mb-0">
                                            <li>Status langsung berubah menjadi <code>supervisor_approved</code></li>
                                            <li>Level supervisor dilewati otomatis</li>
                                            <li>Pengajuan langsung lanjut ke level berikutnya (Manager)</li>
                                        </ul>
                                        <div class="example-box mt-2">
                                            <small><i class="mdi mdi-lightbulb me-1"></i><strong>Contoh:</strong> Kamu Head, ajukan dinas tanggal 20 Januari → Status langsung <code>supervisor_approved</code> → Lanjut ke Manager untuk approval.</small>
                                        </div>
                                    </div>

                                    <div class="warning-box">
                                        <h6><i class="mdi mdi-account-key me-2"></i>🔑 Kalau Kamu Manager (Jabatan 3)</h6>
                                        <p class="mb-2">Sayangnya untuk Manager, sistem <strong>tidak auto-approve</strong>. Tapi tenang, ada mekanisme khusus:</p>
                                        <ul class="mb-0">
                                            <li>Sistem akan otomatis set approver-nya ke user dari <strong>divisi 13</strong></li>
                                            <li>Status tetap <code>pending</code> sampai ada yang approve dari divisi 13</li>
                                            <li>Ini untuk memastikan ada kontrol approval untuk level Manager</li>
                                        </ul>
                                        <div class="example-box mt-2">
                                            <small><i class="mdi mdi-information me-1"></i><strong>Catatan:</strong> Jika kamu Manager dan butuh approval cepat, pastikan user divisi 13 aktif dan bisa merespons pengajuan.</small>
                                        </div>
                                    </div>

                                    <div class="success-box mt-3">
                                        <strong><i class="mdi mdi-check-circle me-2"></i>Keuntungan Auto-Approval:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Proses approval lebih cepat - tidak perlu menunggu approval manual</li>
                                            <li>Lebih efisien - mengurangi waktu tunggu</li>
                                            <li>Khusus untuk Supervisor dan Head yang mengajukan untuk diri sendiri</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ -->
                        <div class="guide-section">
                            <h5 class="text-primary mb-3">
                                <i class="mdi mdi-help-circle me-2"></i>Pertanyaan Umum (FAQ)
                            </h5>

                            <div class="guide-card card">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <h6 class="text-primary"><strong>Q: Gimana cara lihat pengajuan yang sudah saya buat?</strong></h6>
                                        <p class="mb-0"><strong>A:</strong> Gampang sekali! Ikuti langkah berikut:</p>
                                        <ol class="mb-0 mt-2">
                                            <li>Login ke sistem</li>
                                            <li>Klik menu <strong>HR</strong> → <strong>Pengajuan</strong></li>
                                            <li>Di halaman tersebut, kamu akan melihat tab <strong>"Semua Pengajuan"</strong></li>
                                            <li>Di situ kamu bisa lihat semua pengajuan kamu dengan filter berdasarkan status (Pending, Disetujui, Ditolak, dll)</li>
                                        </ol>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="text-primary"><strong>Q: Kenapa pengajuan saya langsung ditolak oleh sistem?</strong></h6>
                                        <p class="mb-0"><strong>A:</strong> Pengajuan bisa langsung ditolak otomatis oleh sistem karena beberapa hal:</p>
                                        <ul class="mb-0 mt-2">
                                            <li><strong>Deadline sudah lewat</strong> - Misalnya kamu ajukan Cuti Tahunan kurang dari 7 hari sebelumnya</li>
                                            <li><strong>Data belum lengkap</strong> - Ada kolom wajib yang belum diisi</li>
                                            <li><strong>File tidak diupload</strong> - Untuk izin Sakit, Cuti Haid, atau Cuti Hamil yang wajib surat dokter</li>
                                            <li><strong>Jenis perizinan tidak sesuai</strong> - Misalnya laki-laki mengajukan Cuti Haid</li>
                                            <li><strong>Jatah cuti habis</strong> - Untuk Cuti Tahunan, jika jatah sudah habis</li>
                                        </ul>
                                        <div class="info-box mt-2">
                                            <small><i class="mdi mdi-lightbulb me-1"></i><strong>Tips:</strong> Cek kembali semua data sebelum submit, dan pastikan deadline sudah sesuai!</small>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="text-primary"><strong>Q: Bisa gak sih batalin pengajuan yang sudah dibuat?</strong></h6>
                                        <p class="mb-0"><strong>A:</strong> Bisa, tapi dengan syarat:</p>
                                        <ul class="mb-0 mt-2">
                                            <li>Status pengajuan masih <code>pending</code> (belum ada yang approve)</li>
                                            <li>Klik tombol <strong>Batalkan</strong> di halaman detail pengajuan</li>
                                        </ul>
                                        <div class="warning-box mt-2">
                                            <small><i class="mdi mdi-alert me-1"></i><strong>Penting:</strong> Jika sudah ada yang approve (status sudah berubah), pengajuan tidak bisa dibatalkan. Kamu harus hubungi HR untuk bantuan lebih lanjut.</small>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="text-primary"><strong>Q: Kenapa saya gak bisa approve pengajuan tertentu?</strong></h6>
                                        <p class="mb-0"><strong>A:</strong> Ada beberapa kemungkinan kenapa kamu tidak bisa approve:</p>
                                        <ol class="mb-0 mt-2">
                                            <li><strong>Level sebelumnya belum approve</strong> - Pengajuan masih menunggu approval dari level di atas kamu</li>
                                            <li><strong>Kamu bukan approver yang ditentukan</strong> - Sistem sudah set approver lain untuk pengajuan tersebut</li>
                                            <li><strong>Pengajuan sudah di-approve/ditolak</strong> - Sudah ada yang mengambil tindakan sebelumnya</li>
                                            <li><strong>Pengajuan sudah selesai</strong> - Status sudah <code>hr_approved</code> atau <code>rejected</code></li>
                                        </ol>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="text-primary"><strong>Q: Gimana cara lihat siapa aja yang sudah approve pengajuan saya?</strong></h6>
                                        <p class="mb-0"><strong>A:</strong> Ikuti langkah berikut:</p>
                                        <ol class="mb-0 mt-2">
                                            <li>Buka detail pengajuan (klik pengajuan yang ingin dilihat)</li>
                                            <li>Scroll ke bawah, cari bagian <strong>"Alur Approval"</strong> atau <strong>"Riwayat Approval"</strong></li>
                                            <li>Di situ kamu bisa lihat:
                                                <ul>
                                                    <li>Semua level approval (SPV, Head, Manager, HR)</li>
                                                    <li>Status masing-masing (✅ Sudah Approve, ⏳ Pending, ❌ Ditolak)</li>
                                                    <li>Nama approver yang sudah approve</li>
                                                    <li>Tanggal dan waktu approval</li>
                                                    <li>Catatan dari approver (jika ada)</li>
                                                </ul>
                                            </li>
                                        </ol>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="text-primary"><strong>Q: Kapan pengajuan saya selesai dan resmi disetujui?</strong></h6>
                                        <p class="mb-0"><strong>A:</strong> Pengajuan selesai dan resmi disetujui ketika:</p>
                                        <ul class="mb-0 mt-2">
                                            <li>Status sudah <code>hr_approved</code> (disetujui HR)</li>
                                            <li>Artinya semua level approval sudah approve sampai ke HR</li>
                                            <li>Pengajuan kamu sudah final dan bisa digunakan</li>
                                        </ul>
                                        <div class="success-box mt-2">
                                            <small><i class="mdi mdi-check-circle me-1"></i><strong>Catatan:</strong> Setelah <code>hr_approved</code>, pengajuan tidak bisa dibatalkan lagi. Jika ada perubahan, hubungi HR.</small>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="text-primary"><strong>Q: Apa yang terjadi kalau pengajuan saya ditolak?</strong></h6>
                                        <p class="mb-0"><strong>A:</strong> Jika pengajuan ditolak:</p>
                                        <ul class="mb-0 mt-2">
                                            <li>Status akan berubah menjadi <code>rejected</code></li>
                                            <li>Kamu akan mendapat notifikasi email dengan alasan penolakan</li>
                                            <li>Pengajuan tidak bisa dilanjutkan ke level berikutnya</li>
                                            <li>Kamu bisa membuat pengajuan baru dengan data yang diperbaiki</li>
                                        </ul>
                                        <div class="info-box mt-2">
                                            <small><i class="mdi mdi-information me-1"></i><strong>Tips:</strong> Baca alasan penolakan dengan teliti, perbaiki masalahnya, lalu buat pengajuan baru.</small>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="text-primary"><strong>Q: Berapa lama waktu yang dibutuhkan untuk proses approval?</strong></h6>
                                        <p class="mb-0"><strong>A:</strong> Waktu approval bervariasi tergantung:</p>
                                        <ul class="mb-0 mt-2">
                                            <li>Jumlah level approval yang harus dilalui</li>
                                            <li>Kecepatan masing-masing approver dalam merespons</li>
                                            <li>Biasanya setiap level punya waktu maksimal 2-3 hari kerja</li>
                                            <li>Jika semua level cepat merespons, bisa selesai dalam 1-2 hari</li>
                                        </ul>
                                        <div class="warning-box mt-2">
                                            <small><i class="mdi mdi-clock-alert me-1"></i><strong>Penting:</strong> Karena itu, ajukan izin jauh-jauh hari, terutama untuk Cuti Tahunan yang butuh 7 hari sebelumnya!</small>
                                        </div>
                                    </div>

                                    <div class="mb-0">
                                        <h6 class="text-primary"><strong>Q: Bisa gak saya edit pengajuan yang sudah dibuat?</strong></h6>
                                        <p class="mb-0"><strong>A:</strong> Tidak bisa langsung edit. Tapi kamu bisa:</p>
                                        <ul class="mb-0 mt-2">
                                            <li>Jika status masih <code>pending</code>, kamu bisa <strong>batalkan</strong> pengajuan lalu buat yang baru</li>
                                            <li>Jika sudah ada yang approve, hubungi HR untuk bantuan</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan -->
                        <div class="guide-section">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="mdi mdi-bookmark-check me-2"></i>Ringkasan Singkat
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-success mb-3">✅ Checklist Sebelum Mengajukan:</h6>
                                            <ul>
                                                <li>✓ Cek deadline untuk jenis izin yang dipilih</li>
                                                <li>✓ Siapkan surat dokter (jika wajib)</li>
                                                <li>✓ Pastikan jatah cuti masih tersedia (untuk Cuti Tahunan)</li>
                                                <li>✓ Siapkan alasan yang jelas dan detail</li>
                                                <li>✓ Cek tanggal yang dipilih tidak bentrok dengan jadwal penting</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary mb-3">📝 Alur Singkat:</h6>
                                            <ol>
                                                <li>Buat pengajuan → Isi data lengkap</li>
                                                <li>Submit pengajuan → Dapat nomor pengajuan</li>
                                                <li>Tunggu approval → Cek status secara berkala</li>
                                                <li>Setelah <code>hr_approved</code> → Pengajuan selesai!</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kontak Support -->
                        <div class="guide-section">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="mdi mdi-headset me-2"></i>Kontak Support
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p>Kalau ada pertanyaan, kendala, atau butuh bantuan dengan sistem, jangan ragu untuk hubungi tim support kami. Kami siap membantu kamu!</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul>
                                                <li><strong>📧 Email</strong>: hr@company.com</li>
                                                <li><strong>📞 Telepon</strong>: (021) 1234-5678</li>
                                                <li><strong>🔢 Ext</strong>: 123</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-box">
                                                <small><i class="mdi mdi-clock me-1"></i><strong>Jam Operasional:</strong><br>
                                                Senin - Jumat: 08:00 - 17:00 WIB<br>
                                                Sabtu: 08:00 - 12:00 WIB</small>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mb-0 mt-3"><em>Kami siap membantu kamu, kok! 😊</em></p>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Kembali -->
                        <div class="text-center mt-4">
                            <a href="{{ route('hr.requests.index') }}" class="btn btn-primary btn-lg">
                                <i class="mdi mdi-arrow-left me-2"></i>Kembali ke Halaman Pengajuan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

