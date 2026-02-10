@extends('main.layouts.main')
@section('title')
    Master Mapping Item
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style type="text/css">
        th {
            white-space: nowrap;
            vertical-align: middle !important;
        }

        .information-col {
            max-width: 20%;
            /* sesuaikan lebar */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .namaitem-col {
            max-width: 20%;
            /* sesuaikan lebar */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .optimal-col {
            max-width: 20%;
            /* sesuaikan lebar */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .tinta-col {
            max-width: 20%;
            /* sesuaikan lebar */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
    </style>
@endsection
@section('page-title')
    Master Mapping Item
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Master Mapping Item</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Master</a></li>
                    <li class="breadcrumb-item active">Mapping Item</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col">
                                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal"
                                    data-target="#modal-upload-mapping">
                                    <i class="mdi mdi-upload"></i> Upload Master Mapping
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="datatable-mapping-item"
                                class="table table-hover table-bordered table-responsive-md w-100">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Item</th>
                                        <th>Jumlah</th>
                                        <th>Panjang</th>
                                        <th>Lebar</th>
                                        <th>Gramasi</th>
                                        <th>Kg / pcs</th>
                                        <th>PCS / DC</th>
                                        <th>Speed</th>
                                        <th>Target</th>
                                        <th>Tipe JO</th>
                                        <th>Optimal</th>
                                        <th>Information</th>
                                        <th>Jumlah Tinta</th>
                                        <th>T1</th>
                                        <th>T2</th>
                                        <th>T3</th>
                                        <th>T4</th>
                                        <th>T5</th>
                                        <th>T6</th>
                                        <th>T7</th>
                                        <th>T8</th>
                                        <th>T9</th>
                                        <th>T10</th>
                                        <th>T11</th>
                                        <th>T12</th>
                                        <th>T13</th>
                                        <th>T14</th>
                                        <th>T15</th>
                                        <th>Coating1</th>
                                        <th>Coating2</th>
                                        <th>P. Paper</th>
                                        <th>L. Paper</th>
                                        <th>T. Paper</th>
                                        <th>Die Cut Item</th>
                                        <th>Machine PTG</th>
                                        <th>Machine CTK</th>
                                        <th>Machine HP</th>
                                        <th>Machine UV</th>
                                        <th>Machine EMB</th>
                                        <th>Machine EPL</th>
                                        <th>Machine PLG</th>
                                        <th>Machine KPS</th>
                                        <th>Machine STR</th>
                                        <th>Machine LEM</th>
                                        <th>Machine LMG</th>
                                        <th>Machine LMS</th>
                                        <th>Machine CAL</th>
                                        <th>Machine WBV</th>
                                        <th>Machine EM1</th>
                                        <th>Machine UV1</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Modal Upload Master Mapping --}}
        <div class="modal fade" id="modal-upload-mapping" tabindex="-1" role="dialog"
            aria-labelledby="modal-upload-mapping-label" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="{{ route('mapping-item.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modal-upload-mapping-label">Upload Master Mapping</h5>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><i
                                    class="mdi mdi-close"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="file_upload">Pilih File Excel</label>
                                <input type="file" class="form-control" name="file_upload" id="file_upload"
                                    accept=".xlsx,.xls" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection
    @section('scripts')
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/assets/pages/datatables-demo.js') }}"></script>

        <script>
            $(document).ready(function() {
                $('#datatable-mapping-item').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('mapping-items.data') }}',
                        type: 'POST',
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                        },
                        error: function(xhr, error, thrown) {
                            console.error('DataTables error:', error, thrown);
                            console.log('Response:', xhr.responseText);
                        }
                    },
                    rowId: 'id', // Menambahkan rowId untuk menghindari error DT_RowId
                    pageLength: 25,
                    responsive: true,
                    language: {
                        processing: "Memproses...",
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        zeroRecords: "Tidak ada data yang ditemukan",
                        info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                        infoEmpty: "Tidak ada data yang tersedia",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        }
                    },
                    columns: [{
                            data: 'kode',
                            name: 'kode'
                        },
                        {
                            data: 'nama_barang',
                            name: 'nama_barang',
                            render: function(data, type, row) {
                                return `<span class="namaitem-col">${data || ''}</span>`;
                            }
                        },
                        {
                            data: 'jumlah',
                            name: 'jumlah'
                        },
                        {
                            data: 'panjang',
                            name: 'panjang'
                        },
                        {
                            data: 'lebar',
                            name: 'lebar'
                        },
                        {
                            data: 'gramasi',
                            name: 'gramasi'
                        },
                        {
                            data: 'kg_per_pcs',
                            name: 'kg_per_pcs'
                        },
                        {
                            data: 'pcs_dc', // Sesuaikan dengan migration
                            name: 'pcs_dc'
                        },
                        {
                            data: 'speed',
                            name: 'speed'
                        },
                        {
                            data: 'target',
                            name: 'target'
                        },
                        {
                            data: 'tipe_jo', // Sesuaikan dengan migration
                            name: 'tipe_jo',
                            render: function(data, type, row) {
                                return `<span class="optimal-col">${data || ''}</span>`;
                            }
                        },
                        {
                            data: 'optimal',
                            name: 'optimal',
                            render: function(data, type, row) {
                                return `<span class="optimal-col">${data || ''}</span>`;
                            }
                        },
                        {
                            data: 'information',
                            name: 'information',
                            render: function(data, type, row) {
                                return `<span class="information-col" title="Klik untuk detail">${data || ''}</span>`;
                            }
                        },
                        {
                            data: 'jumlah_warna', // Sesuaikan dengan migration
                            name: 'jumlah_warna'
                        },
                        {
                            data: 't1',
                            name: 't1',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">${data || ''}</span>`;
                            }
                        },
                        {
                            data: 't2',
                            name: 't2',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">${data || ''}</span>`;
                            }
                        },
                        {
                            data: 't3',
                            name: 't3',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">${data || ''}</span>`;
                            }
                        },
                        {
                            data: 't4',
                            name: 't4',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">${data || ''}</span>`;
                            }
                        },
                        {
                            data: 't5',
                            name: 't5',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">${data || ''}</span>`;
                            }
                        },
                        {
                            data: 't6',
                            name: 't6',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">${data || ''}</span>`;
                            }
                        },
                        {
                            data: 't7',
                            name: 't7',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">${data || ''}</span>`;
                            }
                        },
                        {
                            data: null, // Kolom t8-t15 tidak ada di migration
                            name: 't8',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">-</span>`;
                            }
                        },
                        {
                            data: null, // Kolom t9 tidak ada di migration
                            name: 't9',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">-</span>`;
                            }
                        },
                        {
                            data: null, // Kolom t10 tidak ada di migration
                            name: 't10',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">-</span>`;
                            }
                        },
                        {
                            data: null, // Kolom t11 tidak ada di migration
                            name: 't11',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">-</span>`;
                            }
                        },
                        {
                            data: null, // Kolom t12 tidak ada di migration
                            name: 't12',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">-</span>`;
                            }
                        },
                        {
                            data: null, // Kolom t13 tidak ada di migration
                            name: 't13',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">-</span>`;
                            }
                        },
                        {
                            data: null, // Kolom t14 tidak ada di migration
                            name: 't14',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">-</span>`;
                            }
                        },
                        {
                            data: null, // Kolom t15 tidak ada di migration
                            name: 't15',
                            render: function(data, type, row) {
                                return `<span class="tinta-col">-</span>`;
                            }
                        },
                        {
                            data: 'coating1',
                            name: 'coating1'
                        },
                        {
                            data: 'coating2',
                            name: 'coating2'
                        },
                        {
                            data: 'dimensi1', // Sesuaikan dengan migration
                            name: 'dimensi1'
                        },
                        {
                            data: 'dimensi2', // Sesuaikan dengan migration
                            name: 'dimensi2'
                        },
                        {
                            data: 'dimensi3', // Sesuaikan dengan migration
                            name: 'dimensi3'
                        },
                        {
                            data: 'die_cut_item', // Kolom die_cut_item tidak ada di migration
                            name: 'die_cut_item',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_ptg', // Kolom machine tidak ada di migration
                            name: 'm_ptg',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_ctk', // Kolom machine tidak ada di migration
                            name: 'm_ctk',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_hp', // Kolom machine tidak ada di migration
                            name: 'm_hp',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_uv', // Kolom machine tidak ada di migration
                            name: 'm_uv',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_emb', // Kolom machine tidak ada di migration
                            name: 'm_emb',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_epl', // Kolom machine tidak ada di migration
                            name: 'm_epl',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_plg', // Kolom machine tidak ada di migration
                            name: 'm_plg',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_kps', // Kolom machine tidak ada di migration
                            name: 'm_kps',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_str', // Kolom machine tidak ada di migration
                            name: 'm_str',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_lem', // Kolom machine tidak ada di migration
                            name: 'm_lem',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_lmg', // Kolom machine tidak ada di migration
                            name: 'm_lmg',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_lms', // Kolom machine tidak ada di migration
                            name: 'm_lms',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_cal', // Kolom machine tidak ada di migration
                            name: 'm_cal',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_wbv', // Kolom machine tidak ada di migration
                            name: 'm_wbv',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_em1', // Kolom machine tidak ada di migration
                            name: 'm_em1',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        },
                        {
                            data: 'm_uv1', // Kolom machine tidak ada di migration
                            name: 'm_uv1',
                            render: function(data, type, row) {
                                return data || '-';
                            }
                        }
                    ]
                });

                // Handler hapus
                $(document).on('click', '.btn-delete', function() {
                    if (confirm('Yakin ingin menghapus data ini?')) {
                        var id = $(this).data('id');
                        $.ajax({
                            url: '/mapping-items/' + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                $('#datatable-mapping-item').DataTable().ajax.reload();
                            }
                        });
                    }
                });

                $('form[action="{{ route('mapping-item.upload') }}"]').on('submit', function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);

                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: res.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal', xhr.responseJSON?.message || 'Terjadi kesalahan.',
                                'error');
                        }
                    });
                });

                // $(document).on('click', '.information-col', function() {
                //     var info = $(this).text();
                //     Swal.fire({
                //         title: 'Detail Information',
                //         html: `<span style="white-space:pre-wrap;text-align:left;">${info}</span>`,
                //         width: 600
                //     });
                // });


            });
        </script>
    @endsection
