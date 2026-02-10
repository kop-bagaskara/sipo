<div class="row">
    <div class="col">
        <!-- Basic Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-information mr-2"></i>
                    Informasi Training
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Kode Training</label>
                            <p class="form-control-plaintext">
                                <span class="badge badge-info">{{ $training->training_code }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Training</label>
                            <p class="form-control-plaintext">{{ $training->training_name }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Tipe Training</label>
                            <p class="form-control-plaintext">
                                @switch($training->training_type)
                                    @case('mandatory')
                                        <span class="badge badge-danger">Mandatory</span>
                                    @break
                                    @case('optional')
                                        <span class="badge badge-success">Optional</span>
                                    @break
                                    @case('certification')
                                        <span class="badge badge-warning">Certification</span>
                                    @break
                                    @case('skill_development')
                                        <span class="badge badge-primary">Skill Development</span>
                                    @break
                                @endswitch
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Metode Training</label>
                            <p class="form-control-plaintext">
                                @switch($training->training_method)
                                    @case('classroom')
                                        <span class="badge badge-info">Kelas Tatap Muka</span>
                                    @break
                                    @case('online')
                                        <span class="badge badge-primary">Online</span>
                                    @break
                                    @case('hybrid')
                                        <span class="badge badge-warning">Hybrid</span>
                                    @break
                                    @case('workshop')
                                        <span class="badge badge-success">Workshop</span>
                                    @break
                                    @case('seminar')
                                        <span class="badge badge-secondary">Seminar</span>
                                    @break
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Status</label>
                            <p class="form-control-plaintext">
                                @switch($training->status)
                                    @case('active')
                                        <span class="badge badge-success">Active</span>
                                    @break
                                    @case('inactive')
                                        <span class="badge badge-secondary">Inactive</span>
                                    @break
                                @endswitch
                                @if (!$training->is_active)
                                    <br><span class="badge badge-dark">Inactive</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Dibuat Oleh</label>
                            <p class="form-control-plaintext">{{ $training->creator->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                @if($training->notes)
                <div class="form-group">
                    <label class="font-weight-bold">Catatan</label>
                    <p class="form-control-plaintext">{{ $training->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Target Departments -->
        @if($training->departments->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="mdi mdi-domain mr-2"></i>
                    Departemen Target
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($training->departments as $dept)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>{{ $dept->divisi }}</span>
                                <span class="badge badge-primary">Target</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

</div>
