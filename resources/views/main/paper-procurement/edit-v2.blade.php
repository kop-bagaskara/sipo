@extends('main.layouts.main')
@section('title')
    Edit Pengajuan Meeting Kertas - {{ $meeting->meeting_number }}
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .excel-container {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            margin-top: 20px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid #f0f0f0;
            transition: box-shadow 0.3s ease;
        }

        .excel-container:hover {
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08), 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        .excel-title {
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 35px;
            color: #1a1a1a;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            padding-bottom: 20px;
            border-bottom: 2px solid #e8e8e8;
            position: relative;
        }

        .excel-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #4472C4 0%, #5b8fd8 100%);
            border-radius: 2px;
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
            padding: 14px 12px;
            border: none;
            border-right: 1px solid #f0f0f0;
            border-bottom: 1px solid #f5f5f5;
            text-align: center;
            vertical-align: middle;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500 !important;
            font-size: 14px;
        }

        .excel-table td:last-child {
            border-right: none;
        }

        .excel-table tbody tr {
            transition: all 0.2s ease;
        }

        .excel-table tbody tr:hover td {
            background-color: rgba(68, 114, 196, 0.04) !important;
            transform: scale(1.01);
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
            font-weight: bold !important;
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
            font-weight: bold !important;
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
            border: 1px solid #e0e0e0;
            background: #fff;
            text-align: right;
            padding: 10px 14px;
            font-size: 14px;
            font-family: 'Segoe UI', Arial, sans-serif;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .excel-input:focus {
            outline: none;
            border-color: #4472C4;
            background-color: #f8f9ff;
            box-shadow: 0 0 0 3px rgba(68, 114, 196, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
            transform: translateY(-1px);
        }

        .excel-input:hover {
            border-color: #b0b0b0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
        }

        .excel-input:read-only {
            background-color: #f5f5f5;
            border-color: #e0e0e0;
            cursor: not-allowed;
            box-shadow: none;
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
            width: max-content;
            min-width: 100%;
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
            font-weight: bold !important;
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
            border: none;
            border-right: 1px solid #f0f0f0;
            border-bottom: 1px solid #f5f5f5;
            padding: 14px 12px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stock-detail-row:hover td,
        .total-stok-row:hover td,
        .minus-paper-row:hover td,
        .cover-sampai-row:hover td {
            background-color: rgba(0, 0, 0, 0.02) !important;
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

        /* Styling untuk summary total kebutuhan ton */
        .summary-total-ton-row {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%) !important;
            border-top: 2px solid #4caf50 !important;
        }

        .summary-total-ton-row td {
            font-weight: 600 !important;
            font-size: 15px !important;
            border: none !important;
            border-right: 1px solid rgba(76, 175, 80, 0.2) !important;
            padding: 16px 12px !important;
        }

        .summary-total-ton-cell {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%) !important;
            border-left: 3px solid #4caf50 !important;
        }

        /* Styling untuk row catatan per jenis kertas */
        .catatan-kertas-row {
            background-color: #f8f9fa !important;
        }

        .catatan-kertas-row td {
            border: none !important;
            border-right: 1px solid #dee2e6 !important;
            padding: 8px !important;
        }

        .catatan-kertas-cell {
            background-color: #ffffff !important;
        }

        .catatan-kertas-input {
            transition: all 0.2s ease;
        }

        .catatan-kertas-input:focus {
            outline: none;
            border-color: #4472C4 !important;
            box-shadow: 0 0 0 3px rgba(68, 114, 196, 0.1) !important;
        }

        .summary-grand-total-cell {
            background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%) !important;
            color: #fff !important;
            font-weight: 700 !important;
            border: none !important;
        }

        /* Modern styling untuk semua buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        }

        /* Modern select dropdown */
        select.form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 10px 14px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        select.form-control:focus {
            border-color: #4472C4;
            box-shadow: 0 0 0 3px rgba(68, 114, 196, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
        }
            color: #fff !important;
            font-weight: 700 !important;
            border: none !important;
        }

        /* Modern styling untuk semua buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        }

        .btn:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        }

        /* Modern select dropdown */
        select.form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 10px 14px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        select.form-control:focus {
            border-color: #4472C4;
            box-shadow: 0 0 0 3px rgba(68, 114, 196, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
        }
            color: #ffffff !important;
            font-size: 18px !important;
            font-weight: bold !important;
            border-left: 3px solid #155724 !important;
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

        /* Table wrapper dengan scroll horizontal yang jelas */
        .table-responsive {
            border-radius: 12px;
            overflow-x: auto;
            overflow-y: visible;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            position: relative;
            width: 100%;
            -webkit-overflow-scrolling: touch; /* Smooth scroll untuk mobile */
            background: #fff;
            border: 1px solid #f0f0f0;
        }

        /* Pastikan tabel tidak wrap dan bisa scroll horizontal */
        #workspace-table {
            width: max-content !important;
            min-width: 100%;
            table-layout: auto;
            border-collapse: separate;
            border-spacing: 0;
            display: table !important;
            background: #fff;
        }

        /* Pastikan cell tidak wrap */
        #workspace-table td,
        #workspace-table th {
            white-space: nowrap;
            vertical-align: middle;
            display: table-cell !important;
        }

        /* Pastikan thead dan tbody tetap sebagai table-row-group */
        #workspace-table thead,
        #workspace-table tbody {
            display: table-header-group;
        }

        #workspace-table tbody {
            display: table-row-group;
        }

        #workspace-table tr {
            display: table-row !important;
        }

        /* Header tabel - tidak sticky untuk menghindari overlap dengan topbar */
        #workspace-table thead {
            position: relative !important;
            top: auto !important;
            z-index: auto !important;
        }

        #workspace-table thead th {
            position: relative !important;
            top: auto !important;
            z-index: auto !important;
            background: linear-gradient(135deg, #4a6fa5 0%, #5b8fd8 100%) !important;
            border: none !important;
            border-right: 1px solid rgba(255, 255, 255, 0.2) !important;
        }

        #workspace-table thead th:last-child {
            border-right: none !important;
        }

        /* Pastikan container tidak naik ke atas */
        .excel-container {
            position: relative !important;
            top: auto !important;
            margin-top: 20px !important;
        }

        .table-responsive {
            position: relative !important;
            top: auto !important;
        }

        /* Scrollbar yang lebih jelas dan mudah digunakan */
        .table-responsive::-webkit-scrollbar {
            height: 15px;
            width: 15px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #4472C4;
            border-radius: 8px;
            border: 2px solid #f1f1f1;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #5b8fd8;
        }

        /* Scrollbar untuk Firefox */
        .table-responsive {
            scrollbar-width: auto;
            scrollbar-color: #4472C4 #f1f1f1;
        }

        /* Make all text in workspace table bold */
        #workspace-table,
        #workspace-table *,
        #workspace-table td,
        #workspace-table th,
        #workspace-table input,
        #workspace-table select,
        #workspace-table label,
        #workspace-table .excel-input,
        #workspace-table .form-control,
        .excel-container,
        .excel-container *,
        .excel-container td,
        .excel-container th,
        .excel-container input,
        .excel-container select,
        .excel-container label,
        .excel-container .excel-input,
        .excel-container .form-control {
            font-weight: bold !important;
        }

        /* Make paper header text bold */
        .paper-header,
        .paper-code,
        .paper-name {
            font-weight: bold !important;
        }

        /* Make all summary section text bold */
        .total-stok-row,
        .total-stok-row *,
        .minus-paper-row,
        .minus-paper-row *,
        .cover-sampai-row,
        .cover-sampai-row *,
        .stock-detail-row,
        .stock-detail-row * {
            font-weight: bold !important;
        }

        /* Make location selection text bold */
        .location-select-container,
        .location-select-container *,
        .location-select-container select,
        .location-select-container button {
            font-weight: bold !important;
        }

        /* Make all text in items list bold */
        #items-list-container,
        #items-list-container *,
        #items-list-container td,
        #items-list-container th,
        #items-list-container input,
        #items-list-container label,
        #items-list-container .form-label {
            font-weight: bold !important;
        }

        /* Make PO rows text bold */
        .po-remain-row,
        .po-remain-row *,
        .po-manual-row,
        .po-manual-row * {
            font-weight: bold !important;
        }

        /* Make all select2 text bold */
        .select2-container .select2-selection__rendered,
        .select2-container .select2-selection__choice,
        .select2-results__option {
            font-weight: bold !important;
        }

        /* Make button text bold */
        .btn,
        .btn * {
            font-weight: bold !important;
        }

        /* Make form labels and inputs bold */
        .form-label,
        .form-control,
        .form-group label,
        .add-item-form label,
        .add-item-form input,
        .add-item-form select {
            font-weight: bold !important;
        }

        /* Make card header and body text bold */
        .card-header,
        .card-header *,
        .card-body,
        .card-body * {
            font-weight: bold !important;
        }

        /* Make alert text bold */
        .alert,
        .alert * {
            font-weight: bold !important;
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
            background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%) !important;
            background-color: #fff8e1 !important;
            font-weight: 600;
            color: #333 !important;
            border-right: 1px solid rgba(255, 193, 7, 0.2) !important;
            transition: all 0.2s ease;
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

        /* Global bold for all text in paper procurement page */
        .paper-procurement-page,
        .paper-procurement-page *,
        body[data-sidebar="colored"] .content-wrapper,
        body[data-sidebar="colored"] .content-wrapper * {
            font-weight: bold !important;
        }

        /* Override for specific elements that should be bold */
        .excel-container table td,
        .excel-container table th,
        .excel-container table input,
        .excel-container table select,
        .excel-container table label,
        .excel-container table span,
        .excel-container table div {
            font-weight: bold !important;
        }

        /* Make all numbers and values bold */
        .format-number,
        .number-value,
        .paper-qty-value {
            font-weight: bold !important;
        }
    </style>
@endsection
@section('page-title')
    Edit Pengajuan Meeting Kertas
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Edit Pengajuan Meeting Kertas</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('paper-procurement.index') }}">Pengajuan Kertas</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('paper-procurement.show', $meeting->id) }}">Detail</a></li>
                    <li class="breadcrumb-item active">Edit</li>
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
                                {{ $meeting->meeting_month }}<br>
                                <strong>Periode (3 Bulan):</strong>
                                <span
                                    class="badge bg-info text-white">{{ $allMonths[$meeting->period_month_1] ?? $meeting->period_month_1 }}</span>,
                                <span
                                    class="badge bg-info text-white">{{ $allMonths[$meeting->period_month_2] ?? $meeting->period_month_2 }}</span>,
                                <span
                                    class="badge bg-info text-white">{{ $allMonths[$meeting->period_month_3] ?? $meeting->period_month_3 }}</span>
                                <br>
                                <strong>Customer Group:</strong> <span
                                    class="badge bg-info text-white">{{ $meeting->customer_name }}</span>
                                <br>
                                <strong>Status:</strong> <span
                                    class="badge bg-warning text-white">{{ $meeting->status ?? 'draft' }}</span>
                                <br>
                                <small class="text-muted">
                                    <strong>Items:</strong> {{ $meeting->items->count() ?? 0 }} |
                                    <strong>Locations:</strong> {{ $meeting->locations->count() ?? 0 }} |
                                    <strong>Papers:</strong> {{ $meeting->items->sum(function($item) { return $item->papers->count(); }) ?? 0 }}
                                </small>
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
                        <form id="paper-meeting-form" method="POST" action="{{ route('paper-procurement.update', $meeting->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="meeting_month" value="{{ $meeting->meeting_month }}">
                            <input type="hidden" name="customer_name" value="{{ $meeting->customer_name }}">
                            <input type="hidden" name="period_month_1" value="{{ $meeting->period_month_1 }}">
                            <input type="hidden" name="period_month_2" value="{{ $meeting->period_month_2 }}">
                            <input type="hidden" name="period_month_3" value="{{ $meeting->period_month_3 }}">
                            <input type="hidden" name="tolerance_percentage" value="{{ $meeting->tolerance_percentage ?? 10.00 }}">

                            <div class="excel-container">
                                <div class="excel-title">
                                    KEBUTUHAN MEETING KERTAS BULANAN PPIC
                                </div>

                                <div class="table-responsive">
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
                                    <button type="button" class="btn btn-info me-2" id="btn-show-po">
                                        <i class="mdi mdi-file-document-outline me-1"></i> Show PO
                                    </button>
                                    <a href="{{ route('paper-procurement.index') }}"
                                        class="btn btn-outline-secondary me-2">
                                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="btn-submit">
                                        <i class="mdi mdi-content-save me-1"></i> Update Pengajuan
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
                console.log('Paper Procurement Form Loaded (EDIT MODE)');
                console.log('Paper Types:', @json($paperTypes));
                const tolerance = {{ $meeting->tolerance_percentage ?? 10 }}; // Tolerance dari meeting
                let itemCounter = 0;
                let items = []; // Array untuk menyimpan semua item
                let allPaperColumns = []; // Array untuk menyimpan semua kolom kertas yang digunakan
                let selectedLocations = []; // Array untuk menyimpan locations yang dipilih (global scope)

                // Data kertas dari server
                const paperTypes = @json($paperTypes);
                const productCategories = @json($productCategories ?? []);
                const allLocations = @json($locations ?? []); // Semua locations dari database

                // Data existing dari meeting (untuk EDIT mode)
                const existingMeeting = @json($meeting ?? null);
                const existingItems = @json($meeting->items ?? []);
                const existingLocations = @json($meeting->locations ?? []);
                const existingStocks = @json($meeting->stocks ?? []);
                const existingPORemains = @json($meeting->poRemains ?? []);
                const existingPOManuals = @json($meeting->poManuals ?? []);

                // Debug: Log data yang diterima
                console.log('EDIT MODE: Raw data from server:');
                console.log('- Meeting:', existingMeeting);
                console.log('- Items (raw):', existingItems);
                console.log('- Items count:', existingItems ? existingItems.length : 0);
                console.log('- Locations (raw):', existingLocations);
                console.log('- Locations count:', existingLocations ? existingLocations.length : 0);
                console.log('- Stocks (raw):', existingStocks);
                console.log('- PO Remains (raw):', existingPORemains);
                console.log('- PO Manuals (raw):', existingPOManuals);

                // Load existing data ke dalam items array dan allPaperColumns
                function loadExistingData() {
                    console.log('EDIT MODE: Loading existing meeting data...');
                    console.log('EDIT MODE: existingMeeting:', existingMeeting);
                    console.log('EDIT MODE: existingItems:', existingItems);
                    console.log('EDIT MODE: existingItems length:', existingItems ? existingItems.length : 0);

                    // Reset arrays
                    items = [];
                    allPaperColumns = [];
                    selectedLocations = [];
                    itemCounter = 0;

                    // 1. Load items dari database
                    if (existingItems && Array.isArray(existingItems) && existingItems.length > 0) {
                        console.log('EDIT MODE: Processing', existingItems.length, 'items');
                        existingItems.forEach(function(item, index) {
                            console.log('EDIT MODE: Processing item', index + 1, ':', item);

                            // Kumpulkan semua paper types dari item ini
                            const itemPapers = [];
                            if (item.papers && Array.isArray(item.papers) && item.papers.length > 0) {
                                console.log('EDIT MODE: Item has', item.papers.length, 'papers');
                                item.papers.forEach(function(paper) {
                                    console.log('EDIT MODE: Processing paper:', paper);
                                    itemPapers.push({
                                        code: paper.paper_code || '',
                                        name: paper.paper_name || '',
                                        paperType: paper.paper_type || '',
                                        color: '#ffffff', // Default, akan di-update dari paperTypeColors
                                        zgsm: parseFloat(paper.zgsm) || 0,
                                        zlength: parseFloat(paper.zlength) || 0,
                                        zwidth: parseFloat(paper.zwidth) || 0
                                    });

                                    // Tambahkan ke allPaperColumns jika belum ada
                                    const paperCode = paper.paper_code || '';
                                    const paperType = paper.paper_type || '';
                                    if (paperCode && paperType) {
                                        const exists = allPaperColumns.find(col =>
                                            col.paper && col.paper.code === paperCode
                                        );

                                        if (!exists) {
                                            allPaperColumns.push({
                                                paperType: paperType,
                                                paper: {
                                                    code: paperCode,
                                                    name: paper.paper_name || '',
                                                    size: paper.paper_size || '',
                                                    color: '#ffffff',
                                                    zgsm: parseFloat(paper.zgsm) || 0,
                                                    zlength: parseFloat(paper.zlength) || 0,
                                                    zwidth: parseFloat(paper.zwidth) || 0
                                                }
                                            });
                                            console.log('EDIT MODE: Added paper column:', paperCode, paperType);
                                        }
                                    }
                                });
                            } else {
                                console.warn('EDIT MODE: Item has no papers:', item);
                            }

                            // Buat item object
                            const newItem = {
                                id: 'item_' + (++itemCounter),
                                code: item.product_code || '',
                                name: item.product_name || '',
                                nameDisplay: item.product_code ? `${item.product_code} - ${item.product_name}` : (item.product_name || ''),
                                papers: itemPapers,
                                qty1: parseInt(item.quantity_month_1) || 0,
                                qty2: parseInt(item.quantity_month_2) || 0,
                                qty3: parseInt(item.quantity_month_3) || 0,
                                up: 5, // Default, akan di-update dari paper UP values
                                paperQuantities: {} // Akan diisi dari item.papers
                            };

                            // Load paper quantities
                            if (item.papers && Array.isArray(item.papers) && item.papers.length > 0) {
                                item.papers.forEach(function(paper) {
                                    const paperCode = paper.paper_code || '';
                                    if (paperCode && paper.required_quantity) {
                                        newItem.paperQuantities[paperCode] = parseInt(paper.required_quantity) || 0;
                                    }
                                });
                            }

                            items.push(newItem);
                            console.log('EDIT MODE: Added item:', newItem);
                        });
                    } else {
                        console.warn('EDIT MODE: No existing items found or items is not an array');
                    }

                    // 2. Load locations dari database
                    if (existingLocations && Array.isArray(existingLocations) && existingLocations.length > 0) {
                        console.log('EDIT MODE: Processing', existingLocations.length, 'locations');
                        existingLocations.forEach(function(loc) {
                            selectedLocations.push({
                                Code: loc.location_code || '',
                                Name: loc.location_name || loc.location_code || '',
                                stockData: {}, // Akan di-load saat render
                                materialCodes: []
                            });
                            console.log('EDIT MODE: Added location:', loc.location_code);
                        });
                    } else {
                        console.warn('EDIT MODE: No existing locations found');
                    }

                    // 3. Update UP values dari papers yang sudah ada
                    existingItems.forEach(function(item) {
                        if (item.papers && item.papers.length > 0) {
                            item.papers.forEach(function(paper) {
                                const paperType = paper.paper_type;
                                if (paperType && paper.up_value) {
                                    // UP value akan di-set saat render header
                                }
                            });
                        }
                    });

                    console.log('Loaded items:', items);
                    console.log('Loaded paper columns:', allPaperColumns);
                    console.log('Loaded locations:', selectedLocations);
                }

                    // Panggil loadExistingData saat document ready, lalu render workspace
                $(document).ready(function() {
                    console.log('EDIT MODE: ========== STARTING EDIT MODE ==========');
                    console.log('EDIT MODE: Starting to load existing data...');
                    console.log('EDIT MODE: existingItems type:', typeof existingItems, 'isArray:', Array.isArray(existingItems));
                    console.log('EDIT MODE: Existing items count:', existingItems ? existingItems.length : 0);
                    console.log('EDIT MODE: Existing items raw:', existingItems);
                    console.log('EDIT MODE: Existing locations count:', existingLocations ? existingLocations.length : 0);
                    console.log('EDIT MODE: Existing locations raw:', existingLocations);

                    // Load existing data terlebih dahulu
                    try {
                        loadExistingData();
                        console.log('EDIT MODE: Data loaded successfully');
                        console.log('EDIT MODE: Items after load:', items.length);
                        console.log('EDIT MODE: Items array:', items);
                        console.log('EDIT MODE: Paper columns after load:', allPaperColumns.length);
                        console.log('EDIT MODE: Paper columns array:', allPaperColumns);
                        console.log('EDIT MODE: Locations after load:', selectedLocations.length);
                        console.log('EDIT MODE: Locations array:', selectedLocations);
                    } catch (error) {
                        console.error('EDIT MODE: Error loading existing data:', error);
                        console.error('EDIT MODE: Error stack:', error.stack);
                    }

                    // Setelah data dimuat, render workspace dan load data lainnya
                    // Gunakan setTimeout yang lebih lama untuk memastikan semua fungsi sudah didefinisikan
                    setTimeout(() => {
                        console.log('EDIT MODE: Starting to render workspace...');
                        console.log('EDIT MODE: Current state - items:', items.length, 'paperColumns:', allPaperColumns.length, 'locations:', selectedLocations.length);
                        console.log('EDIT MODE: renderWorkspace function exists?', typeof renderWorkspace);
                        console.log('EDIT MODE: renderItemsList function exists?', typeof renderItemsList);
                        console.log('EDIT MODE: updateItemsListHeader function exists?', typeof updateItemsListHeader);

                        // Pastikan semua fungsi sudah tersedia
                        if (typeof renderWorkspace !== 'function') {
                            console.error('EDIT MODE: renderWorkspace is not defined! Waiting 1 second...');
                            setTimeout(() => {
                                if (typeof renderWorkspace === 'function') {
                                    console.log('EDIT MODE: renderWorkspace now available, proceeding...');
                                    doRenderWorkspace();
                                } else {
                                    console.error('EDIT MODE: renderWorkspace still not available after wait!');
                                }
                            }, 1000);
                        } else {
                            doRenderWorkspace();
                        }

                        // Fungsi helper untuk render workspace
                        function doRenderWorkspace() {
                            try {
                                if (typeof updateItemsListHeader === 'function') {
                                    updateItemsListHeader();
                                }
                                if (typeof renderItemsList === 'function') {
                                    renderItemsList();
                                }

                                // PASTIKAN renderWorkspace() selalu dipanggil
                                renderWorkspace().then(() => {
                                    console.log('EDIT MODE: Workspace rendered successfully');

                                    // Load UP values dari papers yang sudah ada
                                    if (existingItems && Array.isArray(existingItems)) {
                                        existingItems.forEach(function(item) {
                                            if (item.papers && Array.isArray(item.papers) && item.papers.length > 0) {
                                                item.papers.forEach(function(paper) {
                                                    const paperType = paper.paper_type;
                                                    if (paperType && paper.up_value) {
                                                        const upInput = document.querySelector(`input[name="paper_up[${paperType}]"]`);
                                                        if (upInput) {
                                                            upInput.value = parseFloat(paper.up_value) || 5;
                                                        }
                                                    }
                                                });
                                            }
                                        });
                                    }

                                    // Load stocks data untuk locations yang sudah ada
                                    if (selectedLocations.length > 0 && allPaperColumns.length > 0) {
                                        const materialCodes = allPaperColumns.map(col =>
                                            col.paper ? col.paper.code : col.paperType
                                        ).filter(code => code && code.startsWith('K.'));

                                        // Load stock data untuk setiap location
                                        Promise.all(selectedLocations.map(async function(location) {
                                            const locationCode = location.Code;
                                            const existingLocation = existingLocations.find(l => l.location_code === locationCode);

                                            if (existingLocation) {
                                                const locationStocks = existingStocks.filter(s =>
                                                    s.location_id === existingLocation.id
                                                );

                                                const stockData = {};
                                                locationStocks.forEach(function(stock) {
                                                    const paperCode = stock.paper_code;
                                                    if (paperCode) {
                                                        stockData[paperCode] = {
                                                            stockQty: parseFloat(stock.stock_layer_2) || 0,
                                                            MaterialCode: paperCode
                                                        };
                                                    }
                                                });

                                                location.stockData = stockData;
                                            } else {
                                                location.stockData = {};
                                            }
                                            location.materialCodes = [...materialCodes];
                                            return location;
                                        })).then(() => {
                                            // Re-render workspace dengan stock data
                                            renderWorkspace().then(() => {
                                                // Load existing PO data
                                                if (allPaperColumns.length > 0) {
                                                    loadExistingPOData();
                                                }
                                                // Update Total Stok Layers setelah stocks dimuat
                                                setTimeout(() => {
                                                    if (typeof updateTotalStokLayers === 'function') {
                                                        updateTotalStokLayers();
                                                    }
                                                    // Update MINUS PAPER dan TOTAL KEBUTUHAN setelah semua data dimuat
                                                    setTimeout(() => {
                                                        if (typeof updateMinusPaper === 'function') {
                                                            updateMinusPaper();
                                                        }
                                                        if (typeof updateTotalKebutuhanTon === 'function') {
                                                            setTimeout(() => updateTotalKebutuhanTon(), 300);
                                                        }
                                                    }, 300);
                                                }, 500);
                                            });
                                        });
                                    } else {
                                        // Load PO remain jika tidak ada locations
                                        if (allPaperColumns.length > 0) {
                                            // Load existing PO remains dan PO manuals ke dalam workspace
                                            loadExistingPOData();
                                            // Update MINUS PAPER dan TOTAL KEBUTUHAN setelah PO data dimuat
                                            setTimeout(() => {
                                                if (typeof updateMinusPaper === 'function') {
                                                    updateMinusPaper();
                                                }
                                                if (typeof updateTotalKebutuhanTon === 'function') {
                                                    setTimeout(() => updateTotalKebutuhanTon(), 300);
                                                }
                                            }, 500);
                                        }
                                    }
                                }).catch(err => {
                                    console.error('EDIT MODE: Error rendering workspace:', err);
                                });
                            } catch (error) {
                                console.error('EDIT MODE: Error in render process:', error);
                            }
                        }
                    }, 500); // Delay lebih lama untuk memastikan semua fungsi sudah didefinisikan
                });

                // Fungsi untuk load existing PO remains dan PO manuals ke dalam workspace
                function loadExistingPOData() {
                    const tbody = document.getElementById('workspace-tbody');
                    if (!tbody) return;

                    // Load PO Remains
                    if (existingPORemains && existingPORemains.length > 0) {
                        // Group by po_doc_no
                        const poByDocNo = {};
                        existingPORemains.forEach(function(po) {
                            if (!poByDocNo[po.po_doc_no]) {
                                poByDocNo[po.po_doc_no] = [];
                            }
                            poByDocNo[po.po_doc_no].push(po);
                        });

                        // Render PO rows (akan di-insert sebelum MINUS PAPER)
                        const minusPaperRow = tbody.querySelector('.minus-paper-row');
                        let insertBeforeElement = minusPaperRow;

                        Object.keys(poByDocNo).forEach(function(docNo) {
                            const poItems = poByDocNo[docNo];

                            // PO Layer 1
                            const poRow1 = document.createElement('tr');
                            poRow1.className = 'po-remain-row po-layer-1';
                            poRow1.setAttribute('data-po-number', docNo);
                            poRow1.setAttribute('data-layer', '1');

                            const poLabelCell1 = document.createElement('td');
                            poLabelCell1.setAttribute('colspan', 7);
                            poLabelCell1.textContent = docNo + ' (Layer 1)';
                            poLabelCell1.style.backgroundColor = '#d5e8d4';
                            poLabelCell1.style.fontWeight = 'bold';
                            poLabelCell1.style.fontSize = '14px';
                            poRow1.appendChild(poLabelCell1);

                            allPaperColumns.forEach(function(paperCol) {
                                const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                                const matchingPO = poItems.find(item => item.paper_code === paperCode);

                                const cell = document.createElement('td');
                                cell.style.backgroundColor = '#d5e8d4';
                                cell.style.textAlign = 'right';
                                cell.style.padding = '8px';
                                cell.style.fontSize = '14px';
                                cell.style.fontWeight = 'bold';

                                if (matchingPO && matchingPO.qty_remain > 0) {
                                    cell.textContent = formatNumber(matchingPO.qty_remain);
                                    const hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = `po_remain_layer1[${docNo}][${paperCode}]`;
                                    hiddenInput.value = matchingPO.qty_remain;
                                    cell.appendChild(hiddenInput);
                                } else {
                                    cell.textContent = '-';
                                }
                                poRow1.appendChild(cell);
                            });

                            // PO Layer 2
                            const poRow2 = document.createElement('tr');
                            poRow2.className = 'po-remain-row po-layer-2';
                            poRow2.setAttribute('data-po-number', docNo);
                            poRow2.setAttribute('data-layer', '2');

                            const poLabelCell2 = document.createElement('td');
                            poLabelCell2.setAttribute('colspan', 7);
                            poLabelCell2.textContent = docNo + ' (Layer 2)';
                            poLabelCell2.style.backgroundColor = '#c4e1c4';
                            poLabelCell2.style.fontWeight = 'bold';
                            poLabelCell2.style.fontSize = '14px';
                            poRow2.appendChild(poLabelCell2);

                            allPaperColumns.forEach(function(paperCol) {
                                const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                                const paperType = paperCol.paperType;
                                const matchingPO = poItems.find(item => item.paper_code === paperCode);

                                const cell = document.createElement('td');
                                cell.style.backgroundColor = '#c4e1c4';
                                cell.style.textAlign = 'right';
                                cell.style.padding = '8px';
                                cell.style.fontSize = '14px';
                                cell.style.fontWeight = 'bold';

                                if (matchingPO && matchingPO.po_remain_layer_2 > 0) {
                                    cell.textContent = formatNumber(matchingPO.po_remain_layer_2);
                                    const hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = `po_remain_layer2[${docNo}][${paperCode}]`;
                                    hiddenInput.value = matchingPO.po_remain_layer_2;
                                    cell.appendChild(hiddenInput);
                                } else {
                                    cell.textContent = '-';
                                }
                                poRow2.appendChild(cell);
                            });

                            if (insertBeforeElement) {
                                tbody.insertBefore(poRow1, insertBeforeElement);
                                tbody.insertBefore(poRow2, insertBeforeElement);
                            } else {
                                tbody.appendChild(poRow1);
                                tbody.appendChild(poRow2);
                            }
                        });
                    }

                    // Load PO Manuals (BELUM ADA PO)
                    if (existingPOManuals && existingPOManuals.length > 0) {
                        const minusPaperRow = tbody.querySelector('.minus-paper-row');
                        let insertBeforeElement = minusPaperRow;

                        // Cek apakah ada kertas yang belum ada PO (dari existingPOManuals)
                        const papersWithoutPO = allPaperColumns.filter(function(paperCol) {
                            const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                            return existingPOManuals.some(pm => pm.paper_code === paperCode);
                        });

                        if (papersWithoutPO.length > 0) {
                            // BELUM ADA PO Layer 1
                            const noPORow1 = document.createElement('tr');
                            noPORow1.className = 'po-manual-row po-manual-layer-1';
                            noPORow1.setAttribute('data-layer', '1');

                            const noPOLabelCell1 = document.createElement('td');
                            noPOLabelCell1.setAttribute('colspan', 7);
                            noPOLabelCell1.textContent = 'BELUM ADA PO (Layer 1)';
                            noPOLabelCell1.style.backgroundColor = '#ffcc99';
                            noPOLabelCell1.style.fontWeight = 'bold';
                            noPOLabelCell1.style.fontSize = '14px';
                            noPORow1.appendChild(noPOLabelCell1);

                            allPaperColumns.forEach(function(paperCol) {
                                const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                                const paperType = paperCol.paperType;
                                const existingPO = existingPOManuals.find(pm => pm.paper_code === paperCode);

                                const cell = document.createElement('td');
                                cell.style.backgroundColor = '#ffcc99';
                                cell.style.textAlign = 'right';
                                cell.style.padding = '8px';
                                cell.style.fontSize = '14px';

                                if (existingPO) {
                                    const input = document.createElement('input');
                                    input.type = 'text';
                                    input.className = 'excel-input po-manual-input';
                                    input.name = `po_manual_layer1[${paperCode}]`;
                                    input.setAttribute('data-paper-code', paperCode);
                                    input.setAttribute('data-paper-type', paperType);
                                    input.value = formatNumber(existingPO.po_manual_layer_1 || 0);
                                    input.style.width = '100%';
                                    input.style.border = '1px solid #666';
                                    input.style.background = '#fff';
                                    input.style.textAlign = 'right';
                                    input.style.fontSize = '14px';
                                    input.style.padding = '4px';
                                    input.style.fontWeight = 'bold';

                                    const hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = `po_manual_layer1_value[${paperCode}]`;
                                    hiddenInput.value = existingPO.po_manual_layer_1 || 0;

                                    input.addEventListener('input', function(e) {
                                        let val = this.value.replace(',', '.').replace(/[^\d.]/g, '');
                                        const parts = val.split('.');
                                        if (parts.length > 2) {
                                            val = parts[0] + '.' + parts.slice(1).join('');
                                        }
                                        this.value = val;
                                        const numVal = parseFloat(val) || 0;
                                        hiddenInput.value = numVal;
                                        if (typeof updatePOManualLayer2 === 'function') {
                                            updatePOManualLayer2(paperCode, paperType);
                                        }
                                        clearTimeout(input._updateTimeout);
                                        input._updateTimeout = setTimeout(() => {
                                            if (typeof updateMinusPaper === 'function') {
                                                updateMinusPaper();
                                            }
                                        }, 300);
                                    });

                                    input.addEventListener('blur', function(e) {
                                        const val = parseFloat(this.value.replace(',', '.')) || 0;
                                        this.value = formatNumber(val);
                                        hiddenInput.value = val;
                                        if (typeof updatePOManualLayer2 === 'function') {
                                            updatePOManualLayer2(paperCode, paperType);
                                        }
                                        if (typeof updateMinusPaper === 'function') {
                                            setTimeout(() => updateMinusPaper(), 200);
                                        }
                                    });

                                    cell.appendChild(input);
                                    cell.appendChild(hiddenInput);
                                } else {
                                    cell.textContent = '-';
                                }
                                noPORow1.appendChild(cell);
                            });

                            // BELUM ADA PO Layer 2
                            const noPORow2 = document.createElement('tr');
                            noPORow2.className = 'po-manual-row po-manual-layer-2';
                            noPORow2.setAttribute('data-layer', '2');

                            const noPOLabelCell2 = document.createElement('td');
                            noPOLabelCell2.setAttribute('colspan', 7);
                            noPOLabelCell2.textContent = 'BELUM ADA PO (Layer 2)';
                            noPOLabelCell2.style.backgroundColor = '#ffb366';
                            noPOLabelCell2.style.fontWeight = 'bold';
                            noPOLabelCell2.style.fontSize = '14px';
                            noPORow2.appendChild(noPOLabelCell2);

                            allPaperColumns.forEach(function(paperCol) {
                                const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                                const paperType = paperCol.paperType;
                                const existingPO = existingPOManuals.find(pm => pm.paper_code === paperCode);

                                const cell = document.createElement('td');
                                cell.style.backgroundColor = '#ffb366';
                                cell.style.textAlign = 'right';
                                cell.style.padding = '8px';
                                cell.style.fontSize = '14px';
                                cell.style.fontWeight = 'bold';

                                if (existingPO) {
                                    cell.textContent = formatNumber(existingPO.po_manual_layer_2 || 0);
                                    const hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = `po_manual_layer2[${paperCode}]`;
                                    hiddenInput.value = existingPO.po_manual_layer_2 || 0;
                                    cell.appendChild(hiddenInput);
                                } else {
                                    cell.textContent = '-';
                                }
                                noPORow2.appendChild(cell);
                            });

                            if (insertBeforeElement) {
                                tbody.insertBefore(noPORow1, insertBeforeElement);
                                tbody.insertBefore(noPORow2, insertBeforeElement);
                            } else {
                                tbody.appendChild(noPORow1);
                                tbody.appendChild(noPORow2);
                            }

                            // Update Layer 2 setelah dibuat
                            setTimeout(function() {
                                updateAllPOManualLayer2();
                                if (typeof updateMinusPaper === 'function') {
                                    setTimeout(() => updateMinusPaper(), 200);
                                }
                            }, 100);
                        }
                    }

                    // Load catatan dan COVER SAMPAI per kertas
                    setTimeout(function() {
                        // Kumpulkan semua paper types unik dari existing items
                        const allPaperTypes = new Set();
                        existingItems.forEach(function(item) {
                            if (item.papers && item.papers.length > 0) {
                                item.papers.forEach(function(paper) {
                                    if (paper.paper_type) {
                                        allPaperTypes.add(paper.paper_type);
                                    }
                                });
                            }
                        });

                        allPaperTypes.forEach(function(paperType) {
                            // Cari paper dengan paper_type ini
                            let paperData = null;
                            existingItems.forEach(function(item) {
                                if (item.papers && item.papers.length > 0) {
                                    const found = item.papers.find(p => p.paper_type === paperType);
                                    if (found) {
                                        paperData = found;
                                    }
                                }
                            });

                            if (paperData) {
                                // Load catatan
                                if (paperData.catatan) {
                                    const catatanInput = tbody.querySelector(`input[name="catatan_kertas[${paperType}]"]`);
                                    if (catatanInput) {
                                        catatanInput.value = paperData.catatan;
                                    }
                                }

                                // Load COVER SAMPAI
                                if (paperData.cover_sampai) {
                                    const coverSampaiSelect = tbody.querySelector(`select[name="cover_sampai[${paperType}]"]`);
                                    if (coverSampaiSelect) {
                                        coverSampaiSelect.value = paperData.cover_sampai;
                                    }
                                }
                            }
                        });
                    }, 500);
                }

                // Fungsi global untuk update total kebutuhan ton
                function updateTotalKebutuhanTon() {
                    const tbody = document.getElementById('workspace-tbody');
                    if (!tbody) return;

                    let grandTotal = 0;

                    if (!allPaperColumns || allPaperColumns.length === 0) {
                        console.log('updateTotalKebutuhanTon: allPaperColumns is empty');
                        return;
                    }

                    // Cek apakah ada input jumlah kebutuhan (DEC, JAN, FEB) yang tidak 0
                    const totalRow = tbody.querySelector('tr.total-row, tr[data-row-type="total"]');
                    let hasInputKebutuhan = false;

                    if (totalRow) {
                        // Cek kolom bulan (DEC, JAN, FEB) - biasanya index 2, 3, 4
                        const totalRowCells = totalRow.querySelectorAll('td');
                        if (totalRowCells.length >= 5) {
                            const decValue = parseFloat(totalRowCells[2]?.textContent?.replace(/\./g, '') || '0') || 0;
                            const janValue = parseFloat(totalRowCells[3]?.textContent?.replace(/\./g, '') || '0') || 0;
                            const febValue = parseFloat(totalRowCells[4]?.textContent?.replace(/\./g, '') || '0') || 0;

                            // Cek apakah ada nilai yang lebih dari 0
                            hasInputKebutuhan = (decValue > 0 || janValue > 0 || febValue > 0);

                            console.log('updateTotalKebutuhanTon: Checking input kebutuhan - DEC:', decValue, 'JAN:', janValue, 'FEB:', febValue, 'hasInput:', hasInputKebutuhan);
                        }
                    }

                    // Jika belum ada input jumlah kebutuhan, set total menjadi 0
                    if (!hasInputKebutuhan) {
                        console.log('updateTotalKebutuhanTon: No input kebutuhan found, setting total to 0');

                        allPaperColumns.forEach(function(paperCol) {
                            const paperType = paperCol.paperType;
                            if (!paperType) return;

                            const summaryCell = tbody.querySelector(`td.summary-total-ton-cell[data-paper-type="${paperType}"]`);
                            if (summaryCell) {
                                const hiddenInput = summaryCell.querySelector('input[type="hidden"]');
                                summaryCell.textContent = formatNumber(0);
                                if (hiddenInput) {
                                    hiddenInput.value = 0;
                                }
                            }
                        });

                        // Update grand total menjadi 0
                        const grandTotalCell = tbody.querySelector('td.summary-grand-total-cell');
                        if (grandTotalCell) {
                            const grandTotalHiddenInput = grandTotalCell.querySelector('input[type="hidden"]');
                            grandTotalCell.textContent = formatNumber(0);
                            if (grandTotalHiddenInput) {
                                grandTotalHiddenInput.value = 0;
                            }
                        }

                        // Update keterangan status: belum ada input
                        const statusElement = document.getElementById('status-kebutuhan-kertas');
                        const catatanElement = document.getElementById('catatan-kebutuhan-kertas');
                        const summaryLabelCell = document.getElementById('summary-label-cell');

                        if (statusElement && summaryLabelCell) {
                            statusElement.textContent = '⏳ BELUM ADA INPUT KEBUTUHAN';
                            statusElement.style.color = '#fff3cd';
                            statusElement.style.backgroundColor = 'rgba(255, 193, 7, 0.2)';
                            statusElement.style.padding = '4px 12px';
                            statusElement.style.borderRadius = '4px';
                            statusElement.style.display = 'inline-block';
                            summaryLabelCell.style.backgroundColor = '#ffc107'; // Kuning untuk belum ada input

                            // Catatan penjelasan
                            // if (catatanElement) {
                            //     catatanElement.innerHTML = '<strong>JADI BELI APA ENGGA?</strong><br>⏳ <strong>BELUM BISA DITENTUKAN</strong> - Silakan input jumlah kebutuhan terlebih dahulu<br><small>(Input nilai di kolom DEC, JAN, atau FEB untuk memulai perhitungan)</small>';
                            //     catatanElement.style.color = '#fff3cd';
                            // }
                        }

                        return;
                    }

                    // Logika yang benar:
                    // MINUS PAPER = (Total Stok + Total PO) - Total Kebutuhan
                    // - MINUS PAPER TON POSITIF = stok masih ada/cukup = TIDAK PERLU BELI
                    // - MINUS PAPER TON NEGATIF = stok kurang = PERLU BELI
                    // Jadi untuk TOTAL KEBUTUHAN KERTAS, kita hanya ambil nilai NEGATIF (kekurangan)
                    allPaperColumns.forEach(function(paperCol) {
                        const paperType = paperCol.paperType;
                        if (!paperType) return;

                        // Ambil nilai MINUS PAPER (TON) untuk jenis kertas ini
                        const minusPaperTONInput = tbody.querySelector(`input[name="minus_paper_ton[${paperType}]"]`);
                        const rawTonValue = minusPaperTONInput ? (parseFloat(minusPaperTONInput.value) || 0) : 0;
                        let tonValue = 0;

                        // Logika yang benar:
                        // Jika MINUS PAPER TON POSITIF = stok masih ada, TIDAK PERLU BELI (set ke 0)
                        // Jika MINUS PAPER TON NEGATIF = stok kurang, PERLU BELI (convert ke positif untuk display)
                        if (rawTonValue > 0) {
                            // Nilai POSITIF = stok masih ada, TIDAK PERLU BELI
                            tonValue = 0;
                            console.log(`updateTotalKebutuhanTon [${paperType}]: MINUS PAPER TON is POSITIF (${rawTonValue}), stok masih ada, TIDAK PERLU BELI`);
                        } else if (rawTonValue < 0) {
                            // Nilai NEGATIF = stok kurang, PERLU BELI (convert ke positif untuk display)
                            tonValue = Math.abs(rawTonValue); // Convert negatif jadi positif untuk display
                            console.log(`updateTotalKebutuhanTon [${paperType}]: MINUS PAPER TON is NEGATIF (${rawTonValue}), ada kekurangan ${tonValue} TON, PERLU BELI`);
                        } else {
                            // Nilai 0 = tidak ada selisih
                            tonValue = 0;
                            console.log(`updateTotalKebutuhanTon [${paperType}]: MINUS PAPER TON is 0, tidak ada selisih`);
                        }

                        // Update cell summary untuk jenis kertas ini
                        const summaryCell = tbody.querySelector(`td.summary-total-ton-cell[data-paper-type="${paperType}"]`);
                        if (summaryCell) {
                            const hiddenInput = summaryCell.querySelector('input[type="hidden"]');
                            summaryCell.textContent = formatNumber(tonValue);
                            if (hiddenInput) {
                                hiddenInput.value = tonValue;
                            }
                        }

                        grandTotal += tonValue;
                    });

                    // Update grand total
                    const grandTotalCell = tbody.querySelector('td.summary-grand-total-cell');
                    if (grandTotalCell) {
                        const grandTotalHiddenInput = grandTotalCell.querySelector('input[type="hidden"]');
                        grandTotalCell.textContent = formatNumber(grandTotal);
                        if (grandTotalHiddenInput) {
                            grandTotalHiddenInput.value = grandTotal;
                        }
                    }

                    // Update keterangan status: PERLU BELI atau TIDAK PERLU BELI
                    const statusElement = document.getElementById('status-kebutuhan-kertas');
                    const catatanElement = document.getElementById('catatan-kebutuhan-kertas');
                    const summaryLabelCell = document.getElementById('summary-label-cell');

                    if (statusElement && summaryLabelCell) {
                        if (grandTotal > 0) {
                            // Ada kekurangan (MINUS PAPER TON negatif yang sudah di-convert jadi positif), PERLU BELI
                            statusElement.textContent = '⚠️ PERLU BELI KERTAS';
                            statusElement.style.color = '#fff3cd';
                            statusElement.style.backgroundColor = 'rgba(255, 193, 7, 0.3)';
                            statusElement.style.padding = '6px 16px';
                            statusElement.style.borderRadius = '6px';
                            statusElement.style.display = 'inline-block';
                            statusElement.style.fontWeight = 'bold';
                            statusElement.style.marginTop = '6px';
                            summaryLabelCell.style.backgroundColor = '#dc3545'; // Merah untuk perlu beli

                            // Catatan penjelasan
                            // if (catatanElement) {
                            //     catatanElement.innerHTML = '<strong>JADI BELI APA ENGGA?</strong><br>✅ <strong>PERLU BELI</strong> - Ada kekurangan kertas sebesar ' + formatNumber(grandTotal) + ' TON<br><small>(MINUS PAPER TON bernilai negatif = stok kurang dari kebutuhan)</small>';
                            //     catatanElement.style.color = '#fff3cd';
                            // }
                        } else {
                            // Tidak ada kekurangan (MINUS PAPER TON positif atau 0), STOK MASIH ADA
                            statusElement.textContent = '✅ STOK MASIH ADA - TIDAK PERLU BELI';
                            statusElement.style.color = '#d4edda';
                            statusElement.style.backgroundColor = 'rgba(40, 167, 69, 0.3)';
                            statusElement.style.padding = '6px 16px';
                            statusElement.style.borderRadius = '6px';
                            statusElement.style.display = 'inline-block';
                            statusElement.style.fontWeight = 'bold';
                            statusElement.style.marginTop = '6px';
                            summaryLabelCell.style.backgroundColor = '#28a745'; // Hijau untuk tidak perlu beli

                            // Catatan penjelasan
                            // if (catatanElement) {
                            //     catatanElement.innerHTML = '<strong>JADI BELI APA ENGGA?</strong><br>❌ <strong>TIDAK PERLU BELI</strong> - Stok masih mencukupi kebutuhan<br><small>(MINUS PAPER TON bernilai positif = stok lebih dari kebutuhan)</small>';
                            //     catatanElement.style.color = '#d4edda';
                            // }
                        }
                    }

                    console.log('Total Kebutuhan Kertas (TON):', grandTotal, grandTotal > 0 ? 'PERLU BELI' : 'TIDAK PERLU BELI');
                }

                // Fungsi global untuk menghitung dan update MINUS PAPER
                function updateMinusPaper() {
                    const tbody = document.getElementById('workspace-tbody');
                    if (!tbody) {
                        console.log('updateMinusPaper: tbody not found');
                        return;
                    }

                    // Pastikan allPaperColumns terdefinisi dan tidak kosong
                    if (!allPaperColumns || !Array.isArray(allPaperColumns) || allPaperColumns.length === 0) {
                        console.log('updateMinusPaper: allPaperColumns is not defined or empty');
                        return;
                    }

                    console.log('updateMinusPaper: Starting calculation for', allPaperColumns.length, 'paper columns');

                    // Cari row TOTAL untuk mendapatkan total permintaan per kertas
                    const totalRow = tbody.querySelector('tr.total-row, tr[data-row-type="total"]');
                    if (!totalRow) {
                        console.warn('updateMinusPaper: totalRow not found');
                    }

                    allPaperColumns.forEach(function(paperCol, index) {
                        if (!paperCol) return;

                        // Definisikan paperCode dan paperType di awal untuk menghindari "Cannot access before initialization"
                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        const paperType = paperCol.paperType;

                        if (!paperType) {
                            console.log('updateMinusPaper: paperType is not defined for paperCol:', paperCol);
                            return;
                        }

                        // Ambil Total Stok Layer 2
                        const totalStokLayer2Input = tbody.querySelector(`input[name="stock_total_layer2[${paperType}]"]`);
                        const totalStokLayer2 = totalStokLayer2Input ? (parseFloat(totalStokLayer2Input.value) || 0) : 0;

                        // Ambil Total Quantity PO Layer 2 (jumlahkan semua PO remain layer 2 untuk paper code ini)
                        // Format name: po_remain_layer2[DocNo][paperCode]
                        let totalPOLayer2 = 0;

                        // Cari semua input PO remain layer 2 untuk paper code ini
                        const allPOInputs = tbody.querySelectorAll('input[name^="po_remain_layer2["]');
                        console.log(`updateMinusPaper [${paperType}]: Searching for PO Layer 2 for paperCode: ${paperCode}, found ${allPOInputs.length} total PO inputs`);

                        allPOInputs.forEach(function(poInput) {
                            const inputName = poInput.name;
                            // Format: po_remain_layer2[DocNo][paperCode]
                            // Cek apakah input ini untuk paper code yang sedang diproses
                            // Pattern: po_remain_layer2[XXXXX][paperCode]
                            const pattern = new RegExp(`po_remain_layer2\\[.*\\]\\[${paperCode.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\]$`);

                            if (pattern.test(inputName)) {
                                const poValue = parseFloat(poInput.value) || 0;
                                if (poValue > 0) {
                                    totalPOLayer2 += poValue;
                                    console.log(`updateMinusPaper [${paperType}]: ✓ Found PO input "${inputName}" with value ${poValue}, totalPOLayer2 now: ${totalPOLayer2}`);
                                } else {
                                    console.log(`updateMinusPaper [${paperType}]: Found PO input "${inputName}" but value is 0 or invalid`);
                                }
                            }
                        });

                        // Jika masih 0, coba cari dari cell text content (backup method)
                        if (totalPOLayer2 === 0) {
                            console.log(`updateMinusPaper [${paperType}]: Trying backup method - searching PO layer 2 rows`);
                            const poLayer2Rows = tbody.querySelectorAll('tr.po-layer-2[data-layer="2"]');

                            // Cari index kolom untuk paper code ini
                            let paperColIndex = -1;
                            allPaperColumns.forEach(function(col, idx) {
                                const colPaperCode = col.paper ? col.paper.code : col.paperType;
                                if (colPaperCode === paperCode) {
                                    paperColIndex = idx;
                                }
                            });

                            if (paperColIndex >= 0) {
                                poLayer2Rows.forEach(function(poRow) {
                                    const cells = poRow.querySelectorAll('td');
                                    // Cell pertama adalah label (colspan 7), kemudian kolom kertas dimulai dari index 7
                                    const cellIndex = 7 + paperColIndex;
                                    if (cells[cellIndex]) {
                                        const cell = cells[cellIndex];
                                        const cellText = cell.textContent.trim();

                                        // Cek apakah cell berisi angka (bukan "-")
                                        if (cellText !== '-' && cellText !== '') {
                                            // Hapus titik sebagai separator ribuan
                                            const numericValue = parseFloat(cellText.replace(/\./g, '').replace(',', '.')) || 0;
                                            if (numericValue > 0) {
                                                totalPOLayer2 += numericValue;
                                                const docNo = poRow.getAttribute('data-po-number') || 'unknown';
                                                console.log(`updateMinusPaper [${paperType}]: Backup method - Found PO value ${numericValue} from PO ${docNo} (cell text: "${cellText}"), totalPOLayer2 now: ${totalPOLayer2}`);
                                            }
                                        }
                                    }
                                });
                            }
                        }

                        console.log(`updateMinusPaper [${paperType}]: Final calculation - Total Stok Layer 2: ${totalStokLayer2}, Total PO Layer 2: ${totalPOLayer2}, Total Stok + PO: ${totalStokLayer2 + totalPOLayer2}`);

                        // Total Stok + Total PO Layer 2
                        const totalStokDanPO = totalStokLayer2 + totalPOLayer2;

                        // Ambil Total Permintaan dari row TOTAL (kolom kertas)
                        let totalPermintaan = 0;
                        if (totalRow) {
                            // Cari cell di row TOTAL untuk paper type ini berdasarkan data attribute
                            // Pastikan paperCode sudah didefinisikan sebelum digunakan di template string
                            // Gunakan variabel lokal untuk menghindari closure issue
                            const currentPaperCode = paperCode;
                            const currentPaperType = paperType;
                            const paperTotalCell = totalRow.querySelector(`td[data-paper-type="${currentPaperType}"], td[data-paper-code="${currentPaperCode}"]`);
                            if (paperTotalCell) {
                                const paperTotalText = paperTotalCell.textContent || '0';
                                // Hapus titik sebagai separator ribuan, ganti koma dengan titik untuk desimal
                                totalPermintaan = parseFloat(paperTotalText.replace(/\./g, '').replace(',', '.')) || 0;
                            } else {
                                // Fallback: cari berdasarkan index (kolom kertas dimulai setelah kolom TOTAL + TOLERANSI = index 7)
                                const totalRowCells = totalRow.querySelectorAll('td');
                                const paperCellIndex = 7 + index;
                                if (totalRowCells[paperCellIndex]) {
                                    const paperTotalText = totalRowCells[paperCellIndex].textContent || '0';
                                    totalPermintaan = parseFloat(paperTotalText.replace(/\./g, '').replace(',', '.')) || 0;
                                }
                            }
                        }

                        // Tambahkan nilai BELUM ADA PO Layer 2 ke total permintaan
                        const belumAdaPOLayer2Input = tbody.querySelector(`input[name="po_manual_layer2[${paperCode}]"]`);
                        if (belumAdaPOLayer2Input) {
                            const belumAdaPOLayer2 = parseFloat(belumAdaPOLayer2Input.value) || 0;
                            totalPermintaan += belumAdaPOLayer2;
                            console.log(`updateMinusPaper [${paperType}]: Added BELUM ADA PO Layer 2:`, belumAdaPOLayer2, 'to totalPermintaan');
                        }

                        // Jika totalPermintaan masih 0, berarti belum ada input jumlah kebutuhan
                        // Set semua nilai MINUS PAPER menjadi 0
                        if (totalPermintaan === 0) {
                            console.log(`updateMinusPaper [${paperType}]: No input kebutuhan (totalPermintaan = 0), setting MINUS PAPER to 0`);

                            // Set MINUS PAPER PCS = 0
                            const minusPaperPCSInput = tbody.querySelector(`input[name="minus_paper_pcs[${paperType}]"]`);
                            if (minusPaperPCSInput && minusPaperPCSInput.parentElement) {
                                const pcsCell = minusPaperPCSInput.parentElement;
                                pcsCell.textContent = formatNumber(0);
                                minusPaperPCSInput.value = 0;
                                pcsCell.appendChild(minusPaperPCSInput);
                            }

                            // Set MINUS PAPER RIM = 0
                            const minusPaperRIMInput = tbody.querySelector(`input[name="minus_paper_rim[${paperType}]"]`);
                            if (minusPaperRIMInput && minusPaperRIMInput.parentElement) {
                                const rimCell = minusPaperRIMInput.parentElement;
                                rimCell.textContent = formatNumber(0);
                                minusPaperRIMInput.value = 0;
                                rimCell.appendChild(minusPaperRIMInput);
                            }

                            // Set MINUS PAPER TON = 0
                            const minusPaperTONInput = tbody.querySelector(`input[name="minus_paper_ton[${paperType}]"]`);
                            if (minusPaperTONInput && minusPaperTONInput.parentElement) {
                                const tonCell = minusPaperTONInput.parentElement;
                                tonCell.textContent = formatNumber(0);
                                minusPaperTONInput.value = 0;
                                tonCell.appendChild(minusPaperTONInput);
                            }

                            // Skip perhitungan selanjutnya untuk paper type ini
                            return;
                        }

                        // MINUS PAPER PCS = (Total Stok Layer 2 + Total PO Layer 2) - Total Permintaan
                        const minusPaperPCS = totalStokDanPO - totalPermintaan;

                        // Ambil nilai UP
                        const upInput = document.querySelector(`input[name="paper_up[${paperType}]"]`);
                        const upValue = upInput ? (parseFloat(upInput.value) || 5) : 5;

                        // MINUS PAPER RIM = Minus Paper PCS / UP / 500
                        // Formula: RIM = PCS / (UP * 500)
                        const minusPaperRIM = (upValue > 0 && minusPaperPCS !== 0) ? (minusPaperPCS / (upValue * 500)) : 0;

                        // Debug log untuk troubleshooting
                        console.log(`updateMinusPaper [${paperType}]:`, {
                            totalStokLayer2,
                            totalPOLayer2,
                            totalStokDanPO,
                            totalPermintaan,
                            minusPaperPCS,
                            upValue,
                            minusPaperRIM
                        });

                        // Update Layer PCS
                        const minusPaperPCSInput = tbody.querySelector(`input[name="minus_paper_pcs[${paperType}]"]`);
                        if (minusPaperPCSInput && minusPaperPCSInput.parentElement) {
                            const pcsCell = minusPaperPCSInput.parentElement;
                            pcsCell.textContent = formatNumber(minusPaperPCS);
                            minusPaperPCSInput.value = minusPaperPCS;
                            pcsCell.appendChild(minusPaperPCSInput);
                        }

                        // Update Layer RIM
                        const minusPaperRIMInput = tbody.querySelector(`input[name="minus_paper_rim[${paperType}]"]`);
                        if (minusPaperRIMInput && minusPaperRIMInput.parentElement) {
                            const rimCell = minusPaperRIMInput.parentElement;
                            rimCell.textContent = formatNumber(minusPaperRIM);
                            minusPaperRIMInput.value = minusPaperRIM;
                            rimCell.appendChild(minusPaperRIMInput);
                            console.log(`updateMinusPaper [${paperType}]: Updated RIM cell to`, formatNumber(minusPaperRIM));
                        } else {
                            console.warn(`updateMinusPaper [${paperType}]: RIM input not found`);
                        }

                        // MINUS PAPER TON = (zgsm/1000) * (zlength/100) * (zwidth/100) * 500 * (minus_paper_rim) / 1000
                        // Catatan logika yang benar:
                        // MINUS PAPER = (Total Stok + Total PO) - Total Kebutuhan
                        // - MINUS PAPER (TON) POSITIF = stok masih ada/cukup = TIDAK PERLU BELI
                        // - MINUS PAPER (TON) NEGATIF = stok kurang = PERLU BELI
                        let minusPaperTON = 0;
                        // Cari data zgsm, zlength, zwidth dari allPaperColumns
                        const paperData = allPaperColumns.find(function(col) {
                            const colPaperCode = col.paper ? col.paper.code : col.paperType;
                            return colPaperCode === paperCode;
                        });

                        if (paperData && paperData.paper) {
                            const zgsm = parseFloat(paperData.paper.zgsm) || 0;
                            const zlength = parseFloat(paperData.paper.zlength) || 0;
                            const zwidth = parseFloat(paperData.paper.zwidth) || 0;

                            if (zgsm > 0 && zlength > 0 && zwidth > 0 && minusPaperRIM !== 0) {
                                // Formula: (zgsm/1000) * (zlength/100) * (zwidth/100) * 500 * (minus_paper_rim) / 1000
                                // minusPaperRIM bisa positif (stok masih ada) atau negatif (kekurangan)
                                minusPaperTON = (zgsm / 1000) * (zlength / 100) * (zwidth / 100) * 500 * minusPaperRIM / 1000;

                                // Logika yang benar:
                                // Jika hasil POSITIF, berarti stok masih ada (tidak perlu beli)
                                // Jika hasil NEGATIF, berarti ada kekurangan (perlu beli)
                                if (minusPaperTON > 0) {
                                    console.log(`updateMinusPaper [${paperType}]: MINUS PAPER TON is positive (${minusPaperTON}), stok masih ada, tidak perlu beli`);
                                } else if (minusPaperTON < 0) {
                                    console.log(`updateMinusPaper [${paperType}]: MINUS PAPER TON is negative (${minusPaperTON}), ada kekurangan, perlu beli`);
                                }
                            }

                            console.log(`updateMinusPaper [${paperType}]: TON calculation:`, {
                                zgsm,
                                zlength,
                                zwidth,
                                minusPaperRIM,
                                minusPaperTON
                            });
                        } else {
                            console.warn(`updateMinusPaper [${paperType}]: Paper data not found in allPaperColumns for code:`, paperCode);
                        }

                        // Update Layer TON
                        const minusPaperTONInput = tbody.querySelector(`input[name="minus_paper_ton[${paperType}]"]`);
                        if (minusPaperTONInput && minusPaperTONInput.parentElement) {
                            const tonCell = minusPaperTONInput.parentElement;
                            tonCell.textContent = formatNumber(minusPaperTON);
                            minusPaperTONInput.value = minusPaperTON;
                            tonCell.appendChild(minusPaperTONInput);
                            console.log(`updateMinusPaper [${paperType}]: Updated TON cell to`, formatNumber(minusPaperTON));
                        } else {
                            console.warn(`updateMinusPaper [${paperType}]: TON input not found`);
                        }
                    });

                    // Update total kebutuhan ton setelah semua MINUS PAPER (TON) diupdate
                    if (typeof updateTotalKebutuhanTon === 'function') {
                        updateTotalKebutuhanTon();
                    }
                }

                // Fungsi untuk update PO Manual Layer 2 ketika input berubah
                function updatePOManualLayer2(paperCode, paperType) {
                    const tbody = document.getElementById('workspace-tbody');
                    if (!tbody) {
                        console.log('updatePOManualLayer2: tbody not found');
                        return;
                    }

                    // Cari input Layer 1 untuk paper code ini
                    const layer1Input = tbody.querySelector(`input[name="po_manual_layer1[${paperCode}]"]`);
                    if (!layer1Input) {
                        console.log('updatePOManualLayer2: Layer 1 input not found for', paperCode);
                        return;
                    }

                    const qtyValue = parseFloat(layer1Input.value.replace(',', '.')) || 0;

                    // Ambil nilai UP dari input field
                    const upInput = document.querySelector(`input[name="paper_up[${paperType}]"]`);
                    const upValue = upInput ? (parseFloat(upInput.value) || 5) : 5;

                    // Hitung Layer 2 = Qty × 500 × UP
                    const layer2Value = qtyValue * 500 * upValue;

                    // Cari hidden input Layer 2 berdasarkan name, lalu ambil parent cell-nya
                    const layer2HiddenInput = tbody.querySelector(`input[name="po_manual_layer2[${paperCode}]"]`);
                    if (layer2HiddenInput && layer2HiddenInput.parentElement) {
                        const layer2Cell = layer2HiddenInput.parentElement;
                        layer2Cell.textContent = formatNumber(layer2Value);
                        layer2Cell.setAttribute('title', `Qty: ${formatNumber(qtyValue)}, UP: ${upValue}, Hasil: ${formatNumber(qtyValue)} × 500 × ${upValue} = ${formatNumber(layer2Value)}`);

                        // Update hidden input value
                        layer2HiddenInput.value = layer2Value;

                        // Re-append hidden input karena textContent menghapus semua child
                        layer2Cell.appendChild(layer2HiddenInput);
                    } else {
                        console.log('updatePOManualLayer2: Layer 2 hidden input not found for', paperCode, 'Input name:', `po_manual_layer2[${paperCode}]`);
                    }
                }

                // Fungsi untuk update semua PO Manual Layer 2 ketika UP berubah
                function updateAllPOManualLayer2() {
                    const tbody = document.getElementById('workspace-tbody');
                    if (!tbody) return;

                    // Ambil semua input manual Layer 1
                    const manualInputs = tbody.querySelectorAll('input.po-manual-input');
                    manualInputs.forEach(function(input) {
                        const paperCode = input.getAttribute('data-paper-code');
                        const paperType = input.getAttribute('data-paper-type');
                        if (paperCode && paperType) {
                            updatePOManualLayer2(paperCode, paperType);
                        }
                    });

                    // Update MINUS PAPER setelah semua BELUM ADA PO Layer 2 selesai diupdate
                    if (typeof updateMinusPaper === 'function') {
                        setTimeout(() => updateMinusPaper(), 200);
                    }
                }

                // Flag untuk mencegah multiple calls
                let isLoadingPORemain = false;

                // Fungsi global untuk mengambil dan menampilkan PO remain
                async function loadPORemain() {
                    // Prevent multiple simultaneous calls
                    if (isLoadingPORemain) {
                        console.log('loadPORemain: Already loading, skipping...');
                        return;
                    }

                    try {
                        isLoadingPORemain = true;
                        const tbody = document.getElementById('workspace-tbody');
                        if (!tbody) {
                            console.log('Workspace tbody not found');
                            isLoadingPORemain = false;
                            return;
                        }

                        // Hapus semua row PO yang sudah ada sebelumnya (termasuk PO remain dan PO manual)
                        const existingPORows = tbody.querySelectorAll('.po-remain-row, .po-manual-row');
                        existingPORows.forEach(row => row.remove());

                        // Ambil semua material code dari allPaperColumns
                        const materialCodes = allPaperColumns.map(function(paperCol) {
                            return paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        }).filter(code => code && code.startsWith('K.')); // Hanya kode yang dimulai dengan K.

                        if (materialCodes.length === 0) {
                            console.log('No material codes found');
                            return;
                        }

                        // Tampilkan loading indicator
                        const loadingRow = document.createElement('tr');
                        loadingRow.className = 'po-remain-loading';
                        const loadingCell = document.createElement('td');
                        loadingCell.setAttribute('colspan', '100');
                        loadingCell.textContent = 'Memuat data PO...';
                        loadingCell.style.textAlign = 'center';
                        loadingCell.style.padding = '10px';
                        loadingCell.style.backgroundColor = '#fff3cd';
                        loadingRow.appendChild(loadingCell);
                        tbody.appendChild(loadingRow);

                        // Panggil API untuk mengambil PO remain
                        const response = await fetch('{{ route("paper-procurement.api.po-remain") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                material_codes: materialCodes
                            })
                        });

                        // Hapus loading indicator
                        const loadingRows = tbody.querySelectorAll('.po-remain-loading');
                        loadingRows.forEach(row => row.remove());

                        if (!response.ok) {
                            throw new Error('Failed to fetch PO remain');
                        }

                        const result = await response.json();

                        if (result.success && result.raw && result.raw.length > 0) {
                            // Cari posisi untuk menambahkan PO rows (setelah Total Stok Layer 3, sebelum MINUS PAPER)
                            const totalStokRows = tbody.querySelectorAll('.total-stok-row');
                            const minusPaperRow = tbody.querySelector('.minus-paper-row');
                            let insertBeforeElement = minusPaperRow;

                            // Jika tidak ada minus paper row, cari cover sampai row
                            if (!insertBeforeElement) {
                                insertBeforeElement = tbody.querySelector('.cover-sampai-row');
                            }

                            // Group PO by DocNo untuk ditampilkan
                            const poByDocNo = {};

                            result.raw.forEach(function(po) {
                                if (!poByDocNo[po.DocNo]) {
                                    poByDocNo[po.DocNo] = [];
                                }
                                poByDocNo[po.DocNo].push(po);
                            });

                            // Tampilkan setiap PO sebagai 2 layer (Layer 1 dan Layer 2)
                            Object.keys(poByDocNo).forEach(function(docNo) {
                                const poItems = poByDocNo[docNo];

                                // ===== PO LAYER 1: Qty Remain =====
                                const poRow1 = document.createElement('tr');
                                poRow1.className = 'po-remain-row po-layer-1';
                                poRow1.setAttribute('data-po-number', docNo);
                                poRow1.setAttribute('data-layer', '1');

                                const poLabelCell1 = document.createElement('td');
                                poLabelCell1.setAttribute('colspan', 7);
                                poLabelCell1.textContent = docNo + ' (Layer 1)';
                                poLabelCell1.style.backgroundColor = '#d5e8d4';
                                poLabelCell1.style.fontWeight = 'bold';
                                poLabelCell1.style.fontSize = '14px';
                                poRow1.appendChild(poLabelCell1);

                                // Kolom kertas untuk PO Layer 1 - Qty Remain
                                allPaperColumns.forEach(function(paperCol) {
                                    const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                                    const paperType = paperCol.paperType;

                                    // Cari PO item yang sesuai dengan material code ini
                                    const matchingPO = poItems.find(item => item.MaterialCode === paperCode);

                                    const cell = document.createElement('td');
                                    cell.style.backgroundColor = '#d5e8d4';
                                    cell.style.textAlign = 'right';
                                    cell.style.padding = '8px';
                                    cell.style.fontSize = '14px';
                                    cell.style.fontWeight = 'bold';

                                    if (matchingPO && matchingPO.QtyRemain > 0) {
                                        cell.textContent = formatNumber(matchingPO.QtyRemain);
                                        cell.setAttribute('title', `Qty: ${formatNumber(matchingPO.Qty)}, Received: ${formatNumber(matchingPO.QtyReceived || 0)}, Remain: ${formatNumber(matchingPO.QtyRemain)}`);

                                        // Hidden input untuk form submission
                                        const hiddenInput = document.createElement('input');
                                        hiddenInput.type = 'hidden';
                                        hiddenInput.name = `po_remain_layer1[${docNo}][${paperCode}]`;
                                        hiddenInput.value = matchingPO.QtyRemain;
                                        cell.appendChild(hiddenInput);
                                    } else {
                                        cell.textContent = '-';
                                    }

                                    poRow1.appendChild(cell);
                                });

                                // ===== PO LAYER 2: Qty Remain × 500 × UP =====
                                const poRow2 = document.createElement('tr');
                                poRow2.className = 'po-remain-row po-layer-2';
                                poRow2.setAttribute('data-po-number', docNo);
                                poRow2.setAttribute('data-layer', '2');

                                const poLabelCell2 = document.createElement('td');
                                poLabelCell2.setAttribute('colspan', 7);
                                poLabelCell2.textContent = docNo + ' (Layer 2)';
                                poLabelCell2.style.backgroundColor = '#c4e1c4';
                                poLabelCell2.style.fontWeight = 'bold';
                                poLabelCell2.style.fontSize = '14px';
                                poRow2.appendChild(poLabelCell2);

                                // Kolom kertas untuk PO Layer 2 - Qty Remain × 500 × UP
                                allPaperColumns.forEach(function(paperCol) {
                                    const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                                    const paperType = paperCol.paperType;

                                    // Cari PO item yang sesuai dengan material code ini
                                    const matchingPO = poItems.find(item => item.MaterialCode === paperCode);

                                    const cell = document.createElement('td');
                                    cell.style.backgroundColor = '#c4e1c4';
                                    cell.style.textAlign = 'right';
                                    cell.style.padding = '8px';
                                    cell.style.fontSize = '14px';
                                    cell.style.fontWeight = 'bold';

                                    if (matchingPO && matchingPO.QtyRemain > 0) {
                                        // Ambil nilai UP dari input field
                                        const upInput = document.querySelector(`input[name="paper_up[${paperType}]"]`);
                                        const upValue = upInput ? (parseFloat(upInput.value) || 5) : 5; // Default UP = 5

                                        // Layer 2 = QtyRemain × 500 × UP
                                        const layer2Value = matchingPO.QtyRemain * 500 * upValue;
                                        cell.textContent = formatNumber(layer2Value);
                                        cell.setAttribute('title', `Qty Remain: ${formatNumber(matchingPO.QtyRemain)}, UP: ${upValue}, Hasil: ${formatNumber(matchingPO.QtyRemain)} × 500 × ${upValue} = ${formatNumber(layer2Value)}`);

                                        // Hidden input untuk form submission
                                        const hiddenInput = document.createElement('input');
                                        hiddenInput.type = 'hidden';
                                        hiddenInput.name = `po_remain_layer2[${docNo}][${paperCode}]`;
                                        hiddenInput.value = layer2Value;
                                        cell.appendChild(hiddenInput);
                                    } else {
                                        cell.textContent = '-';
                                    }

                                    poRow2.appendChild(cell);
                                });

                                // Insert kedua row sebelum MINUS PAPER atau di akhir jika tidak ada
                                if (insertBeforeElement) {
                                    tbody.insertBefore(poRow1, insertBeforeElement);
                                    tbody.insertBefore(poRow2, insertBeforeElement);
                                } else {
                                    tbody.appendChild(poRow1);
                                    tbody.appendChild(poRow2);
                                }
                            });
                        }

                        // ===== SECTION: BELUM ADA PO (Manual Input) - SELALU CEK =====
                        // Cari kertas yang belum ada PO (baik dari result.raw atau jika result.raw kosong, semua kertas)
                        const papersWithPO = new Set();
                        if (result.success && result.raw && result.raw.length > 0) {
                            result.raw.forEach(function(po) {
                                if (po.MaterialCode && po.QtyRemain > 0) {
                                    papersWithPO.add(po.MaterialCode);
                                }
                            });
                        }

                        // Cek apakah ada kertas yang belum ada PO
                        const papersWithoutPO = allPaperColumns.filter(function(paperCol) {
                            const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                            return paperCode && paperCode.startsWith('K.') && !papersWithPO.has(paperCode);
                        });

                        // Tampilkan input manual jika ada kertas yang belum ada PO
                        if (papersWithoutPO.length > 0) {
                            // Cari posisi untuk menambahkan (setelah PO rows, sebelum MINUS PAPER)
                            const minusPaperRow = tbody.querySelector('.minus-paper-row');
                            let insertBeforeElement = minusPaperRow;

                            if (!insertBeforeElement) {
                                insertBeforeElement = tbody.querySelector('.cover-sampai-row');
                            }

                            // ===== BELUM ADA PO LAYER 1: Input Manual =====
                            const noPORow1 = document.createElement('tr');
                            noPORow1.className = 'po-manual-row po-manual-layer-1';
                            noPORow1.setAttribute('data-layer', '1');

                            const noPOLabelCell1 = document.createElement('td');
                            noPOLabelCell1.setAttribute('colspan', 7);
                            noPOLabelCell1.textContent = 'BELUM ADA PO (Layer 1)';
                            noPOLabelCell1.style.backgroundColor = '#ffcc99';
                            noPOLabelCell1.style.fontWeight = 'bold';
                            noPOLabelCell1.style.fontSize = '14px';
                            noPORow1.appendChild(noPOLabelCell1);

                            // Kolom kertas untuk input manual Layer 1
                            allPaperColumns.forEach(function(paperCol) {
                                const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                                const paperType = paperCol.paperType;

                                const cell = document.createElement('td');
                                cell.style.backgroundColor = '#ffcc99';
                                cell.style.textAlign = 'right';
                                cell.style.padding = '8px';
                                cell.style.fontSize = '14px';

                                // Cek apakah kertas ini belum ada PO
                                const hasNoPO = papersWithoutPO.some(p => {
                                    const pCode = p.paper ? p.paper.code : p.paperType;
                                    return pCode === paperCode;
                                });

                                if (hasNoPO) {
                                    // Buat input field untuk quantity manual
                                    const input = document.createElement('input');
                                    input.type = 'text';
                                    input.className = 'excel-input po-manual-input';
                                    input.name = `po_manual_layer1[${paperCode}]`;
                                    input.setAttribute('data-paper-code', paperCode);
                                    input.setAttribute('data-paper-type', paperType);
                                    input.value = '0';
                                    input.style.width = '100%';
                                    input.style.border = '1px solid #666';
                                    input.style.background = '#fff';
                                    input.style.textAlign = 'right';
                                    input.style.fontSize = '14px';
                                    input.style.padding = '4px';
                                    input.style.fontWeight = 'bold';

                                    // Hidden input untuk form submission
                                    const hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = `po_manual_layer1_value[${paperCode}]`;
                                    hiddenInput.value = '0';

                                    // Format input untuk number dan update Layer 2
                                    let updateTimeout;
                                    input.addEventListener('input', function(e) {
                                        let val = this.value;
                                        val = val.replace(',', '.');
                                        val = val.replace(/[^\d.]/g, '');
                                        const parts = val.split('.');
                                        if (parts.length > 2) {
                                            val = parts[0] + '.' + parts.slice(1).join('');
                                        }
                                        this.value = val;

                                        // Update hidden input
                                        const numVal = parseFloat(val) || 0;
                                        hiddenInput.value = numVal;

                                        // Update Layer 2 langsung (tanpa debounce untuk respons lebih cepat)
                                        if (typeof updatePOManualLayer2 === 'function') {
                                            updatePOManualLayer2(paperCode, paperType);
                                        }

                                        // Update MINUS PAPER dengan debounce untuk menghindari terlalu banyak pemanggilan
                                        clearTimeout(updateTimeout);
                                        updateTimeout = setTimeout(() => {
                                            if (typeof updateMinusPaper === 'function') {
                                                updateMinusPaper();
                                            }
                                        }, 300);
                                    });

                                    input.addEventListener('blur', function(e) {
                                        const val = parseFloat(this.value.replace(',', '.')) || 0;
                                        this.value = formatNumber(val);
                                        hiddenInput.value = val;
                                        if (typeof updatePOManualLayer2 === 'function') {
                                            updatePOManualLayer2(paperCode, paperType);
                                        }
                                        // Update MINUS PAPER setelah BELUM ADA PO berubah
                                        if (typeof updateMinusPaper === 'function') {
                                            setTimeout(() => updateMinusPaper(), 200);
                                        }
                                    });

                                    cell.appendChild(input);
                                    cell.appendChild(hiddenInput);
                                } else {
                                    cell.textContent = '-';
                                }

                                noPORow1.appendChild(cell);
                            });

                            // ===== BELUM ADA PO LAYER 2: Auto-calculate =====
                            const noPORow2 = document.createElement('tr');
                            noPORow2.className = 'po-manual-row po-manual-layer-2';
                            noPORow2.setAttribute('data-layer', '2');

                            const noPOLabelCell2 = document.createElement('td');
                            noPOLabelCell2.setAttribute('colspan', 7);
                            noPOLabelCell2.textContent = 'BELUM ADA PO (Layer 2)';
                            noPOLabelCell2.style.backgroundColor = '#ffb366';
                            noPOLabelCell2.style.fontWeight = 'bold';
                            noPOLabelCell2.style.fontSize = '14px';
                            noPORow2.appendChild(noPOLabelCell2);

                            // Kolom kertas untuk Layer 2 (auto-calculate)
                            allPaperColumns.forEach(function(paperCol) {
                                const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                                const paperType = paperCol.paperType;

                                const cell = document.createElement('td');
                                cell.style.backgroundColor = '#ffb366';
                                cell.style.textAlign = 'right';
                                cell.style.padding = '8px';
                                cell.style.fontSize = '14px';
                                cell.style.fontWeight = 'bold';

                                // Cek apakah kertas ini belum ada PO
                                const hasNoPO = papersWithoutPO.some(p => {
                                    const pCode = p.paper ? p.paper.code : p.paperType;
                                    return pCode === paperCode;
                                });

                                if (hasNoPO) {
                                    cell.textContent = '0';
                                    cell.setAttribute('data-paper-code', paperCode);
                                    cell.setAttribute('data-paper-type', paperType);

                                    // Hidden input untuk form submission
                                    const hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = `po_manual_layer2[${paperCode}]`;
                                    hiddenInput.value = '0';
                                    cell.appendChild(hiddenInput);
                                } else {
                                    cell.textContent = '-';
                                }

                                noPORow2.appendChild(cell);
                            });

                            // Insert sebelum MINUS PAPER
                            if (insertBeforeElement) {
                                tbody.insertBefore(noPORow1, insertBeforeElement);
                                tbody.insertBefore(noPORow2, insertBeforeElement);
                            } else {
                                tbody.appendChild(noPORow1);
                                tbody.appendChild(noPORow2);
                            }

                            // Update Layer 2 untuk semua input manual setelah dibuat
                            setTimeout(function() {
                                updateAllPOManualLayer2();
                                // Update MINUS PAPER setelah PO remain dan PO manual layer 2 selesai diupdate
                                if (typeof updateMinusPaper === 'function') {
                                    setTimeout(() => updateMinusPaper(), 200);
                                }
                            }, 100);
                        }
                    } catch (error) {
                        console.error('Error loading PO remain:', error);
                        // Hapus loading indicator jika ada error
                        const tbody = document.getElementById('workspace-tbody');
                        if (tbody) {
                            const loadingRows = tbody.querySelectorAll('.po-remain-loading');
                            loadingRows.forEach(row => row.remove());
                        }
                    } finally {
                        // Reset flag setelah selesai (baik sukses maupun error)
                        // Update MINUS PAPER setelah semua PO selesai dimuat
                        if (typeof updateMinusPaper === 'function') {
                            setTimeout(() => updateMinusPaper(), 300);
                        }
                        isLoadingPORemain = false;
                    }
                }

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
                                renderWorkspace()
                                    .then(() => {
                                        // Load PO remain setelah workspace selesai di-render
                                        if (allPaperColumns.length > 0) {
                                            loadPORemain();
                                        }
                                    })
                                    .catch(err => console.error('Error rendering workspace:', err));
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
                                <div style="margin-top: 8px;">
                                    <input type="number"
                                           class="excel-input paper-up-input"
                                           name="paper_up[${paperCol.paperType}]"
                                           data-paper-type="${paperCol.paperType}"
                                           value="5"
                                           min="0"
                                           step="0.01"
                                           placeholder="UP"
                                           style="width: 100%; border: 2px solid #ff0000; border-radius: 4px; background: #fff; text-align: center; font-size: 13px; font-weight: bold; padding: 4px 6px;">
                                </div>
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

                    // Update header dengan kolom kertas (UP input sudah ada di header)
                    updateTableHeader();

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
                            // Definisikan paperCode di awal untuk menghindari "Cannot access before initialization"
                            const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                            const hasPaper = paperCode ? itemUsesPaper(item, paperCode) : itemUsesPaperType(item, paperCol.paperType);

                            let paperCell;

                            if (hasPaper) {
                                // Produk menggunakan kertas ini - gunakan warna kertas yang digunakan produk (rowColor)
                                // Bukan warna kertas asli, tapi warna yang sama dengan kolom non-kertas
                                foundUsedPaper = true;
                                // Cek paperQuantities dengan paperCode dulu, lalu fallback ke paperType
                                const paperQty = item.paperQuantities && (item.paperQuantities[paperCode] || item.paperQuantities[paperCol.paperType]) ?
                                    (item.paperQuantities[paperCode] || item.paperQuantities[paperCol.paperType]) :
                                    totalWithTol;

                                // Dapatkan class kertas (untuk CSS base layer)
                                let paperClass = getPaperClass(paperCol.paperType);

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
                                // Gunakan paperCode sebagai key untuk konsistensi
                                const key = paperCode || paperCol.paperType;
                                if (!paperTotals[key]) {
                                    paperTotals[key] = 0;
                                }
                                // Cek paperQuantities dengan paperCode dulu, lalu fallback ke paperType
                                let paperQty = itemTotalWithTol; // Default
                                if (item.paperQuantities) {
                                    if (paperCode && item.paperQuantities[paperCode] !== undefined) {
                                        paperQty = item.paperQuantities[paperCode];
                                    } else if (item.paperQuantities[paperCol.paperType] !== undefined) {
                                        paperQty = item.paperQuantities[paperCol.paperType];
                                    }
                                }
                                paperTotals[key] += paperQty;

                                // Debug log
                                console.log(`paperTotals [${key}]:`, {
                                    itemId: item.id,
                                    paperCode,
                                    paperType: paperCol.paperType,
                                    paperQty,
                                    currentTotal: paperTotals[key],
                                    paperQuantities: item.paperQuantities
                                });
                            }
                        });
                    });

                    // Buat row TOTAL
                    const totalRow = document.createElement('tr');
                    totalRow.className = 'total-row';
                    totalRow.setAttribute('data-row-type', 'total');

                    // Helper function untuk membuat cell dengan class tg-total
                    function createTotalCell(content, align = 'left', colspan = 1) {
                        const cell = document.createElement('td');
                        cell.className = 'tg-total';
                        // Pastikan warna kuning konsisten dengan CSS
                        cell.style.backgroundColor = '#FFE699';
                        cell.style.setProperty('background-color', '#FFE699', 'important');
                        cell.style.setProperty('background', '#FFE699', 'important');
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
                    console.log('paperTotals object:', paperTotals);
                    allPaperColumns.forEach(function(paperCol) {
                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        const paperType = paperCol.paperType;
                        // Cek dengan paperCode dulu, lalu fallback ke paperType untuk backward compatibility
                        const paperTotal = paperTotals[paperCode] || paperTotals[paperType] || 0;
                        console.log(`Total for ${paperCode} (${paperType}):`, paperTotal, 'from paperTotals:', paperTotals);

                        // Buat cell dengan class tg-total (kuning) untuk baris TOTAL
                        // SEMUA cell di baris TOTAL harus kuning, termasuk kolom kertas
                        const paperTotalCell = document.createElement('td');
                        paperTotalCell.className = 'tg-total'; // Gunakan class kuning untuk baris TOTAL
                        paperTotalCell.style.textAlign = 'right';
                        paperTotalCell.style.fontWeight = 'bold';
                        // Force set warna kuning dengan inline style untuk memastikan konsisten dengan CSS
                        paperTotalCell.style.backgroundColor = '#FFE699';
                        paperTotalCell.style.setProperty('background-color', '#FFE699', 'important');
                        paperTotalCell.style.setProperty('background', '#FFE699', 'important');
                        paperTotalCell.textContent = formatNumber(paperTotal);

                        // Tambahkan data attribute untuk memudahkan pencarian
                        paperTotalCell.setAttribute('data-paper-type', paperType);
                        paperTotalCell.setAttribute('data-paper-code', paperCode);
                        totalRow.appendChild(paperTotalCell);
                    });

                    tbody.appendChild(totalRow);

                    // Update MINUS PAPER setelah row TOTAL dibuat
                    // Delay lebih lama untuk memastikan semua data sudah ter-render
                    setTimeout(function() {
                        if (typeof updateMinusPaper === 'function' && allPaperColumns && allPaperColumns.length > 0) {
                            console.log('Calling updateMinusPaper after TOTAL row created');
                            updateMinusPaper();
                        }
                    }, 300);

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
                            renderWorkspace()
                                .then(() => {
                                    // Load PO remain setelah workspace selesai di-render
                                    if (allPaperColumns.length > 0) {
                                        loadPORemain();
                                    }
                                    // Update MINUS PAPER setelah semua selesai
                                    if (typeof updateMinusPaper === 'function') {
                                        setTimeout(() => updateMinusPaper(), 200);
                                    }
                                })
                                .catch(err => console.error('Error rendering workspace:', err));
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

                        // Update MINUS PAPER setelah Total Stok Layer 2 berubah
                        if (typeof updateMinusPaper === 'function') {
                            updateMinusPaper();
                        }
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

                    // PO remain akan di-load setelah renderWorkspace selesai atau via button

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

                        // Fungsi untuk update PO Layer 2 ketika UP berubah
                        function updatePOLayer2() {
                            const tbody = document.getElementById('workspace-tbody');
                            if (!tbody) return;

                            // Ambil semua PO Layer 2 rows
                            const poLayer2Rows = tbody.querySelectorAll('.po-layer-2');

                            poLayer2Rows.forEach(function(poRow) {
                                const docNo = poRow.getAttribute('data-po-number');

                                // Cari semua PO Layer 1 untuk mendapatkan QtyRemain
                                const poLayer1Row = tbody.querySelector(`.po-layer-1[data-po-number="${docNo}"]`);
                                if (!poLayer1Row) return;

                                // Update setiap kolom kertas di Layer 2
                                allPaperColumns.forEach(function(paperCol) {
                                    const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                                    const paperType = paperCol.paperType;

                                    // Ambil QtyRemain dari Layer 1 (hidden input)
                                    const layer1Input = poLayer1Row.querySelector(`input[name="po_remain_layer1[${docNo}][${paperCode}]"]`);
                                    if (!layer1Input) return;

                                    const qtyRemain = parseFloat(layer1Input.value) || 0;
                                    if (qtyRemain <= 0) return;

                                    // Ambil nilai UP dari input field
                                    const upInput = document.querySelector(`input[name="paper_up[${paperType}]"]`);
                                    const upValue = upInput ? (parseFloat(upInput.value) || 5) : 5;

                                    // Hitung Layer 2 = QtyRemain × 500 × UP
                                    const layer2Value = qtyRemain * 500 * upValue;

                                    // Cari hidden input di Layer 2 untuk paper code ini
                                    const layer2Input = poRow.querySelector(`input[name="po_remain_layer2[${docNo}][${paperCode}]"]`);
                                    if (layer2Input && layer2Input.parentElement) {
                                        const cell = layer2Input.parentElement;

                                        // Update cell content
                                        cell.textContent = formatNumber(layer2Value);
                                        cell.setAttribute('title', `Qty Remain: ${formatNumber(qtyRemain)}, UP: ${upValue}, Hasil: ${formatNumber(qtyRemain)} × 500 × ${upValue} = ${formatNumber(layer2Value)}`);

                                        // Update hidden input value
                                        layer2Input.value = layer2Value;
                                    }
                                });
                            });
                        }

                        // Attach event listener ke semua input UP di header kolom kertas
                        allPaperColumns.forEach(function(paperCol) {
                            const upInputName = `paper_up[${paperCol.paperType}]`;
                            const upInput = document.querySelector(`input[name="${upInputName}"]`);
                            if (upInput && !upInput.hasAttribute('data-listener-attached')) {
                                upInput.setAttribute('data-listener-attached', 'true');
                                upInput.addEventListener('input', function() {
                                    updateTotalStokLayers();
                                    updatePOLayer2(); // Update PO Layer 2 juga
                                    updateAllPOManualLayer2(); // Update PO Manual Layer 2 juga
                                    if (typeof updateMinusPaper === 'function') {
                                        updateMinusPaper(); // Update MINUS PAPER juga
                                    }
                                });
                                upInput.addEventListener('change', function() {
                                    updateTotalStokLayers();
                                    updatePOLayer2(); // Update PO Layer 2 juga
                                    updateAllPOManualLayer2(); // Update PO Manual Layer 2 juga
                                    if (typeof updateMinusPaper === 'function') {
                                        updateMinusPaper(); // Update MINUS PAPER juga
                                    }
                                });
                            }
                        });
                    }, 100);

                    // Section 3: MINUS PAPER - 3 Layer (PCS, RIM, TON)
                    // Fungsi updateMinusPaper sudah didefinisikan di scope global

                    // MINUS PAPER Layer 1: PCS
                    const minusPaperRow1 = document.createElement('tr');
                    minusPaperRow1.className = 'minus-paper-row minus-paper-pcs';
                    const minusPaperCell1 = document.createElement('td');
                    minusPaperCell1.setAttribute('colspan', 7);
                    minusPaperCell1.textContent = 'MINUS PAPER (PCS)';
                    minusPaperCell1.style.backgroundColor = '#fff2cc';
                    minusPaperCell1.style.fontWeight = 'bold';
                    minusPaperCell1.style.fontSize = '15px';
                    minusPaperRow1.appendChild(minusPaperCell1);

                    allPaperColumns.forEach(function(paperCol) {
                        if (!paperCol) return;

                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        const paperType = paperCol.paperType;

                        if (!paperType) {
                            console.warn('MINUS PAPER PCS: paperType is not defined for paperCol:', paperCol);
                            return;
                        }

                        const cell = document.createElement('td');
                        cell.style.backgroundColor = '#fff2cc';
                        cell.style.textAlign = 'right';
                        cell.style.padding = '8px';
                        cell.style.fontSize = '15px';
                        cell.style.fontWeight = 'bold';
                        cell.textContent = '0';

                        // Hidden input untuk form submission
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `minus_paper_pcs[${paperType}]`;
                        hiddenInput.value = '0';
                        cell.appendChild(hiddenInput);

                        minusPaperRow1.appendChild(cell);
                    });
                    tbody.appendChild(minusPaperRow1);

                    // MINUS PAPER Layer 2: RIM
                    const minusPaperRow2 = document.createElement('tr');
                    minusPaperRow2.className = 'minus-paper-row minus-paper-rim';
                    const minusPaperCell2 = document.createElement('td');
                    minusPaperCell2.setAttribute('colspan', 7);
                    minusPaperCell2.textContent = 'MINUS PAPER (RIM)';
                    minusPaperCell2.style.backgroundColor = '#ffe6b3';
                    minusPaperCell2.style.fontWeight = 'bold';
                    minusPaperCell2.style.fontSize = '15px';
                    minusPaperRow2.appendChild(minusPaperCell2);

                    allPaperColumns.forEach(function(paperCol) {
                        if (!paperCol) return;

                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        const paperType = paperCol.paperType;

                        if (!paperType) {
                            console.warn('MINUS PAPER RIM: paperType is not defined for paperCol:', paperCol);
                            return;
                        }

                        const cell = document.createElement('td');
                        cell.style.backgroundColor = '#ffe6b3';
                        cell.style.textAlign = 'right';
                        cell.style.padding = '8px';
                        cell.style.fontSize = '15px';
                        cell.style.fontWeight = 'bold';
                        cell.textContent = '0';

                        // Hidden input untuk form submission
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `minus_paper_rim[${paperType}]`;
                        hiddenInput.value = '0';
                        cell.appendChild(hiddenInput);

                        minusPaperRow2.appendChild(cell);
                    });
                    tbody.appendChild(minusPaperRow2);

                    // MINUS PAPER Layer 3: TON (placeholder)
                    const minusPaperRow3 = document.createElement('tr');
                    minusPaperRow3.className = 'minus-paper-row minus-paper-ton';
                    const minusPaperCell3 = document.createElement('td');
                    minusPaperCell3.setAttribute('colspan', 7);
                    minusPaperCell3.textContent = 'MINUS PAPER (TON)';
                    minusPaperCell3.style.backgroundColor = '#ffd9b3';
                    minusPaperCell3.style.fontWeight = 'bold';
                    minusPaperCell3.style.fontSize = '15px';
                    minusPaperRow3.appendChild(minusPaperCell3);

                    allPaperColumns.forEach(function(paperCol) {
                        if (!paperCol) return;

                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        const paperType = paperCol.paperType;

                        if (!paperType) {
                            console.warn('updateMinusPaper: paperType is not defined for paperCol:', paperCol);
                            return;
                        }

                        const cell = document.createElement('td');
                        cell.style.backgroundColor = '#ffd9b3';
                        cell.style.textAlign = 'right';
                        cell.style.padding = '8px';
                        cell.style.fontSize = '15px';
                        cell.style.fontWeight = 'bold';
                        cell.textContent = '-';

                        // Hidden input untuk form submission
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `minus_paper_ton[${paperType}]`;
                        hiddenInput.value = '0';
                        cell.appendChild(hiddenInput);

                        minusPaperRow3.appendChild(cell);
                    });
                    tbody.appendChild(minusPaperRow3);

                    // Update MINUS PAPER setelah semua data siap
                    // Pastikan allPaperColumns sudah terisi sebelum memanggil updateMinusPaper
                    setTimeout(function() {
                        if (typeof updateMinusPaper === 'function' && allPaperColumns && allPaperColumns.length > 0) {
                            // Pastikan semua elemen allPaperColumns memiliki paperType
                            const hasValidColumns = allPaperColumns.every(function(col) {
                                return col && col.paperType;
                            });
                            if (hasValidColumns) {
                                updateMinusPaper();
                            } else {
                                console.warn('updateMinusPaper: Some columns are missing paperType');
                            }
                        }
                    }, 300);

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

                    // Section 5: SUMMARY TOTAL KEBUTUHAN KERTAS (TON)
                    const summaryRow = document.createElement('tr');
                    summaryRow.className = 'summary-total-ton-row';
                    summaryRow.style.backgroundColor = '#d4edda';
                    summaryRow.style.borderTop = '3px solid #28a745';

                    const summaryLabelCell = document.createElement('td');
                    summaryLabelCell.setAttribute('colspan', 7);
                    summaryLabelCell.setAttribute('id', 'summary-label-cell');
                    summaryLabelCell.style.backgroundColor = '#28a745';
                    summaryLabelCell.style.color = '#ffffff';
                    summaryLabelCell.style.fontWeight = 'bold';
                    summaryLabelCell.style.fontSize = '16px';
                    summaryLabelCell.style.textAlign = 'center';
                    summaryLabelCell.style.padding = '12px';
                    summaryLabelCell.style.position = 'relative';

                    // Buat container untuk label dan catatan
                    const labelContainer = document.createElement('div');
                    labelContainer.style.display = 'flex';
                    labelContainer.style.flexDirection = 'column';
                    labelContainer.style.alignItems = 'center';
                    labelContainer.style.gap = '8px';

                    // Label utama
                    const mainLabel = document.createElement('div');
                    mainLabel.textContent = 'TOTAL KEBUTUHAN KERTAS (TON)';
                    mainLabel.style.fontSize = '16px';
                    mainLabel.style.fontWeight = 'bold';
                    labelContainer.appendChild(mainLabel);

                    // Status (PERLU BELI / TIDAK PERLU BELI)
                    const statusElement = document.createElement('span');
                    statusElement.setAttribute('id', 'status-kebutuhan-kertas');
                    statusElement.style.fontSize = '14px';
                    statusElement.style.fontWeight = '600';
                    statusElement.style.display = 'block';
                    labelContainer.appendChild(statusElement);

                    // Catatan penjelasan
                    const catatanElement = document.createElement('div');
                    catatanElement.setAttribute('id', 'catatan-kebutuhan-kertas');
                    catatanElement.style.fontSize = '11px';
                    catatanElement.style.fontWeight = 'normal';
                    catatanElement.style.opacity = '0.9';
                    catatanElement.style.marginTop = '4px';
                    catatanElement.style.textAlign = 'center';
                    catatanElement.style.lineHeight = '1.4';
                    catatanElement.style.maxWidth = '90%';
                    labelContainer.appendChild(catatanElement);

                    summaryLabelCell.appendChild(labelContainer);
                    summaryRow.appendChild(summaryLabelCell);

                    // Kolom untuk total per jenis kertas dan grand total
                    let grandTotal = 0;
                    allPaperColumns.forEach(function(paperCol) {
                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        const paperType = paperCol.paperType;

                        const cell = document.createElement('td');
                        cell.className = 'summary-total-ton-cell';
                        cell.style.backgroundColor = '#d4edda';
                        cell.style.textAlign = 'right';
                        cell.style.padding = '12px';
                        cell.style.fontSize = '16px';
                        cell.style.fontWeight = 'bold';
                        cell.style.borderLeft = '2px solid #28a745';
                        cell.setAttribute('data-paper-type', paperType);
                        cell.setAttribute('data-paper-code', paperCode);
                        cell.textContent = '0';

                        // Hidden input untuk form submission
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `total_kebutuhan_ton[${paperType}]`;
                        hiddenInput.value = '0';
                        cell.appendChild(hiddenInput);

                        summaryRow.appendChild(cell);
                    });

                    // Cell untuk grand total (jika ada lebih dari 1 kolom kertas)
                    if (allPaperColumns.length > 1) {
                        const grandTotalCell = document.createElement('td');
                        grandTotalCell.className = 'summary-grand-total-cell';
                        grandTotalCell.style.backgroundColor = '#28a745';
                        grandTotalCell.style.color = '#ffffff';
                        grandTotalCell.style.textAlign = 'right';
                        grandTotalCell.style.padding = '12px';
                        grandTotalCell.style.fontSize = '18px';
                        grandTotalCell.style.fontWeight = 'bold';
                        grandTotalCell.style.borderLeft = '3px solid #155724';
                        grandTotalCell.textContent = '0';

                        // Hidden input untuk grand total
                        const grandTotalHiddenInput = document.createElement('input');
                        grandTotalHiddenInput.type = 'hidden';
                        grandTotalHiddenInput.name = 'grand_total_kebutuhan_ton';
                        grandTotalHiddenInput.value = '0';
                        grandTotalCell.appendChild(grandTotalHiddenInput);

                        summaryRow.appendChild(grandTotalCell);
                    }

                    tbody.appendChild(summaryRow);

                    // Section 6: CATATAN PER JENIS KERTAS (row baru di bawah summary)
                    const catatanRow = document.createElement('tr');
                    catatanRow.className = 'catatan-kertas-row';
                    catatanRow.style.backgroundColor = '#f8f9fa';
                    catatanRow.style.borderTop = '2px solid #dee2e6';

                    // Label cell
                    const catatanLabelCell = document.createElement('td');
                    catatanLabelCell.setAttribute('colspan', 7);
                    catatanLabelCell.textContent = 'CATATAN PER JENIS KERTAS';
                    catatanLabelCell.style.backgroundColor = '#6c757d';
                    catatanLabelCell.style.color = '#ffffff';
                    catatanLabelCell.style.fontWeight = 'bold';
                    catatanLabelCell.style.fontSize = '14px';
                    catatanLabelCell.style.textAlign = 'center';
                    catatanLabelCell.style.padding = '10px';
                    catatanRow.appendChild(catatanLabelCell);

                    // Input catatan untuk setiap jenis kertas
                    allPaperColumns.forEach(function(paperCol) {
                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        const paperType = paperCol.paperType;
                        const paperName = paperCol.paper ? paperCol.paper.name : paperType;

                        const catatanCell = document.createElement('td');
                        catatanCell.className = 'catatan-kertas-cell';
                        catatanCell.style.backgroundColor = '#f8f9fa';
                        catatanCell.style.padding = '8px';
                        catatanCell.style.verticalAlign = 'top';
                        catatanCell.setAttribute('data-paper-type', paperType);
                        catatanCell.setAttribute('data-paper-code', paperCode);

                        // Input field untuk catatan
                        const catatanInput = document.createElement('input');
                        catatanInput.type = 'text';
                        catatanInput.name = `catatan_kertas[${paperType}]`;
                        catatanInput.className = 'form-control catatan-kertas-input';
                        catatanInput.placeholder = 'Catatan untuk ' + (paperName.length > 20 ? paperName.substring(0, 20) + '...' : paperName);
                        catatanInput.style.width = '100%';
                        catatanInput.style.padding = '6px 10px';
                        catatanInput.style.fontSize = '12px';
                        catatanInput.style.border = '1px solid #ced4da';
                        catatanInput.style.borderRadius = '4px';
                        catatanInput.style.fontWeight = 'normal';
                        catatanInput.style.backgroundColor = '#ffffff';
                        catatanInput.setAttribute('data-paper-type', paperType);
                        catatanInput.setAttribute('data-paper-code', paperCode);

                        catatanCell.appendChild(catatanInput);
                        catatanRow.appendChild(catatanCell);
                    });

                    // Cell kosong untuk grand total (jika ada lebih dari 1 kolom kertas)
                    if (allPaperColumns.length > 1) {
                        const emptyCell = document.createElement('td');
                        emptyCell.style.backgroundColor = '#f8f9fa';
                        emptyCell.style.padding = '8px';
                        catatanRow.appendChild(emptyCell);
                    }

                    tbody.appendChild(catatanRow);

                    // Update total kebutuhan ton setelah summary row dibuat
                    setTimeout(function() {
                        if (typeof updateTotalKebutuhanTon === 'function') {
                            updateTotalKebutuhanTon();
                        }
                    }, 200);

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

                    // Update MINUS PAPER setelah semua rendering selesai
                    setTimeout(() => {
                        if (typeof updateMinusPaper === 'function') {
                            updateMinusPaper();
                        }
                    }, 500);

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
                            console.log(`Updated paperQuantities for item ${itemId}:`, {
                                paperCode,
                                value,
                                paperQuantities: item.paperQuantities
                            });

                            // Update preview workspace setelah nilai tersimpan
                            // Gunakan setTimeout untuk memastikan nilai sudah tersimpan
                            setTimeout(() => {
                                renderWorkspace()
                                    .then(() => {
                                        // Update MINUS PAPER setelah semua selesai
                                        if (typeof updateMinusPaper === 'function') {
                                            setTimeout(() => updateMinusPaper(), 200);
                                        }
                                    })
                                    .catch(err => console.error('Error rendering workspace:', err));
                            }, 50);
                        }
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
                    renderWorkspace()
                        .then(() => {
                            // Update Layer 2 setelah workspace di-render karena qty item berubah
                            if (typeof updateTotalStokLayers === 'function') {
                                updateTotalStokLayers();
                            }
                            // Load PO remain setelah workspace selesai di-render
                            if (allPaperColumns.length > 0) {
                                loadPORemain();
                            }
                        })
                        .catch(err => console.error('Error rendering workspace:', err));
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
                            color: paperData.color || '#ffffff',
                            zgsm: paperData.zgsm || 0,
                            zlength: paperData.zlength || 0,
                            zwidth: paperData.zwidth || 0
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
                            // Buat object paper dengan Code dan Name, termasuk zgsm, zlength, zwidth
                            allPaperColumns.push({
                                paperType: paperType,
                                paper: {
                                    code: paperCode,
                                    name: paperName,
                                    size: '', // Akan diisi jika ada di database
                                    color: paperObj.color || '#ffffff',
                                    zgsm: paperObj.zgsm || 0,
                                    zlength: paperObj.zlength || 0,
                                    zwidth: paperObj.zwidth || 0
                                }
                            });
                        } else {
                            // Update data zgsm, zlength, zwidth jika kolom sudah ada
                            const existingCol = allPaperColumns.find(col =>
                                col.paper && col.paper.code === paperCode
                            );
                            if (existingCol && existingCol.paper) {
                                existingCol.paper.zgsm = paperObj.zgsm || existingCol.paper.zgsm || 0;
                                existingCol.paper.zlength = paperObj.zlength || existingCol.paper.zlength || 0;
                                existingCol.paper.zwidth = paperObj.zwidth || existingCol.paper.zwidth || 0;
                            }
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
                    renderWorkspace()
                        .then(() => {
                            // Load PO remain setelah workspace selesai di-render
                            if (allPaperColumns.length > 0) {
                                loadPORemain();
                            }
                        })
                        .catch(err => console.error('Error rendering workspace:', err));

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
                    renderWorkspace()
                        .then(() => {
                            // Load PO remain setelah workspace selesai di-render
                            if (allPaperColumns.length > 0) {
                                loadPORemain();
                            }
                        })
                        .catch(err => console.error('Error rendering workspace:', err));

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
                            renderWorkspace()
                                .then(() => {
                                    // Load PO remain setelah workspace selesai di-render
                                    if (allPaperColumns.length > 0) {
                                        loadPORemain();
                                    }
                                    // Update MINUS PAPER setelah semua selesai
                                    if (typeof updateMinusPaper === 'function') {
                                        setTimeout(() => updateMinusPaper(), 200);
                                    }
                                })
                                .catch(err => console.error('Error rendering workspace:', err));
                        }
                    });
                }

                // Event listener untuk button Show PO
                $('#btn-show-po').on('click', function() {
                    if (allPaperColumns.length === 0) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Belum Ada Kertas',
                                text: 'Silakan tambahkan item dengan kertas terlebih dahulu.'
                            });
                        } else {
                            alert('Silakan tambahkan item dengan kertas terlebih dahulu.');
                        }
                        return;
                    }
                    loadPORemain();
                });

                // Reset form
                $('#btn-clear-form').on('click', function() {
                    $('#item_name').val(null).trigger('change');
                    $('#item_papers').val(null).trigger('change');
                });

                // Handler untuk form submission - kumpulkan semua data sebelum submit
                $('#paper-meeting-form').on('submit', function(e) {
                    const form = $(this);
                    const tbody = document.getElementById('workspace-tbody');
                    if (!tbody) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Workspace table tidak ditemukan.'
                        });
                        return false;
                    }

                    // 1. Kumpulkan data locations
                    const locationsData = [];
                    selectedLocations.forEach(function(location, index) {
                        locationsData.push({
                            code: location.Code,
                            name: location.Name,
                            sort_order: index + 1
                        });
                    });

                    // 2. Kumpulkan data stocks per location
                    const stocksData = [];
                    selectedLocations.forEach(function(location) {
                        const locationCode = location.Code;
                        const locationStocks = {
                            location_code: locationCode,
                            papers: {}
                        };

                        // Cari semua stock row untuk location ini
                        const locationRows = tbody.querySelectorAll(`tr.stock-detail-row[data-location-code="${locationCode}"]`);
                        locationRows.forEach(function(row) {
                            allPaperColumns.forEach(function(paperCol, colIndex) {
                                const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                                const paperType = paperCol.paperType;

                                // Cari cell stock untuk paper ini (index = 7 + colIndex)
                                const cells = row.querySelectorAll('td');
                                const stockCellIndex = 7 + colIndex;
                                if (cells[stockCellIndex]) {
                                    const stockInput = cells[stockCellIndex].querySelector('input[name^="stock_total_layer"]');
                                    if (stockInput) {
                                        const layer = stockInput.name.match(/stock_total_layer(\d+)/);
                                        if (layer) {
                                            const layerNum = layer[1];
                                            if (!locationStocks.papers[paperCode]) {
                                                locationStocks.papers[paperCode] = {
                                                    paper_code: paperCode,
                                                    paper_type: paperType,
                                                    stock_layer_1: 0,
                                                    stock_layer_2: 0,
                                                    stock_layer_3: 0
                                                };
                                            }
                                            locationStocks.papers[paperCode][`stock_layer_${layerNum}`] = parseFloat(stockInput.value) || 0;
                                        }
                                    }
                                }
                            });
                        });

                        if (Object.keys(locationStocks.papers).length > 0) {
                            stocksData.push(locationStocks);
                        }
                    });

                    // 3. Kumpulkan data PO Remains
                    const poRemainsData = [];
                    const poRemainRows = tbody.querySelectorAll('tr.po-remain-row.po-layer-2');
                    poRemainRows.forEach(function(row) {
                        const docNo = row.getAttribute('data-po-number');
                        if (!docNo) return;

                        allPaperColumns.forEach(function(paperCol, colIndex) {
                            const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                            const paperType = paperCol.paperType;

                            // Cari cell PO remain layer 2 untuk paper ini
                            const cells = row.querySelectorAll('td');
                            const poCellIndex = 7 + colIndex;
                            if (cells[poCellIndex]) {
                                const poInput = cells[poCellIndex].querySelector('input[name^="po_remain_layer2["]');
                                if (poInput && poInput.value && parseFloat(poInput.value) > 0) {
                                    // Cari juga layer 1 untuk qty_remain
                                    const layer1Row = tbody.querySelector(`tr.po-layer-1[data-po-number="${docNo}"]`);
                                    let qtyRemain = 0;
                                    let poRemainLayer1 = 0;
                                    if (layer1Row) {
                                        const layer1Cells = layer1Row.querySelectorAll('td');
                                        const layer1PoInput = layer1Cells[poCellIndex]?.querySelector('input[name^="po_remain_layer1["]');
                                        if (layer1PoInput) {
                                            qtyRemain = parseFloat(layer1PoInput.value) || 0;
                                            poRemainLayer1 = qtyRemain;
                                        }
                                    }

                                    const poRemainLayer2 = parseFloat(poInput.value) || 0;

                                    // Ambil UP value
                                    const upInput = document.querySelector(`input[name="paper_up[${paperType}]"]`);
                                    const upValue = upInput ? (parseFloat(upInput.value) || 5) : 5;

                                    poRemainsData.push({
                                        po_doc_no: docNo,
                                        paper_code: paperCode,
                                        paper_type: paperType,
                                        qty_remain: qtyRemain,
                                        po_remain_layer_1: poRemainLayer1,
                                        po_remain_layer_2: poRemainLayer2,
                                        up_value: upValue
                                    });
                                }
                            }
                        });
                    });

                    // 4. Kumpulkan data PO Manuals (BELUM ADA PO)
                    const poManualsData = {};
                    allPaperColumns.forEach(function(paperCol) {
                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        const paperType = paperCol.paperType;

                        const poManualLayer1Input = tbody.querySelector(`input[name="po_manual_layer1[${paperCode}]"]`);
                        const poManualLayer2Input = tbody.querySelector(`input[name="po_manual_layer2[${paperCode}]"]`);

                        if (poManualLayer1Input || poManualLayer2Input) {
                            const poManualLayer1 = poManualLayer1Input ? (parseFloat(poManualLayer1Input.value) || 0) : 0;
                            const poManualLayer2 = poManualLayer2Input ? (parseFloat(poManualLayer2Input.value) || 0) : 0;

                            if (poManualLayer1 > 0 || poManualLayer2 > 0) {
                                const upInput = document.querySelector(`input[name="paper_up[${paperType}]"]`);
                                const upValue = upInput ? (parseFloat(upInput.value) || 5) : 5;

                                poManualsData[paperCode] = {
                                    paper_code: paperCode,
                                    paper_type: paperType,
                                    po_manual_layer_1: poManualLayer1,
                                    po_manual_layer_2: poManualLayer2,
                                    up_value: upValue
                                };
                            }
                        }
                    });

                    // 5. Kumpulkan data perhitungan per jenis kertas
                    const paperCalculationsData = {};
                    allPaperColumns.forEach(function(paperCol) {
                        const paperCode = paperCol.paper ? paperCol.paper.code : paperCol.paperType;
                        const paperType = paperCol.paperType;

                        // Ambil data dari allPaperColumns
                        const paperData = paperCol.paper || {};

                        // Ambil UP value
                        const upInput = document.querySelector(`input[name="paper_up[${paperType}]"]`);
                        const upValue = upInput ? (parseFloat(upInput.value) || 5) : 5;

                        // Ambil COVER SAMPAI
                        const coverSampaiSelect = tbody.querySelector(`select[name="cover_sampai[${paperType}]"]`);
                        const coverSampai = coverSampaiSelect ? coverSampaiSelect.value : null;

                        // Ambil MINUS PAPER values
                        const minusPaperPCSInput = tbody.querySelector(`input[name="minus_paper_pcs[${paperType}]"]`);
                        const minusPaperRIMInput = tbody.querySelector(`input[name="minus_paper_rim[${paperType}]"]`);
                        const minusPaperTONInput = tbody.querySelector(`input[name="minus_paper_ton[${paperType}]"]`);

                        // Ambil TOTAL KEBUTUHAN TON dari summary cell
                        const summaryCell = tbody.querySelector(`td.summary-total-ton-cell[data-paper-type="${paperType}"]`);
                        let totalKebutuhanTON = 0;
                        if (summaryCell) {
                            const hiddenInput = summaryCell.querySelector('input[type="hidden"]');
                            if (hiddenInput) {
                                totalKebutuhanTON = parseFloat(hiddenInput.value) || 0;
                            } else {
                                // Fallback: ambil dari text content
                                const cellText = summaryCell.textContent.trim();
                                if (cellText && cellText !== '-' && cellText !== '0') {
                                    totalKebutuhanTON = parseFloat(cellText.replace(/\./g, '').replace(',', '.')) || 0;
                                }
                            }
                        }

                        // Ambil catatan
                        const catatanInput = tbody.querySelector(`input[name="catatan_kertas[${paperType}]"]`);
                        const catatan = catatanInput ? catatanInput.value : null;

                        paperCalculationsData[paperType] = {
                            paper_code: paperCode,
                            paper_name: paperData.name || null,
                            up_value: upValue,
                            zgsm: parseFloat(paperData.zgsm) || 0,
                            zlength: parseFloat(paperData.zlength) || 0,
                            zwidth: parseFloat(paperData.zwidth) || 0,
                            cover_sampai: coverSampai,
                            minus_paper_pcs: minusPaperPCSInput ? (parseFloat(minusPaperPCSInput.value) || 0) : 0,
                            minus_paper_rim: minusPaperRIMInput ? (parseFloat(minusPaperRIMInput.value) || 0) : 0,
                            minus_paper_ton: minusPaperTONInput ? (parseFloat(minusPaperTONInput.value) || 0) : 0,
                            total_kebutuhan_ton: totalKebutuhanTON,
                            catatan: catatan
                        };
                    });

                    // 6. Tambahkan product_code ke items jika belum ada
                    items.forEach(function(item) {
                        const itemInput = form.find(`input[name="items[${item.id}][product_name]"]`);
                        if (itemInput.length > 0) {
                            // Cek apakah sudah ada input product_code
                            let productCodeInput = form.find(`input[name="items[${item.id}][product_code]"]`);
                            if (productCodeInput.length === 0) {
                                // Buat hidden input untuk product_code
                                const hiddenCodeInput = $('<input>').attr({
                                    type: 'hidden',
                                    name: `items[${item.id}][product_code]`
                                }).val(item.code || '');
                                form.append(hiddenCodeInput);
                            } else {
                                productCodeInput.val(item.code || '');
                            }
                        }
                    });

                    // 7. Tambahkan semua data sebagai hidden inputs
                    if (locationsData.length > 0) {
                        form.append($('<input>').attr({
                            type: 'hidden',
                            name: 'locations_json'
                        }).val(JSON.stringify(locationsData)));
                    }

                    if (stocksData.length > 0) {
                        form.append($('<input>').attr({
                            type: 'hidden',
                            name: 'stocks_json'
                        }).val(JSON.stringify(stocksData)));
                    }

                    if (poRemainsData.length > 0) {
                        form.append($('<input>').attr({
                            type: 'hidden',
                            name: 'po_remains_json'
                        }).val(JSON.stringify(poRemainsData)));
                    }

                    if (Object.keys(poManualsData).length > 0) {
                        form.append($('<input>').attr({
                            type: 'hidden',
                            name: 'po_manuals_json'
                        }).val(JSON.stringify(poManualsData)));
                    }

                    if (Object.keys(paperCalculationsData).length > 0) {
                        form.append($('<input>').attr({
                            type: 'hidden',
                            name: 'paper_calculations_json'
                        }).val(JSON.stringify(paperCalculationsData)));
                    }

                    console.log('Form submission data:', {
                        locations: locationsData,
                        stocks: stocksData,
                        po_remains: poRemainsData,
                        po_manuals: poManualsData,
                        paper_calculations: paperCalculationsData
                    });
                });
            });
        </script>
    @endsection
@endsection
