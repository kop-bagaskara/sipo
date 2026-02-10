<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Good Receipt - {{ $goodReceipt->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .document-title {
            font-size: 18px;
            color: #666;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-section {
            width: 48%;
        }
        .info-section h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px 0;
            border: none;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .items-table .text-center {
            text-align: center;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-accepted { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .status-partial { background-color: #ffeaa7; color: #6c5ce7; }
        .condition-good { background-color: #d4edda; color: #155724; }
        .condition-damaged { background-color: #fff3cd; color: #856404; }
        .condition-defective { background-color: #f8d7da; color: #721c24; }
        .notes-section {
            margin-top: 30px;
        }
        .notes-section h3 {
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">SIPO KRISAN</div>
        <div class="document-title">GOOD RECEIPT</div>
    </div>

    <div class="receipt-info">
        <div class="info-section">
            <h3>Receipt Information</h3>
            <table class="info-table">
                <tr>
                    <td>Receipt Number:</td>
                    <td>{{ $goodReceipt->receipt_number }}</td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td>
                        <span class="status-badge status-{{ $goodReceipt->status }}">
                            {{ strtoupper($goodReceipt->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>Received By:</td>
                    <td>{{ $goodReceipt->receiver->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Received Date:</td>
                    <td>{{ $goodReceipt->received_date->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Quality Check:</td>
                    <td>{{ $goodReceipt->quality_check ? 'PASSED' : 'FAILED' }}</td>
                </tr>
                <tr>
                    <td>Quantity Match:</td>
                    <td>{{ $goodReceipt->quantity_match ? 'MATCH' : 'NOT MATCH' }}</td>
                </tr>
            </table>
        </div>

        <div class="info-section">
            <h3>Supplier Information</h3>
            <table class="info-table">
                <tr>
                    <td>Ticket Number:</td>
                    <td>{{ $goodReceipt->supplierTicket->ticket_number }}</td>
                </tr>
                <tr>
                    <td>Supplier:</td>
                    <td>{{ $goodReceipt->supplierTicket->supplier_name }}</td>
                </tr>
                <tr>
                    <td>Contact:</td>
                    <td>{{ $goodReceipt->supplierTicket->supplier_contact ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>{{ $goodReceipt->supplierTicket->supplier_email ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Delivery Date:</td>
                    <td>{{ $goodReceipt->supplierTicket->delivery_date->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <h3>Received Items</h3>
    <table class="items-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Item Name</th>
                <th class="text-center">Quantity</th>
                <th class="text-center">Unit</th>
                <th class="text-center">Condition</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($goodReceipt->received_items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['item_name'] }}</td>
                    <td class="text-center">{{ $item['quantity'] }}</td>
                    <td class="text-center">{{ strtoupper($item['unit']) }}</td>
                    <td class="text-center">
                        <span class="status-badge condition-{{ $item['condition'] }}">
                            {{ strtoupper($item['condition']) }}
                        </span>
                    </td>
                    <td>{{ $item['notes'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($goodReceipt->notes || $goodReceipt->condition_notes)
    <div class="notes-section">
        @if($goodReceipt->notes)
        <h3>Receipt Notes</h3>
        <p>{{ $goodReceipt->notes }}</p>
        @endif

        @if($goodReceipt->condition_notes)
        <h3>Condition Notes</h3>
        <p>{{ $goodReceipt->condition_notes }}</p>
        @endif
    </div>
    @endif

    <div class="footer">
        <div class="signature">
            <div class="signature-line">
                <strong>Received By</strong><br>
                {{ $goodReceipt->receiver->name ?? 'N/A' }}<br>
                {{ $goodReceipt->received_date->format('d/m/Y') }}
            </div>
        </div>
        <div class="signature">
            <div class="signature-line">
                <strong>Authorized By</strong><br>
                <br>
                {{ now()->format('d/m/Y') }}
            </div>
        </div>
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>
</body>
</html>
