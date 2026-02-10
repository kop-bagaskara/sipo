@extends('main.layouts.main')
@section('title')
    Detail Pengajuan Pembelian Kertas - Excel Style
@endsection
@section('css')
    <style>
        /* Excel-like styling */
        .excel-table {
            border-collapse: collapse;
            width: 100%;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .excel-table th, .excel-table td {
            border: 1px solid #000;
            padding: 4px 8px;
            text-align: center;
            vertical-align: middle;
        }

        .excel-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
        }

        /* Header colors - persis seperti Excel */
        .header-dpc250 {
            background-color: #d5e8d4 !important; /* Light green */
        }

        .header-ivory230 {
            background-color: #ffe6cc !important; /* Light orange/peach */
        }

        .header-sinarvanda {
            background-color: #dae8fc !important; /* Light blue */
        }

        /* Row colors - persis seperti Excel */
        .row-carton {
            background-color: white !important; /* Carton Juara - putih */
        }

        .row-packaging {
            background-color: #ffe6cc !important; /* Pack Packaging Juara - light orange/peach */
        }

        .row-inner-frame {
            background-color: #dae8fc !important; /* Inner Frame Juara - biru */
        }

        .row-esse {
            background-color: white !important; /* Esse - putih */
        }

        /* Column colors - persis seperti Excel */
        .col-dpc250 {
            background-color: #d5e8d4 !important; /* Light green */
        }

        .col-ivory230 {
            background-color: #ffe6cc !important; /* Light orange/peach */
        }

        .col-sinarvanda {
            background-color: #dae8fc !important; /* Light blue */
        }

        /* Override column colors untuk baris yang berbeda */
        .row-carton .col-dpc250 {
            background-color: #d5e8d4 !important; /* Carton pakai DPC 250 - hijau */
        }

        .row-packaging .col-dpc250 {
            background-color: #d5e8d4 !important; /* Pack Packaging DPC 250 - hijau */
        }

        /* Pack Packaging kolom PRODUK - hijau */
        .row-packaging td:nth-child(1) {
            background-color: #d5e8d4 !important;
        }

        /* Pack Packaging kolom PERIODE (OKT, NOV, DES) - hijau */
        .row-packaging td:nth-child(2),
        .row-packaging td:nth-child(3),
        .row-packaging td:nth-child(4) {
            background-color: #d5e8d4 !important;
        }

        /* Pack Packaging kolom TOTAL (OKT-DES dan +TOLERANSI) - hijau */
        .row-packaging td:nth-child(5),
        .row-packaging td:nth-child(6) {
            background-color: #d5e8d4 !important;
        }

        .row-packaging .col-ivory230 {
            background-color: #ffe6cc !important; /* Pack Packaging IVORY 230 - orange/peach */
        }

        .row-packaging .col-sinarvanda {
            background-color: #dae8fc !important; /* Pack Packaging VANDA 220 - biru */
        }

        /* Inner Frame override semua kolom menjadi biru - lebih spesifik */
        .row-inner-frame td {
            background-color: #dae8fc !important;
        }

        .row-inner-frame td:first-child {
            background-color: #dae8fc !important;
        }

        .row-inner-frame .col-dpc250,
        .row-inner-frame .col-ivory230,
        .row-inner-frame .col-sinarvanda {
            background-color: #dae8fc !important;
        }

        .row-esse .col-dpc250,
        .row-esse .col-ivory230,
        .row-esse .col-sinarvanda {
            background-color: white !important; /* Esse - putih seperti barisnya */
        }

        .excel-input {
            border: none;
            background: transparent;
            width: 100%;
            text-align: center;
            font-size: 11px;
        }

        .excel-input:focus {
            outline: 2px solid #0078d4;
            background: white;
        }
    </style>
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Detail Pengajuan Pembelian Kertas - Excel Style</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('paper-procurement.list') }}">Daftar Pengajuan</a></li>
                <li class="breadcrumb-item active">Detail Pengajuan - Excel</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i> Matrix Kebutuhan Kertas - Excel Style
                    </h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="excel-table">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width: 200px;">PRODUK</th>
                                    <th colspan="3">PERIODE</th>
                                    <th colspan="2">TOTAL</th>
                                    <th colspan="5">SPESIFIKASI KERTAS</th>
                                </tr>
                                <tr>
                                    <th>OKT</th>
                                    <th>NOV</th>
                                    <th>DES</th>
                                    <th>OKT-DES</th>
                                    <th>+TOLERANSI</th>
                                    <th class="header-dpc250">DPC 250<br>(73 x 52) @4 up (IKDP)</th>
                                    <th class="header-ivory230">IVORY 230<br>(62.5 x 94) @ 30 up (IKDP VR)</th>
                                    <th class="header-ivory230">IVORY 230<br>(62.5 x 94) @ 30 up (SPN)</th>
                                    <th class="header-ivory230">IVORY 230<br>(61 x 97.5) @ 63 up (IK VR)</th>
                                    <th class="header-sinarvanda">IVORY SINAR VANDA 220<br>(100.5 x 64) @ 32 up</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Carton Juara (White Background) -->
                                <tr class="row-carton">
                                    <td class="text-left"><strong>Carton Juara Teh Manis</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-carton">
                                    <td class="text-left"><strong>Carton Juara Ja'abu</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-carton">
                                    <td class="text-left"><strong>Carton Juara Mangga</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-carton">
                                    <td class="text-left"><strong>Carton Juara Berry</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="150000" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">150,000</strong></td>
                                    <td><strong class="total-tolerance">165,000</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">165,000</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-carton">
                                    <td class="text-left"><strong>Carton Juara Pisang</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-carton">
                                    <td class="text-left"><strong>Carton Juara Cokelat</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>

                                <!-- Pack Packaging Juara (Light Green Background) -->
                                <tr class="row-packaging">
                                    <td class="text-left"><strong>Pack Packaging Juara Teh Manis</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-packaging">
                                    <td class="text-left"><strong>Pack Packaging Juara Ja'abu</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-packaging">
                                    <td class="text-left"><strong>Pack Packaging Juara Mangga</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-packaging">
                                    <td class="text-left"><strong>Pack Packaging Juara Berry</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="1500000" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">1,500,000</strong></td>
                                    <td><strong class="total-tolerance">1,650,000</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">1,650,000</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-packaging">
                                    <td class="text-left"><strong>Pack Packaging Juara Pisang</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-packaging">
                                    <td class="text-left"><strong>Pack Packaging Juara Cokelat</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>

                                <!-- Inner Frame Juara (Light Orange/Peach Background) -->
                                <tr class="row-inner-frame">
                                    <td class="text-left"><strong>Inner Frame Juara Teh Manis</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-inner-frame">
                                    <td class="text-left"><strong>Inner Frame Juara Ja'abu</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-inner-frame">
                                    <td class="text-left"><strong>Inner Frame Juara Mangga</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-inner-frame">
                                    <td class="text-left"><strong>Inner Frame Juara Berry</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="1500000" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">1,500,000</strong></td>
                                    <td><strong class="total-tolerance">1,650,000</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">1,650,000</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-inner-frame">
                                    <td class="text-left"><strong>Inner Frame Juara Pisang</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-inner-frame">
                                    <td class="text-left"><strong>Inner Frame Juara Cokelat</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>

                                <!-- Esse Products (White Background) -->
                                <tr class="row-esse">
                                    <td class="text-left"><strong>Esse Cigar Cacao</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-esse">
                                    <td class="text-left"><strong>Esse Cigar Purple</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-esse">
                                    <td class="text-left"><strong>Esse India Black</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-esse">
                                    <td class="text-left"><strong>Esse India Change Blue</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-esse">
                                    <td class="text-left"><strong>EsseIndia Change Juicy</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-esse">
                                    <td class="text-left"><strong>Esse Change Double Kedara</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                                <tr class="row-esse">
                                    <td class="text-left"><strong>Pack Packaging Prime</strong></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><input type="number" class="excel-input" value="0" onchange="updateMatrix()"></td>
                                    <td><strong class="total-period">0</strong></td>
                                    <td><strong class="total-tolerance">0</strong></td>
                                    <td class="col-dpc250"><strong class="paper-dpc250">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-spn">0</strong></td>
                                    <td class="col-ivory230"><strong class="paper-ivory-ik-vr">0</strong></td>
                                    <td class="col-sinarvanda"><strong class="paper-sinar-vanda">0</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function updateMatrix() {
        // Calculate totals for each row
        const rows = document.querySelectorAll('.excel-table tbody tr');

        rows.forEach(row => {
            const inputs = row.querySelectorAll('input[type="number"]');
            let total = 0;

            inputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            // Update period total
            const totalPeriodCell = row.querySelector('.total-period');
            if (totalPeriodCell) {
                totalPeriodCell.textContent = total.toLocaleString();
            }

            // Update tolerance total
            const totalToleranceCell = row.querySelector('.total-tolerance');
            if (totalToleranceCell) {
                totalToleranceCell.textContent = (total * 1.1).toLocaleString();
            }

            // Update paper requirements based on product type
            updatePaperRequirements(row, total);
        });
    }

    function updatePaperRequirements(row, total) {
        const tolerance = total * 1.1;

        // Reset all paper columns to 0
        const paperCells = row.querySelectorAll('.paper-dpc250, .paper-ivory-ikdp-vr, .paper-ivory-spn, .paper-ivory-ik-vr, .paper-sinar-vanda');
        paperCells.forEach(cell => {
            cell.textContent = '0';
        });

        // Get product name to determine which papers it can use
        const productName = row.querySelector('td:first-child strong')?.textContent || '';

        if (tolerance > 0) {
            // Carton products use DPC 250
            if (productName.includes('Carton')) {
                const dpc250Cell = row.querySelector('.paper-dpc250');
                if (dpc250Cell) {
                    dpc250Cell.textContent = tolerance.toLocaleString();
                }
            }
            // Pack Packaging products use IVORY 230 IKDP VR
            else if (productName.includes('Pack Packaging')) {
                const ikdpVrCell = row.querySelector('.paper-ivory-ikdp-vr');
                if (ikdpVrCell) {
                    ikdpVrCell.textContent = tolerance.toLocaleString();
                }
            }
            // Inner Frame products use IVORY 230 IK VR
            else if (productName.includes('Inner Frame')) {
                const ikVrCell = row.querySelector('.paper-ivory-ik-vr');
                if (ikVrCell) {
                    ikVrCell.textContent = tolerance.toLocaleString();
                }
            }
            // Esse products use IVORY SINAR VANDA 220
            else if (productName.includes('Esse')) {
                const sinarVandaCell = row.querySelector('.paper-sinar-vanda');
                if (sinarVandaCell) {
                    sinarVandaCell.textContent = tolerance.toLocaleString();
                }
            }
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateMatrix();
    });
</script>
@endsection
