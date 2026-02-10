<?php $__env->startSection('title'); ?>
    Data Karyawan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        /* Cursor pointer untuk baris tabel */
        #employeeDataTable tbody tr {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        #employeeDataTable tbody tr:hover {
            background-color: #f8f9fa !important;
        }

        /* Modal styling */
        #employeeDetailModal .modal-content {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        #employeeDetailModal .modal-header {
            border-radius: 10px 10px 0 0;
        }

        #employeeDetailModal .card {
            border-radius: 8px;
            transition: transform 0.2s;
        }

        #employeeDetailModal .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        #employeeDetailModal .card-title {
            font-size: 1rem;
            font-weight: 600;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
        }

        #employeeDetailModal table td {
            padding: 0.5rem 0;
            vertical-align: top;
        }

        #employeeDetailModal .badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Data Karyawan</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active">Data Karyawan</li>
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
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Daftar Data Karyawan</h4>
                                <div class="d-flex gap-2">
                                    <a href="<?php echo e(route('hr.employee-data.template')); ?>" class="btn btn-info">
                                        <i class="mdi mdi-download me-2"></i>Download Template
                                    </a>
                                    <a href="<?php echo e(route('hr.employee-data.import')); ?>" class="btn btn-success">
                                        <i class="mdi mdi-upload me-2"></i>Import Excel
                                    </a>
                                    <a href="<?php echo e(route('hr.employee-data.create')); ?>" class="btn btn-primary">
                                        <i class="mdi mdi-plus me-2"></i>Tambah Data Karyawan
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="employeeDataTable" class="table table-bordered table-striped table-hover dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>Foto</th>
                                            <th>NIP</th>
                                            <th>Nama Karyawan</th>
                                            <th>LP</th>
                                            <th>LVL</th>
                                            <th>DEPT</th>
                                            <th>BAGIAN</th>
                                            <th>TGL MASUK</th>
                                            <th>STATUS UPDATE</th>
                                            <th>TANGGAL AWAL</th>
                                            <th>TANGGAL BERAKHIR</th>
                                            <th>MASA KERJA</th>
                                            <th>TEMPAT LAHIR</th>
                                            <th>TGL LAHIR</th>
                                            <th>USIA</th>
                                            <th>ALAMAT KTP</th>
                                            <th>Email</th>
                                            <th>No HP</th>
                                            <th>ALAMAT DOMISILI</th>
                                            <th>NOMOR KONTAK DARURAT</th>
                                            <th>AGAMA</th>
                                            <th>PENDIDIKAN</th>
                                            <th>JURUSAN</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Employee -->
    <div class="modal fade" id="employeeDetailModal" tabindex="-1" role="dialog" aria-labelledby="employeeDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white" id="employeeDetailModalLabel">
                        <i class="mdi mdi-account-circle me-2"></i>Detail Karyawan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"> <i class="mdi mdi-close me-2"></i></button>
                </div>
                <div class="modal-body" id="employeeDetailContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i>Tutup
                    </button>
                    <a href="#" id="editEmployeeBtn" class="btn btn-warning">
                        <i class="mdi mdi-pencil me-1"></i>Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js')); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#employeeDataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('hr.employee-data.data')); ?>",
                    type: "GET"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'foto', name: 'foto', orderable: false, searchable: false },
                    { data: 'nip', name: 'nip' },
                    { data: 'nama_karyawan', name: 'nama_karyawan' },
                    { data: 'lp', name: 'lp' },
                    { data: 'lvl', name: 'lvl' },
                    { data: 'dept', name: 'dept' },
                    { data: 'bagian', name: 'bagian' },
                    { data: 'tgl_masuk', name: 'tgl_masuk' },
                    { data: 'status_update', name: 'status_update' },
                    { data: 'tanggal_awal', name: 'tanggal_awal' },
                    { data: 'tanggal_berakhir', name: 'tanggal_berakhir' },
                    { data: 'masa_kerja', name: 'masa_kerja' },
                    { data: 'tempat_lahir', name: 'tempat_lahir' },
                    { data: 'tgl_lahir', name: 'tgl_lahir' },
                    { data: 'usia', name: 'usia' },
                    { data: 'alamat_ktp', name: 'alamat_ktp' },
                    { data: 'email', name: 'email' },
                    { data: 'no_hp', name: 'no_hp' },
                    { data: 'alamat_domisili', name: 'alamat_domisili' },
                    { data: 'nomor_kontak_darurat', name: 'nomor_kontak_darurat' },
                    { data: 'agama', name: 'agama' },
                    { data: 'pendidikan', name: 'pendidikan' },
                    { data: 'jurusan', name: 'jurusan' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'asc']],
                pageLength: 25,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                scrollX: true,
                responsive: true,
                rowCallback: function(row, data) {
                    // Tambahkan cursor pointer dan event click
                    $(row).css('cursor', 'pointer');
                    $(row).attr('data-employee-id', data.id);
                    $(row).on('click', function(e) {
                        // Jangan trigger jika klik pada action button atau link
                        if ($(e.target).closest('a, button, .btn').length === 0) {
                            const employeeId = $(this).attr('data-employee-id') || data.id;
                            if (employeeId) {
                                showEmployeeDetail(employeeId);
                            }
                        }
                    });
                }
            });

            // Function to show employee detail
            function showEmployeeDetail(employeeId) {
                // Extract ID dari format "row_123" atau langsung ID
                const id = typeof employeeId === 'string' && employeeId.includes('_')
                    ? employeeId.split('_')[1]
                    : employeeId;

                // Reset modal content
                $('#employeeDetailContent').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                `);

                // Show modal
                $('#employeeDetailModal').modal('show');

                // Fetch employee detail
                $.ajax({
                    url: "<?php echo e(route('hr.employee-data.detail', ':id')); ?>".replace(':id', id),
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.data) {
                            const emp = response.data;
                            renderEmployeeDetail(emp);
                            // Set edit button link
                            $('#editEmployeeBtn').attr('href', "<?php echo e(route('hr.employee-data.edit', ':id')); ?>".replace(':id', id));
                        } else {
                            $('#employeeDetailContent').html(`
                                <div class="alert alert-danger">
                                    <i class="mdi mdi-alert-circle me-2"></i>Data karyawan tidak ditemukan
                                </div>
                            `);
                        }
                    },
                    error: function(xhr) {
                        $('#employeeDetailContent').html(`
                            <div class="alert alert-danger">
                                <i class="mdi mdi-alert-circle me-2"></i>Terjadi kesalahan saat memuat data
                            </div>
                        `);
                    }
                });
            }

            // Function to render employee detail
            function renderEmployeeDetail(emp) {
                const foto = emp.foto_path
                    ? `<img src="<?php echo e(asset('storage')); ?>/${emp.foto_path}" alt="Foto" class="img-fluid rounded" style="max-width: 200px; max-height: 200px; object-fit: cover;">`
                    : `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 200px; height: 200px;">
                        <i class="mdi mdi-account-circle" style="font-size: 100px; color: #ccc;"></i>
                       </div>`;

                const formatDate = (date) => {
                    if (!date) return '-';
                    const d = new Date(date);
                    return d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
                };

                const html = `
                    <div class="row">
                        <div class="col-md-12 text-center mb-4">
                            ${foto}
                            <h4 class="mt-3 mb-1">${emp.nama_karyawan || '-'}</h4>
                            <p class="text-muted mb-0">NIP: ${emp.nip || '-'}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="mdi mdi-information-outline me-2"></i>Informasi Personal
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td width="40%"><strong>Jenis Kelamin / Level</strong></td>
                                            <td>${emp.lp || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tempat Lahir</strong></td>
                                            <td>${emp.tempat_lahir || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Lahir</strong></td>
                                            <td>${formatDate(emp.tgl_lahir)}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Usia</strong></td>
                                            <td>${emp.usia ? emp.usia + ' tahun' : '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Agama</strong></td>
                                            <td>${emp.agama || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Pendidikan</strong></td>
                                            <td>${emp.pendidikan || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jurusan</strong></td>
                                            <td>${emp.jurusan || '-'}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="mdi mdi-briefcase-outline me-2"></i>Informasi Pekerjaan
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td width="40%"><strong>Level</strong></td>
                                            <td>${emp.lvl || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Departemen</strong></td>
                                            <td>${emp.dept || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Bagian</strong></td>
                                            <td>${emp.bagian || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Masuk</strong></td>
                                            <td>${formatDate(emp.tgl_masuk)}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Masa Kerja</strong></td>
                                            <td>${emp.masa_kerja || '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status Update</strong></td>
                                            <td><span class="badge badge-info">${emp.status_update || '-'}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Awal Kontrak</strong></td>
                                            <td>${formatDate(emp.tanggal_awal)}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Berakhir Kontrak</strong></td>
                                            <td>${formatDate(emp.tanggal_berakhir)}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="mdi mdi-map-marker-outline me-2"></i>Alamat
                                    </h6>
                                    <p class="mb-2"><strong>Alamat KTP:</strong></p>
                                    <p class="text-muted mb-3">${emp.alamat_ktp || '-'}</p>
                                    <p class="mb-2"><strong>Alamat Domisili:</strong></p>
                                    <p class="text-muted mb-0">${emp.alamat_domisili || '-'}</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="mdi mdi-phone-outline me-2"></i>Kontak
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td width="40%"><strong>Email</strong></td>
                                            <td>${emp.email ? `<a href="mailto:${emp.email}">${emp.email}</a>` : '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>No. HP</strong></td>
                                            <td>${emp.no_hp ? `<a href="tel:${emp.no_hp}">${emp.no_hp}</a>` : '-'}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kontak Darurat</strong></td>
                                            <td>${emp.nomor_kontak_darurat || '-'}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#employeeDetailContent').html(html);
            }

            // Delete function
            window.deleteEmployee = function(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data karyawan akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?php echo e(url('hr/employee-data')); ?>/" + id,
                            type: 'DELETE',
                            data: {
                                _token: "<?php echo e(csrf_token()); ?>"
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Terhapus!',
                                        response.message,
                                        'success'
                                    );
                                    table.ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.message || 'Terjadi kesalahan saat menghapus data.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan saat menghapus data.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            };
        });
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/employee-data/index.blade.php ENDPATH**/ ?>