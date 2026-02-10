<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            page-break-inside: avoid;
        }
        td {
            border: 1px solid #000;
            padding: 2px 4px;
            font-size: 10px;
        }
        .large-text {
            font-size: 24px;
            font-weight: bold;
        }
        .medium-text {
            font-size: 14px;
        }
        .small-text {
            font-size: 10px;
        }
    </style>
</head>
<body>
    @php
        $colMap = [];
        $colIndex = 0;
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $colMap[$colIndex] = $col;
            $colIndex++;
        }
    @endphp

    <table>
        @for($row = 1; $row <= $highestRow; $row++)
            <tr>
                @for($colIdx = 0; $colIdx < count($colMap); $colIdx++)
                    @php
                        $col = $colMap[$colIdx];
                        $cell = $sheet->getCell($col . $row);
                        $value = $cell->getValue();
                        $style = $cell->getStyle();
                        $fontSize = $style->getFont()->getSize();
                        $isBold = $style->getFont()->getBold();
                    @endphp
                    <td style="
                        @if($fontSize && $fontSize > 20) font-size: {{ $fontSize }}px; font-weight: bold; @endif
                        @if($isBold) font-weight: bold; @endif
                    ">
                        {{ $value ?? '' }}
                    </td>
                @endfor
            </tr>
        @endfor
    </table>
</body>
</html>

