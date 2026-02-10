@extends('main.layouts.main')

@section('title', 'Setting Approval Per Divisi')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sliders-h"></i>
                        Setting Approval Perizinan Tidak Masuk Kerja Per Divisi
                    </h3>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline"></i>
                        Konfigurasi alur approval untuk setiap divisi. Centang level approval yang aktif untuk divisi
                        tersebut.
                        Level yang tidak dicentang akan dilewati (skip) dalam alur approval.
                    </div>

                    <div class="accordion" id="divisionAccordion">
                        @forelse($divisions as $index => $divisi)
                            <div class="card">
                                <div class="card-header" id="heading{{ $divisi->id }}">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left" type="button"
                                            data-toggle="collapse" data-target="#collapse{{ $divisi->id }}"
                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                            aria-controls="collapse{{ $divisi->id }}">
                                            <i class="mdi mdi-account-box-outline"></i> Divisi -
                                            {{ $divisi->divisi }}

                                            @if ($divisi->approvalSetting)
                                                <span class="badge badge-info float-right">
                                                    {{ $divisi->approvalSetting->approval_levels ? count($divisi->approvalSetting->approval_levels) : 0 }}
                                                    Level
                                                </span>
                                            @else
                                                <span class="badge badge-secondary float-right">
                                                    Belum Dikonfigurasi
                                                </span>
                                            @endif
                                        </button>
                                    </h2>
                                </div>

                                <div id="collapse{{ $divisi->id }}" class="collapse {{ $index === 0 ? 'show' : '' }}"
                                    aria-labelledby="heading{{ $divisi->id }}" data-parent="#divisionAccordion">
                                    <div class="card-body">
                                        <form action="{{ route('hr.approval-settings.divisions.update', $divisi->id) }}"
                                            method="POST" class="division-approval-form"
                                            data-division-id="{{ $divisi->id }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="row">
                                                <!-- SPV Level -->
                                                <div class="col-md-4">
                                                    <div class="card card-outline card-info">
                                                        <div class="card-header">
                                                            <h3 class="card-title text-white">
                                                                <div class="icheck-info d-inline">
                                                                    <input type="checkbox"
                                                                        id="spv_enabled_{{ $divisi->id }}"
                                                                        name="spv_enabled" value="1"
                                                                        {{ $divisi->approvalSetting && $divisi->approvalSetting->spv_enabled ? 'checked' : '' }}>
                                                                    <label for="spv_enabled_{{ $divisi->id }}">
                                                                        <i class="mdi mdi-account-check"></i> SPV
                                                                    </label>
                                                                </div>
                                                            </h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <small class="text-muted">
                                                                <i class="mdi mdi-information"></i>
                                                                Otomatis mengambil user dengan jabatan SPV di divisi ini
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- HEAD Level -->
                                                <div class="col-md-4">
                                                    <div class="card card-outline card-warning">
                                                        <div class="card-header">
                                                            <h3 class="card-title text-white">
                                                                <div class="icheck-warning d-inline">
                                                                    <input type="checkbox"
                                                                        id="head_enabled_{{ $divisi->id }}"
                                                                        name="head_enabled" value="1"
                                                                        {{ $divisi->approvalSetting && $divisi->approvalSetting->head_enabled ? 'checked' : '' }}>
                                                                    <label for="head_enabled_{{ $divisi->id }}">
                                                                        <i class="mdi mdi-account-star"></i> HEAD
                                                                    </label>
                                                                </div>
                                                            </h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <small class="text-muted">
                                                                <i class="mdi mdi-information"></i>
                                                                Otomatis mengambil user dengan jabatan HEAD di divisi ini
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- MANAGER Level -->
                                                <div class="col-md-4">
                                                    <div class="card card-outline card-success">
                                                        <div class="card-header">
                                                            <h3 class="card-title text-white">
                                                                <div class="icheck-success d-inline">
                                                                    <input type="checkbox"
                                                                        id="manager_enabled_{{ $divisi->id }}"
                                                                        name="manager_enabled" value="1"
                                                                        {{ $divisi->approvalSetting && $divisi->approvalSetting->manager_enabled ? 'checked' : '' }}>
                                                                    <label for="manager_enabled_{{ $divisi->id }}">
                                                                        <i class="mdi mdi-account-key"></i> MANAGER
                                                                    </label>
                                                                </div>
                                                            </h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <small class="text-muted">
                                                                <i class="mdi mdi-information"></i>
                                                                Otomatis mengambil user dengan jabatan MANAGER di divisi ini
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- HRD Level (Fixed) -->
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="alert alert-secondary">
                                                        <i class="mdi mdi-account-box-outline"></i>
                                                        <strong>HRD (Fixed)</strong> - HRD selalu menjadi approval terakhir
                                                        dan tidak dapat diubah
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Preview Flow -->
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h4 class="card-title">
                                                                <i class="mdi mdi-project-diagram"></i>
                                                                Preview Alur Approval
                                                            </h4>
                                                        </div>
                                                        <div class="card-body">
                                                            <div id="flow-preview-{{ $divisi->id }}"
                                                                class="flow-preview">
                                                                <div class="text-center text-muted">
                                                                    <small>Pilih level approval di atas untuk melihat
                                                                        preview alur</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="row mt-3">
                                                <div class="col-12 text-right">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="mdi mdi-content-save"></i>
                                                        Simpan Setting
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Belum ada data divisi.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Handle checkbox changes - update preview
            $('input[name="spv_enabled"], input[name="head_enabled"], input[name="manager_enabled"]').on('change', function() {
                const divisiId = $(this).closest('form').data('division-id');
                updateFlowPreview(divisiId);
            });

            // Update flow preview function (simplified - client side only)
            function updateFlowPreview(divisiId) {
                const spvEnabled = $('#spv_enabled_' + divisiId).is(':checked');
                const headEnabled = $('#head_enabled_' + divisiId).is(':checked');
                const managerEnabled = $('#manager_enabled_' + divisiId).is(':checked');

                let flow = [];
                let flowText = [];

                // SPV
                if (spvEnabled) {
                    flow.push('<span class="badge badge-info"><i class="mdi mdi-account-check"></i> SPV Divisi (Otomatis)</span>');
                    flowText.push('SPV');
                }

                // HEAD
                if (headEnabled) {
                    flow.push('<span class="badge badge-warning"><i class="mdi mdi-account-star"></i> HEAD Divisi (Otomatis)</span>');
                    flowText.push('HEAD');
                }

                // MANAGER
                if (managerEnabled) {
                    flow.push('<span class="badge badge-success"><i class="mdi mdi-account-key"></i> MANAGER Divisi (Otomatis)</span>');
                    flowText.push('MANAGER');
                }

                // HRD selalu ada
                flow.push('<span class="badge badge-secondary"><i class="mdi mdi-account-box"></i> HRD (Fixed)</span>');
                flowText.push('HRD');

                const previewHtml = flow.length > 0 ?
                    '<div class="text-center">' + flow.join(' <i class="mdi mdi-arrow-right"></i> ') + '</div>' +
                    '<div class="text-center mt-2"><small class="text-muted">Alur: ' + flowText.join(' → ') + '</small></div>' :
                    '<div class="text-center text-muted"><small>Centang level approval di atas untuk melihat preview alur</small></div>';

                $('#flow-preview-' + divisiId).html(previewHtml);
            }

            // Initialize flow preview on page load
            $('.division-approval-form').each(function() {
                const divisiId = $(this).data('division-id');
                updateFlowPreview(divisiId);
            });
        });
    </script>
@endsection
