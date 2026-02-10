@extends('main.layouts.main')

@section('title')
    Import Data Karyawan
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Import Data Karyawan</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('hr.employee-data.index') }}">Data Karyawan</a></li>
                                <li class="breadcrumb-item active">Import</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Import Data Karyawan dari Excel</h4>
                        </div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if(session('import_errors') && count(session('import_errors')) > 0)
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <h5 class="alert-heading"><i class="mdi mdi-alert me-2"></i>Terjadi Kesalahan Validasi</h5>
                                    <ul class="mb-0">
                                        @foreach(session('import_errors') as $error)
                                            <li>Baris {{ $error['row'] ?? 'N/A' }} - {{ $error['attribute'] ?? 'N/A' }}:
                                                @if(is_array($error['errors']))
                                                    {{ implode(', ', $error['errors']) }}
                                                @else
                                                    {{ $error['errors'] }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <h5 class="alert-heading"><i class="mdi mdi-information me-2"></i>Panduan Import</h5>
                                        <ol class="mb-0">
                                            <li><strong>Download Template:</strong> Klik tombol "Download Template" untuk mendapatkan format Excel yang benar</li>
                                            <li><strong>Isi Data:</strong> Isi data karyawan sesuai dengan format template</li>
                                            <li><strong>Format Tanggal:</strong> Gunakan format YYYY-MM-DD (contoh: 2020-01-15) atau DD/MM/YYYY (contoh: 15/01/2020)</li>
                                            <li><strong>Field Wajib:</strong> NIP dan Nama Karyawan wajib diisi</li>
                                            <li><strong>Auto Calculate:</strong> USIA akan dihitung otomatis dari TGL LAHIR, MASA KERJA akan dihitung dari TGL MASUK</li>
                                            <li><strong>Update Data:</strong> Jika NIP sudah ada, data akan diupdate</li>
                                            <li><strong>Foto:</strong> Foto tidak bisa diimport via Excel, harus diupload manual</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('hr.employee-data.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="file" class="form-label">Pilih File Excel <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                                            id="file" name="file" accept=".xlsx,.xls" required>
                                        <div class="form-text">
                                            Format yang diperbolehkan: XLSX, XLS (Maksimal 10MB)
                                        </div>
                                        @error('file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('hr.employee-data.index') }}" class="btn btn-secondary">
                                                <i class="mdi mdi-arrow-left me-2"></i>Kembali
                                            </a>
                                            <a href="{{ route('hr.employee-data.template') }}" class="btn btn-info">
                                                <i class="mdi mdi-download me-2"></i>Download Template
                                            </a>
                                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                                <i class="mdi mdi-upload me-2"></i>Import Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5 class="card-title">Format Kolom Excel:</h5>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Nama Kolom</th>
                                                            <th>Wajib</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>1</td>
                                                            <td><strong>NIP</strong></td>
                                                            <td><span class="badge bg-danger">Ya</span></td>
                                                            <td>Nomor Induk Pegawai (unik)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>2</td>
                                                            <td><strong>Nama Karyawan</strong></td>
                                                            <td><span class="badge bg-danger">Ya</span></td>
                                                            <td>Nama lengkap karyawan</td>
                                                        </tr>
                                                        <tr>
                                                            <td>3</td>
                                                            <td>LP</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Jenis Kelamin atau Level</td>
                                                        </tr>
                                                        <tr>
                                                            <td>4</td>
                                                            <td>LVL</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Level jabatan</td>
                                                        </tr>
                                                        <tr>
                                                            <td>5</td>
                                                            <td>DEPT</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Departemen</td>
                                                        </tr>
                                                        <tr>
                                                            <td>6</td>
                                                            <td>BAGIAN</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Bagian/Divisi</td>
                                                        </tr>
                                                        <tr>
                                                            <td>7</td>
                                                            <td>TGL MASUK</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Tanggal masuk kerja (format: YYYY-MM-DD atau DD/MM/YYYY)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>8</td>
                                                            <td>STATUS UPDATE</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Status update</td>
                                                        </tr>
                                                        <tr>
                                                            <td>9</td>
                                                            <td>TANGGAL AWAL</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Tanggal awal kontrak (format: YYYY-MM-DD atau DD/MM/YYYY)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>10</td>
                                                            <td>TANGGAL BERAKHIR</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Tanggal berakhir kontrak (format: YYYY-MM-DD atau DD/MM/YYYY)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>11</td>
                                                            <td>MASA KERJA</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Akan dihitung otomatis dari TGL MASUK</td>
                                                        </tr>
                                                        <tr>
                                                            <td>12</td>
                                                            <td>TEMPAT LAHIR</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Tempat lahir</td>
                                                        </tr>
                                                        <tr>
                                                            <td>13</td>
                                                            <td>TGL LAHIR</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Tanggal lahir (format: YYYY-MM-DD atau DD/MM/YYYY)</td>
                                                        </tr>
                                                        <tr>
                                                            <td>14</td>
                                                            <td>USIA</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Akan dihitung otomatis dari TGL LAHIR</td>
                                                        </tr>
                                                        <tr>
                                                            <td>15</td>
                                                            <td>ALAMAT KTP</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Alamat sesuai KTP</td>
                                                        </tr>
                                                        <tr>
                                                            <td>16</td>
                                                            <td>Email</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Alamat email</td>
                                                        </tr>
                                                        <tr>
                                                            <td>17</td>
                                                            <td>No HP</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Nomor handphone</td>
                                                        </tr>
                                                        <tr>
                                                            <td>18</td>
                                                            <td>ALAMAT DOMISILI</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Alamat domisili</td>
                                                        </tr>
                                                        <tr>
                                                            <td>19</td>
                                                            <td>NOMOR KONTAK DARURAT</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Nomor kontak darurat</td>
                                                        </tr>
                                                        <tr>
                                                            <td>20</td>
                                                            <td>AGAMA</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Agama</td>
                                                        </tr>
                                                        <tr>
                                                            <td>21</td>
                                                            <td>PENDIDIKAN</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Tingkat pendidikan</td>
                                                        </tr>
                                                        <tr>
                                                            <td>22</td>
                                                            <td>JURUSAN</td>
                                                            <td><span class="badge bg-secondary">Tidak</span></td>
                                                            <td>Jurusan pendidikan</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        document.getElementById('importForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengimport...';

            // Form akan submit secara normal
            // Jika ada error, akan ditampilkan oleh Laravel
        });
    </script>
@endsection

