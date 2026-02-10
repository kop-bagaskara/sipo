<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Label TSPM - {{ $template->template_name ?? 'Label' }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
        }

        /* Container for 4 labels (2x2 grid) - exact A4 landscape size */
        .labels-container {
            width: 297mm;
            height: 210mm;
            position: relative;
            border: none;
            display: table;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 3mm;
        }

        .labels-row {
            display: table-row;
        }

        /* Single label box - exactly 1/4 of A4 landscape */
        .label-box {
            /* width: 145mm; */
            height: 102mm; /* Fixed height: setengah dari tinggi A4 landscape (210mm/2 = 105mm, dikurangi border spacing) */
            max-height: 102mm;
            display: table-cell;
            border: 1px solid #000;
            padding: 3mm;
            vertical-align: top;
            overflow: hidden;
        }

        /* Header */
        .header-company {
            font-weight: bold;
            font-size: 23px;
            text-align: center;
            margin-bottom: 2px;
            line-height: 1.2;
        }

        .header-address {
            font-size: 11px;
            text-align: center;
            margin-bottom: 5px;
            line-height: 1.2;
        }

        /* Table for fields */
        .field-table {
            width: 100%;
            height: 60%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 8px;
        }

        .field-table td {
            padding: 3px 5px;
            vertical-align: top;

        }

        .field-label {
            font-weight: bold;
            white-space: nowrap;
        }

        .field-value {
            padding-left: 5px;
        }
    </style>
</head>
<body>
    @php
        $quantity = $quantity ?? 4;
        $labelsPerPage = 4; // 2x2 grid
        $totalLabels = max(4, $quantity);
        $totalPages = ceil($totalLabels / $labelsPerPage);
    @endphp

    @for($page = 0; $page < $totalPages; $page++)
        <div class="labels-container">
            <!-- Row 1: Label 1 and Label 2 -->
            <div class="labels-row">
                @for($row = 0; $row < 2; $row++)
                    @php
                        $labelIndex = ($page * $labelsPerPage) + $row;
                    @endphp
                    <div class="label-box">
                        <!-- Header -->
                        <div class="header-company">
                            PT.KRISANTHIUM OFFSET PRINTING
                        </div>

                        <div class="header-address">
                            Jl.Rungkut Industri III/19 Surabaya Telp. 8438096,8438182-Fax.8432186
                        </div>
                        <hr>

                        <!-- Content Fields -->
                        <table class="field-table" style="font-size: 13px;">
                            <tr>
                                <td class="field-label" style="width: 150px;">NAMA PRODUK</td>
                                <td class="field-label">:</td>
                                <td class="field-value" colspan="4">{{ $fieldValues['nama_produk'] ?? $fieldValues['NAMA_PRODUK'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">KODE DESIGN</td>
                                <td class="field-label">:</td>
                                <td class="field-value" colspan="4">{{ $fieldValues['kode_item'] ?? $fieldValues['KODE_DESIGN'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">NO.WO</td>
                                <td class="field-label">:</td>
                                <td style="width: 150px;">{{ $fieldValues['no_wo'] ?? $fieldValues['NO_WO'] ?? '' }}</td>
                                <td class="field-label" style="width: 50px;">NO.PO</td>
                                <td>:</td>
                                <td>{{ $fieldValues['no_po'] ?? $fieldValues['NO_PO'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">TGL.PRODUKSI</td>
                                <td class="field-label">:</td>
                                <td style="width: 150px;">{{ $fieldValues['tgl_produksi'] ?? $fieldValues['TGL_PRODUKSI'] ?? '' }}</td>
                                <td class="field-label" style="width: 50px;">SHIFT</td>
                                <td class="field-label">:</td>
                                <td class="field-value">{{ $fieldValues['shift'] ?? $fieldValues['SHIFT'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">TGL.EXPIRED</td>
                                <td class="field-label">:</td>
                                <td class="field-value" colspan="3">{{ $fieldValues['tgl_expired'] ?? $fieldValues['TGL_EXPIRED'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">OPERATOR</td>
                                <td class="field-label">:</td>
                                <td class="field-value">{{ $fieldValues['operator'] ?? $fieldValues['OPERATOR'] ?? '' }}</td>
                                @if(isset($fieldValues['TGL_SECONDARY']) && !empty($fieldValues['TGL_SECONDARY']))
                                <td class="field-value" style="padding-left: 15px;">{{ $fieldValues['TGL_SECONDARY'] }}</td>
                                @else
                                <td></td>
                                @endif
                            </tr>
                            <tr>
                                <td class="field-label">MESIN</td>
                                <td class="field-label">:</td>
                                <td class="field-value" colspan="3">{{ $fieldValues['mesin'] ?? $fieldValues['MESIN'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">TGL.RC</td>
                                <td class="field-label">:</td>
                                <td class="field-value" colspan="3">{{ $fieldValues['TGL_RC'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">ISI</td>
                                <td class="field-label">:</td>
                                <td class="field-value" colspan="3">{{ $fieldValues['isi'] ?? $fieldValues['ISI'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">TGL.KIRIM</td>
                                <td class="field-label">:</td>
                                <td class="field-value" colspan="3">{{ $fieldValues['tgl_kirim'] ?? $fieldValues['TGL_KIRIM'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">NO.BOX</td>
                                <td class="field-label">:</td>
                                <td class="field-value" colspan="3">{{ $fieldValues['no_box'] ?? $fieldValues['NO_BOX'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label" colspan="4">QF.KOP-FN-8.5.2-003 REV: {{ $fieldValues['REV'] ?? '' }}</td>
                            </tr>
                        </table>
                    </div>
                @endfor
            </div>

            <!-- Row 2: Label 3 and Label 4 -->
            <div class="labels-row">
                @for($row = 2; $row < 4; $row++)
                    @php
                        $labelIndex = ($page * $labelsPerPage) + $row;
                    @endphp
                    <div class="label-box">
                        <!-- Header -->
                        <div class="header-company">
                            PT.KRISANTHIUM OFFSET PRINTING
                        </div>

                        <div class="header-address">
                            Jl.Rungkut Industri III/19 Surabaya Telp. 8438096,8438182-Fax.8432186
                        </div>

                        <!-- Content Fields -->
                        <table class="field-table">
                            <tr>
                                <td class="field-label">NAMA PRODUK :</td>
                                <td class="field-value">{{ $fieldValues['nama_produk'] ?? $fieldValues['NAMA_PRODUK'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">KODE DESIGN :</td>
                                <td class="field-value">{{ $fieldValues['kode_item'] ?? $fieldValues['KODE_DESIGN'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">NO.WO :</td>
                                <td class="field-value">{{ $fieldValues['no_wo'] ?? $fieldValues['NO_WO'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">NO.PO :</td>
                                <td class="field-value">{{ $fieldValues['no_po'] ?? $fieldValues['NO_PO'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">TGL.PRODUKSI :</td>
                                <td class="field-value">{{ $fieldValues['tgl_produksi'] ?? $fieldValues['TGL_PRODUKSI'] ?? '' }}</td>
                                <td class="field-label" style="padding-left: 15px;">SHIFT :</td>
                                <td class="field-value">{{ $fieldValues['shift'] ?? $fieldValues['SHIFT'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">TGL.EXPIRED :</td>
                                <td class="field-value" colspan="3">{{ $fieldValues['tgl_expired'] ?? $fieldValues['TGL_EXPIRED'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">OPERATOR :</td>
                                <td class="field-value">{{ $fieldValues['operator'] ?? $fieldValues['OPERATOR'] ?? '' }}</td>
                                @if(isset($fieldValues['TGL_SECONDARY']) && !empty($fieldValues['TGL_SECONDARY']))
                                <td class="field-value" style="padding-left: 15px;">{{ $fieldValues['TGL_SECONDARY'] }}</td>
                                @else
                                <td></td>
                                @endif
                            </tr>
                            <tr>
                                <td class="field-label">MESIN :</td>
                                <td class="field-value" colspan="3">{{ $fieldValues['mesin'] ?? $fieldValues['MESIN'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">TGL.RC :</td>
                                <td class="field-value" colspan="3">{{ $fieldValues['TGL_RC'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">ISI :</td>
                                <td class="field-value" colspan="3">{{ $fieldValues['isi'] ?? $fieldValues['ISI'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">TGL.KIRIM :</td>
                                <td class="field-value" colspan="3">{{ $fieldValues['tgl_kirim'] ?? $fieldValues['TGL_KIRIM'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label">NO.BOX :</td>
                                <td class="field-value" colspan="3">{{ $fieldValues['no_box'] ?? $fieldValues['NO_BOX'] ?? '' }}</td>
                            </tr>
                            <tr>
                                <td class="field-label" colspan="4">QF.KOP-FN-8.5.2-003 REV: {{ $fieldValues['REV'] ?? '' }}</td>
                            </tr>
                        </table>
                    </div>
                @endfor
            </div>
        </div>

        @if($page < $totalPages - 1)
            <div style="page-break-after: always;"></div>
        @endif
    @endfor
</body>
</html>
