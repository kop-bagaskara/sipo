@extends('main.layouts.main')
@section('title')
    Template {{ ucfirst($template->template_type) }} - {{ $customer->customer_name }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .template-header-card {
            background-color: #4472C4;
            color: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
        }
        .template-header-card h3 {
            color: white;
            margin: 0;
        }
        .label-history-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
            transition: all 0.3s ease;
        }
        .label-history-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .field-value-item {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 5px;
            padding: 5px 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 12px;
        }
        .field-value-item strong {
            color: #495057;
        }
    </style>
@endsection
@section('page-title')
    Template {{ ucfirst($template->template_type) }} - {{ $customer->customer_name }}
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Template {{ ucfirst($template->template_type) }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('label-management.index') }}">Label Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('label-management.customer.show', $customer->id) }}">{{ $customer->customer_name }}</a></li>
                <li class="breadcrumb-item active">Template {{ ucfirst($template->template_type) }}</li>
            </ol>
        </div>
    </div>

    <!-- Template Header -->
    <div class="row">
        <div class="col-12">
            <div class="template-header-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3>
                            <span class="badge badge-light" style="font-size: 14px; padding: 5px 15px; margin-right: 10px;">
                                {{ strtoupper($template->template_type) }}
                            </span>
                            Template Label {{ ucfirst($template->template_type) }}
                        </h3>
                        <p class="mb-0 mt-2" style="opacity: 0.9;">
                            <i class="mdi mdi-account"></i> {{ $customer->customer_name }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('label-management.template.generate-form', $template->id) }}" class="btn btn-light btn-lg">
                            <i class="mdi mdi-plus-circle"></i> Generate Label Baru
                        </a>
                        <a href="{{ route('label-management.customer.show', $customer->id) }}" class="btn btn-outline-light btn-lg ml-2">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- List Label yang Pernah Dibuat -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        List Label yang Pernah Dibuat
                        @if($template->generations && $template->generations->count() > 0)
                            <span class="badge badge-primary">({{ $template->generations->count() }})</span>
                        @endif
                    </h4>

                    @if($template->generations && $template->generations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Field Values</th>
                                        <th>File PDF</th>
                                        <th>Quantity</th>
                                        <th>Tanggal Generate</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($template->generations as $index => $generation)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($generation->field_values)
                                                    @foreach($generation->field_values as $key => $value)
                                                        @if(!empty($value))
                                                            <div class="field-value-item">
                                                                <strong>{{ $key }}:</strong> {{ $value }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <code>{{ $generation->pdf_file_name }}</code>
                                            </td>
                                            <td>{{ $generation->quantity }}</td>
                                            <td>{{ $generation->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('label-management.template.preview', $template->id) }}?generationId={{ $generation->id }}"
                                                   class="btn btn-sm btn-warning"
                                                   target="_blank"
                                                   title="Preview Template dengan data ini">
                                                    <i class="mdi mdi-eye"></i> Preview Template
                                                </a>
                                                <a href="{{ asset('storage/' . $generation->pdf_file_path) }}"
                                                   class="btn btn-sm btn-primary"
                                                   target="_blank"
                                                   download>
                                                    <i class="mdi mdi-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center" style="padding: 60px;">
                            <i class="mdi mdi-file-document-outline" style="font-size: 64px; color: #ccc; margin-bottom: 20px;"></i>
                            <h5 class="text-muted">Belum ada label yang pernah dibuat</h5>
                            <p class="text-muted">Klik tombol "Generate Label Baru" di atas untuk membuat label pertama</p>
                            <a href="{{ route('label-management.template.generate-form', $template->id) }}" class="btn btn-primary btn-lg mt-3">
                                <i class="mdi mdi-plus-circle"></i> Generate Label Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
@endsection

