<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Label {{ $template->template_name ?? 'Label' }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
        }


        .header-company {
            text-align: center;
            font-weight: bold;
            font-size: 33px;
            margin-bottom: 3px;
            line-height: 1.2;
        }

        .header-address {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            line-height: 1.4;
            border-bottom: 2px solid #000;
        }

        .customer-info,
        .item-info {
            margin-bottom: 3px;
            font-size: 10px;
        }

        .customer-info strong,
        .item-info strong {
            font-weight: bold;
        }

        .separator {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .fields-wrapper {
            position: relative;
            margin-top: 5px;
        }

        .field-row {
            display: flex;
            margin-bottom: 3px;
            font-size: 10px;
            position: relative;
            align-items: baseline;
        }

        .field-label {
            width: 110px;
            font-weight: bold;
        }

        .field-value {
            flex-grow: 1;
        }

        .field-value-large {
            font-size: 20px;
            font-weight: bold;
        }

        .isi-right {
            position: absolute;
            right: 80px;
            font-size: 10px;
        }

        .fr-large {
            position: absolute;
            right: 10px;
            top: 0;
            font-size: 48px;
            font-weight: bold;
            line-height: 1;
            z-index: 1;
        }

        .footer-text {
            font-size: 7px;
            text-align: right;
            margin-top: 10px;
            padding-top: 5px;
        }

        .label-container {
            width: 100%;
            border: 1px solid #000;
            margin: 20px;
            padding: 5px;
            box-sizing: border-box;
            position: relative;
            margin-bottom: 10px;
        }

        /* Page break setiap 2 label (setiap halaman) */
        .label-container:nth-child(2n) {
            page-break-after: always;
        }

        .label-container:last-child {
            page-break-after: auto;
        }

        .main-table {
            width: 95%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .main-table td {
            border: none;
            padding: 1px 1px;
            /* margin: 0; */
            vertical-align: top;
        }

        .main-table tr {
            border-bottom: 1px solid #000;
            margin: 0;
            padding: 0;
        }

        .main-table tr:last-child {
            border-bottom: none;
        }

        .header-row {
            border-bottom: 1px solid #000;
        }

        .header-row td {
            text-align: center;
            padding: 3px;
        }
    </style>
</head>

<body>
    @php
        $numLabels = $quantity ?? request()->input('quantity', 1);
        $customerName = $customerName ?? $customer->customer_name ?? '';
        $item = $item ?? $fieldValues['ITEM'] ?? $template->product_name ?? '';
    @endphp

    @for ($i = 0; $i < $numLabels; $i++)
        <table class="label-container main-table">
            <!-- Header Company -->
            <tr class="header-row" style="border-bottom:none;">
                <td colspan="4" style="font-weight: bold; font-size: 28px; padding: 3px;">PT. KRISANTHIUM OFFSET PRINTING</td>
            </tr>
            <tr class="header-row">
                <td colspan="4" style="font-size: 14px; font-weight: bold; padding: 2px;">Jl. Rungkut Industri III /19 Surabaya Telp.8438096,8438182-Fax.8432186</td>
            </tr>

            <!-- Data Rows -->
            <tr>
                <td style="font-size: 25px; width: 250px;">CUSTOMER</td>
                <td style="font-size: 25px;">:</td>
                <td colspan="2" style="font-weight: bold;font-size: 30px;">{{ strtoupper($customerName) }}</td>
            </tr>
            <tr>
                <td style="font-size: 25px;">ITEM</td>
                <td style="font-size: 25px;">:</td>
                <td style="font-weight: bold;font-size: 30px;">{{ $item }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="font-size: 25px; vertical-align: middle;">PC NO</td>
                <td style="font-size: 25px; vertical-align: middle;">:</td>
                <td style="font-size: 65px;font-weight: bold;">{{ $fieldValues['PC_NO'] ?? '' }}</td>
                <td style="font-size: 20px; vertical-align: middle;">ISI : {{ $fieldValues['ISI'] ?? '' }} PCS</td>
            </tr>
            <tr>
                <td style="font-size: 25px; vertical-align: middle;">MC NO</td>
                <td style="font-size: 25px; vertical-align: middle;">:</td>
                <td style="font-size: 65px;font-weight: bold;">{{ $fieldValues['MC_NO'] ?? '' }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="font-size: 25px; vertical-align: middle;">NO. WOT</td>
                <td style="font-size: 25px; vertical-align: middle;">:</td>
                <td style="font-size: 25px;">{{ $fieldValues['WOT'] ?? '' }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="font-size: 25px;">TGL. PRODUKSI</td>
                <td style="font-size: 25px;">:</td>
                <td style="font-size: 25px;">{{ $fieldValues['TGL_PRODUKSI'] ?? '' }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="font-size: 25px;">MESIN/SHIFT</td>
                <td style="font-size: 25px;">:</td>
                <td style="font-size: 25px;">{{ $fieldValues['MESIN_SHIFT'] ?? '' }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="font-size: 25px;">OPERATOR</td>
                <td style="font-size: 25px;">:</td>
                <td style="font-size: 25px;">{{ $fieldValues['OPERATOR'] ?? '' }}</td>
                <td rowspan="4" style="font-size: 75px;font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000; width: 150px; padding: 5px;">FR</td>
            </tr>
            <tr>
                <td style="font-size: 25px;">BATCH NO.</td>
                <td style="font-size: 25px;">:</td>
                <td style="font-size: 25px;">{{ $fieldValues['BATCH_NO'] ?? '' }}</td>
            </tr>
            <tr>
                <td style="font-size: 25px;">NO. BOX</td>
                <td style="font-size: 25px;">:</td>
                <td style="font-size: 25px;">{{ $fieldValues['NO_BOX'] ?? '' }}</td>
            </tr>
            <tr>
                <td style="font-size: 25px;">TANGGAL KIRIM</td>
                <td style="font-size: 25px;">:</td>
                <td style="font-size: 25px;">{{ $fieldValues['TANGGAL_KIRIM'] ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="4" style="font-size: 15px; text-align: left; padding: 3px;">QF.KOP-FN-8.5.2-002 REV:01</td>
            </tr>
        </table>
        {{-- <br> --}}
        <table class="label-container main-table">
            <!-- Header Company -->
            <tr class="header-row" style="border-bottom:none;">
                <td colspan="4" style="font-weight: bold; font-size: 28px; padding: 3px;">PT. KRISANTHIUM OFFSET PRINTING</td>
            </tr>
            <tr class="header-row">
                <td colspan="4" style="font-size: 14px; font-weight: bold; padding: 2px;">Jl. Rungkut Industri III /19 Surabaya Telp.8438096,8438182-Fax.8432186</td>
            </tr>

            <!-- Data Rows -->
            <tr>
                <td style="font-size: 20px; width: 200px;">CUSTOMER</td>
                <td style="font-size: 20px;">:</td>
                <td colspan="2" style="font-weight: bold;font-size: 30px;">{{ strtoupper($customerName) }}</td>
            </tr>
            <tr>
                <td style="font-size: 20px;">ITEM</td>
                <td style="font-size: 20px;">:</td>
                <td style="font-weight: bold;font-size: 30px;">{{ $item }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="font-size: 20px;">PC NO</td>
                <td style="font-size: 20px;">:</td>
                <td style="font-size: 65px;font-weight: bold;">{{ $fieldValues['PC_NO'] ?? '' }}</td>
                <td style="font-size: 20px;justify-content: center;align-items: center;">ISI : {{ $fieldValues['ISI'] ?? '' }} PCS</td>
            </tr>
            <tr>
                <td style="font-size: 20px;">MC NO</td>
                <td style="font-size: 20px;">:</td>
                <td style="font-size: 65px;font-weight: bold;">{{ $fieldValues['MC_NO'] ?? '' }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="font-size: 20px;">NO. WOT</td>
                <td style="font-size: 20px;">:</td>
                <td style="font-size: 20px;">{{ $fieldValues['WOT'] ?? '' }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="font-size: 20px;">TGL. PRODUKSI</td>
                <td style="font-size: 20px;">:</td>
                <td style="font-size: 20px;">{{ $fieldValues['TGL_PRODUKSI'] ?? '' }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="font-size: 20px;">MESIN/SHIFT</td>
                <td style="font-size: 20px;">:</td>
                <td style="font-size: 20px;">{{ $fieldValues['MESIN_SHIFT'] ?? '' }}</td>
                <td></td>
            </tr>
            <tr>
                <td style="font-size: 20px;">OPERATOR</td>
                <td style="font-size: 20px;">:</td>
                <td style="font-size: 20px;">{{ $fieldValues['OPERATOR'] ?? '' }}</td>
                <td rowspan="4" style="font-size: 75px;font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #000; width: 150px; padding: 5px;">FR</td>
            </tr>
            <tr>
                <td style="font-size: 20px;">BATCH NO.</td>
                <td style="font-size: 20px;">:</td>
                <td style="font-size: 20px;">{{ $fieldValues['BATCH_NO'] ?? '' }}</td>
            </tr>
            <tr>
                <td style="font-size: 20px;">NO. BOX</td>
                <td style="font-size: 20px;">:</td>
                <td style="font-size: 20px;">{{ $fieldValues['NO_BOX'] ?? '' }}</td>
            </tr>
            <tr>
                <td style="font-size: 20px;">TANGGAL KIRIM</td>
                <td style="font-size: 20px;">:</td>
                <td style="font-size: 20px;">{{ $fieldValues['TANGGAL_KIRIM'] ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="4" style="font-size: 15px; text-align: left; padding: 3px;">QF.KOP-FN-8.5.2-002 REV:01</td>
            </tr>
        </table>

    @endfor
</body>

</html>
