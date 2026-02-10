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
            padding: 20px;
        }
        {!! $template->css_styles ?? '' !!}
        
        /* Hide all editor handles and UI elements in preview/PDF */
        .resize-handle,
        .element-handle,
        .field-placeholder {
            display: none !important;
            visibility: hidden !important;
        }
        
        /* Remove any editor-specific styles */
        table {
            position: static !important;
            border-collapse: collapse !important;
            border-spacing: 0 !important;
            width: 100%;
            border: none !important;
        }
        table:hover {
            outline: none !important;
        }
        table.dragging {
            opacity: 1 !important;
            cursor: default !important;
        }
        
        /* Ensure table borders are collapsed (1 line, not separate boxes) */
        /* Strategy: Remove all borders first, then add only the necessary ones */
        table td,
        table th {
            border: none !important;
            padding: 5px !important;
            margin: 0 !important;
        }
        
        /* Add border only to right and bottom of each cell (except edges) */
        table td:not(:last-child),
        table th:not(:last-child) {
            border-right: 1px solid #000 !important;
        }
        
        table tr:not(:last-child) td,
        table tr:not(:last-child) th {
            border-bottom: 1px solid #000 !important;
        }
        
        /* Add outer borders */
        table {
            border: 1px solid #000 !important;
        }
        
        /* Override any inline styles that might prevent border collapse */
        table[style*="border"],
        table td[style*="border"],
        table th[style*="border"] {
            border-collapse: collapse !important;
        }
        
        /* Force remove inline border styles from cells */
        table td[style],
        table th[style] {
            border: none !important;
        }
    </style>
</head>
<body>
    @php
        $htmlContent = $template->html_template ?? '';
        
        // Check if html_template is JSON (from visual builder) or plain HTML
        $templateData = json_decode($htmlContent, true);
        
        if ($templateData && isset($templateData['html'])) {
            // Use HTML from visual builder
            $htmlContent = $templateData['html'];
        }
        
        // Replace placeholders with actual values
        $htmlContent = str_replace('{{CUSTOMER}}', $customerName ?? $customer->customer_name ?? '', $htmlContent);
        $htmlContent = str_replace('{{QUANTITY}}', $quantity ?? 1, $htmlContent);
        
        // Replace all field placeholders
        foreach ($fieldValues as $fieldName => $fieldValue) {
            $htmlContent = str_replace('{{' . $fieldName . '}}', $fieldValue ?? '', $htmlContent);
        }
    @endphp
    
    {!! $htmlContent !!}
    {{-- @endphp --}}
</body>
</html>

