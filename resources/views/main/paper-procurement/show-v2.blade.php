@extends('main.layouts.main')

@section('title')
    Detail Meeting Kertas - {{ $meeting->meeting_number }}
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .excel-container {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            margin-top: 20px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #f0f0f0;
        }

        .excel-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 30px;
            color: #1a1a1a;
            text-transform: uppercase;
            padding-bottom: 15px;
            border-bottom: 2px solid #e8e8e8;
        }

        .tg {
            border-collapse: collapse;
            border-spacing: 0;
            width: max-content;
            min-width: 100%;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }

        .tg th {
            background: linear-gradient(135deg, #4472C4 0%, #5b8fd8 100%);
            color: white;
            padding: 12px 8px;
            border: 1px solid #2c5282;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        .tg td {
            padding: 10px 8px;
            border: 1px solid #d1d5db;
            vertical-align: middle;
            font-weight: bold !important;
            font-size: 13px;
        }

        .tg-total {
            background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%) !important;
            color: #333 !important;
        }

        /* Paper Colors - Matching create-v2 */
        .tg-paper-dpc250 { background-color: #D9E1F2 !important; }
        .tg-paper-ivory230-ikdp-vr { background-color: #d5e8d4 !important; }
        .tg-paper-ivory230-spn { background-color: #d5e8d4 !important; }
        .tg-paper-ivory230-ik-vr { background-color: #E8E8E8 !important; }
        .tg-paper-ivory-sinar-vanda-220 { background-color: #dbeafe !important; }

        .view-label { color: #666; font-size: 11px; text-transform: uppercase; font-weight: 700; margin-bottom: 2px; }
        .view-value { font-size: 15px; font-weight: 800; color: #333; margin-bottom: 15px; }
        
        .table-responsive {
            border-radius: 8px;
            overflow-x: auto;
            border: 1px solid #ddd;
        }

        @media print {
            .no-print { display: none !important; }
            .excel-container { box-shadow: none; border: none; padding: 0; }
            body { background: white !important; }
        }

        .paper-header-info { font-size: 10px; line-height: 1.2; font-weight: 700; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .text-left { text-align: left !important; }
    </style>
@endsection

@section('content')
<div class="row page-titles no-print">
    <div class="col-md-5 align-self-center">
        <h3 class="text-themecolor">Detail Meeting Kertas</h3>
    </div>
    <div class="col-md-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('paper-procurement.index') }}">Pengajuan</a></li>
            <li class="breadcrumb-item active">Detail</li>
        </ol>
    </div>
</div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <!-- Info Top -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="view-label">Nomor Meeting</div>
                    <div class="view-value text-primary">{{ $meeting->meeting_number }}</div>
                </div>
                <div class="col-md-3">
                    <div class="view-label">Customer</div>
                    <div class="view-value">{{ $meeting->customer_name }}</div>
                </div>
                <div class="col-md-3">
                    <div class="view-label">Bulan Meeting</div>
                    <div class="view-value">{{ $meeting->meeting_month }}</div>
                </div>
                <div class="col-md-3 text-end">
                    <div class="view-label">Status</div>
                    <span class="badge badge-pill badge-{{ $meeting->status == 'approved' ? 'success' : ($meeting->status == 'rejected' ? 'danger' : 'info') }} py-2 px-3">
                        {{ strtoupper($meeting->status) }}
                    </span>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="view-label">Periode</div>
                    <div class="view-value">
                        <span class="badge badge-info">{{ $meeting->period_month_1 }}</span>
                        <span class="badge badge-info">{{ $meeting->period_month_2 }}</span>
                        <span class="badge badge-info">{{ $meeting->period_month_3 }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="view-label">Toleransi</div>
                    <div class="view-value">{{ $meeting->tolerance_percentage }}%</div>
                </div>
                <div class="col-md-3">
                    <div class="view-label">Dibuat Oleh</div>
                    <div class="view-value">{{ $meeting->creator->name ?? 'Admin' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="view-label">Waktu Simpan</div>
                    <div class="view-value">{{ $meeting->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            <!-- Workspace Table -->
            <div class="excel-container">
                <div class="excel-title">KEBUTUHAN MEETING KERTAS BULANAN PPIC</div>
                <div class="table-responsive">
                    <table class="tg" id="workspace-table">
                        <thead id="workspace-thead"></thead>
                        <tbody id="workspace-tbody"></tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-4 no-print text-end">
                <div class="col-12">
                    <a href="{{ route('paper-procurement.index') }}" class="btn btn-secondary px-4">
                        <i class="mdi mdi-arrow-left"></i> Kembali
                    </a>
                    <button type="button" onclick="window.print()" class="btn btn-info px-4">
                        <i class="mdi mdi-printer"></i> Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const meeting = @json($meeting);
    const items = @json($meeting->items);
    const locations = @json($meeting->locations);
    const stocks = @json($meeting->stocks);
    const poRemains = @json($meeting->poRemains);
    const poManuals = @json($meeting->poManuals);

    const paperTypeColors = {
        'DPC 250': '#D9E1F2',
        'IVORY 230 IKDP VR': '#d5e8d4',
        'IVORY 230 SPN': '#d5e8d4',
        'IVORY 230 IK VR': '#E8E8E8',
        'IVORY SINAR VANDA 220': '#dbeafe',
    };

    function getPaperClass(paperType) {
        if (!paperType) return 'tg-0pky';
        const normalized = paperType.toLowerCase().replace(/\s+/g, ' ').trim();
        if (normalized.includes('dpc 250')) return 'tg-paper-dpc250';
        if (normalized.includes('ikdp vr')) return 'tg-paper-ivory230-ikdp-vr';
        if (normalized.includes('spn')) return 'tg-paper-ivory230-spn';
        if (normalized.includes('ik vr')) return 'tg-paper-ivory230-ik-vr';
        if (normalized.includes('sinar vanda')) return 'tg-paper-ivory-sinar-vanda-220';
        return 'tg-0pky';
    }

    $(document).ready(function() {
        renderWorkspaceView();
    });

    function renderWorkspaceView() {
        const thead = $('#workspace-thead');
        const tbody = $('#workspace-tbody');
        
        let paperTypes = [];
        let paperInfos = {};

        items.forEach(item => {
            item.papers.forEach(p => {
                if (!paperTypes.includes(p.paper_type)) {
                    paperTypes.push(p.paper_type);
                    paperInfos[p.paper_type] = {
                        code: p.paper_code,
                        name: p.paper_name,
                        up: p.up_value
                    };
                }
            });
        });

        // Header
        let headerHtml = `
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2" style="min-width: 250px;">PRODUK</th>
                <th rowspan="2">UP</th>
                <th colspan="3">JUMLAH KEBUTUHAN</th>
                <th rowspan="2">TOTAL</th>
                <th rowspan="2">TOTAL + TOLERANSI</th>
        `;
        paperTypes.forEach(type => {
            headerHtml += `<th class="${getPaperClass(type)}">${type}</th>`;
        });
        headerHtml += `</tr><tr>`;
        headerHtml += `<th>${meeting.period_month_1}</th><th>${meeting.period_month_2}</th><th>${meeting.period_month_3}</th>`;
        paperTypes.forEach(type => {
            headerHtml += `<th class="${getPaperClass(type)}" style="font-size: 10px;">${paperInfos[type].code}</th>`;
        });
        headerHtml += `</tr>`;
        thead.html(headerHtml);

        // Nilai UP Row
        let upRowHtml = `<tr style="background-color: #f8f9fa;">
            <td colspan="8" class="text-right">NILAI UP</td>
        `;
        paperTypes.forEach(type => {
            upRowHtml += `<td class="text-center ${getPaperClass(type)}" style="color: red; font-weight: bold;">${paperInfos[type].up}</td>`;
        });
        upRowHtml += `</tr>`;
        tbody.append(upRowHtml);

        // Items Rows
        items.forEach((item, idx) => {
            const firstPaper = item.papers[0];
            const rowColor = firstPaper ? (paperTypeColors[firstPaper.paper_type] || '#ffffff') : '#ffffff';

            let rowHtml = `<tr>
                <td class="text-center" style="background-color: ${rowColor} !important;">${idx + 1}</td>
                <td class="text-left" style="background-color: ${rowColor} !important;">
                    ${item.product_code ? `<strong>${item.product_code}</strong> - ` : ''}${item.product_name || '-'}
                </td>
                <td class="text-center" style="background-color: ${rowColor} !important;">${item.papers[0]?.up_value || '-'}</td>
                <td class="text-right" style="background-color: ${rowColor} !important;">${item.quantity_month_1.toLocaleString()}</td>
                <td class="text-right" style="background-color: ${rowColor} !important;">${item.quantity_month_2.toLocaleString()}</td>
                <td class="text-right" style="background-color: ${rowColor} !important;">${item.quantity_month_3.toLocaleString()}</td>
                <td class="text-right" style="background-color: ${rowColor} !important;">${item.total_quantity.toLocaleString()}</td>
                <td class="text-right" style="background-color: ${rowColor} !important;">${item.total_with_tolerance.toLocaleString()}</td>
            `;
            paperTypes.forEach(type => {
                const p = item.papers.find(paper => paper.paper_type === type);
                const cellColor = p ? rowColor : (paperTypeColors[type] || '#ffffff');
                rowHtml += `<td class="text-right ${getPaperClass(type)}" style="background-color: ${cellColor} !important;">
                    ${p ? p.required_quantity.toLocaleString() : '-'}
                </td>`;
            });
            rowHtml += `</tr>`;
            tbody.append(rowHtml);
        });

        // Summary Rows (Sama seperti logika create-v2)
        // ... (sisanya tetap sama namun ditambahkan style background yang fix)
        
        // Total Permintaan
        let totalPermintaanHtml = `<tr class="tg-total">
            <td colspan="8" class="text-right">TOTAL PERMINTAAN</td>
        `;
        paperTypes.forEach(type => {
            const total = items.reduce((sum, item) => {
                const p = item.papers.find(ip => ip.paper_type === type);
                return sum + (p ? p.required_quantity : 0);
            }, 0);
            totalPermintaanHtml += `<td class="text-right tg-total" style="background-color: #FFE699 !important;">${total.toLocaleString()}</td>`;
        });
        tbody.append(totalPermintaanHtml + `</tr>`);

        // Stok
        locations.forEach(loc => {
            let locHtml = `<tr><td colspan="8" class="text-left" style="background-color: #ffcccc !important;">${loc.location_code}</td>`;
            paperTypes.forEach(type => {
                const stock = stocks.find(s => s.location_id === loc.id && s.paper_type === type);
                locHtml += `<td class="text-right" style="background-color: #ffcccc !important;">${stock ? parseFloat(stock.stock_layer_2).toLocaleString() : '0'}</td>`;
            });
            tbody.append(locHtml + `</tr>`);
        });

        // PO
        poRemains.forEach(po => {
            let poHtml = `<tr><td colspan="8" class="text-left" style="background-color: #d1e7dd !important;">${po.po_doc_no}</td>`;
            paperTypes.forEach(type => {
                const val = (po.paper_type === type) ? parseFloat(po.po_remain_layer_2).toLocaleString() : '-';
                poHtml += `<td class="text-right" style="background-color: #d1e7dd !important;">${val}</td>`;
            });
            tbody.append(poHtml + `</tr>`);
        });

        // Manual
        let manualHtml = `<tr><td colspan="8" class="text-left" style="background-color: #fff3cd !important;">BELUM ADA PO (Layer 2)</td>`;
        paperTypes.forEach(type => {
            const m = poManuals.find(pm => pm.paper_type === type);
            manualHtml += `<td class="text-right" style="background-color: #fff3cd !important;">${m ? parseFloat(m.po_manual_layer_2).toLocaleString() : '0'}</td>`;
        });
        tbody.append(manualHtml + `</tr>`);

        // Minus Rows
        [{l:'MINUS PAPER (PCS)', f:'minus_paper_pcs', c:'#fff2cc'},
         {l:'MINUS PAPER (RIM)', f:'minus_paper_rim', c:'#ffe6b3'},
         {l:'MINUS PAPER (TON)', f:'minus_paper_ton', c:'#ffd9b3'}].forEach(r => {
            let rowHtml = `<tr><td colspan="8" class="text-right" style="background-color: ${r.c} !important;">${r.l}</td>`;
            paperTypes.forEach(type => {
                const p = items.flatMap(i => i.papers).find(pp => pp.paper_type === type);
                const val = p ? parseFloat(p[r.f]) : 0;
                rowHtml += `<td class="text-right" style="background-color: ${r.c} !important;">${val.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>`;
            });
            tbody.append(rowHtml + `</tr>`);
        });

        // Final Grand Total
        let summaryHtml = `<tr class="tg-total" style="font-size: 16px; border-top: 2px solid #333;">
            <td colspan="8" class="text-right" style="background-color: #FFE699 !important;">TOTAL KEBUTUHAN KERTAS (TON)</td>
        `;
        paperTypes.forEach(type => {
            const p = items.flatMap(i => i.papers).find(pp => pp.paper_type === type);
            const val = p ? parseFloat(p.total_kebutuhan_ton) : 0;
            summaryHtml += `<td class="text-right text-danger tg-total" style="font-size: 18px; background-color: #FFE699 !important;">${val.toFixed(4)}</td>`;
        });
        tbody.append(summaryHtml + `</tr>`);

        // Notes
        let notesHtml = `<tr><td colspan="8" class="text-right">CATATAN / KEPUTUSAN</td>`;
        paperTypes.forEach(type => {
            const p = items.flatMap(i => i.papers).find(pp => pp.paper_type === type);
            notesHtml += `<td class="text-left" style="font-style: italic; font-size: 12px; color: #d32f2f; min-width: 150px;">${p?.catatan || '-'}</td>`;
        });
        tbody.append(notesHtml + `</tr>`);
    }
</script>
@endsection
