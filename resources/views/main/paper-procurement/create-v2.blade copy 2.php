@extends('main.layouts.main')
@section('title')
    Buat Pengajuan Meeting Kertas
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .excel-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e0e0e0;
        }

        .excel-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 30px;
            color: #1a1a1a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding-bottom: 15px;
            border-bottom: 3px solid #4472C4;
        }

        .excel-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        .excel-table th {
            background: linear-gradient(135deg, #4472C4 0%, #5b8fd8 100%);
            color: white;
            padding: 14px 10px;
            text-align: center;
            border: 1px solid #2c5282;
            font-weight: 700;
            vertical-align: middle;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
        }

        .excel-table th:hover {
            background: linear-gradient(135deg, #5b8fd8 0%, #4472C4 100%);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(68, 114, 196, 0.3);
        }

        /* Header kolom kertas harus menggunakan warna kertas masing-masing */
        .excel-table th.col-paper {
            color: #000 !important;
        }

        .excel-table td {
            padding: 10px 8px;
            border: 1px solid #d1d5db;
            text-align: center;
            vertical-align: middle;
            transition: all 0.2s ease;
        }

        .excel-table tbody tr:hover td {
            background-color: rgba(68, 114, 196, 0.05);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        /* PASTIKAN TIDAK ADA CSS YANG OVERRIDE WARNA CELL */
        /* Semua warna akan di-set via inline style dengan !important */
        .excel-table tbody tr.item-row td,
        .excel-table tbody tr.item-row td.col-paper,
        .excel-table tbody tr.item-row td.col-product,
        .excel-table tbody tr.item-row td.col-month,
        .excel-table tbody tr.item-row td.col-total,
        table.excel-table tbody tr.item-row td,
        table.excel-table tbody tr.item-row td.col-paper,
        #workspace-table tbody tr.item-row td,
        #workspace-table tbody tr.item-row td.col-paper {
            /* Warna akan di-set via inline style dengan !important - JANGAN SET DI SINI */
            background-image: none !important;
        }

        /* OVERRIDE SEMUA CSS YANG MUNGKIN MENGOVERRIDE - dengan spesifisitas sangat tinggi */
        table#workspace-table.excel-table tbody tr.item-row td.col-paper,
        table#workspace-table.excel-table tbody tr.item-row td.col-paper *,
        table#workspace-table.excel-table tbody tr.item-row td.col-paper::before,
        table#workspace-table.excel-table tbody tr.item-row td.col-paper::after {
            background-image: none !important;
        }

        /* Override untuk Bootstrap table classes */
        .table table#workspace-table.excel-table tbody tr.item-row td.col-paper,
        .table-responsive table#workspace-table.excel-table tbody tr.item-row td.col-paper,
        .table-bordered table#workspace-table.excel-table tbody tr.item-row td.col-paper {
            background-image: none !important;
        }

        /* CSS dengan spesifisitas tinggi untuk memastikan warna tidak di-override */
        #workspace-table tbody tr.item-row td.col-paper[data-bg-color] {
            background-image: none !important;
        }

        /* Override untuk Bootstrap atau framework CSS lain yang mungkin meng-override */
        table#workspace-table tbody tr.item-row td.col-paper,
        table#workspace-table tbody tr.item-row td.col-paper[style*="background"] {
            background-image: none !important;
        }

        /* CSS untuk memastikan warna dari data attribute tidak di-override */
        /* Hapus - tidak digunakan, semua warna di-set via inline style dengan !important */
        /* PASTIKAN tidak ada CSS yang override warna cell */
        /* JANGAN SET background-color DI SINI - semua akan di-set via inline style dengan !important */
        /* HAPUS SEMUA CSS YANG BISA OVERRIDE - termasuk dari header */
        .excel-table tbody tr.item-row td.col-paper,
        .excel-table tbody tr.item-row td.col-paper *,
        table.excel-table tbody tr.item-row td.col-paper,
        #workspace-table tbody tr.item-row td.col-paper,
        #workspace-table tbody tr.item-row td,
        #workspace-table tbody tr.item-row td *,
        #workspace-table tbody tr.item-row td.col-paper[data-paper-type],
        #workspace-table tbody tr.item-row td.col-paper:empty {
            /* Hapus background-image yang mungkin di-set oleh CSS lain */
            background-image: none !important;
            /* JANGAN SET background-color DI SINI - akan di-set via inline style */
        }

        /* Override khusus untuk cell kosong - pastikan tidak ada warna dari header */
        #workspace-table tbody tr.item-row td.col-paper:has(span:contains('-')) {
            /* Warna akan di-set via inline style dengan !important */
        }

        .excel-table .col-product {
            text-align: left;
            padding-left: 8px;
            font-weight: 500;
            width: 200px;
            min-width: 200px;
            /* JANGAN SET background-color DI SINI - akan di-set via inline style */
        }

        .excel-table .col-month {
            width: 100px;
            text-align: right;
        }

        .excel-table .col-total {
            width: 120px;
            text-align: right;
            /* JANGAN SET background-color DI SINI - akan di-set via inline style */
            font-weight: 600;
        }

        .excel-table .col-paper {
            width: 180px;
            text-align: center;
            /* Warna akan di-set via inline style dengan !important */
        }

        .item-row .col-paper {
            /* Warna akan di-set via inline style dengan !important */
        }

        /* CSS dengan spesifisitas SANGAT TINGGI untuk cell kertas yang digunakan */
        /* Pastikan inline style bisa override CSS class kertas */
        /* Hapus semua background dari CSS untuk cell yang digunakan - biarkan inline style yang mengatur */
        #workspace-table tbody tr td.paper-cell-used,
        table#workspace-table tbody tr td.paper-cell-used,
        table#workspace-table.excel-table tbody tr td.paper-cell-used,
        .table table#workspace-table.excel-table tbody tr td.paper-cell-used,
        .table-responsive table#workspace-table.excel-table tbody tr td.paper-cell-used,
        body table#workspace-table.excel-table tbody tr td.paper-cell-used,
        html body table#workspace-table.excel-table tbody tr td.paper-cell-used,
        /* Override semua class kertas untuk cell yang digunakan */
        #workspace-table tbody tr td.paper-cell-used.tg-paper-dpc250,
        #workspace-table tbody tr td.paper-cell-used.tg-paper-ivory230-ikdp-vr,
        #workspace-table tbody tr td.paper-cell-used.tg-paper-ivory230-spn,
        #workspace-table tbody tr td.paper-cell-used.tg-paper-ivory230-ik-vr,
        #workspace-table tbody tr td.paper-cell-used.tg-paper-ivory-sinar-vanda-220 {
            /* Hapus semua background dari CSS - biarkan inline style yang mengatur */
            background-color: unset !important;
            background: unset !important;
            background-image: none !important;
        }

        /* CSS untuk cell kertas yang belum menemukan kertas yang digunakan */
        /* Cell ini harus menggunakan warna baris produk, bukan warna kertas asli */
        /* Hapus semua background dari CSS untuk memastikan inline style bisa bekerja */
        #workspace-table tbody tr td[data-before-used-paper="true"],
        table#workspace-table tbody tr td[data-before-used-paper="true"],
        #workspace-table tbody tr td[data-paper-type]:not(.paper-cell-used):not([data-before-used-paper="false"]),
        table#workspace-table tbody tr td[data-paper-type]:not(.paper-cell-used):not([data-before-used-paper="false"]) {
            /* Hapus semua background dari CSS - biarkan inline style yang mengatur */
            background-color: unset !important;
            background: unset !important;
            background-image: none !important;
        }

        /* Card Container untuk 2 tabel terpisah */
        .workspace-card-container {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .excel-input {
            width: 100%;
            border: none;
            background: transparent;
            text-align: right;
            padding: 8px 12px;
            font-size: 15px;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-weight: 600;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .excel-input:focus {
            outline: 2px solid #0078D4;
            background-color: #E7F3FF;
            box-shadow: 0 0 0 3px rgba(0, 120, 212, 0.1);
            transform: scale(1.02);
        }

        .excel-input:hover {
            background-color: rgba(0, 120, 212, 0.05);
        }

        .excel-input:read-only {
            background-color: #E7E6E6;
            cursor: not-allowed;
        }

        /* Style untuk input di section stock/procurement */
        .stock-procurement-input {
            font-weight: inherit;
        }

        .stock-procurement-input:focus {
            outline: 2px solid #0078D4;
            background-color: #E7F3FF !important;
        }

        /* Style untuk select dropdown di COVER SAMPAI */
        .cover-sampai-cell select {
            cursor: pointer;
        }

        .cover-sampai-cell select:focus {
            outline: 2px solid #0078D4;
            background-color: #E7F3FF !important;
        }

        /* Style untuk span di preview workspace */
        .excel-table td span {
            display: block;
            text-align: right;
            padding: 6px 8px;
            font-size: 15px;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-weight: bold;
        }

        /* Paper header styling sudah didefinisikan di bawah */

        .paper-input {
            width: 100%;
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: right;
            font-size: 15px;
            font-weight: bold;
            margin-top: 4px;
        }

        .formula-cell {
            background-color: inherit;
            /* Ikuti warna baris */
        }

        .item-row .formula-cell {
            background-color: inherit !important;
            /* Ikuti warna baris */
        }

        .add-item-form {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .add-item-form .form-group {
            margin-bottom: 1rem;
        }

        .add-item-form .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            color: #333;
        }

        /* Pastikan Select2 memiliki ukuran yang konsisten */
        .add-item-form .select2-container {
            width: 100% !important;
        }

        .add-item-form .select2-container--default .select2-selection--single,
        .add-item-form .select2-container--default .select2-selection--multiple {
            height: 38px !important;
            min-height: 38px !important;
            max-height: none !important;
        }

        .add-item-form .select2-container--default .select2-selection--multiple {
            min-height: 38px !important;
        }

        .add-item-form .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            padding-top: 4px;
            padding-bottom: 4px;
            min-height: 28px;
        }

        .paper-checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }

        .paper-checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 12px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .paper-checkbox-item input[type="checkbox"] {
            margin: 0;
        }

        .item-row {
            background-color: #fff;
        }

        .item-row {
            transition: all 0.3s ease;
        }

        .item-row:hover {
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.9) !important;
        }

        /* CSS class untuk setiap warna baris - HANYA untuk cell non-kertas */
        .row-color-blue {
            background-color: #D9E1F2 !important;
        }

        /* HANYA cell yang TIDAK memiliki class tg-paper-* yang menggunakan warna baris */
        .row-color-blue td:not([class*="tg-paper-"]) {
            background-color: #D9E1F2 !important;
        }

        .row-color-yellow {
            background-color: #FFE699 !important;
        }

        /* HANYA cell yang TIDAK memiliki class tg-paper-* yang menggunakan warna baris */
        .row-color-yellow td:not([class*="tg-paper-"]) {
            background-color: #FFE699 !important;
        }

        /* Pastikan semua cell dalam baris memiliki warna yang sama */
        .item-row td {
            /* Warna akan di-set via inline style dengan !important */
        }

        /* Kolom total ikuti warna baris */
        .item-row .col-total {
            /* Warna akan di-set via inline style dengan !important */
        }

        /* Kolom kertas - SETIAP cell menggunakan class kertas masing-masing, BUKAN warna baris */
        /* PASTIKAN class kertas bisa override warna baris */
        .item-row td[class*="tg-paper-"],
        tr td[class*="tg-paper-"],
        .tg tr td[class*="tg-paper-"] {
            /* Warna akan di-set via class kertas masing-masing dengan !important */
        }

        .items-list-table {
            margin-bottom: 0;
        }

        .paper-badge {
            display: inline-block;
            padding: 4px 8px;
            margin: 2px;
            background-color: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 4px;
            font-size: 11px;
        }

        /* Styling untuk multiple tabel (satu per item) */
        #workspace-container {
            margin-top: 20px;
        }

        .item-table-wrapper {
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            overflow: hidden;
        }

        .item-table-wrapper:last-child {
            margin-bottom: 0;
        }

        .item-table-wrapper .excel-table {
            margin-bottom: 0;
        }

        /* Styling tabel seperti contoh - menggunakan class tg */
        .tg {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
        }

        .tg td,
        .tg th {
            border-color: #d1d5db;
            border-style: solid;
            border-width: 1px;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 15px;
            overflow: hidden;
            padding: 12px 10px;
            word-break: normal;
            text-align: left;
            vertical-align: middle;
            transition: all 0.2s ease;
        }

        .tg th {
            font-weight: 700;
            background: linear-gradient(135deg, #4472C4 0%, #5b8fd8 100%);
            color: white;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.3px;
        }

        .tg th:hover {
            background: linear-gradient(135deg, #5b8fd8 0%, #4472C4 100%);
        }

        /* Class default untuk cell tanpa warna khusus */
        .tg-0pky {
            border-color: inherit;
            text-align: left;
            vertical-align: top;
        }

        /* Class untuk warna kertas - akan ditambahkan dinamis */
        /* PASTIKAN class kertas bisa override warna baris (rowClass) dengan !important */
        /* Setiap cell di kolom kertas HARUS menggunakan class kertas masing-masing */
        /* Spesifisitas SANGAT TINGGI untuk override rowClass - HARUS di atas semua CSS lain */
        /* PRIORITAS: Class kertas HARUS override rowClass, bahkan jika rowClass adalah class kertas lain */
        table#workspace-table.tg tbody tr td.tg-paper-dpc250,
        table#workspace-table.tg tbody tr td.tg-paper-dpc250[class*="tg-paper-"],
        table#workspace-table.tg tbody tr td.tg-paper-dpc250[class*="tg-0pky"],
        .tg-paper-dpc250,
        td.tg-paper-dpc250,
        th.tg-paper-dpc250,
        .tg td.tg-paper-dpc250,
        .tg th.tg-paper-dpc250,
        tr td.tg-paper-dpc250,
        tr th.tg-paper-dpc250,
        table.tg td.tg-paper-dpc250,
        table.tg th.tg-paper-dpc250,
        #workspace-table td.tg-paper-dpc250,
        #workspace-table th.tg-paper-dpc250,
        /* Override rowClass di cell kertas - spesifisitas sangat tinggi */
        td.tg-0pky.tg-paper-dpc250,
        td.tg-paper-ivory230-ikdp-vr.tg-paper-dpc250,
        td.tg-paper-ivory230-spn.tg-paper-dpc250,
        td.tg-paper-ivory230-ik-vr.tg-paper-dpc250,
        table.tg td.tg-0pky.tg-paper-dpc250,
        table.tg td.tg-paper-ivory230-ikdp-vr.tg-paper-dpc250,
        table.tg td.tg-paper-ivory230-spn.tg-paper-dpc250,
        table.tg td.tg-paper-ivory230-ik-vr.tg-paper-dpc250,
        #workspace-table td.tg-0pky.tg-paper-dpc250,
        #workspace-table td.tg-paper-ivory230-ikdp-vr.tg-paper-dpc250,
        #workspace-table td.tg-paper-ivory230-spn.tg-paper-dpc250,
        #workspace-table td.tg-paper-ivory230-ik-vr.tg-paper-dpc250,
        table#workspace-table.tg tbody tr td.tg-0pky.tg-paper-dpc250,
        table#workspace-table.tg tbody tr td.tg-paper-ivory230-ikdp-vr.tg-paper-dpc250,
        table#workspace-table.tg tbody tr td.tg-paper-ivory230-spn.tg-paper-dpc250,
        table#workspace-table.tg tbody tr td.tg-paper-ivory230-ik-vr.tg-paper-dpc250 {
            background-color: #fff2cc !important;
            /* Kuning/cream - sama dengan Carton Juara */
            background: #fff2cc !important;
            /* Kuning/cream */
            border-color: inherit;
            text-align: left;
            vertical-align: top;
            color: #000 !important;
        }

        .tg th.tg-paper-dpc250,
        tr th.tg-paper-dpc250,
        table.tg th.tg-paper-dpc250 {
            background-color: #fff2cc !important;
            /* Kuning/cream - sama dengan Carton Juara */
            background: #fff2cc !important;
            /* Kuning/cream */
            border-color: inherit;
            text-align: center !important;
            /* Header harus center */
            vertical-align: top;
            color: #000 !important;
        }

        .tg-paper-ivory230-ikdp-vr,
        td.tg-paper-ivory230-ikdp-vr,
        th.tg-paper-ivory230-ikdp-vr,
        .tg td.tg-paper-ivory230-ikdp-vr,
        .tg th.tg-paper-ivory230-ikdp-vr,
        tr td.tg-paper-ivory230-ikdp-vr,
        tr th.tg-paper-ivory230-ikdp-vr,
        table.tg td.tg-paper-ivory230-ikdp-vr,
        table.tg th.tg-paper-ivory230-ikdp-vr,
        #workspace-table td.tg-paper-ivory230-ikdp-vr,
        #workspace-table th.tg-paper-ivory230-ikdp-vr,
        /* Override rowClass di cell kertas */
        td.tg-0pky.tg-paper-ivory230-ikdp-vr,
        td.tg-paper-dpc250.tg-paper-ivory230-ikdp-vr,
        td.tg-paper-ivory230-spn.tg-paper-ivory230-ikdp-vr,
        td.tg-paper-ivory230-ik-vr.tg-paper-ivory230-ikdp-vr,
        table.tg td.tg-0pky.tg-paper-ivory230-ikdp-vr,
        table.tg td.tg-paper-dpc250.tg-paper-ivory230-ikdp-vr,
        table.tg td.tg-paper-ivory230-spn.tg-paper-ivory230-ikdp-vr,
        table.tg td.tg-paper-ivory230-ik-vr.tg-paper-ivory230-ikdp-vr,
        #workspace-table td.tg-0pky.tg-paper-ivory230-ikdp-vr,
        #workspace-table td.tg-paper-dpc250.tg-paper-ivory230-ikdp-vr,
        #workspace-table td.tg-paper-ivory230-spn.tg-paper-ivory230-ikdp-vr,
        #workspace-table td.tg-paper-ivory230-ik-vr.tg-paper-ivory230-ikdp-vr {
            background-color: #d5e8d4 !important;
            /* Hijau muda - sama dengan Pack Packaging Juara */
            background: #d5e8d4 !important;
            /* Hijau muda */
            border-color: inherit;
            text-align: left;
            vertical-align: top;
            color: #000 !important;
        }

        .tg th.tg-paper-ivory230-ikdp-vr,
        tr th.tg-paper-ivory230-ikdp-vr,
        table.tg th.tg-paper-ivory230-ikdp-vr {
            background-color: #d5e8d4 !important;
            /* Hijau muda - sama dengan Pack Packaging Juara */
            background: #d5e8d4 !important;
            /* Hijau muda */
            border-color: inherit;
            text-align: center !important;
            /* Header harus center */
            vertical-align: top;
            color: #000 !important;
        }

        .tg-paper-ivory230-spn,
        td.tg-paper-ivory230-spn,
        th.tg-paper-ivory230-spn,
        .tg td.tg-paper-ivory230-spn,
        .tg th.tg-paper-ivory230-spn,
        tr td.tg-paper-ivory230-spn,
        tr th.tg-paper-ivory230-spn,
        table.tg td.tg-paper-ivory230-spn,
        table.tg th.tg-paper-ivory230-spn,
        #workspace-table td.tg-paper-ivory230-spn,
        #workspace-table th.tg-paper-ivory230-spn,
        /* Override rowClass di cell kertas */
        td.tg-0pky.tg-paper-ivory230-spn,
        td.tg-paper-dpc250.tg-paper-ivory230-spn,
        td.tg-paper-ivory230-ikdp-vr.tg-paper-ivory230-spn,
        td.tg-paper-ivory230-ik-vr.tg-paper-ivory230-spn,
        table.tg td.tg-0pky.tg-paper-ivory230-spn,
        table.tg td.tg-paper-dpc250.tg-paper-ivory230-spn,
        table.tg td.tg-paper-ivory230-ikdp-vr.tg-paper-ivory230-spn,
        table.tg td.tg-paper-ivory230-ik-vr.tg-paper-ivory230-spn,
        #workspace-table td.tg-0pky.tg-paper-ivory230-spn,
        #workspace-table td.tg-paper-dpc250.tg-paper-ivory230-spn,
        #workspace-table td.tg-paper-ivory230-ikdp-vr.tg-paper-ivory230-spn,
        #workspace-table td.tg-paper-ivory230-ik-vr.tg-paper-ivory230-spn {
            background-color: #d5e8d4 !important;
            /* Hijau muda - sama dengan Pack Packaging Juara */
            background: #d5e8d4 !important;
            /* Hijau muda */
            border-color: inherit;
            text-align: left;
            vertical-align: top;
            color: #000 !important;
        }

        .tg th.tg-paper-ivory230-spn,
        tr th.tg-paper-ivory230-spn,
        table.tg th.tg-paper-ivory230-spn {
            background-color: #d5e8d4 !important;
            /* Hijau muda - sama dengan Pack Packaging Juara */
            background: #d5e8d4 !important;
            /* Hijau muda */
            border-color: inherit;
            text-align: center !important;
            /* Header harus center */
            vertical-align: top;
            color: #000 !important;
        }

        /* IVORY 230 IK VR - ORANGE - sama dengan Inner Frame Juara */
        /* Override semua kemungkinan selector yang bisa meng-override warna */
        /* PRIORITAS TERTINGGI - HARUS SELALU ORANGE TIDAK PEDULI ROW COLOR APAPUN */
        /* Selector paling spesifik - HARUS di atas CSS untuk class kertas lain */
        /* SPESIFISITAS SANGAT TINGGI untuk override rowClass */
        table#workspace-table.tg tbody tr td.tg-paper-ivory230-ik-vr,
        table#workspace-table.tg tbody tr td.tg-paper-ivory230-ik-vr[class*="tg-paper-"],
        table#workspace-table.tg tbody tr td.tg-paper-ivory230-ik-vr[class*="tg-0pky"],
        table#workspace-table.tg tbody tr td.tg-paper-dpc250.tg-paper-ivory230-ik-vr,
        table#workspace-table.tg tbody tr td.tg-0pky.tg-paper-ivory230-ik-vr,
        td.tg-paper-ivory230-ik-vr,
        th.tg-paper-ivory230-ik-vr,
        .tg td.tg-paper-ivory230-ik-vr,
        .tg th.tg-paper-ivory230-ik-vr,
        table.tg td.tg-paper-ivory230-ik-vr,
        table.tg th.tg-paper-ivory230-ik-vr,
        #workspace-table td.tg-paper-ivory230-ik-vr,
        #workspace-table th.tg-paper-ivory230-ik-vr,
        tr td.tg-paper-ivory230-ik-vr,
        tr th.tg-paper-ivory230-ik-vr,
        .tg tr td.tg-paper-ivory230-ik-vr,
        .tg tr th.tg-paper-ivory230-ik-vr,
        .row-color-blue td.tg-paper-ivory230-ik-vr,
        .row-color-yellow td.tg-paper-ivory230-ik-vr,
        /* Override jika cell memiliki class kertas lain juga */
        td.tg-paper-dpc250.tg-paper-ivory230-ik-vr,
        td.tg-paper-ivory230-ikdp-vr.tg-paper-ivory230-ik-vr,
        td.tg-paper-ivory230-spn.tg-paper-ivory230-ik-vr,
        td.tg-paper-ivory-sinar-vanda-220.tg-paper-ivory230-ik-vr,
        /* Override rowClass yang mungkin di-apply ke cell (spesifisitas sangat tinggi) */
        table.tg tr td.tg-paper-dpc250.tg-paper-ivory230-ik-vr,
        table.tg tr td.tg-0pky.tg-paper-ivory230-ik-vr,
        #workspace-table tr td.tg-paper-dpc250.tg-paper-ivory230-ik-vr,
        #workspace-table tr td.tg-0pky.tg-paper-ivory230-ik-vr,
        table#workspace-table.tg tr td.tg-paper-dpc250.tg-paper-ivory230-ik-vr,
        table#workspace-table.tg tr td.tg-0pky.tg-paper-ivory230-ik-vr {
            background-color: #ffe6cc !important;
            /* Orange/peach - sama dengan Inner Frame Juara */
            background: #ffe6cc !important;
            /* Orange/peach */
            border-color: inherit;
            text-align: left;
            vertical-align: top;
            color: #000 !important;
        }

        .tg th.tg-paper-ivory230-ik-vr,
        table.tg th.tg-paper-ivory230-ik-vr,
        #workspace-table th.tg-paper-ivory230-ik-vr,
        tr th.tg-paper-ivory230-ik-vr,
        .tg tr th.tg-paper-ivory230-ik-vr {
            text-align: center !important;
            /* Header harus center */
        }

        .tg-paper-ivory-sinar-vanda-220,
        td.tg-paper-ivory-sinar-vanda-220,
        th.tg-paper-ivory-sinar-vanda-220,
        .tg td.tg-paper-ivory-sinar-vanda-220,
        .tg th.tg-paper-ivory-sinar-vanda-220,
        tr td.tg-paper-ivory-sinar-vanda-220,
        tr th.tg-paper-ivory-sinar-vanda-220,
        table.tg td.tg-paper-ivory-sinar-vanda-220,
        table.tg th.tg-paper-ivory-sinar-vanda-220,
        #workspace-table td.tg-paper-ivory-sinar-vanda-220,
        #workspace-table th.tg-paper-ivory-sinar-vanda-220,
        /* Override rowClass di cell kertas */
        td.tg-0pky.tg-paper-ivory-sinar-vanda-220,
        td.tg-paper-dpc250.tg-paper-ivory-sinar-vanda-220,
        td.tg-paper-ivory230-ikdp-vr.tg-paper-ivory-sinar-vanda-220,
        td.tg-paper-ivory230-spn.tg-paper-ivory-sinar-vanda-220,
        td.tg-paper-ivory230-ik-vr.tg-paper-ivory-sinar-vanda-220,
        table.tg td.tg-0pky.tg-paper-ivory-sinar-vanda-220,
        table.tg td.tg-paper-dpc250.tg-paper-ivory-sinar-vanda-220,
        table.tg td.tg-paper-ivory230-ikdp-vr.tg-paper-ivory-sinar-vanda-220,
        table.tg td.tg-paper-ivory230-spn.tg-paper-ivory-sinar-vanda-220,
        table.tg td.tg-paper-ivory230-ik-vr.tg-paper-ivory-sinar-vanda-220,
        #workspace-table td.tg-0pky.tg-paper-ivory-sinar-vanda-220,
        #workspace-table td.tg-paper-dpc250.tg-paper-ivory-sinar-vanda-220,
        #workspace-table td.tg-paper-ivory230-ikdp-vr.tg-paper-ivory-sinar-vanda-220,
        #workspace-table td.tg-paper-ivory230-spn.tg-paper-ivory-sinar-vanda-220,
        #workspace-table td.tg-paper-ivory230-ik-vr.tg-paper-ivory-sinar-vanda-220 {
            background-color: #dbeafe !important;
            /* Biru muda - sama dengan Esse Cigar/India */
            background: #dbeafe !important;
            /* Biru muda */
            border-color: inherit;
            text-align: left;
            vertical-align: top;
            color: #000 !important;
        }

        .tg th.tg-paper-ivory-sinar-vanda-220 {
            background-color: #dbeafe !important;
            /* Biru muda - sama dengan Esse Cigar/India */
            border-color: inherit;
            text-align: center !important;
            /* Header harus center */
            vertical-align: top;
            color: #000 !important;
        }

        /* CSS untuk section stock dan procurement details di bawah TOTAL */
        .stock-detail-row td,
        .total-stok-row td,
        .minus-paper-row td,
        .cover-sampai-row td {
            border: 1px solid #d1d5db;
            padding: 12px 10px;
            font-size: 15px;
            font-weight: 700;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .stock-detail-row:hover td,
        .total-stok-row:hover td,
        .minus-paper-row:hover td,
        .cover-sampai-row:hover td {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        .stock-detail-cell,
        .total-stok-cell,
        .minus-paper-cell,
        .cover-sampai-cell {
            border: 1px solid #000;
            padding: 8px;
            font-size: 15px;
            font-weight: bold;
        }

        /* Pastikan input di stock rows juga bold dan memiliki padding */
        .stock-detail-row input,
        .total-stok-row input,
        .minus-paper-row input {
            font-weight: bold !important;
            padding: 6px 8px !important;
            font-size: 15px !important;
        }

        /* Pastikan label cells memiliki font size yang lebih besar */
        .total-stok-row td:first-child,
        .minus-paper-row td:first-child,
        .cover-sampai-row td:first-child {
            font-size: 15px !important;
        }

        /* Styling untuk tombol di location rows */
        .stock-detail-row .btn-danger,
        .btn-primary.btn-sm {
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stock-detail-row .btn-danger:hover,
        .btn-primary.btn-sm:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .stock-detail-row .btn-danger:active,
        .btn-primary.btn-sm:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Select dropdown styling */
        select.form-control {
            border-radius: 6px;
            border: 1px solid #d1d5db;
            transition: all 0.2s ease;
            height: 38px !important;
            min-height: 38px !important;
        }

        select.form-control:focus {
            border-color: #0078D4;
            box-shadow: 0 0 0 3px rgba(0, 120, 212, 0.1);
        }

        /* Select2 styling untuk konsistensi ukuran */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            height: 38px !important;
            min-height: 38px !important;
            border-radius: 6px;
            border: 1px solid #d1d5db;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px;
            padding-right: 20px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 8px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            padding: 4px 8px;
            min-height: 28px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            margin-top: 4px;
            margin-bottom: 4px;
        }

        /* Paper header styling */
        .paper-header {
            font-size: 13px;
            line-height: 1.4;
            padding: 8px 6px;
            font-weight: 700;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.1);
        }

        .paper-code {
            font-size: 13px;
            color: #666;
            margin-top: 2px;
            font-weight: 600;
        }

        /* Table wrapper dengan scroll yang lebih halus */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        /* Smooth scrollbar */
        .table-responsive::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 5px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 5px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Warna untuk baris total - HARUS override semua class kertas */
        /* Spesifisitas tinggi untuk memastikan warna kuning selalu ter-apply di baris TOTAL */
        .tg-total,
        td.tg-total,
        th.tg-total,
        .tg td.tg-total,
        .tg th.tg-total,
        table.tg td.tg-total,
        table.tg th.tg-total,
        #workspace-table td.tg-total,
        #workspace-table th.tg-total,
        tr td.tg-total,
        tr th.tg-total,
        /* Override class kertas di baris TOTAL */
        td.tg-total.tg-paper-dpc250,
        td.tg-total.tg-paper-ivory230-ikdp-vr,
        td.tg-total.tg-paper-ivory230-spn,
        td.tg-total.tg-paper-ivory230-ik-vr,
        td.tg-total.tg-paper-ivory-sinar-vanda-220,
        table.tg td.tg-total.tg-paper-dpc250,
        table.tg td.tg-total.tg-paper-ivory230-ikdp-vr,
        table.tg td.tg-total.tg-paper-ivory230-spn,
        table.tg td.tg-total.tg-paper-ivory230-ik-vr,
        table.tg td.tg-total.tg-paper-ivory-sinar-vanda-220,
        #workspace-table td.tg-total.tg-paper-dpc250,
        #workspace-table td.tg-total.tg-paper-ivory230-ikdp-vr,
        #workspace-table td.tg-total.tg-paper-ivory230-spn,
        #workspace-table td.tg-total.tg-paper-ivory230-ik-vr,
        #workspace-table td.tg-total.tg-paper-ivory-sinar-vanda-220 {
            background-color: #FFE699 !important;
            /* Yellow - HARUS kuning di baris TOTAL */
            background: #FFE699 !important;
            /* Yellow - HARUS kuning di baris TOTAL */
            font-weight: bold;
            color: #000 !important;
        }

        /* FINAL OVERRIDE - Spesifisitas SANGAT TINGGI di akhir untuk memastikan warna kertas ter-apply */
        /* HARUS di akhir style tag untuk override semua CSS lain */
        /* PRIORITAS: DPC 250 harus biru, IVORY 230 IK VR harus abu-abu */
        /* DPC 250 - HARUS BIRU - Spesifisitas sangat tinggi - OVERRIDE SEMUA KEMUNGKINAN */
        html body .container-fluid table#workspace-table.tg tbody tr[data-item-id] td.tg-paper-dpc250,
        html body .container-fluid table#workspace-table.tg tbody tr[data-item-id] td.tg-paper-dpc250[style],
        html body .container-fluid table#workspace-table.tg tbody tr[data-item-id] td.tg-paper-dpc250[class*="tg-paper-"],
        html body .container-fluid table#workspace-table.tg tbody tr[data-item-id] td.tg-0pky.tg-paper-dpc250,
        html body .container-fluid table#workspace-table.tg tbody tr[data-item-id] td.tg-paper-ivory230-ik-vr.tg-paper-dpc250,
        html body .container-fluid table#workspace-table.tg tbody tr[data-item-id] td[class*="tg-paper-dpc250"],
        html body .container-fluid table#workspace-table.tg tbody tr[data-item-id] td.tg-0pky[class*="tg-paper-dpc250"],
        table#workspace-table.tg tbody tr[data-item-id] td.tg-paper-dpc250,
        table#workspace-table.tg tbody tr[data-item-id] td.tg-0pky.tg-paper-dpc250,
        #workspace-table tbody tr[data-item-id] td.tg-paper-dpc250,
        #workspace-table tbody tr[data-item-id] td.tg-0pky.tg-paper-dpc250 {
            background-color: #D9E1F2 !important;
            /* Light blue - HARUS BIRU */
            background: #D9E1F2 !important;
            /* Light blue - HARUS BIRU */
            background-image: none !important;
            background-position: initial !important;
            background-size: initial !important;
            background-repeat: initial !important;
            background-attachment: initial !important;
        }

        /* IVORY 230 IK VR - HARUS ABU-ABU - Spesifisitas sangat tinggi, tapi TIDAK override DPC 250 */
        html body .container-fluid table#workspace-table.tg tbody tr[data-item-id] td.tg-paper-ivory230-ik-vr:not(.tg-paper-dpc250),
        html body .container-fluid table#workspace-table.tg tbody tr[data-item-id] td.tg-paper-ivory230-ik-vr[style]:not(.tg-paper-dpc250),
        html body .container-fluid table#workspace-table.tg tbody tr[data-item-id] td.tg-0pky.tg-paper-ivory230-ik-vr:not(.tg-paper-dpc250),
        html body .container-fluid table#workspace-table.tg tbody tr[data-item-id] td[class*="tg-paper-ivory230-ik-vr"]:not([class*="tg-paper-dpc250"]),
        html body .container-fluid table#workspace-table.tg tbody tr[data-item-id] td.tg-0pky[class*="tg-paper-ivory230-ik-vr"]:not([class*="tg-paper-dpc250"]),
        table#workspace-table.tg tbody tr[data-item-id] td.tg-paper-ivory230-ik-vr:not(.tg-paper-dpc250),
        table#workspace-table.tg tbody tr[data-item-id] td.tg-0pky.tg-paper-ivory230-ik-vr:not(.tg-paper-dpc250),
        #workspace-table tbody tr[data-item-id] td.tg-paper-ivory230-ik-vr:not(.tg-paper-dpc250),
        #workspace-table tbody tr[data-item-id] td.tg-0pky.tg-paper-ivory230-ik-vr:not(.tg-paper-dpc250) {
            background-color: #E8E8E8 !important;
            /* Light gray - ABU-ABU */
            background: #E8E8E8 !important;
            /* Light gray - ABU-ABU */
            background-image: none !important;
            background-position: initial !important;
            background-size: initial !important;
            background-repeat: initial !important;
            background-attachment: initial !important;
        }
    </style>
@endsection
@section('page-title')
    Buat Pengajuan Meeting Kertas
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Buat Pengajuan Meeting Kertas</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('paper-procurement.index') }}">Pengajuan Kertas</a></li>
                    <li class="breadcrumb-item active">Buat Pengajuan</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Info Card -->
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading">
                                <i class="mdi mdi-information-outline"></i> Informasi Pengajuan
                            </h5>
                            <p class="mb-2">
                                <strong>Bulan Meeting:</strong>
                                {{ $meetingMonth ?? ($allMonths[$month] ?? $month) . ' ' . date('Y') }}<br>
                                <strong>Periode (3 Bulan):</strong>
                                <span
                                    class="badge bg-info text-white">{{ $allMonths[$periodMonth1] ?? $periodMonth1 }}</span>,
                                <span
                                    class="badge bg-info text-white">{{ $allMonths[$periodMonth2] ?? $periodMonth2 }}</span>,
                                <span
                                    class="badge bg-info text-white">{{ $allMonths[$periodMonth3] ?? $periodMonth3 }}</span>
                                <br>
                                <strong>Customer Group:</strong> <span
                                    class="badge bg-info text-white">{{ $customerGroup }}</span>
                            </p>
                        </div>

                        <!-- Form Tambah Item -->
                        <div class="add-item-form">
                            <h5 class="mb-3">
                                <i class="mdi mdi-plus-circle text-primary"></i> Tambah Item
                            </h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="item_name">Nama Item <span class="text-danger">*</span></label>
                                        <select class="form-control" id="item_name" style="width: 100%;">
                                            <option value="">-- Pilih atau Cari Material --</option>
                                        </select>
                                        <input type="hidden" id="item_code" value="">
                                        <input type="hidden" id="item_name_text" value="">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="item_papers">Pilih Kertas yang Digunakan <span class="text-danger">*</span></label>
                                        <select class="form-control" id="item_papers" style="width: 100%;" multiple>
                                            <!-- Options will be loaded via Select2 AJAX -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="button" class="btn btn-info" id="btn-add-item">
                                        <i class="mdi mdi-plus me-1"></i> Tambah Item
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="btn-clear-form">
                                        <i class="mdi mdi-refresh me-1"></i> Reset Form
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Item (Card Layout) -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="mdi mdi-format-list-bulleted"></i> Daftar Item
                                </h5>
                            </div>
                            <div class="card-body">
                                <div id="items-list-container">
                                    <div class="text-center py-4 text-muted">
                                        <i class="mdi mdi-information-outline" style="font-size: 32px;"></i>
                                        <p class="mt-2 mb-0">Belum ada item. Silakan tambah item terlebih dahulu.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Workspace (Excel-like) -->
                        <form id="paper-meeting-form" method="POST" action="{{ route('paper-procurement.store') }}">
                            @csrf
                            <input type="hidden" name="meeting_month"
                                value="{{ $meetingMonth ?? ($allMonths[$month] ?? $month) . ' ' . date('Y') }}">
                            <input type="hidden" name="customer_name" value="{{ $customerGroup }}">
                            <input type="hidden" name="period_month_1" value="{{ $periodMonth1 }}">
                            <input type="hidden" name="period_month_2" value="{{ $periodMonth2 }}">
                            <input type="hidden" name="period_month_3" value="{{ $periodMonth3 }}">
                            <input type="hidden" name="tolerance_percentage" value="10.00">

                            <div class="excel-container">
                                <div class="excel-title">
                                    KEBUTUHAN MEETING KERTAS BULANAN PPIC
                                </div>

                                <div class="table-responsive" style="overflow-x: auto;">
                                    <!-- Excel-like table dengan styling seperti contoh -->
                                    <table class="tg" id="workspace-table">
                                        <thead>
                                            <tr id="workspace-header">
                                                <!-- Header akan di-render via JavaScript -->
                                            </tr>
                                        </thead>
                                        <tbody id="workspace-tbody">
                                            <tr>
                                                <td colspan="100" class="text-center py-4 text-muted">
                                                    <i class="mdi mdi-information-outline" style="font-size: 32px;"></i>
                                                    <p class="mt-2 mb-0">Belum ada item. Silakan tambah item terlebih
                                                        dahulu.</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Hidden container untuk menyimpan data items (untuk form submission) -->
                            <div id="items-data-container" style="display: none;"></div>

                            <div class="row mt-4">
                                <div class="col-12 text-end">
                                    <a href="{{ route('paper-procurement.index') }}"
                                        class="btn btn-outline-secondary me-2">
                                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="btn-submit" disabled>
                                        <i class="mdi mdi-content-save me-1"></i> Simpan Pengajuan
                                    </button>
                                </div>
                            </div>
                        </form>





                    </div>
                </div>
            </div>




        </div>

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // Inisialisasi Select2 untuk search material
                $('#item_name').select2({
                    placeholder: '-- Pilih atau Cari Material --',
                    allowClear: true,
                    ajax: {
                        url: '{{ route("paper-procurement.search-material") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data,
                                pagination: {
                                    more: (params.page * 50) < data.length
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 2,
                    templateResult: function(data) {
                        if (data.loading) {
                            return data.text;
                        }
                        return $('<div>' + data.text + '</div>');
                    },
                    templateSelection: function(data) {
                        return data.text || data.code + ' - ' + data.name;
                    }
                });

                // Inisialisasi Select2 untuk search paper (multiple selection)
                $('#item_papers').select2({
                    placeholder: '-- Pilih atau Cari Kertas --',
                    allowClear: true,
                    multiple: true,
                    ajax: {
                        url: '{{ route("paper-procurement.search-paper") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data,
                                pagination: {
                                    more: (params.page * 50) < data.length
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 2,
                    templateResult: function(data) {
                        if (data.loading) {
                            return data.text;
                        }
                        // Tampilkan Code - Name dengan warna background sesuai paperType
                        const color = data.color || '#ffffff';
                        return $('<div style="padding: 4px; background-color: ' + color + ';">' + data.text + '</div>');
                    },
                    templateSelection: function(data) {
                        return data.text || data.code + ' - ' + data.name;
                    }
                });

                // Event ketika material dipilih
                $('#item_name').on('select2:select', function(e) {
                    const data = e.params.data;
                    $('#item_code').val(data.code);
                    $('#item_name_text').val(data.name);
                });

                // Event ketika material di-clear
                $('#item_name').on('select2:clear', function(e) {
                    $('#item_code').val('');
                    $('#item_name_text').val('');
                });

                // Event ketika paper dipilih atau di-clear
                $('#item_papers').on('select2:select', function(e) {
                    const data = e.params.data;
                    console.log('Paper selected:', data);
                });

                $('#item_papers').on('select2:unselect', function(e) {
                    const data = e.params.data;
                    console.log('Paper unselected:', data);
                });
                console.log('Paper Procurement Form Loaded');
                console.log('Paper Types:', @json($paperTypes));
                const tolerance = 10; // 10%
                let itemCounter = 0;
                let items = []; // Array untuk menyimpan semua item
                let allPaperColumns = []; // Array untuk menyimpan semua kolom kertas yang digunakan
                let selectedLocations = []; // Array untuk menyimpan locations yang dipilih (global scope)

                // Data kertas dari server
                const paperTypes = @json($paperTypes);
                const productCategories = @json($productCategories ?? []);
                const allLocations = @json($locations ?? []); // Semua locations dari database

                // Fungsi untuk fetch stock kertas berdasarkan location
                async function fetchPaperStockByLocation(locationCode, materialCodes = null) {
                    try {
                        // Build URL dengan parameter materialcode jika ada
                        // Tidak perlu period, karena query menggunakan DATE_ADD seperti InventoryController
                        let url = `{{ route('paper-procurement.api.paper-stock') }}?location=${encodeURIComponent(locationCode)}&unit_option=Smallest`;

                        // Jika materialCodes ada, tambahkan ke parameter
                        if (materialCodes) {
                            if (Array.isArray(materialCodes)) {
                                // Jika array, tambahkan setiap code sebagai parameter
                                materialCodes.forEach(code => {
                                    url += `&materialcode[]=${encodeURIComponent(code)}`;
                                });
                            } else {
                                // Jika string, tambahkan sebagai single parameter
                                url += `&materialcode=${encodeURIComponent(materialCodes)}`;
                            }
                        }

                        const response = await fetch(url);

                        // Check if response is OK
                        if (!response.ok) {
                            const text = await response.text();
                            console.error('Error response:', text);
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        // Check if response is JSON
                        const contentType = response.headers.get("content-type");
                        if (!contentType || !contentType.includes("application/json")) {
                            const text = await response.text();
                            console.error('Response is not JSON:', text.substring(0, 200));
                            throw new Error('Response is not JSON');
                        }

                        const data = await response.json();

                        if (data.success && data.stock) {
                            // Normalize stockQty values - convert koma to titik for decimal separator
                            const normalizedStock = {};
                            Object.keys(data.stock).forEach(function(key) {
                                if (data.stock[key] && typeof data.stock[key].stockQty !== 'undefined') {
                                    const stockQty = data.stock[key].stockQty;
                                    // Convert koma to titik jika ada, lalu parse ke float
                                    let normalizedQty = stockQty;
                                    if (typeof stockQty === 'string' && stockQty.includes(',')) {
                                        normalizedQty = parseFloat(stockQty.replace(',', '.'));
                                    } else {
                                        normalizedQty = parseFloat(stockQty) || 0;
                                    }
                                    normalizedStock[key] = {
                                        ...data.stock[key],
                                        stockQty: normalizedQty
                                    };
                                } else {
                                    normalizedStock[key] = data.stock[key];
                                }
                            });
                            return normalizedStock; // Object dengan Code sebagai key
                        }
                        return {};
                    } catch (error) {
                        console.error('Error fetching paper stock:', error);
                        console.error('Location:', locationCode, 'MaterialCodes:', materialCodes);
                        return {};
                    }
                }

                // Fungsi untuk render location row (global function agar bisa dipanggil dari manapun)
                function renderLocationRow(location, tbody, allPaperColumns, createStockCell, stockData = {}) {
                    const locationRow = document.createElement('tr');
                    locationRow.className = 'stock-detail-row';
                    locationRow.setAttribute('data-location-code', location.Code);

                    // Kolom No + PRODUK + UP: Location Code + tombol hapus (colspan 3)
                    const locationLabelCell = document.createElement('td');
                    locationLabelCell.setAttribute('colspan', 2);
                    locationLabelCell.style.backgroundColor = '#ffcccc';
                    locationLabelCell.style.fontWeight = 'bold';
                    locationLabelCell.style.position = 'relative';
                    locationLabelCell.style.padding = '4px 30px 4px 8px';
                    locationLabelCell.style.textAlign = 'left';

                    // Tampilkan location code saja
                    const locationText = document.createElement('span');
                    locationText.textContent = location.Code || location.Name;
                    locationText.style.fontSize = '15px';
                    locationLabelCell.appendChild(locationText);

                    // Tombol hapus
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm btn-danger';
                    removeBtn.style.position = 'absolute';
                    removeBtn.style.right = '4px';
                    removeBtn.style.top = '2px';
                    removeBtn.style.padding = '2px 6px';
                    removeBtn.innerHTML = '<i class="mdi mdi-close" style="font-size: 12px;"></i>';
                    removeBtn.onclick = function() {
                        Swal.fire({
                            title: 'Hapus Location?',
                            text: 'Location ' + location.Code + ' akan dihapus',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Hapus dari selectedLocations
                                selectedLocations = selectedLocations.filter(sel => sel.Code !== location.Code);
                                // Re-render workspace untuk update dropdown
                                renderWorkspace().catch(err => console.error('Error rendering workspace:', err));
                            }
                        });
                    };
                    locationLabelCell.appendChild(removeBtn);
                    locationRow.appendChild(locationLabelCell);

                    // Kolom bulan (3 kolom) - kosong
                    for (let i = 0; i < 3; i++) {
                        const emptyCell = document.createElement('td');
                        emptyCell.style.backgroundColor = '#ffcccc';
                        locationRow.appendChild(emptyCell);
                    }

                    // Kolom TOTAL + TOTAL + TOLERANSI (colspan 2) - kosong
                    const emptyTotalCell = document.createElement('td');
                    emptyTotalCell.setAttribute('colspan', 2);
                    emptyTotalCell.style.backgroundColor = '#ffcccc';
                    locationRow.appendChild(emptyTotalCell);

                    // Kolom kertas untuk location dengan input field
                    const locationCode = location.Code || location.Name;
                    allPaperColumns.forEach(function(paperCol) {
                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;

                        // Cari stock untuk paper code ini
                        let stockValue = 0;
                        if (stockData[paperCode]) {
                            // Pastikan nilai menggunakan titik sebagai separator desimal
                            const rawValue = stockData[paperCode].stockQty || 0;
                            // Convert koma ke titik jika ada, lalu parse ke float
                            stockValue = parseFloat(String(rawValue).replace(',', '.')) || 0;
                        }

                        const cell = createStockCell(stockValue, '#ffcccc', `stock_${locationCode}[${paperCode}]`,
                            paperCode);
                        locationRow.appendChild(cell);
                    });

                    return locationRow;
                }
                const periodMonth1 = '{{ $periodMonth1 }}';
                const periodMonth2 = '{{ $periodMonth2 }}';
                const periodMonth3 = '{{ $periodMonth3 }}';

                // Inject CSS dengan spesifisitas sangat tinggi untuk memastikan warna tidak di-override
                const styleId = 'paper-procurement-dynamic-styles';
                if (!document.getElementById(styleId)) {
                    const style = document.createElement('style');
                    style.id = styleId;
                    // Generate CSS untuk setiap warna kertas dengan spesifisitas sangat tinggi
                    const paperColors = {
                        'DPC 250': '#D9E1F2',
                        'IVORY 230 IKDP VR': '#ffcece',
                        'IVORY 230 SPN': '#E8E8E8',
                        'IVORY 230 IK VR': '#E8E8E8',
                        'IVORY SINAR VANDA 220': '#E8E8E8'
                    };

                    let cssRules = `
                        /* CSS dengan spesifisitas sangat tinggi untuk override Bootstrap */
                        table#workspace-table.excel-table tbody tr.item-row td.col-paper[data-bg-color] {
                            background-image: none !important;
                        }
                        table#workspace-table.excel-table tbody tr.item-row td.col-paper[style*="background-color"] {
                            background-image: none !important;
                        }
                    `;

                    // Tambahkan CSS rule untuk setiap warna dengan spesifisitas sangat tinggi
                    // PENDEKATAN BARU: Pakai CSS variable dan multiple selector dengan specificity tinggi
                    Object.keys(paperColors).forEach(paperType => {
                        const color = paperColors[paperType];
                        const safeClass = 'paper-bg-' + paperType.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
                        cssRules += `
                            /* Spesifisitas sangat tinggi untuk override semua CSS - PENDEKATAN BARU */
                            table#workspace-table.excel-table tbody tr.item-row td.col-paper.${safeClass},
                            table#workspace-table.excel-table tbody tr.item-row td.col-paper.${safeClass}[style],
                            table#workspace-table.excel-table tbody tr.item-row td.col-paper.${safeClass}[data-bg-color],
                            .table table#workspace-table.excel-table tbody tr.item-row td.col-paper.${safeClass},
                            .table-responsive table#workspace-table.excel-table tbody tr.item-row td.col-paper.${safeClass},
                            .table-bordered table#workspace-table.excel-table tbody tr.item-row td.col-paper.${safeClass},
                            body table#workspace-table.excel-table tbody tr.item-row td.col-paper.${safeClass},
                            html body table#workspace-table.excel-table tbody tr.item-row td.col-paper.${safeClass},
                            html body .container-fluid table#workspace-table.excel-table tbody tr.item-row td.col-paper.${safeClass} {
                                --cell-bg-color: ${color} !important;
                                background-color: var(--cell-bg-color, ${color}) !important;
                                background: var(--cell-bg-color, ${color}) none no-repeat scroll 0% 0% / auto padding-box border-box !important;
                                background-image: none !important;
                                background-position: 0% 0% !important;
                                background-size: auto !important;
                                background-repeat: no-repeat !important;
                                background-attachment: scroll !important;
                            }

                            /* Override untuk pseudo-elements */
                            table#workspace-table.excel-table tbody tr.item-row td.col-paper.${safeClass}::before,
                            table#workspace-table.excel-table tbody tr.item-row td.col-paper.${safeClass}::after {
                                display: none !important;
                            }

                            /* Force dengan attribute selector juga */
                            table#workspace-table.excel-table tbody tr.item-row td.col-paper[data-bg-color="${color}"] {
                                --cell-bg-color: ${color} !important;
                                background-color: var(--cell-bg-color, ${color}) !important;
                                background: var(--cell-bg-color, ${color}) none no-repeat scroll 0% 0% / auto padding-box border-box !important;
                            }
                        `;
                    });

                    style.textContent = cssRules;
                    // Inject style tag dengan priority tinggi - di akhir head atau sebelum closing head
                    const head = document.head || document.getElementsByTagName('head')[0];
                    if (head.lastChild) {
                        head.insertBefore(style, head.lastChild);
                    } else {
                        head.appendChild(style);
                    }

                    // Force re-apply setelah style tag di-inject
                    setTimeout(() => {
                        console.log('Dynamic CSS injected, forcing re-render');
                        // Trigger reflow untuk memastikan CSS ter-apply
                        document.body.offsetHeight;
                    }, 100);
                }

                // Mapping warna untuk setiap jenis kertas (berdasarkan paperType)
                // WARNA INI HARUS SAMA dengan warna kategori produk yang menggunakan kertas tersebut
                // Sesuai dengan Excel: warna baris produk = warna kolom kertas yang digunakan
                const paperTypeColors = {
                    'DPC 250': '#fff2cc', // Kuning/cream - sama dengan Carton Juara (baris kuning)
                    'IVORY 230 IKDP VR': '#d5e8d4', // Hijau muda - sama dengan Pack Packaging Juara (baris hijau)
                    'IVORY 230 SPN': '#d5e8d4', // Hijau muda - sama dengan Pack Packaging Juara
                    'IVORY 230 IK VR': '#ffe6cc', // Orange/peach - sama dengan Inner Frame Juara (baris orange)
                    'IVORY SINAR VANDA 220': '#dbeafe', // Biru muda - sama dengan Esse Cigar/India (baris biru)
                    // Tambahkan warna untuk jenis kertas lainnya jika perlu
                };

                // Helper function: mendapatkan paper codes dari item.papers (array of objects)
                function getPaperCodesFromItem(item) {
                    if (!item.papers || item.papers.length === 0) {
                        return [];
                    }
                    // Jika papers adalah array of objects dengan code property
                    if (typeof item.papers[0] === 'object' && item.papers[0].code) {
                        return item.papers.map(p => p.code);
                    }
                    // Fallback: jika masih array of strings (backward compatibility)
                    return item.papers.map(p => String(p).trim());
                }

                // Helper function: check apakah item menggunakan paper tertentu (by code)
                function itemUsesPaper(item, paperCode) {
                    const paperCodes = getPaperCodesFromItem(item);
                    return paperCodes.includes(paperCode);
                }

                // Helper function: check apakah item menggunakan paperType tertentu
                function itemUsesPaperType(item, paperType) {
                    if (!item.papers || item.papers.length === 0) {
                        return false;
                    }
                    // Jika papers adalah array of objects
                    if (typeof item.papers[0] === 'object') {
                        return item.papers.some(p =>
                            (p.paperType && String(p.paperType).trim().toLowerCase() === String(paperType).trim().toLowerCase()) ||
                            (p.code && String(p.code).trim() === String(paperType).trim())
                        );
                    }
                    // Fallback: jika masih array of strings
                    return item.papers.map(p => String(p).trim().toLowerCase()).includes(String(paperType).trim().toLowerCase());
                }

                // Fungsi untuk mendapatkan index kertas pertama yang digunakan item (sesuai urutan allPaperColumns)
                // Digunakan untuk sorting - items dengan kertas yang sama akan dikelompokkan
                function getPaperIndexForItem(item) {
                    if (!item.papers || item.papers.length === 0) {
                        return 9999; // Items tanpa kertas di akhir
                    }

                    // Cari kolom kertas pertama (sesuai urutan allPaperColumns) yang digunakan item
                    for (let i = 0; i < allPaperColumns.length; i++) {
                        const paperCol = allPaperColumns[i];
                        const paperCode = paperCol.paper ? paperCol.paper.code : null;

                        // Cek apakah item menggunakan kertas ini (by code atau paperType)
                        if (paperCode && itemUsesPaper(item, paperCode)) {
                            return i;
                        }
                        if (itemUsesPaperType(item, paperCol.paperType)) {
                            return i;
                        }
                    }

                    // Jika tidak ditemukan di allPaperColumns, return index besar (di akhir)
                    return 9999;
                }

                // Fungsi untuk mendapatkan warna berdasarkan kertas pertama yang digunakan item
                // Logika: cari kertas pertama (sesuai urutan allPaperColumns) yang digunakan item
                // Ini memastikan warna baris sesuai dengan kertas yang benar-benar digunakan item dan konsisten dengan sorting
                function getRowColorForItem(item) {
                    if (!item.papers || item.papers.length === 0) {
                        return '#fff'; // Default white jika tidak ada kertas
                    }

                    // Cari kertas pertama (sesuai urutan allPaperColumns) yang digunakan item
                    // Urutan allPaperColumns menentukan prioritas warna baris (sinkron dengan sorting)
                    for (let i = 0; i < allPaperColumns.length; i++) {
                        const paperCol = allPaperColumns[i];
                        const paperCode = paperCol.paper ? paperCol.paper.code : null;

                        // Cek apakah item menggunakan kertas ini (by code atau paperType)
                        if (paperCode && itemUsesPaper(item, paperCode)) {
                            const rowColor = paperTypeColors[paperCol.paperType] || paperCol.paper.color || '#fff';
                            console.log('getRowColorForItem - Item:', item.name, 'Paper:', paperCol.paperType, 'Color:', rowColor);
                            return rowColor;
                        }
                        if (itemUsesPaperType(item, paperCol.paperType)) {
                            const rowColor = paperTypeColors[paperCol.paperType] || '#fff';
                            console.log('getRowColorForItem - Item:', item.name, 'Paper:', paperCol.paperType, 'Color:', rowColor);
                            return rowColor;
                        }
                    }

                    // Jika tidak ditemukan di allPaperColumns, gunakan kertas pertama dari item
                    const firstPaper = item.papers[0];
                    let firstPaperType = null;
                    if (typeof firstPaper === 'object' && firstPaper.paperType) {
                        firstPaperType = firstPaper.paperType;
                    } else if (typeof firstPaper === 'object' && firstPaper.code) {
                        // Cari paperType berdasarkan code di allPaperColumns
                        for (let i = 0; i < allPaperColumns.length; i++) {
                            if (allPaperColumns[i].paper && allPaperColumns[i].paper.code === firstPaper.code) {
                                firstPaperType = allPaperColumns[i].paperType;
                                break;
                            }
                        }
                    } else {
                        firstPaperType = String(firstPaper).trim();
                    }
                    const rowColor = paperTypeColors[firstPaperType] || '#fff';
                    console.warn('getRowColorForItem - No matching paper found in allPaperColumns, using first from item:', firstPaperType, 'Color:', rowColor);
                    return rowColor;
                }

                // Fungsi untuk mendapatkan warna header kolom kertas berdasarkan jenis kertas (case-insensitive)
                function getPaperHeaderColor(paperType) {
                    const paperTypeStr = String(paperType).trim();

                    // Cek exact match dulu
                    if (paperTypeColors[paperTypeStr]) {
                        return paperTypeColors[paperTypeStr];
                    }

                    // Cek case-insensitive match
                    const normalized = paperTypeStr.toLowerCase();
                    for (const key in paperTypeColors) {
                        if (key.toLowerCase() === normalized) {
                            return paperTypeColors[key];
                        }
                    }

                    // Cek partial match untuk IVORY 230 IK VR
                    if (normalized.includes('ivory') && normalized.includes('230') && normalized.includes('ik vr')) {
                        return '#ffe6cc'; // Orange/peach untuk IVORY 230 IK VR (sama dengan Inner Frame Juara)
                    }

                    return '#F2F2F2'; // Default
                }

                function formatNumber(num) {
                    if (num === null || num === undefined || num === '') return '0';
                    // Konversi ke number dulu, handle koma sebagai separator desimal
                    let numStr = String(num).replace(',', '.'); // Convert koma ke titik
                    const numValue = parseFloat(numStr);
                    if (isNaN(numValue)) return '0';

                    // Pisahkan bagian integer dan desimal
                    const parts = numValue.toString().split('.');
                    // Format bagian integer dengan titik sebagai separator ribuan
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    // Gabungkan kembali dengan titik sebagai separator desimal (PASTIKAN titik, bukan koma)
                    return parts.join('.');
                }

                // Helper function untuk convert hex ke RGB (untuk color verification)
                function hexToRgb(hex) {
                    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                    return result ? {
                        r: parseInt(result[1], 16),
                        g: parseInt(result[2], 16),
                        b: parseInt(result[3], 16)
                    } : null;
                }

                // Helper function untuk convert RGB string ke hex
                function rgbToHex(rgbString) {
                    const match = rgbString.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
                    if (match) {
                        const r = parseInt(match[1]).toString(16).padStart(2, '0');
                        const g = parseInt(match[2]).toString(16).padStart(2, '0');
                        const b = parseInt(match[3]).toString(16).padStart(2, '0');
                        return '#' + r + g + b;
                    }
                    return rgbString; // Return as-is if not RGB format
                }

                // Helper function untuk convert RGB string ke object
                function parseRgb(rgbString) {
                    const match = rgbString.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
                    return match ? {
                        r: parseInt(match[1]),
                        g: parseInt(match[2]),
                        b: parseInt(match[3])
                    } : null;
                }

                // Helper function untuk compare dua warna (hex vs rgb)
                function colorsMatch(hexColor, rgbColor) {
                    const hexRgb = hexToRgb(hexColor);
                    const parsedRgb = parseRgb(rgbColor);
                    if (!hexRgb || !parsedRgb) return false;
                    return hexRgb.r === parsedRgb.r && hexRgb.g === parsedRgb.g && hexRgb.b === parsedRgb.b;
                }

                function parseNumber(str) {
                    // Handle koma sebagai separator desimal, titik sebagai separator ribuan
                    const strValue = str.toString();
                    // Jika ada koma, anggap sebagai separator desimal
                    // Format: 48,918202 (koma desimal) -> 48.918202
                    let normalized = strValue;
                    if (normalized.includes(',')) {
                        // Hapus semua titik (separator ribuan), ganti koma dengan titik
                        normalized = normalized.replace(/\./g, '').replace(',', '.');
                    } else {
                        // Jika tidak ada koma, hapus titik (kemungkinan separator ribuan)
                        normalized = normalized.replace(/\./g, '');
                    }
                    return parseFloat(normalized) || 0;
                }

                // Fungsi untuk menentukan kategori produk berdasarkan nama produk
                function getProductCategory(productName) {
                    if (!productName || !productCategories || productCategories.length === 0) {
                        return null;
                    }

                    const productNameLower = String(productName).toLowerCase().trim();

                    // Cari kategori yang sesuai dengan nama produk
                    for (let i = 0; i < productCategories.length; i++) {
                        const category = productCategories[i];
                        if (category.products && Array.isArray(category.products)) {
                            // Cek apakah nama produk ada dalam daftar produk kategori ini
                            for (let j = 0; j < category.products.length; j++) {
                                const categoryProduct = String(category.products[j]).toLowerCase().trim();
                                if (productNameLower === categoryProduct || productNameLower.includes(
                                    categoryProduct) || categoryProduct.includes(productNameLower)) {
                                    return category;
                                }
                            }
                        }
                    }

                    return null;
                }

                // Fungsi untuk mendapatkan warna baris berdasarkan kategori produk
                // Warna baris harus sama dengan warna kertas yang digunakan produk tersebut
                function getRowColorByCategory(productName) {
                    const category = getProductCategory(productName);
                    if (category && category.paper_types && category.paper_types.length > 0) {
                        // Ambil kertas pertama yang digunakan kategori ini
                        const firstPaperType = category.paper_types[0];
                        // Gunakan warna kertas sebagai warna baris (sesuai Excel)
                        const paperColor = paperTypeColors[firstPaperType] || category.color || '#ffffff';
                        return paperColor;
                    }
                    // Fallback: gunakan warna kategori jika ada
                    if (category && category.color) {
                        return category.color;
                    }
                    // Default: putih jika tidak ada kategori
                    return '#ffffff';
                }

                // Fungsi untuk mendapatkan class CSS berdasarkan jenis kertas
                function getPaperClass(paperType) {
                    const paperTypeStr = String(paperType).trim();

                    // Normalize untuk perbandingan (case-insensitive, hapus spasi berlebih)
                    const normalized = paperTypeStr.toLowerCase().replace(/\s+/g, ' ').trim();

                    // Mapping langsung untuk jenis kertas yang sudah didefinisikan di CSS
                    // Gunakan normalized untuk matching yang lebih fleksibel
                    const paperClassMap = {
                        'dpc 250': 'tg-paper-dpc250',
                        'ivory 230 ikdp vr': 'tg-paper-ivory230-ikdp-vr',
                        'ivory 230 spn': 'tg-paper-ivory230-spn',
                        'ivory 230 ik vr': 'tg-paper-ivory230-ik-vr',
                        'ivory sinar vanda 220': 'tg-paper-ivory-sinar-vanda-220'
                    };

                    // Cek apakah ada mapping langsung (case-insensitive)
                    if (paperClassMap[normalized]) {
                        // console.log('getPaperClass - Found mapping:', paperTypeStr, '->', paperClassMap[normalized]);
                        return paperClassMap[normalized];
                    }

                    // Jika tidak ada, cek dengan partial match untuk IVORY 230 IK VR
                    if (normalized.includes('ivory') && normalized.includes('230') && normalized.includes('ik vr')) {
                        // console.log('getPaperClass - Partial match for IVORY 230 IK VR:', paperTypeStr, '-> tg-paper-ivory230-ik-vr');
                        return 'tg-paper-ivory230-ik-vr';
                    }

                    // Jika tidak ada, generate class name dari paperType
                    const classNormalized = paperTypeStr
                        .replace(/\s+/g, '-')
                        .replace(/[^a-zA-Z0-9-]/g, '')
                        .toLowerCase();
                    const generatedClass = 'tg-paper-' + classNormalized;
                    console.warn('getPaperClass - No mapping found, generated:', paperTypeStr, '->', generatedClass);
                    return generatedClass;
                }

                // Fungsi untuk update header tabel dengan kolom kertas menggunakan class tg
                function updateTableHeader() {
                    const headerRow = document.getElementById('workspace-header');
                    if (!headerRow) return;

                    headerRow.innerHTML = '';

                    // Kolom No
                    const noCell = document.createElement('th');
                    noCell.className = 'tg-0pky';
                    noCell.textContent = 'No';
                    headerRow.appendChild(noCell);

                    // Kolom PRODUK
                    const productCell = document.createElement('th');
                    productCell.className = 'tg-0pky';
                    productCell.textContent = 'PRODUK';
                    headerRow.appendChild(productCell);

                    // Kolom UP dihapus dari header, dipindahkan ke baris setelah header
                    // Kolom Bulan
                    const month1Cell = document.createElement('th');
                    month1Cell.className = 'tg-0pky';
                    month1Cell.textContent = periodMonth1.toUpperCase();
                    headerRow.appendChild(month1Cell);

                    const month2Cell = document.createElement('th');
                    month2Cell.className = 'tg-0pky';
                    month2Cell.textContent = periodMonth2.toUpperCase();
                    headerRow.appendChild(month2Cell);

                    const month3Cell = document.createElement('th');
                    month3Cell.className = 'tg-0pky';
                    month3Cell.textContent = periodMonth3.toUpperCase();
                    headerRow.appendChild(month3Cell);

                    // Kolom TOTAL
                    const totalCell = document.createElement('th');
                    totalCell.className = 'tg-0pky';
                    totalCell.textContent = `TOTAL ${periodMonth1.toUpperCase()} - ${periodMonth3.toUpperCase()}`;
                    headerRow.appendChild(totalCell);

                    // Kolom TOTAL + TOLERANSI
                    const totalTolCell = document.createElement('th');
                    totalTolCell.className = 'tg-0pky';
                    totalTolCell.textContent =
                        `TOTAL ${periodMonth1.toUpperCase()} - ${periodMonth3.toUpperCase()} + TOLERANSI`;
                    headerRow.appendChild(totalTolCell);

                    // Kolom kertas dengan warna masing-masing (menggunakan class sesuai jenis kertas)
                    allPaperColumns.forEach(function(paperCol) {
                        const paper = paperCol.paper;
                        const normalizedPaperType = String(paperCol.paperType).trim().toLowerCase();
                        const paperCode = paper ? paper.code : paperCol.paperType;
                        const paperName = paper ? paper.name : '';
                        const paperSize = paper ? paper.size : '';

                        let paperClass = getPaperClass(paperCol.paperType);

                        // PASTIKAN IVORY 230 IK VR selalu abu-abu
                        // Cek dengan berbagai variasi string matching
                        const isIvory230IKVR = (normalizedPaperType.includes('ivory') &&
                                normalizedPaperType.includes('230') &&
                                (normalizedPaperType.includes('ik vr') || normalizedPaperType.includes('ikvr'))
                                ) ||
                            normalizedPaperType.includes('k.040.0230.pln.061') ||
                            String(paperCol.paperType).toLowerCase().includes('ivory 230 ik vr') ||
                            (paperCode && paperCode.toLowerCase().includes('k.040.0230.pln.061'));

                        if (isIvory230IKVR) {
                            paperClass = 'tg-paper-ivory230-ik-vr';
                            console.log('HEADER - IVORY 230 IK VR DETECTED - PaperType:', paperCol.paperType,
                                'Normalized:', normalizedPaperType);
                        }

                        const paperCell = document.createElement('th');
                        paperCell.className = paperClass;
                        paperCell.setAttribute('data-paper-code', paperCode);
                        paperCell.setAttribute('data-paper-type', paperCol.paperType);
                        paperCell.innerHTML = `
                            <div class="paper-header">
                                ${paperCode}<br>
                                ${paperName ? paperName + '<br>' : ''}
                                ${paperSize ? paperSize + '<br>' : ''}
                                ${paperCol.paperType ? '<span class="paper-code">' + paperCol.paperType + '</span>' : ''}
                            </div>
                        `;
                        headerRow.appendChild(paperCell);
                    });
                }

                // Fungsi untuk update header tabel daftar item dengan kolom kertas
                function updateItemsListHeader() {
                    // Function tidak diperlukan lagi karena menggunakan card layout
                    // Tetap dipanggil untuk kompatibilitas dengan kode lain
                    return;
                    const thead = $('#items-list-table thead tr');

                    // Hapus kolom kertas yang ada (sebelum kolom Aksi)
                    thead.find('th.paper-col-header').remove();

                    // Tambahkan kolom kertas baru
                    allPaperColumns.forEach(function(paperCol) {
                        const paper = paperCol.paper;
                        const th = $(
                            '<th class="paper-col-header" style="width: 150px; min-width: 150px;"></th>');
                        th.html(`
                            <div style="font-size: 10px; line-height: 1.3;">
                                <strong>${paperCol.paperType}</strong><br>
                                ${paper.size}<br>
                                <small style="color: #666;">${paper.code}</small>
                            </div>
                        `);
                        // Insert sebelum kolom Aksi
                        thead.find('th:last').before(th);
                    });
                }

                // Fungsi untuk render tabel daftar item
                function renderItemsList() {
                    const container = $('#items-list-container');
                    container.empty();

                    if (items.length === 0) {
                        container.html(`
                            <div class="text-center py-4 text-muted">
                                <i class="mdi mdi-information-outline" style="font-size: 32px;"></i>
                                <p class="mt-2 mb-0">Belum ada item. Silakan tambah item terlebih dahulu.</p>
                            </div>
                        `);
                        return;
                    }

                    // Buat row untuk grid layout
                    const row = $('<div class="row"></div>');

                    items.forEach(function(item, index) {
                        // Total + Toleransi untuk default paper quantity
                        const totalTol = Math.ceil(((item.qty1 || 0) + (item.qty2 || 0) + (item.qty3 || 0)) * (
                            1 + tolerance / 100));

                        // Card untuk setiap item
                        const col = $('<div class="col-12 col-md-6 col-lg-4 mb-3"></div>');
                        const card = $('<div class="card h-100"></div>');
                        card.attr('data-item-id', item.id);
                        card.css({
                            'border': '1px solid #dee2e6',
                            'box-shadow': '0 1px 3px rgba(0,0,0,0.1)'
                        });

                        // Card Header
                        const cardHeader = $(
                            '<div class="card-header d-flex justify-content-between align-items-center"></div>'
                            );
                        cardHeader.css({
                            'background-color': '#f8f9fa',
                            'border-bottom': '2px solid #dee2e6'
                        });

                        const headerLeft = $('<div></div>');
                        const itemNo = $('<span class="badge bg-primary me-2"></span>').text(index + 1);
                        const itemTitle = $('<strong></strong>').text(`Item #${index + 1}`);
                        headerLeft.append(itemNo).append(itemTitle);

                        const headerRight = $('<div></div>');
                        const editBtn = $('<button>').attr({
                            type: 'button',
                            class: 'btn btn-sm btn-warning me-1',
                            title: 'Update Item'
                        }).html('<i class="mdi mdi-pencil"></i>').on('click', function() {
                            updateItem(item.id);
                        });
                        const deleteBtn = $('<button>').attr({
                            type: 'button',
                            class: 'btn btn-sm btn-danger',
                            title: 'Hapus Item'
                        }).html('<i class="mdi mdi-delete"></i>').on('click', function() {
                            removeItem(item.id);
                        });
                        headerRight.append(editBtn).append(deleteBtn);

                        cardHeader.append(headerLeft).append(headerRight);
                        card.append(cardHeader);

                        // Card Body
                        const cardBody = $('<div class="card-body"></div>');
                        cardBody.css('padding', '15px');

                        // Nama Item (Code dan Name)
                        const nameGroup = $('<div class="mb-3"></div>');
                        const nameLabel = $(
                            '<label class="form-label mb-1" style="font-size: 12px; font-weight: bold;"></label>'
                            ).text('Material');
                        const nameDisplay = $('<div class="form-control form-control-sm" style="background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 6px 12px; font-size: 12px;"></div>');
                        const displayText = item.code ? `${item.code} - ${item.name}` : item.name;
                        nameDisplay.text(displayText);
                        const codeHidden = $('<input>').attr({
                            type: 'hidden',
                            name: `items[${item.id}][product_code]`,
                            value: item.code || ''
                        });
                        const nameHidden = $('<input>').attr({
                            type: 'hidden',
                            name: `items[${item.id}][product_name]`,
                            value: item.name
                        });
                        nameGroup.append(nameLabel).append(nameDisplay).append(codeHidden).append(nameHidden);
                        cardBody.append(nameGroup);

                        // Kertas yang Digunakan (Display only - menunjukkan Code dan Name)
                        const papersGroup = $('<div class="mb-3"></div>');
                        const papersLabel = $(
                            '<label class="form-label mb-1" style="font-size: 12px; font-weight: bold;"></label>'
                            ).text('Kertas yang Digunakan');
                        const papersContainer = $(
                            '<div style="border: 1px solid #dee2e6; border-radius: 4px; padding: 8px; max-height: 150px; overflow-y: auto;"></div>'
                            );

                        // Tampilkan kertas yang dipilih dengan Code dan Name
                        if (item.papers && item.papers.length > 0) {
                            item.papers.forEach(function(paperObj) {
                                const paperCode = typeof paperObj === 'object' ? paperObj.code : paperObj;
                                const paperName = typeof paperObj === 'object' ? paperObj.name : '';
                                const displayText = paperCode + (paperName ? ' - ' + paperName : '');

                                const paperBadge = $('<div class="mb-1" style="padding: 4px 8px; background-color: #e3f2fd; border: 1px solid #90caf9; border-radius: 4px; font-size: 11px;"></div>');
                                paperBadge.text(displayText);
                                papersContainer.append(paperBadge);
                            });
                        } else {
                            const noPaperText = $('<div class="text-muted" style="font-size: 11px; font-style: italic;"></div>');
                            noPaperText.text('Belum ada kertas dipilih');
                            papersContainer.append(noPaperText);
                        }

                        papersGroup.append(papersLabel).append(papersContainer);
                        cardBody.append(papersGroup);

                        // Kuantitas Bulanan
                        const qtyGroup = $('<div class="mb-3"></div>');
                        const qtyLabel = $(
                            '<label class="form-label mb-2" style="font-size: 12px; font-weight: bold;"></label>'
                            ).text('Kuantitas Bulanan');

                        const qtyRow = $('<div class="row g-2"></div>');

                        // Bulan 1
                        const qty1Col = $('<div class="col-4"></div>');
                        const qty1Label = $('<label class="form-label mb-1" style="font-size: 10px;"></label>')
                            .text(periodMonth1.toUpperCase());
                        const qty1Input = $('<input>').attr({
                            type: 'number',
                            class: 'form-control form-control-sm item-qty-month-1',
                            value: item.qty1 || 0,
                            min: 0,
                            step: 1,
                            placeholder: '0'
                        }).data('item-id', item.id);
                        const qty1Hidden = $('<input>').attr({
                            type: 'hidden',
                            name: `items[${item.id}][qty_month_1]`,
                            value: item.qty1 || 0
                        });
                        qty1Col.append(qty1Label).append(qty1Input).append(qty1Hidden);

                        // Bulan 2
                        const qty2Col = $('<div class="col-4"></div>');
                        const qty2Label = $('<label class="form-label mb-1" style="font-size: 10px;"></label>')
                            .text(periodMonth2.toUpperCase());
                        const qty2Input = $('<input>').attr({
                            type: 'number',
                            class: 'form-control form-control-sm item-qty-month-2',
                            value: item.qty2 || 0,
                            min: 0,
                            step: 1,
                            placeholder: '0'
                        }).data('item-id', item.id);
                        const qty2Hidden = $('<input>').attr({
                            type: 'hidden',
                            name: `items[${item.id}][qty_month_2]`,
                            value: item.qty2 || 0
                        });
                        qty2Col.append(qty2Label).append(qty2Input).append(qty2Hidden);

                        // Bulan 3
                        const qty3Col = $('<div class="col-4"></div>');
                        const qty3Label = $('<label class="form-label mb-1" style="font-size: 10px;"></label>')
                            .text(periodMonth3.toUpperCase());
                        const qty3Input = $('<input>').attr({
                            type: 'number',
                            class: 'form-control form-control-sm item-qty-month-3',
                            value: item.qty3 || 0,
                            min: 0,
                            step: 1,
                            placeholder: '0'
                        }).data('item-id', item.id);
                        const qty3Hidden = $('<input>').attr({
                            type: 'hidden',
                            name: `items[${item.id}][qty_month_3]`,
                            value: item.qty3 || 0
                        });
                        qty3Col.append(qty3Label).append(qty3Input).append(qty3Hidden);

                        qtyRow.append(qty1Col).append(qty2Col).append(qty3Col);
                        qtyGroup.append(qtyLabel).append(qtyRow);
                        cardBody.append(qtyGroup);

                        // Total & Total + Toleransi
                        const totalGroup = $('<div class="mb-3"></div>');
                        const totalRow = $('<div class="row g-2"></div>');

                        const totalCol = $('<div class="col-6"></div>');
                        const totalLabel = $(
                            '<label class="form-label mb-1" style="font-size: 11px; font-weight: bold;"></label>'
                            ).text('TOTAL');
                        const totalInput = $('<input>').attr({
                            type: 'text',
                            class: 'form-control form-control-sm item-total',
                            readonly: true,
                            value: formatNumber((item.qty1 || 0) + (item.qty2 || 0) + (item.qty3 || 0))
                        }).data('item-id', item.id);
                        totalInput.css('font-weight', 'bold');
                        totalCol.append(totalLabel).append(totalInput);

                        const totalTolCol = $('<div class="col-6"></div>');
                        const totalTolLabel = $(
                            '<label class="form-label mb-1" style="font-size: 11px; font-weight: bold;"></label>'
                            ).text('TOTAL + TOLERANSI');
                        const totalTolInput = $('<input>').attr({
                            type: 'text',
                            class: 'form-control form-control-sm item-total-tolerance',
                            readonly: true,
                            value: formatNumber(totalTol)
                        }).data('item-id', item.id);
                        totalTolInput.css('font-weight', 'bold');
                        totalTolCol.append(totalTolLabel).append(totalTolInput);

                        totalRow.append(totalCol).append(totalTolCol);
                        totalGroup.append(totalRow);
                        cardBody.append(totalGroup);

                        // Kuantitas Kertas (hanya untuk kertas yang digunakan)
                        const usedPapers = item.papers || [];
                        if (usedPapers.length > 0) {
                            const paperQtyGroup = $('<div class="mb-0"></div>');
                            const paperQtyLabel = $(
                                '<label class="form-label mb-2" style="font-size: 12px; font-weight: bold;"></label>'
                                ).text('Kuantitas per Kertas');
                            const paperQtyContainer = $(
                                '<div style="border: 1px solid #dee2e6; border-radius: 4px; padding: 8px; max-height: 200px; overflow-y: auto;"></div>'
                                );

                            allPaperColumns.forEach(function(paperCol) {
                                // Cek apakah item menggunakan kertas ini (by code)
                                const paperCode = paperCol.paper ? paperCol.paper.code : null;
                                const hasPaper = paperCode ? itemUsesPaper(item, paperCode) : itemUsesPaperType(item, paperCol.paperType);
                                if (hasPaper) {
                                    const paperQtyItem = $('<div class="mb-2"></div>');
                                    const paperQtyItemLabel = $(
                                        '<label class="form-label mb-1" style="font-size: 10px;"></label>'
                                        );
                                    const paperColor = paperTypeColors[paperCol.paperType] || '#ffffff';
                                    paperQtyItemLabel.css({
                                        'background-color': paperColor,
                                        'padding': '2px 6px',
                                        'border-radius': '3px',
                                        'display': 'inline-block',
                                        'font-weight': 'bold'
                                    });
                                    const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                                    const paperName = paperCol.paper ? paperCol.paper.name : '';
                                    // Tampilkan Code - Name
                                    paperQtyItemLabel.text(paperCode + (paperName ? ' - ' + paperName : ''));
                                    const paperQty = item.paperQuantities && item.paperQuantities[
                                        paperCode] ? item.paperQuantities[paperCode] : totalTol;
                                    const paperInput = $('<input>').attr({
                                        type: 'number',
                                        class: 'form-control form-control-sm item-paper-qty',
                                        value: paperQty,
                                        min: 0,
                                        step: 1
                                    }).data({
                                        'item-id': item.id,
                                        'paper-code': paperCode
                                    });

                                    // Hidden inputs untuk form submission - menggunakan Code sebagai key
                                    const hiddenType = $('<input>').attr({
                                        type: 'hidden',
                                        name: `items[${item.id}][papers][${paperCode}][paper_type]`
                                    }).val(paperCol.paperType || paperCode);

                                    const hiddenCode = $('<input>').attr({
                                        type: 'hidden',
                                        name: `items[${item.id}][papers][${paperCode}][paper_code]`
                                    }).val(paperCol.paper ? paperCol.paper.code : paperCode);

                                    const hiddenName = $('<input>').attr({
                                        type: 'hidden',
                                        name: `items[${item.id}][papers][${paperCode}][paper_name]`
                                    }).val(paperCol.paper ? paperCol.paper.name : '');

                                    const hiddenSize = $('<input>').attr({
                                        type: 'hidden',
                                        name: `items[${item.id}][papers][${paperCode}][paper_size]`
                                    }).val(paperCol.paper ? paperCol.paper.size : '');

                                    const hiddenQty = $('<input>').attr({
                                        type: 'hidden',
                                        name: `items[${item.id}][papers][${paperCode}][quantity]`,
                                        value: paperQty
                                    });

                                    paperQtyItem.append(paperQtyItemLabel).append(paperInput).append(
                                        hiddenType).append(hiddenCode).append(hiddenName).append(hiddenSize).append(
                                        hiddenQty);
                                    paperQtyContainer.append(paperQtyItem);
                                }
                            });

                            paperQtyGroup.append(paperQtyLabel).append(paperQtyContainer);
                            cardBody.append(paperQtyGroup);
                        }

                        card.append(cardBody);
                        col.append(card);
                        row.append(col);
                    });

                    container.append(row);

                    // Attach event listeners untuk input kuantitas
                    attachItemsListEventListeners();
                }

                // Fungsi untuk render workspace menggunakan tabel dengan class tg - styling seperti contoh
                async function renderWorkspace() {
                    const tbody = document.getElementById('workspace-tbody');
                    if (!tbody) return;

                    // Clear tbody
                    tbody.innerHTML = '';

                    if (items.length === 0) {
                        const emptyRow = document.createElement('tr');
                        const emptyCell = document.createElement('td');
                        emptyCell.setAttribute('colspan', '100');
                        emptyCell.className = 'text-center py-4 text-muted';
                        emptyCell.innerHTML = `
                            <i class="mdi mdi-information-outline" style="font-size: 32px;"></i>
                            <p class="mt-2 mb-0">Belum ada item. Silakan tambah item terlebih dahulu.</p>
                        `;
                        emptyRow.appendChild(emptyCell);
                        tbody.appendChild(emptyRow);
                        $('#btn-submit').prop('disabled', true);
                        return;
                    }

                    $('#btn-submit').prop('disabled', false);

                    // Update header dengan kolom kertas
                    updateTableHeader();

                    // Baris UP (setelah header, sebelum data rows)
                    const upRow = document.createElement('tr');
                    upRow.className = 'up-row';

                    // Kolom No - kosong
                    const noCell = document.createElement('td');
                    noCell.style.backgroundColor = '#ffcccc';
                    noCell.style.border = '2px solid #ff0000';
                    upRow.appendChild(noCell);

                    // Kolom PRODUK - label "UP"
                    const productCell = document.createElement('td');
                    productCell.textContent = 'UP';
                    productCell.style.backgroundColor = '#ffcccc';
                    productCell.style.fontWeight = 'bold';
                    productCell.style.fontSize = '15px';
                    productCell.style.border = '2px solid #ff0000';
                    productCell.style.textAlign = 'left';
                    productCell.style.padding = '8px';
                    upRow.appendChild(productCell);

                    // Kolom bulan (DEC, JAN, FEB) - kosong
                    for (let i = 0; i < 3; i++) {
                        const monthCell = document.createElement('td');
                        monthCell.style.backgroundColor = '#ffcccc';
                        monthCell.style.border = '2px solid #ff0000';
                        upRow.appendChild(monthCell);
                    }

                    // Kolom TOTAL - kosong
                    const totalCell = document.createElement('td');
                    totalCell.style.backgroundColor = '#ffcccc';
                    totalCell.style.border = '2px solid #ff0000';
                    upRow.appendChild(totalCell);

                    // Kolom TOTAL + TOLERANSI - kosong
                    const totalTolCell = document.createElement('td');
                    totalTolCell.style.backgroundColor = '#ffcccc';
                    totalTolCell.style.border = '2px solid #ff0000';
                    upRow.appendChild(totalTolCell);

                    // Kolom kertas untuk input UP per jenis kertas (di bawah setiap header jenis kertas)
                    allPaperColumns.forEach(function(paperCol) {
                        const cell = document.createElement('td');
                        cell.style.backgroundColor = '#ffcccc';
                        cell.style.textAlign = 'center';
                        cell.style.padding = '8px';
                        cell.style.border = '2px solid #ff0000';

                        const upInput = document.createElement('input');
                        upInput.type = 'number';
                        upInput.className = 'excel-input paper-up-input';
                        upInput.name = `paper_up[${paperCol.paperType}]`;
                        upInput.setAttribute('data-paper-type', paperCol.paperType);
                        upInput.value = '5'; // Default UP = 5
                        upInput.min = '0';
                        upInput.step = '0.01';
                        upInput.style.width = '100%';
                        upInput.style.border = '2px solid #ff0000';
                        upInput.style.borderRadius = '4px';
                        upInput.style.background = '#fff';
                        upInput.style.textAlign = 'center';
                        upInput.style.fontSize = '15px';
                        upInput.style.fontWeight = 'bold';
                        upInput.style.padding = '6px 8px';
                        upInput.placeholder = 'UP';

                        // Event listener untuk update Layer 2 saat UP berubah
                        upInput.addEventListener('change', function() {
                            if (typeof updateTotalStokLayers === 'function') {
                                updateTotalStokLayers();
                            }
                        });

                        upInput.addEventListener('input', function() {
                            // Debounce untuk performa
                            clearTimeout(upInput._updateTimeout);
                            upInput._updateTimeout = setTimeout(function() {
                                if (typeof updateTotalStokLayers === 'function') {
                                    updateTotalStokLayers();
                                }
                            }, 300);
                        });

                        cell.appendChild(upInput);
                        upRow.appendChild(cell);
                    });
                    tbody.appendChild(upRow);

                    // Sort items berdasarkan kertas yang digunakan - items dengan kertas yang sama dikelompokkan
                    const sortedItems = [...items].sort(function(a, b) {
                        const indexA = getPaperIndexForItem(a);
                        const indexB = getPaperIndexForItem(b);
                        if (indexA === indexB) return 0;
                        return indexA - indexB;
                    });

                    // Function untuk force set style dengan cara yang sangat agresif
                    // HARUS di luar loop agar bisa digunakan untuk semua cell dan di semua scope
                    function forceSetCellColor(cell, color) {
                        if (!cell) return; // Safety check
                        // Method 1: setProperty dengan !important (multiple times)
                        cell.style.setProperty('background-color', color, 'important');
                        cell.style.setProperty('background', color, 'important');
                        cell.style.setProperty('background-color', color, 'important');
                        cell.style.setProperty('background', color, 'important');

                        // Method 2: Set langsung ke style object
                        cell.style.backgroundColor = color;
                        cell.style.background = color;

                        // Method 3: Remove semua background properties yang mungkin meng-interfere
                        cell.style.removeProperty('background-image');
                        cell.style.removeProperty('background-position');
                        cell.style.removeProperty('background-size');
                        cell.style.removeProperty('background-repeat');
                        cell.style.removeProperty('background-attachment');

                        // Method 4: Set lagi dengan setProperty
                        cell.style.setProperty('background-color', color, 'important');
                        cell.style.setProperty('background', color, 'important');

                        // Force reflow
                        void cell.offsetHeight;
                    }

                    // Render setiap item sebagai baris tabel - menggunakan sortedItems
                    sortedItems.forEach(function(item, index) {
                        const total = (item.qty1 || 0) + (item.qty2 || 0) + (item.qty3 || 0);
                        const totalWithTol = Math.ceil(total * (1 + tolerance / 100));

                        // Tentukan kertas pertama yang digunakan produk (untuk warna baris)
                        // Cari kertas pertama yang digunakan produk (sesuai urutan allPaperColumns)
                        let firstUsedPaper = null;
                        let rowColor = '#ffffff'; // Default putih

                        for (let i = 0; i < allPaperColumns.length; i++) {
                            const paperCol = allPaperColumns[i];
                            const paperCode = paperCol.paper ? paperCol.paper.code : null;

                            // Cek apakah item menggunakan kertas ini (by code atau paperType)
                            if (paperCode && itemUsesPaper(item, paperCode)) {
                                firstUsedPaper = paperCol;
                                rowColor = paperTypeColors[paperCol.paperType] || paperCol.paper.color || '#ffffff';
                                break; // Ambil kertas pertama yang digunakan
                            }
                            if (itemUsesPaperType(item, paperCol.paperType)) {
                                firstUsedPaper = paperCol;
                                rowColor = paperTypeColors[paperCol.paperType] || '#ffffff';
                                break; // Ambil kertas pertama yang digunakan
                            }
                        }

                        // Buat class name untuk kategori produk (untuk styling)
                        const category = getProductCategory(item.name);
                        let rowClass = 'tg-0pky'; // Default
                        if (category) {
                            // Generate class name dari kategori
                            const categoryClass = 'tg-category-' + category.name.toLowerCase().replace(/\s+/g,
                                '-').replace(/[^a-zA-Z0-9-]/g, '');
                            rowClass = categoryClass;
                        }

                        // Buat row
                        const row = document.createElement('tr');
                        row.setAttribute('data-item-id', item.id);

                        // Helper function untuk membuat cell dengan warna kertas yang digunakan
                        function createCell(content, align = 'left') {
                            const cell = document.createElement('td');
                            cell.className = rowClass;

                            // Set warna background berdasarkan kertas yang digunakan produk
                            cell.style.setProperty('background-color', rowColor, 'important');
                            cell.style.setProperty('background', rowColor, 'important');
                            // Remove any background-image yang mungkin di-set oleh CSS
                            cell.style.removeProperty('background-image');

                            if (align === 'right') {
                                cell.style.textAlign = 'right';
                            } else if (align === 'center') {
                                cell.style.textAlign = 'center';
                            }

                            if (typeof content === 'string' || typeof content === 'number') {
                                cell.textContent = content;
                            } else {
                                cell.appendChild(content);
                            }
                            return cell;
                        }

                        // Kolom non-kertas - semua pakai warna kertas yang digunakan
                        row.appendChild(createCell(index + 1, 'center'));
                        // Tampilkan Code dan Name di workspace
                        const productDisplay = item.code ? `${item.code} - ${item.name}` : item.name;
                        row.appendChild(createCell(productDisplay, 'left'));

                        // Kolom UP dihapus dari row item, dipindahkan ke baris setelah header
                        row.appendChild(createCell(formatNumber(item.qty1 || 0), 'right'));
                        row.appendChild(createCell(formatNumber(item.qty2 || 0), 'right'));
                        row.appendChild(createCell(formatNumber(item.qty3 || 0), 'right'));
                        row.appendChild(createCell(formatNumber(total), 'right'));
                        row.appendChild(createCell(formatNumber(totalWithTol), 'right'));

                        // Kolom kertas - loop berhenti setelah menemukan kertas yang digunakan
                        // Jika produk menggunakan kertas, beri warna kertas. Jika tidak, berhenti memberi warna.
                        let foundUsedPaper = false; // Flag untuk menandai sudah menemukan kertas yang digunakan

                        allPaperColumns.forEach(function(paperCol) {
                            // Cek apakah item menggunakan kertas ini (by code)
                            const paperCode = paperCol.paper ? paperCol.paper.code : null;
                            const hasPaper = paperCode ? itemUsesPaper(item, paperCode) : itemUsesPaperType(item, paperCol.paperType);

                            let paperCell;

                            if (hasPaper) {
                                // Produk menggunakan kertas ini - gunakan warna kertas yang digunakan produk (rowColor)
                                // Bukan warna kertas asli, tapi warna yang sama dengan kolom non-kertas
                                foundUsedPaper = true;
                                const paperQty = item.paperQuantities && item.paperQuantities[paperCol
                                        .paperType] ?
                                    item.paperQuantities[paperCol.paperType] :
                                    totalWithTol;

                                // Dapatkan class kertas (untuk CSS base layer)
                                let paperClass = getPaperClass(paperCol.paperType);
                                const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;

                                // Buat cell dengan warna kertas yang digunakan produk (rowColor)
                                paperCell = document.createElement('td');
                                // Tetap pakai class kertas sebagai base layer, tapi akan di-override oleh inline style
                                paperCell.className = paperClass;
                                paperCell.classList.add(
                                'paper-cell-used'); // Class khusus untuk override CSS
                                paperCell.setAttribute('data-paper-type', paperCol.paperType);
                                paperCell.setAttribute('data-paper-code', paperCode);
                                paperCell.style.textAlign = 'right';

                                // Set warna sama dengan kolom non-kertas (warna kertas yang digunakan produk)
                                // Gunakan inline style dengan !important untuk override CSS class kertas
                                paperCell.style.setProperty('background-color', rowColor, 'important');
                                paperCell.style.setProperty('background', rowColor, 'important');
                                paperCell.style.removeProperty('background-image');

                                paperCell.textContent = formatNumber(paperQty);

                                // Hidden inputs - menggunakan Code sebagai key
                                const hiddenType = document.createElement('input');
                                hiddenType.type = 'hidden';
                                hiddenType.name =
                                    `items[${item.id}][papers][${paperCode}][paper_type]`;
                                hiddenType.value = paperCol.paperType || paperCode;

                                const hiddenCode = document.createElement('input');
                                hiddenCode.type = 'hidden';
                                hiddenCode.name =
                                    `items[${item.id}][papers][${paperCode}][paper_code]`;
                                hiddenCode.value = paperCode;

                                const hiddenName = document.createElement('input');
                                hiddenName.type = 'hidden';
                                hiddenName.name =
                                    `items[${item.id}][papers][${paperCode}][paper_name]`;
                                hiddenName.value = paperCol.paper ? paperCol.paper.name : '';

                                const hiddenSize = document.createElement('input');
                                hiddenSize.type = 'hidden';
                                hiddenSize.name =
                                    `items[${item.id}][papers][${paperCode}][paper_size]`;
                                hiddenSize.value = paperCol.paper ? paperCol.paper.size : '';

                                const hiddenQty = document.createElement('input');
                                hiddenQty.type = 'hidden';
                                hiddenQty.name =
                                    `items[${item.id}][papers][${paperCode}][quantity]`;
                                hiddenQty.value = paperQty;

                                paperCell.appendChild(hiddenType);
                                paperCell.appendChild(hiddenCode);
                                paperCell.appendChild(hiddenName);
                                paperCell.appendChild(hiddenSize);
                                paperCell.appendChild(hiddenQty);

                            } else {
                                // Produk tidak menggunakan kertas ini
                                // Dapatkan class dan warna kertas asli
                                let paperClass = getPaperClass(paperCol.paperType);
                                const paperColor = paperTypeColors[paperCol.paperType] || '#ffffff';

                                paperCell = document.createElement('td');
                                paperCell.className = paperClass; // Gunakan class kertas asli
                                paperCell.style.textAlign = 'center';
                                paperCell.textContent = '-';

                                // Logika warna:
                                // - Jika BELUM menemukan kertas yang digunakan → gunakan rowColor (warna baris produk)
                                // - Jika SUDAH menemukan kertas yang digunakan → gunakan paperColor (warna kertas asli)
                                if (foundUsedPaper) {
                                    // Sudah menemukan kertas yang digunakan sebelumnya → gunakan warna kertas asli
                                    paperCell.setAttribute('data-before-used-paper',
                                    'false'); // Flag khusus
                                    paperCell.style.setProperty('background-color', paperColor,
                                        'important');
                                    paperCell.style.setProperty('background', paperColor, 'important');
                                } else {
                                    // Belum menemukan kertas yang digunakan → gunakan warna baris produk
                                    // HAPUS class kertas untuk memastikan inline style bisa override
                                    paperCell.className =
                                    ''; // Hapus class kertas agar tidak di-override oleh CSS
                                    paperCell.setAttribute('data-paper-type', paperCol.paperType);
                                    paperCell.setAttribute('data-before-used-paper',
                                    'true'); // Flag khusus
                                    // Set warna baris produk dengan !important
                                    paperCell.style.setProperty('background-color', rowColor,
                                        'important');
                                    paperCell.style.setProperty('background', rowColor, 'important');
                                    // Force apply multiple times untuk memastikan
                                    setTimeout(() => {
                                        paperCell.style.setProperty('background-color',
                                            rowColor, 'important');
                                        paperCell.style.setProperty('background', rowColor,
                                            'important');
                                    }, 0);
                                }
                                paperCell.style.removeProperty('background-image');
                            }

                            row.appendChild(paperCell);

                            // Force apply inline style SETELAH append
                            if (hasPaper) {
                                // Cell kertas yang digunakan - gunakan rowColor
                                // Set style langsung setelah append menggunakan fungsi forceSetCellColor
                                forceSetCellColor(paperCell, rowColor);

                                // Also set via setTimeout sebagai backup (multiple attempts)
                                setTimeout(() => {
                                    forceSetCellColor(paperCell, rowColor);
                                }, 0);

                                setTimeout(() => {
                                    forceSetCellColor(paperCell, rowColor);
                                }, 10);

                                setTimeout(() => {
                                    forceSetCellColor(paperCell, rowColor);
                                }, 50);
                            } else {
                                // Cell kertas yang tidak digunakan - force apply warna sesuai logika
                                if (!foundUsedPaper) {
                                    // Belum menemukan kertas yang digunakan - gunakan rowColor
                                    forceSetCellColor(paperCell, rowColor);
                                    setTimeout(() => {
                                        forceSetCellColor(paperCell, rowColor);
                                        paperCell.style.setProperty('background-color',
                                            rowColor, 'important');
                                        paperCell.style.setProperty('background', rowColor,
                                            'important');
                                    }, 0);
                                    setTimeout(() => {
                                        forceSetCellColor(paperCell, rowColor);
                                    }, 10);
                                } else {
                                    // Sudah menemukan kertas yang digunakan - gunakan paperColor
                                    const paperColor = paperTypeColors[paperCol.paperType] || '#ffffff';
                                    forceSetCellColor(paperCell, paperColor);
                                }
                            }
                        });

                        tbody.appendChild(row);

                        // Force apply style untuk semua cell kolom kertas yang digunakan setelah row di-append
                        // HANYA untuk cell yang memiliki class paper-cell-used (cell kertas yang digunakan)
                        // Gunakan rowColor (warna kertas yang digunakan produk), bukan paperColor
                        requestAnimationFrame(() => {
                            const paperCells = row.querySelectorAll('td.paper-cell-used');
                            paperCells.forEach(cell => {
                                // Gunakan rowColor (warna kertas yang digunakan produk)
                                forceSetCellColor(cell, rowColor);
                                cell.style.setProperty('background-color', rowColor,
                                    'important');
                                cell.style.setProperty('background', rowColor, 'important');
                            });
                        });

                        // Backup dengan setTimeout juga
                        setTimeout(() => {
                            const paperCells = row.querySelectorAll('td.paper-cell-used');
                            paperCells.forEach(cell => {
                                // Gunakan rowColor (warna kertas yang digunakan produk)
                                forceSetCellColor(cell, rowColor);
                            });
                        }, 50);
                    });

                    // Tambahkan baris TOTAL di bawah semua item - BENTUK HURUF J menggunakan tabel
                    // Bentuk J: Kolom No+PRODUK digabung, kolom quantity (bulan) di tengah, kolom TOTAL+TOLERANSI digabung, kolom kertas di kanan

                    // Hitung total untuk setiap kolom - gunakan sortedItems untuk konsistensi
                    let totalQty1 = 0;
                    let totalQty2 = 0;
                    let totalQty3 = 0;
                    const paperTotals = {}; // Object untuk menyimpan total per jenis kertas

                    sortedItems.forEach(function(item) {
                        totalQty1 += (item.qty1 || 0);
                        totalQty2 += (item.qty2 || 0);
                        totalQty3 += (item.qty3 || 0);

                        const itemTotal = (item.qty1 || 0) + (item.qty2 || 0) + (item.qty3 || 0);
                        const itemTotalWithTol = Math.ceil(itemTotal * (1 + tolerance / 100));

                        // Hitung total per jenis kertas
                        allPaperColumns.forEach(function(paperCol) {
                            // Cek apakah item menggunakan kertas ini (by code)
                            const paperCode = paperCol.paper ? paperCol.paper.code : null;
                            const hasPaper = paperCode ? itemUsesPaper(item, paperCode) : itemUsesPaperType(item, paperCol.paperType);

                            if (hasPaper) {
                                if (!paperTotals[paperCol.paperType]) {
                                    paperTotals[paperCol.paperType] = 0;
                                }
                                const paperQty = item.paperQuantities && item.paperQuantities[paperCol
                                        .paperType] ?
                                    item.paperQuantities[paperCol.paperType] :
                                    itemTotalWithTol;
                                paperTotals[paperCol.paperType] += paperQty;
                            }
                        });
                    });

                    // Buat row TOTAL
                    const totalRow = document.createElement('tr');

                    // Helper function untuk membuat cell dengan class tg-total
                    function createTotalCell(content, align = 'left', colspan = 1) {
                        const cell = document.createElement('td');
                        cell.className = 'tg-total';
                        if (colspan > 1) {
                            cell.setAttribute('colspan', colspan);
                        }
                        if (align === 'right') {
                            cell.style.textAlign = 'right';
                        } else if (align === 'center') {
                            cell.style.textAlign = 'center';
                        }
                        cell.style.fontSize = '15px';
                        cell.style.fontWeight = 'bold';
                        cell.textContent = content;
                        return cell;
                    }

                    // BENTUK J: Kolom No + PRODUK digabung menjadi satu cell dengan "TOTAL"
                    totalRow.appendChild(createTotalCell('TOTAL', 'center', 2));

                    // Kolom UP dihapus dari baris TOTAL - sudah dipindahkan ke baris setelah header
                    // Kolom Bulan 1, 2, 3 - PUSAT BENTUK J (kolom quantity)
                    totalRow.appendChild(createTotalCell(formatNumber(totalQty1), 'right'));
                    totalRow.appendChild(createTotalCell(formatNumber(totalQty2), 'right'));
                    totalRow.appendChild(createTotalCell(formatNumber(totalQty3), 'right'));

                    // Hitung total OCT - DEC
                    const totalOctDec = totalQty1 + totalQty2 + totalQty3;
                    // Hitung total OCT - DEC + TOLERANSI
                    const totalOctDecWithTol = Math.ceil(totalOctDec * (1 + tolerance / 100));

                    // Kolom TOTAL OCT - DEC
                    totalRow.appendChild(createTotalCell(formatNumber(totalOctDec), 'right'));

                    // Kolom TOTAL OCT - DEC + TOLERANSI
                    totalRow.appendChild(createTotalCell(formatNumber(totalOctDecWithTol), 'right'));

                    // Kolom kertas - tampilkan total per jenis kertas (bagian kanan bentuk J)
                    // DI BARIS TOTAL: SEMUA kolom (termasuk kolom kertas) menggunakan warna kuning (tg-total)
                    allPaperColumns.forEach(function(paperCol) {
                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        const paperTotal = paperTotals[paperCode] || 0;

                        // Buat cell dengan class tg-total (kuning) untuk baris TOTAL
                        // SEMUA cell di baris TOTAL harus kuning, termasuk kolom kertas
                        const paperTotalCell = document.createElement('td');
                        paperTotalCell.className = 'tg-total'; // Gunakan class kuning untuk baris TOTAL
                        paperTotalCell.style.textAlign = 'right';
                        paperTotalCell.style.fontWeight = 'bold';
                        paperTotalCell.textContent = formatNumber(paperTotal);

                        totalRow.appendChild(paperTotalCell);
                    });

                    tbody.appendChild(totalRow);

                    // Helper function untuk membuat input field di section stock/procurement
                    function createStockInput(value, bgColor, name, paperType = null) {
                        const input = document.createElement('input');
                        input.type = 'text'; // Gunakan text untuk kontrol format yang lebih baik
                        input.className = 'excel-input stock-procurement-input';
                        input.inputMode = 'decimal'; // Untuk mobile keyboard numeric
                        // Pastikan value menggunakan format number yang benar (titik sebagai separator desimal)
                        let numValue = 0;
                        let displayValue = '0';
                        if (value !== null && value !== undefined && value !== '') {
                            // Convert koma ke titik jika ada, lalu parse ke float
                            const strValue = String(value).trim();
                            numValue = parseFloat(strValue.replace(',', '.')) || 0;
                            // Format untuk display: gunakan titik sebagai separator desimal, titik sebagai separator ribuan
                            displayValue = formatNumber(numValue);
                        }
                        // Set value sebagai string dengan format yang benar (titik sebagai separator desimal)
                        input.value = displayValue;
                        input.style.width = '100%';
                        input.style.border = 'none';
                        input.style.background = 'transparent';
                        input.style.textAlign = 'right';
                        input.style.fontSize = '15px';
                        input.style.fontFamily = 'Segoe UI, Arial, sans-serif';
                        input.style.fontWeight = 'bold';
                        input.style.padding = '6px 8px';

                        // Handle input untuk memastikan format yang benar
                        input.addEventListener('input', function(e) {
                            let val = this.value;
                            // Ganti koma dengan titik
                            val = val.replace(',', '.');
                            // Hapus karakter non-numeric kecuali titik
                            val = val.replace(/[^\d.]/g, '');
                            // Pastikan hanya ada satu titik
                            const parts = val.split('.');
                            if (parts.length > 2) {
                                val = parts[0] + '.' + parts.slice(1).join('');
                            }
                            this.value = val;
                        });

                        // Update Layer saat input berubah
                        input.addEventListener('input', function(e) {
                            // Update Layer 1 dan Layer 2 saat user mengetik
                            if (typeof updateTotalStokLayers === 'function') {
                                // Debounce untuk performa
                                clearTimeout(input._updateTimeout);
                                input._updateTimeout = setTimeout(function() {
                                    updateTotalStokLayers();
                                }, 300);
                            }
                        });

                        // Format dan update saat blur
                        input.addEventListener('blur', function(e) {
                            const val = parseFloat(this.value.replace(',', '.')) || 0;
                            this.value = formatNumber(val);
                            this.style.outline = 'none';
                            this.style.backgroundColor = 'transparent';
                            // Update Layer 1 dan Layer 2 setelah stock berubah
                            if (typeof updateTotalStokLayers === 'function') {
                                updateTotalStokLayers();
                            }
                        });

                        // Set name attribute untuk form submission
                        if (name) {
                            input.name = name;
                        }

                        // Set data attribute untuk identifikasi
                        if (paperType) {
                            input.setAttribute('data-paper-type', paperType);
                        }

                        // Style untuk focus
                        input.addEventListener('focus', function() {
                            this.style.outline = '2px solid #0078D4';
                            this.style.backgroundColor = '#E7F3FF';
                        });

                        return input;
                    }

                    // Helper function untuk membuat cell dengan input field
                    function createStockCell(value, bgColor, name, paperType = null, align = 'right') {
                        const cell = document.createElement('td');
                        cell.style.backgroundColor = bgColor;
                        cell.style.textAlign = align;
                        cell.style.padding = '8px'; // Tambah padding agar lebih terlihat

                        if (align === 'right') {
                            const input = createStockInput(value, bgColor, name, paperType);
                            cell.appendChild(input);
                        } else {
                            cell.textContent = value;
                            cell.style.fontWeight = 'bold';
                            cell.style.padding = '8px'; // Tambah padding untuk non-input cells juga
                        }

                        return cell;
                    }

                    // Tambahkan section baru di bawah TOTAL untuk stock dan procurement details
                    // Section: Locations yang sudah dipilih - render sebelum Total Stok
                    // Ambil material codes dari data-paper-code di header kolom kertas
                    const materialCodes = [];
                    const headerRow = document.getElementById('workspace-header');
                    if (headerRow) {
                        const paperHeaderCells = headerRow.querySelectorAll('th[data-paper-code]');
                        paperHeaderCells.forEach(function(cell) {
                            const paperCode = cell.getAttribute('data-paper-code');
                            if (paperCode && !materialCodes.includes(paperCode)) {
                                materialCodes.push(paperCode);
                            }
                        });
                    }

                    // Render location rows dengan stock data (jika sudah ada) atau fetch terlebih dahulu
                    const locationRowsPromises = selectedLocations.map(async function(location) {
                        // Jika stock data belum ada atau material codes berubah, fetch terlebih dahulu
                        const materialCodesStr = JSON.stringify(materialCodes.sort());
                        const cachedMaterialCodesStr = location.materialCodes ? JSON.stringify(location.materialCodes.sort()) : null;

                        if (!location.stockData || materialCodesStr !== cachedMaterialCodesStr) {
                            location.stockData = await fetchPaperStockByLocation(location.Code, materialCodes);
                            location.materialCodes = [...materialCodes]; // Simpan material codes untuk cache
                        }

                        // Render location row dengan stock data
                        return renderLocationRow(location, tbody, allPaperColumns, createStockCell, location.stockData || {});
                    });

                    // Tunggu semua location rows selesai di-render
                    const locationRows = await Promise.all(locationRowsPromises);
                    locationRows.forEach(function(locationRow) {
                        tbody.appendChild(locationRow);
                    });

                    // Section: Row untuk menambah location (dropdown + tombol +)
                    const addLocationRow = document.createElement('tr');
                    addLocationRow.className = 'add-location-row';

                    // Kolom untuk dropdown dan tombol
                    const addLocationCell = document.createElement('td');
                    addLocationCell.setAttribute('colspan', 7);
                    addLocationCell.style.backgroundColor = '#f0f0f0';
                    addLocationCell.style.padding = '8px';
                    addLocationCell.style.fontSize = '15px';

                    const addLocationContainer = document.createElement('div');
                    addLocationContainer.style.display = 'flex';
                    addLocationContainer.style.gap = '10px';
                    addLocationContainer.style.alignItems = 'center';

                    // Dropdown untuk memilih location
                    const locationSelect = document.createElement('select');
                    locationSelect.className = 'form-control';
                    locationSelect.style.width = '200px';
                    locationSelect.style.display = 'inline-block';
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '-- Pilih Location --';
                    locationSelect.appendChild(defaultOption);

                    // Tambahkan options dari allLocations yang belum dipilih
                    allLocations.forEach(function(location) {
                        const isSelected = selectedLocations.some(sel => sel.Code === location.Code);
                        if (!isSelected) {
                            const option = document.createElement('option');
                            option.value = location.Code;
                            option.textContent = location.Code + (location.Name ? ' - ' + location.Name : '');
                            option.setAttribute('data-location', JSON.stringify(location));
                            locationSelect.appendChild(option);
                        }
                    });

                    // Tombol +
                    const addBtn = document.createElement('button');
                    addBtn.type = 'button';
                    addBtn.className = 'btn btn-primary btn-sm';
                    addBtn.innerHTML = '<i class="mdi mdi-plus"></i> Tambah Location';
                    addBtn.onclick = async function() {
                        const selectedOption = locationSelect.options[locationSelect.selectedIndex];
                        if (!selectedOption || !selectedOption.value) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Pilih Location',
                                text: 'Silakan pilih location terlebih dahulu'
                            });
                            return;
                        }

                        const locationData = JSON.parse(selectedOption.getAttribute('data-location'));

                        // Show loading indicator
                        const loadingToast = Swal.fire({
                            title: 'Mengambil Stock...',
                            text: 'Sedang mencari stock kertas untuk location ' + locationData.Code,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        try {
                            // Ambil material codes dari data-paper-code di header kolom kertas
                            const materialCodes = [];
                            const headerRow = document.getElementById('workspace-header');
                            if (headerRow) {
                                const paperHeaderCells = headerRow.querySelectorAll('th[data-paper-code]');
                                paperHeaderCells.forEach(function(cell) {
                                    const paperCode = cell.getAttribute('data-paper-code');
                                    if (paperCode && !materialCodes.includes(paperCode)) {
                                        materialCodes.push(paperCode);
                                    }
                                });
                            }

                            // Fetch stock data untuk location yang baru ditambahkan dengan filter material code
                            const stockData = await fetchPaperStockByLocation(locationData.Code, materialCodes);
                            locationData.stockData = stockData; // Simpan stock data ke location object
                            locationData.materialCodes = [...materialCodes]; // Simpan material codes untuk cache

                            selectedLocations.push(locationData);

                            // Close loading
                            Swal.close();

                            // Re-render workspace untuk update tampilan dengan stock data
                            renderWorkspace().catch(err => console.error('Error rendering workspace:', err));
                        } catch (error) {
                            console.error('Error fetching stock:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal mengambil stock kertas. Silakan coba lagi.'
                            });
                        }
                    };

                    addLocationContainer.appendChild(locationSelect);
                    addLocationContainer.appendChild(addBtn);
                    addLocationCell.appendChild(addLocationContainer);
                    addLocationRow.appendChild(addLocationCell);

                    // Kolom kertas untuk row add location - kosong
                    allPaperColumns.forEach(function() {
                        const emptyCell = document.createElement('td');
                        emptyCell.style.backgroundColor = '#f0f0f0';
                        addLocationRow.appendChild(emptyCell);
                    });

                    // Note: Location rows sudah di-append di dalam loop untuk async stock loading
                    // Append add location row
                    tbody.appendChild(addLocationRow);

                    // Section 2: Total Stok - 3 Layer (Orange Highlight)
                    // Fungsi untuk menghitung total dari semua location rows
                    function calculateTotalFromLocations(paperCode) {
                        let total = 0;
                        selectedLocations.forEach(function(location) {
                            // Cari input field untuk location dan paper code ini
                            const locationCode = location.Code || location.Name;
                            const inputName = `stock_${locationCode}[${paperCode}]`;
                            const input = document.querySelector(`input[name="${inputName}"]`);
                            if (input) {
                                // Parse nilai dengan handle koma sebagai separator desimal
                                const inputValue = input.value || '0';
                                const numValue = parseFloat(String(inputValue).replace(',', '.')) || 0;
                                total += numValue;
                            }
                        });
                        return total;
                    }

                    // Fungsi untuk menghitung Layer 2 berdasarkan Layer 1 dan UP dari input per jenis kertas
                    function calculateLayer2FromItems(paperCode, paperType) {
                        // Ambil Layer 1 (total dari semua locations untuk kertas ini)
                        const layer1Value = calculateTotalFromLocations(paperCode);

                        // Ambil UP dari input field per jenis kertas (di baris setelah header)
                        let upValue = 5; // Default UP
                        const upInput = document.querySelector(`input[name="paper_up[${paperType}]"]`);
                        if (upInput && upInput.value !== '') {
                            upValue = parseFloat(upInput.value) || 5;
                        }

                        // Layer 2 = Layer 1 × UP × 500
                        const layer2Value = layer1Value * upValue * 500;
                        return layer2Value;
                    }

                    // Fungsi untuk update Layer 1 dan Layer 2
                    function updateTotalStokLayers() {
                        allPaperColumns.forEach(function(paperCol) {
                            const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                            // Layer 1: Akumulasi dari semua location rows (atas Layer 1)
                            // Parse nilai dari input dengan handle koma
                            let layer1Value = calculateTotalFromLocations(paperCode);
                            // Pastikan layer1Value adalah number (handle koma jika ada)
                            layer1Value = parseFloat(String(layer1Value).replace(',', '.')) || 0;

                            // Update Layer 1 - cari cell berdasarkan hidden input name
                            const layer1HiddenInput = totalStokRow1.querySelector(`input[name="stock_total_layer1[${paperCol.paperType}]"]`);
                            if (layer1HiddenInput && layer1HiddenInput.parentElement) {
                                const layer1Cell = layer1HiddenInput.parentElement;
                                layer1Cell.textContent = formatNumber(layer1Value);
                                layer1HiddenInput.value = layer1Value;
                                // Re-append hidden input karena textContent menghapus semua child
                                layer1Cell.appendChild(layer1HiddenInput);
                            }

                            // Layer 2: Hitung berdasarkan UP dari input per jenis kertas
                            const layer2Value = calculateLayer2FromItems(paperCode, paperCol.paperType);

                            // Update Layer 2 - cari cell berdasarkan hidden input name
                            const layer2HiddenInput = totalStokRow2.querySelector(`input[name="stock_total_layer2[${paperCol.paperType}]"]`);
                            if (layer2HiddenInput && layer2HiddenInput.parentElement) {
                                const layer2Cell = layer2HiddenInput.parentElement;
                                layer2Cell.textContent = formatNumber(layer2Value);
                                layer2HiddenInput.value = layer2Value;
                                // Re-append hidden input karena textContent menghapus semua child
                                layer2Cell.appendChild(layer2HiddenInput);
                            }
                        });
                    }

                    // Layer 1: Akumulasi total dari semua lokasi
                    const totalStokRow1 = document.createElement('tr');
                    totalStokRow1.className = 'total-stok-row';
                    const totalStokCell1 = document.createElement('td');
                    totalStokCell1.setAttribute('colspan', 7);
                    totalStokCell1.textContent = 'Total Stok (Layer 1)';
                    totalStokCell1.style.backgroundColor = '#ffcc99';
                    totalStokCell1.style.fontWeight = 'bold';
                    totalStokCell1.style.fontSize = '15px';
                    totalStokRow1.appendChild(totalStokCell1);

                    allPaperColumns.forEach(function(paperCol) {
                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        const totalValue = calculateTotalFromLocations(paperCode);
                        // Layer 1: Read-only, menampilkan total dari locations
                        const cell = document.createElement('td');
                        cell.style.backgroundColor = '#ffcc99';
                        cell.style.textAlign = 'right';
                        cell.style.padding = '8px';
                        cell.style.fontWeight = 'bold';
                        cell.style.fontSize = '15px';
                        cell.textContent = formatNumber(totalValue);
                        // Hidden input untuk form submission
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `stock_total_layer1[${paperCol.paperType}]`;
                        hiddenInput.value = totalValue;
                        cell.appendChild(hiddenInput);
                        totalStokRow1.appendChild(cell);
                    });
                    tbody.appendChild(totalStokRow1);

                    // Layer 2: Layer 1 * up * 500 (up default 2)
                    const totalStokRow2 = document.createElement('tr');
                    totalStokRow2.className = 'total-stok-row';
                    const totalStokCell2 = document.createElement('td');
                    totalStokCell2.setAttribute('colspan', 7);
                    totalStokCell2.textContent = 'Total Stok (Layer 2)';
                    totalStokCell2.style.backgroundColor = '#ffcc99';
                    totalStokCell2.style.fontWeight = 'bold';
                    totalStokCell2.style.fontSize = '15px';
                    totalStokRow2.appendChild(totalStokCell2);

                    allPaperColumns.forEach(function(paperCol, index) {
                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        // Layer 2 dihitung berdasarkan UP dari input per jenis kertas
                        const layer2Value = calculateLayer2FromItems(paperCode, paperCol.paperType);

                        // Layer 2: Read-only, menampilkan hasil perhitungan
                        const cell = document.createElement('td');
                        cell.style.backgroundColor = '#ffcc99';
                        cell.style.textAlign = 'right';
                        cell.style.padding = '8px';
                        cell.style.fontWeight = 'bold';
                        cell.style.fontSize = '15px';
                        cell.textContent = formatNumber(layer2Value);
                        // Hidden input untuk form submission
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `stock_total_layer2[${paperCol.paperType}]`;
                        hiddenInput.value = layer2Value;
                        cell.appendChild(hiddenInput);
                        totalStokRow2.appendChild(cell);
                    });
                    tbody.appendChild(totalStokRow2);

                    // Layer 3: Kosong
                    const totalStokRow3 = document.createElement('tr');
                    totalStokRow3.className = 'total-stok-row';
                    const totalStokCell3 = document.createElement('td');
                    totalStokCell3.setAttribute('colspan', 7);
                    totalStokCell3.textContent = 'Total Stok (Layer 3)';
                    totalStokCell3.style.backgroundColor = '#ffcc99';
                    totalStokCell3.style.fontWeight = 'bold';
                    totalStokCell3.style.fontSize = '15px';
                    totalStokRow3.appendChild(totalStokCell3);

                    allPaperColumns.forEach(function(paperCol) {
                        const cell = createStockCell('0', '#ffcc99', `stock_total_layer3[${paperCol.paperType}]`,
                            paperCol.paperType);
                        cell.style.fontWeight = 'bold';
                        const input = cell.querySelector('input');
                        if (input) {
                            input.style.fontWeight = 'bold';
                        }
                        totalStokRow3.appendChild(cell);
                    });
                    tbody.appendChild(totalStokRow3);

                    // Attach event listener ke semua input stock di location rows untuk update Layer 1 & 2
                    setTimeout(function() {
                        selectedLocations.forEach(function(location) {
                            const locationCode = location.Code || location.Name;
                            allPaperColumns.forEach(function(paperCol) {
                                const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                                const inputName = `stock_${locationCode}[${paperCode}]`;
                                const input = document.querySelector(`input[name="${inputName}"]`);
                                if (input) {
                                    input.addEventListener('input', function() {
                                        updateTotalStokLayers();
                                    });
                                    input.addEventListener('change', function() {
                                        updateTotalStokLayers();
                                    });
                                }
                            });
                        });

                        // Attach event listener ke semua input UP per jenis kertas (di baris setelah header)
                        allPaperColumns.forEach(function(paperCol) {
                            const upInputName = `paper_up[${paperCol.paperType}]`;
                            const upInput = document.querySelector(`input[name="${upInputName}"]`);
                            if (upInput) {
                                upInput.addEventListener('input', function() {
                                    updateTotalStokLayers();
                                });
                                upInput.addEventListener('change', function() {
                                    updateTotalStokLayers();
                                });
                            }
                        });
                    }, 100);

                    // Section 3: MINUS PAPER (Yellow Highlight)
                    const minusPaperRow = document.createElement('tr');
                    minusPaperRow.className = 'minus-paper-row';
                    const minusPaperCell = document.createElement('td');
                    minusPaperCell.setAttribute('colspan', 7);
                    minusPaperCell.textContent = 'MINUS PAPER';
                    minusPaperCell.style.backgroundColor = '#fff2cc';
                    minusPaperCell.style.fontWeight = 'bold';
                    minusPaperCell.style.fontSize = '15px';
                    minusPaperRow.appendChild(minusPaperCell);

                    // Kolom kertas untuk MINUS PAPER dengan input field
                    allPaperColumns.forEach(function(paperCol) {
                        const cell = createStockCell('0', '#fff2cc', `minus_paper[${paperCol.paperType}]`,
                            paperCol.paperType);
                        cell.style.fontWeight = 'bold';
                        // Make input bold too
                        const input = cell.querySelector('input');
                        if (input) {
                            input.style.fontWeight = 'bold';
                        }
                        minusPaperRow.appendChild(cell);
                    });
                    tbody.appendChild(minusPaperRow);

                    // Section 4: COVER SAMPAI (untuk bulan) - menggunakan dropdown/select
                    const coverSampaiRow = document.createElement('tr');
                    coverSampaiRow.className = 'cover-sampai-row';
                    const coverSampaiCell = document.createElement('td');
                    coverSampaiCell.setAttribute('colspan', 7);
                    coverSampaiCell.textContent = 'COVER SAMPAI';
                    coverSampaiCell.style.fontWeight = 'bold';
                    coverSampaiCell.style.fontSize = '15px';
                    coverSampaiRow.appendChild(coverSampaiCell);

                    // Kolom kertas untuk COVER SAMPAI (dropdown bulan)
                    allPaperColumns.forEach(function(paperCol) {
                        const cell = document.createElement('td');
                        cell.className = 'cover-sampai-cell';
                        cell.style.textAlign = 'center';
                        cell.style.padding = '8px';

                        const select = document.createElement('select');
                        select.className = 'excel-input';
                        select.name = `cover_sampai[${paperCol.paperType}]`;
                        select.setAttribute('data-paper-type', paperCol.paperType);
                        select.style.width = '100%';
                        select.style.border = 'none';
                        select.style.background = 'transparent';
                        select.style.textAlign = 'center';
                        select.style.fontSize = '15px';
                        select.style.fontFamily = 'Segoe UI, Arial, sans-serif';
                        select.style.padding = '6px 8px';
                        select.style.fontWeight = 'bold';
                        select.style.cursor = 'pointer';

                        // Options untuk bulan
                        const months = [{
                                value: 'JAN',
                                label: 'JAN'
                            },
                            {
                                value: 'FEB',
                                label: 'FEB'
                            },
                            {
                                value: 'MAR',
                                label: 'MAR'
                            },
                            {
                                value: 'APR',
                                label: 'APR'
                            },
                            {
                                value: 'MAY',
                                label: 'MAY'
                            },
                            {
                                value: 'JUN',
                                label: 'JUN'
                            },
                            {
                                value: 'JUL',
                                label: 'JUL'
                            },
                            {
                                value: 'AUG',
                                label: 'AUG'
                            },
                            {
                                value: 'SEP',
                                label: 'SEP'
                            },
                            {
                                value: 'OCT',
                                label: 'OCT'
                            },
                            {
                                value: 'NOV',
                                label: 'NOV'
                            },
                            {
                                value: 'DEC',
                                label: 'DEC'
                            }
                        ];

                        months.forEach(function(month) {
                            const option = document.createElement('option');
                            option.value = month.value;
                            option.textContent = month.label;
                            if (month.value === periodMonth3.toUpperCase()) {
                                option.selected = true;
                            }
                            select.appendChild(option);
                        });

                        // Style untuk focus
                        select.addEventListener('focus', function() {
                            this.style.outline = '2px solid #0078D4';
                            this.style.backgroundColor = '#E7F3FF';
                        });

                        select.addEventListener('blur', function() {
                            this.style.outline = 'none';
                            this.style.backgroundColor = 'transparent';
                        });

                        cell.appendChild(select);
                        coverSampaiRow.appendChild(cell);
                    });
                    tbody.appendChild(coverSampaiRow);

                    // FORCE UPDATE WARNA untuk semua cell kolom kertas setelah semua row di-render
                    // Ini penting untuk memastikan warna kertas ter-apply dengan benar
                    // Gunakan multiple attempts untuk memastikan style ter-apply
                    function forceUpdatePaperCellColors() {
                        const allRows = tbody.querySelectorAll('tr[data-item-id]');
                        let updatedCount = 0;
                        allRows.forEach(row => {
                            const paperCells = row.querySelectorAll('td[class*="tg-paper-"]');
                            paperCells.forEach(cell => {
                                // Cari paperType dari class
                                const classList = Array.from(cell.classList);
                                const paperClass = classList.find(cls => cls.startsWith('tg-paper-'));
                                if (paperClass) {
                                    // Extract paper type dari class
                                    let paperType = null;
                                    for (const key in paperTypeColors) {
                                        const expectedClass = getPaperClass(key);
                                        if (expectedClass === paperClass) {
                                            paperType = key;
                                            break;
                                        }
                                    }
                                    if (paperType && paperTypeColors[paperType]) {
                                        const paperColor = paperTypeColors[paperType];
                                        // Force set dengan !important - multiple attempts
                                        cell.style.setProperty('background-color', paperColor,
                                            'important');
                                        cell.style.setProperty('background', paperColor, 'important');
                                        // Remove any other background styles
                                        cell.style.removeProperty('background-image');
                                        // Also remove background-position, background-size, etc
                                        cell.style.removeProperty('background-position');
                                        cell.style.removeProperty('background-size');
                                        cell.style.removeProperty('background-repeat');
                                        cell.style.removeProperty('background-attachment');
                                        // Force reflow
                                        void cell.offsetHeight;
                                        updatedCount++;
                                    } else {
                                        console.warn('Force update - PaperType not found or no color:',
                                            paperType, 'Class:', paperClass);
                                    }
                                }
                            });
                        });
                        // console.log('Force update warna kolom kertas selesai, updated:', updatedCount, 'cells');
                        return updatedCount;
                    }

                    // Multiple attempts dengan timing berbeda untuk memastikan style ter-apply
                    setTimeout(() => {
                        requestAnimationFrame(() => {
                            forceUpdatePaperCellColors();
                            // Second attempt setelah render
                            setTimeout(() => {
                                requestAnimationFrame(() => {
                                    forceUpdatePaperCellColors();
                                    // Third attempt setelah render
                                    setTimeout(() => {
                                        requestAnimationFrame(() => {
                                            forceUpdatePaperCellColors
                                        ();
                                        });
                                    }, 200);
                                });
                            }, 100);
                        });
                    }, 50);

                    // Gunakan MutationObserver untuk memastikan warna tetap ter-apply jika ada perubahan DOM
                    const observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                                // Jika style berubah, force update lagi
                                const cell = mutation.target;
                                if (cell && cell.classList && cell.classList.toString().includes(
                                        'tg-paper-')) {
                                    const classList = Array.from(cell.classList);
                                    const paperClass = classList.find(cls => cls.startsWith(
                                        'tg-paper-'));
                                    if (paperClass) {
                                        let paperType = null;
                                        for (const key in paperTypeColors) {
                                            const expectedClass = getPaperClass(key);
                                            if (expectedClass === paperClass) {
                                                paperType = key;
                                                break;
                                            }
                                        }
                                        if (paperType && paperTypeColors[paperType]) {
                                            const paperColor = paperTypeColors[paperType];
                                            cell.style.setProperty('background-color', paperColor,
                                                'important');
                                            cell.style.setProperty('background', paperColor,
                                                'important');
                                        }
                                    }
                                }
                            }
                        });
                    });

                    // Observe semua cell kolom kertas
                    setTimeout(() => {
                        const allPaperCells = tbody.querySelectorAll('td[class*="tg-paper-"]');
                        allPaperCells.forEach(cell => {
                            observer.observe(cell, {
                                attributes: true,
                                attributeFilter: ['style', 'class']
                            });
                        });
                    }, 200);

                    // Inject CSS langsung di JavaScript sebagai fallback untuk memastikan warna ter-apply
                    setTimeout(() => {
                        const dynamicStyleId = 'paper-procurement-force-colors';
                        let dynamicStyle = document.getElementById(dynamicStyleId);
                        if (!dynamicStyle) {
                            dynamicStyle = document.createElement('style');
                            dynamicStyle.id = dynamicStyleId;
                            document.head.appendChild(dynamicStyle);
                        }

                        // Generate CSS untuk setiap jenis kertas dengan spesifisitas sangat tinggi
                        let cssText = '';
                        for (const paperType in paperTypeColors) {
                            const paperColor = paperTypeColors[paperType];
                            const paperClass = getPaperClass(paperType);
                            cssText += `
                                table#workspace-table.tg tbody tr[data-item-id] td.${paperClass},
                                table#workspace-table.tg tbody tr[data-item-id] td.${paperClass}[style] {
                                    background-color: ${paperColor} !important;
                                    background: ${paperColor} !important;
                                    background-image: none !important;
                                }
                            `;
                        }
                        dynamicStyle.textContent = cssText;
                        // console.log('Dynamic CSS injected for paper colors');
                    }, 300);

                    // Gunakan interval untuk terus memastikan warna ter-apply (jika ada perubahan DOM)
                    // Langsung force update semua paper cell tanpa perlu cek warna computed
                    const colorCheckInterval = setInterval(() => {
                        const allRows = tbody.querySelectorAll('tr[data-item-id]');
                        allRows.forEach(row => {
                            const paperCells = row.querySelectorAll('td[class*="tg-paper-"]');
                            paperCells.forEach(cell => {
                                const classList = Array.from(cell.classList);
                                const paperClass = classList.find(cls => cls.startsWith(
                                    'tg-paper-'));
                                if (paperClass) {
                                    let paperType = null;
                                    for (const key in paperTypeColors) {
                                        const expectedClass = getPaperClass(key);
                                        if (expectedClass === paperClass) {
                                            paperType = key;
                                            break;
                                        }
                                    }
                                    if (paperType && paperTypeColors[paperType]) {
                                        const paperColor = paperTypeColors[paperType];
                                        // Langsung force update tanpa perlu cek warna computed
                                        forceSetCellColor(cell, paperColor);
                                    }
                                }
                            });
                        });
                    }, 200);

                    // Stop interval setelah 5 detik untuk menghindari infinite loop
                    setTimeout(() => {
                        clearInterval(colorCheckInterval);
                    }, 5000);

                }

                // Fungsi untuk attach event listeners di tabel daftar item
                function attachItemsListEventListeners() {
                    // Event untuk input nama item
                    $(document).off('input', '.item-name-input');
                    $(document).on('input', '.item-name-input', function() {
                        const itemId = $(this).data('item-id');
                        const newName = $(this).val();
                        // Update hidden input untuk form submission
                        $(`input[name="items[${itemId}][product_name]"]`).val(newName);
                        // Update item data
                        const item = items.find(i => i.id === itemId);
                        if (item) {
                            item.name = newName;
                        }
                    });

                    // Event untuk input kuantitas per bulan
                    $(document).off('input', '.item-qty-month-1, .item-qty-month-2, .item-qty-month-3');
                    $(document).on('input', '.item-qty-month-1, .item-qty-month-2, .item-qty-month-3', function() {
                        const itemId = $(this).data('item-id');
                        calculateItemTotals(itemId);
                    });

                    // Event untuk input kuantitas kertas
                    $(document).off('input', '.item-paper-qty');
                    $(document).on('input', '.item-paper-qty', function() {
                        const itemId = $(this).data('item-id');
                        const paperCode = $(this).data('paper-code'); // Sekarang menggunakan code, bukan paper-type
                        const value = parseNumber($(this).val()) || 0;

                        // Update hidden input untuk form submission
                        const card = $(`#items-list-container .card[data-item-id="${itemId}"]`);
                        card.find(`input[name="items[${itemId}][papers][${paperCode}][quantity]"]`).val(value);

                        const item = items.find(i => i.id === itemId);
                        if (item) {
                            if (!item.paperQuantities) {
                                item.paperQuantities = {};
                            }
                            item.paperQuantities[paperCode] = value; // Gunakan code sebagai key
                        }

                        // Update preview workspace
                        renderWorkspace();
                    });

                    // Note: Event handler untuk checkbox kertas dihapus karena sekarang menggunakan Select2
                    // Kertas dipilih saat menambah item, bukan melalui checkbox di card
                }

                // Fungsi untuk menghitung total di tabel daftar item
                function calculateItemTotals(itemId) {
                    const card = $(`#items-list-container .card[data-item-id="${itemId}"]`);
                    if (card.length === 0) return;

                    const qty1 = parseNumber(card.find('.item-qty-month-1').val()) || 0;
                    const qty2 = parseNumber(card.find('.item-qty-month-2').val()) || 0;
                    const qty3 = parseNumber(card.find('.item-qty-month-3').val()) || 0;

                    const total = qty1 + qty2 + qty3;
                    const totalWithTolerance = Math.ceil(total * (1 + tolerance / 100));

                    card.find('.item-total[data-item-id="' + itemId + '"]').val(formatNumber(total));
                    card.find('.item-total-tolerance[data-item-id="' + itemId + '"]').val(formatNumber(
                        totalWithTolerance));

                    // Update hidden inputs untuk form submission
                    card.find(`input[name="items[${itemId}][qty_month_1]"]`).val(qty1);
                    card.find(`input[name="items[${itemId}][qty_month_2]"]`).val(qty2);
                    card.find(`input[name="items[${itemId}][qty_month_3]"]`).val(qty3);

                    // Update item data
                    const item = items.find(i => i.id === itemId);
                    if (item) {
                        item.qty1 = qty1;
                        item.qty2 = qty2;
                        item.qty3 = qty3;
                    }

                    // Auto fill paper quantity jika masih 0
                    card.find('.item-paper-qty[data-item-id="' + itemId + '"]').each(function() {
                        const currentVal = parseNumber($(this).val());
                        if (currentVal === 0 && totalWithTolerance > 0) {
                            $(this).val(formatNumber(totalWithTolerance));
                            const paperCode = $(this).data('paper-code'); // Sekarang menggunakan code
                            if (item && item.paperQuantities) {
                                item.paperQuantities[paperCode] = totalWithTolerance;
                            }
                            // Update hidden input untuk quantity
                            card.find(`input[name="items[${itemId}][papers][${paperCode}][quantity]"]`).val(
                                totalWithTolerance);
                        }
                    });

                    // Update preview workspace juga
                    renderWorkspace().catch(err => console.error('Error rendering workspace:', err)).then(() => {
                        // Update Layer 2 setelah workspace di-render karena qty item berubah
                        if (typeof updateTotalStokLayers === 'function') {
                            updateTotalStokLayers();
                        }
                    });
                }

                // Fungsi untuk attach event listeners di preview workspace (read-only)
                function attachEventListeners() {
                    // Preview workspace tidak perlu event listeners karena read-only
                }

                // Fungsi untuk tambah item
                $('#btn-add-item').on('click', function() {
                    console.log('Add item button clicked');
                    const itemCode = $('#item_code').val().trim();
                    const itemName = $('#item_name_text').val().trim();
                    const itemNameDisplay = $('#item_name').val().trim(); // Code - Name format

                    // Ambil kertas yang dipilih dari Select2 (multiple selection)
                    const selectedPapers = [];
                    $('#item_papers').select2('data').forEach(function(paperData) {
                        // Simpan sebagai object dengan Code dan Name (bukan paperType)
                        selectedPapers.push({
                            code: paperData.code,
                            name: paperData.name,
                            paperType: paperData.paperType || null, // Untuk backward compatibility
                            color: paperData.color || '#ffffff'
                        });
                    });

                    console.log('Item Code:', itemCode);
                    console.log('Item Name:', itemName);
                    console.log('Item Name Display:', itemNameDisplay);
                    console.log('Selected Papers:', selectedPapers);

                    if (!itemCode || !itemName) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Material Harus Dipilih',
                                text: 'Silakan pilih material dari database terlebih dahulu.'
                            });
                        } else {
                            alert('Material Harus Dipilih');
                        }
                        return;
                    }

                    if (selectedPapers.length === 0) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Pilih Kertas',
                                text: 'Silakan pilih minimal satu jenis kertas yang digunakan.'
                            });
                        } else {
                            alert('Silakan pilih minimal satu jenis kertas yang digunakan.');
                        }
                        return;
                    }

                    // Tambahkan kolom kertas baru jika belum ada
                    // Sekarang menggunakan Code sebagai identifier, bukan paperType
                    selectedPapers.forEach(function(paperObj) {
                        console.log('Processing paper:', paperObj);
                        const paperCode = paperObj.code;
                        const paperName = paperObj.name;
                        const paperType = paperObj.paperType || paperCode; // Fallback ke code jika paperType tidak ada

                        // Cek apakah kolom kertas dengan Code ini sudah ada
                        const exists = allPaperColumns.find(col =>
                            col.paper && col.paper.code === paperCode
                        );

                        if (!exists) {
                            // Buat object paper dengan Code dan Name
                            allPaperColumns.push({
                                paperType: paperType,
                                paper: {
                                    code: paperCode,
                                    name: paperName,
                                    size: '', // Akan diisi jika ada di database
                                    color: paperObj.color || '#ffffff'
                                }
                            });
                        }
                    });

                    console.log('All Paper Columns:', allPaperColumns);
                    console.log('Selected Papers:', selectedPapers);

                    // Buat item baru - papers sekarang berisi object dengan Code dan Name
                    const newItem = {
                        id: 'item_' + (++itemCounter),
                        code: itemCode,
                        name: itemName,
                        nameDisplay: itemNameDisplay, // Code - Name format untuk display
                        papers: selectedPapers, // Array of objects: [{code, name, paperType, color}, ...]
                        qty1: 0,
                        qty2: 0,
                        qty3: 0,
                        up: 5, // Default UP value
                        paperQuantities: {}
                    };

                    items.push(newItem);
                    console.log('Items after add:', items);
                    console.log('New item papers:', newItem.papers);

                    // Render tabel daftar item dan workspace
                    updateItemsListHeader();
                    renderItemsList();
                    renderWorkspace().catch(err => console.error('Error rendering workspace:', err));

                    // Reset form
                    $('#item_name').val(null).trigger('change');
                    $('#item_code').val('');
                    $('#item_name_text').val('');
                    $('#item_papers').val(null).trigger('change'); // Clear Select2

                    // Scroll ke tabel daftar item
                    $('html, body').animate({
                        scrollTop: $('#items-list-container').offset().top - 100
                    }, 500);
                });

                // Fungsi untuk update item
                function updateItem(itemId) {
                    const item = items.find(i => i.id === itemId);
                    if (!item) return;

                    const card = $(`#items-list-container .card[data-item-id="${itemId}"]`);
                    const newName = card.find('.item-name-input').val().trim();
                    const selectedPapers = [];

                    card.find('.item-paper-checkbox:checked').each(function() {
                        selectedPapers.push($(this).val());
                    });

                    if (!newName) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Nama Item Harus Diisi',
                            text: 'Silakan masukkan nama item terlebih dahulu.'
                        });
                        return;
                    }

                    if (selectedPapers.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilih Kertas',
                            text: 'Silakan pilih minimal satu jenis kertas yang digunakan.'
                        });
                        return;
                    }

                    // Update item - papers sekarang berisi object dengan Code dan Name
                    item.name = newName;
                    // selectedPapers sudah dalam format object (dari Select2), jadi langsung assign
                    item.papers = selectedPapers;

                    // Update hidden input untuk form submission
                    card.find(`input[name="items[${itemId}][product_name]"]`).val(newName);

                    // Update kolom kertas jika ada kertas baru
                    selectedPapers.forEach(function(paperObj) {
                        const paperCode = paperObj.code;
                        const paperName = paperObj.name;
                        const paperType = paperObj.paperType || paperCode;

                        // Cek apakah kolom kertas dengan Code ini sudah ada
                        const exists = allPaperColumns.find(col =>
                            col.paper && col.paper.code === paperCode
                        );

                        if (!exists) {
                            // Buat object paper dengan Code dan Name
                            allPaperColumns.push({
                                paperType: paperType,
                                paper: {
                                    code: paperCode,
                                    name: paperName,
                                    size: '',
                                    color: paperObj.color || '#ffffff'
                                }
                            });
                        }
                    });

                    // Update kolom kertas (hapus yang tidak digunakan)
                    const usedPaperCodes = new Set();
                    items.forEach(item => {
                        if (item.papers && item.papers.length > 0) {
                            item.papers.forEach(paper => {
                                // Jika paper adalah object, ambil code; jika string, gunakan string
                                const paperCode = typeof paper === 'object' ? paper.code : paper;
                                usedPaperCodes.add(paperCode);
                            });
                        }
                    });

                    allPaperColumns = allPaperColumns.filter(col => {
                        const paperCode = col.paper ? col.paper.code : null;
                        return paperCode && usedPaperCodes.has(paperCode);
                    });

                    // Render ulang
                    updateItemsListHeader();
                    renderItemsList();
                    renderWorkspace().catch(err => console.error('Error rendering workspace:', err));

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Item berhasil diupdate.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }

                // Fungsi untuk hapus item
                function removeItem(itemId) {
                    Swal.fire({
                        title: 'Hapus Item?',
                        text: 'Item akan dihapus dari daftar.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            items = items.filter(item => item.id !== itemId);

                            // Update kolom kertas (hapus yang tidak digunakan)
                            const usedPapers = new Set();
                            items.forEach(item => {
                                item.papers.forEach(paper => usedPapers.add(paper));
                            });

                            allPaperColumns = allPaperColumns.filter(col =>
                                usedPapers.has(col.paperType)
                            );

                            updateItemsListHeader();
                            renderItemsList();
                            renderWorkspace().catch(err => console.error('Error rendering workspace:', err));
                        }
                    });
                }

                // Reset form
                $('#btn-clear-form').on('click', function() {
                    $('#item_name').val(null).trigger('change');
                    $('#item_papers').val(null).trigger('change');
                });
            });
        </script>
    @endsection
@endsection
