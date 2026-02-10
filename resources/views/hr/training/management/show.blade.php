@extends('main.layouts.main')
@section('title')
    Manajemen Training
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .cust-col {
            white-space: nowrap;
        }
    </style>
@endsection
@section('page-title')
    Manajemen Training
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Manajemen Training</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Manajemen Training</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-information mr-2"></i>
                            Informasi Training
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Kode Training</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge badge-info">{{ $training->training_code }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Nama Training</label>
                                    <p class="form-control-plaintext">{{ $training->training_name }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
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
                            <div class="col-md-3">
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

                                            @case('ongoing')
                                                <span class="badge badge-warning">Ongoing</span>
                                            @break

                                            @case('completed')
                                                <span class="badge badge-info">Completed</span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge badge-danger">Cancelled</span>
                                            @break
                                        @endswitch
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Participants -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-account-plus mr-2"></i>
                            Tambah Peserta Training
                        </h4>
                    </div>
                    <div class="card-body">
                        <form id="addParticipantsForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="department_filter">Pilih Departemen</label>
                                        <select name="department" id="department_filter" class="form-control" onchange="loadEmployeesByDepartment()">
                                            <option value="">Pilih Departemen</option>
                                            @foreach ($departments as $dept)
                                                <option value="{{ $dept }}">{{ $dept }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="button" class="btn btn-warning" onclick="resetForm()">
                                                <i class="mdi mdi-refresh mr-2"></i>Reset Form
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Employee List -->
                        <div id="employeeList" class="mt-4" style="display: none;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="mdi mdi-account-group mr-2"></i>
                                                Daftar Karyawan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <div class="form-check">
                                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="form-check-input">
                                                        <label class="form-check-label font-weight-bold" for="selectAll">
                                                            Pilih Semua
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="employeeCards" class="row">
                                                <!-- Employee cards akan diisi via AJAX -->
                                            </div>

                                            <div id="employeePagination" class="d-flex justify-content-center mt-3">
                                                <!-- Pagination akan diisi via AJAX -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Participants Table -->
                        <div id="selectedParticipantsList" class="mt-4" style="display: none;">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">
                                                <i class="mdi mdi-account-check mr-2"></i>
                                                Peserta yang Akan Didaftarkan
                                            </h5>
                                            <div>
                                                <button type="button" class="btn btn-success" onclick="saveAllParticipants()" id="saveAllBtn">
                                                    <i class="mdi mdi-content-save mr-2"></i>Simpan Semua Peserta
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">No</th>
                                                            <th width="15%">NIP</th>
                                                            <th width="25%">Nama</th>
                                                            <th width="20%">Email</th>
                                                            <th width="15%">Divisi</th>
                                                            <th width="15%">Bagian</th>
                                                            <th width="5%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="selectedParticipantsTableBody">
                                                        <!-- Data akan diisi via JavaScript -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Participants -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-account-group mr-2"></i>
                            Peserta Terdaftar
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">NIP</th>
                                        <th width="20%">Nama</th>
                                        <th width="15%">Email</th>
                                        <th width="12%">Divisi</th>
                                        <th width="12%">Bagian</th>
                                        <th width="8%">Status</th>
                                        <th width="8%">Tipe</th>
                                        <th width="8%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($participants as $index => $participant)
                                        <tr>
                                            <td>{{ $participants->firstItem() + $index }}</td>
                                            <td>{{ $participant->employee->Nip ?? 'N/A' }}</td>
                                            <td>{{ $participant->employee->Nama ?? 'N/A' }}</td>
                                            <td>{{ $participant->employee->Email ?? 'N/A' }}</td>
                                            <td>{{ $participant->employee->{'Kode Divisi'} ?? 'N/A' }}</td>
                                            <td>{{ $participant->employee->{'Kode Bagian'} ?? 'N/A' }}</td>
                                            <td>
                                                @switch($participant->registration_status)
                                                    @case('registered')
                                                        <span class="badge badge-warning">Terdaftar</span>
                                                    @break

                                                    @case('approved')
                                                        <span class="badge badge-success">Disetujui</span>
                                                    @break

                                                    @case('rejected')
                                                        <span class="badge badge-danger">Ditolak</span>
                                                    @break

                                                    @case('attended')
                                                        <span class="badge badge-info">Hadir</span>
                                                    @break

                                                    @case('completed')
                                                        <span class="badge badge-primary">Selesai</span>
                                                    @break

                                                    @case('cancelled')
                                                        <span class="badge badge-secondary">Dibatalkan</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @switch($participant->registration_type)
                                                    @case('mandatory')
                                                        <span class="badge badge-danger">Mandatory</span>
                                                    @break

                                                    @case('voluntary')
                                                        <span class="badge badge-success">Voluntary</span>
                                                    @break

                                                    @case('recommended')
                                                        <span class="badge badge-warning">Recommended</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if (in_array($participant->registration_status, ['registered', 'approved']))
                                                    <button class="btn btn-sm btn-danger"
                                                        onclick="removeParticipant({{ $participant->id }})"
                                                        title="Hapus Peserta">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    <i class="mdi mdi-information-outline me-2"></i>
                                                    Belum ada peserta yang terdaftar
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($participants->hasPages())
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $participants->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>
        @endsection

        @section('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
            <script>
                // Check if SweetAlert is loaded
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof Swal === 'undefined') {
                        console.error('SweetAlert2 is not loaded!');
                        alert('SweetAlert2 library tidak dimuat. Silakan refresh halaman.');
                    } else {
                        console.log('SweetAlert2 loaded successfully');
                    }
                });

                let selectedEmployees = [];
                let currentPage = 1;
                let allEmployeeData = {}; // Menyimpan data karyawan dari semua departemen

                 function loadEmployeesByDepartment(page = 1) {
                     currentPage = page;
                     const department = document.getElementById('department_filter').value;

                     if (!department) {
                         document.getElementById('employeeList').style.display = 'none';
                         return;
                     }

                     $.ajax({
                         url: '{{ route('hr.training.management.get-employees') }}',
                         method: 'GET',
                         data: {
                             department: department,
                             page: page
                         },
                         success: function(response) {
                             displayEmployeeCards(response.data);
                             displayPagination(response);
                             document.getElementById('employeeList').style.display = 'block';
                         },
                         error: function(xhr) {
                             Swal.fire({
                                 icon: 'error',
                                 title: 'Error!',
                                 text: 'Gagal memuat data karyawan: ' + xhr.responseText
                             });
                         }
                     });
                 }

                 function displayEmployeeCards(employees) {
                     const cardsContainer = document.getElementById('employeeCards');
                     cardsContainer.innerHTML = '';

                     // Simpan data karyawan untuk departemen ini
                     const department = document.getElementById('department_filter').value;
                     allEmployeeData[department] = employees;

                     employees.forEach(function(employee) {
                         const isSelected = selectedEmployees.includes(employee.Nip);
                         const card = `
                             <div class="col-md-4 mb-3">
                                 <div class="card employee-card ${isSelected ? 'border-primary' : ''}" style="cursor: pointer;" onclick="toggleEmployeeCard('${employee.Nip}')">
                                     <div class="card-body">
                                         <div class="d-flex align-items-start">
                                             <div class="form-check me-3">
                                                 <input type="checkbox" name="employee_ids[]"
                                                        value="${employee.Nip}"
                                                        class="form-check-input employee-checkbox"
                                                        id="employee_checkbox_${employee.Nip}"
                                                        ${isSelected ? 'checked' : ''}
                                                        onchange="toggleEmployeeSelection('${employee.Nip}')">
                                                 <label class="form-check-label" for="employee_checkbox_${employee.Nip}"></label>
                                             </div>
                                             <div class="flex-grow-1">
                                                 <h6 class="card-title mb-1">${employee.Nama}</h6>
                                                 <p class="card-text text-muted mb-1">
                                                     <small><i class="mdi mdi-identifier mr-1"></i>${employee.Nip}</small>
                                                 </p>
                                                 <p class="card-text text-muted mb-1">
                                                     <small><i class="mdi mdi-email mr-1"></i>${employee.Email || 'N/A'}</small>
                                                 </p>
                                                 <p class="card-text text-muted mb-1">
                                                     <small><i class="mdi mdi-office-building mr-1"></i>${employee['Kode Divisi'] || 'N/A'}</small>
                                                 </p>
                                                 <p class="card-text text-muted mb-1">
                                                     <small><i class="mdi mdi-domain mr-1"></i>${employee['Kode Bagian'] || 'N/A'}</small>
                                                 </p>
                                                 <p class="card-text text-muted mb-0">
                                                     <small><i class="mdi mdi-briefcase mr-1"></i>${employee['Kode Jabatan'] || 'N/A'}</small>
                                                 </p>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         `;
                         cardsContainer.innerHTML += card;
                     });
                 }

                function displayPagination(response) {
                    const paginationDiv = document.getElementById('employeePagination');
                    let paginationHtml = '';

                    if (response.last_page > 1) {
                        paginationHtml = '<nav><ul class="pagination">';

                         // Previous button
                         if (response.current_page > 1) {
                             paginationHtml += `<li class="page-item">
                 <a class="page-link" href="#" onclick="loadEmployeesByDepartment(${response.current_page - 1})">Previous</a>
             </li>`;
                         }

                         // Page numbers
                         for (let i = 1; i <= response.last_page; i++) {
                             const activeClass = i === response.current_page ? 'active' : '';
                             paginationHtml += `<li class="page-item ${activeClass}">
                 <a class="page-link" href="#" onclick="loadEmployeesByDepartment(${i})">${i}</a>
             </li>`;
                         }

                         // Next button
                         if (response.current_page < response.last_page) {
                             paginationHtml += `<li class="page-item">
                 <a class="page-link" href="#" onclick="loadEmployeesByDepartment(${response.current_page + 1})">Next</a>
             </li>`;
                         }

                        paginationHtml += '</ul></nav>';
                    }

                    paginationDiv.innerHTML = paginationHtml;
                }

                 function toggleSelectAll() {
                     const selectAll = document.getElementById('selectAll');
                     const checkboxes = document.querySelectorAll('#employeeCards .employee-checkbox');

                     checkboxes.forEach(function(checkbox) {
                         checkbox.checked = selectAll.checked;
                         toggleEmployeeSelection(checkbox.value);
                     });

                     // Update card appearance
                     updateCardAppearance();
                 }

                 function toggleEmployeeCard(nip) {
                     const checkbox = document.querySelector(`input[value="${nip}"]`);
                     if (checkbox) {
                         checkbox.checked = !checkbox.checked;
                         toggleEmployeeSelection(nip);
                         updateCardAppearance();
                     }
                 }

                 function updateCardAppearance() {
                     const cards = document.querySelectorAll('.employee-card');
                     cards.forEach(function(card) {
                         const checkbox = card.querySelector('.employee-checkbox');
                         if (checkbox && checkbox.checked) {
                             card.classList.add('border-primary');
                             card.classList.remove('border-light');
                         } else {
                             card.classList.remove('border-primary');
                             card.classList.add('border-light');
                         }
                     });
                 }

                 function toggleEmployeeSelection(nip) {
                     console.log('Toggling employee:', nip);
                     const index = selectedEmployees.indexOf(nip);
                     if (index > -1) {
                         selectedEmployees.splice(index, 1);
                         console.log('Removed employee, selectedEmployees now:', selectedEmployees);
                     } else {
                         selectedEmployees.push(nip);
                         console.log('Added employee, selectedEmployees now:', selectedEmployees);
                     }

                     updateSelectedParticipantsTable();
                 }

                 function updateSelectedParticipantsTable() {
                     console.log('Updating table with selectedEmployees:', selectedEmployees);
                     const selectedParticipantsList = document.getElementById('selectedParticipantsList');
                     const tableBody = document.getElementById('selectedParticipantsTableBody');

                     if (selectedEmployees.length > 0) {
                         selectedParticipantsList.style.display = 'block';

                         // Get employee data from cards (jika ada) atau dari allEmployeeData
                         const employeeData = [];
                         selectedEmployees.forEach(function(nip) {
                             console.log('Processing NIP:', nip);

                             // Coba ambil dari cards yang sedang ditampilkan
                             const checkbox = document.querySelector(`input[value="${nip}"]`);
                             let employeeInfo = null;

                             if (checkbox) {
                                 const card = checkbox.closest('.card');
                                 if (card) {
                                     const name = card.querySelector('.card-title');
                                     const nipElement = card.querySelector('.mdi-identifier');
                                     const emailElement = card.querySelector('.mdi-email');
                                     const divisiElement = card.querySelector('.mdi-office-building');
                                     const bagianElement = card.querySelector('.mdi-domain');

                                     employeeInfo = {
                                         nip: nip,
                                         name: name ? name.textContent.trim() : 'N/A',
                                         email: emailElement ? emailElement.parentElement.textContent.trim() : 'N/A',
                                         divisi: divisiElement ? divisiElement.parentElement.textContent.trim() : 'N/A',
                                         bagian: bagianElement ? bagianElement.parentElement.textContent.trim() : 'N/A'
                                     };
                                 }
                             }

                             // Jika tidak ditemukan di cards, cari dari allEmployeeData
                             if (!employeeInfo) {
                                 Object.keys(allEmployeeData).forEach(function(department) {
                                     const employees = allEmployeeData[department];
                                     const employee = employees.find(emp => emp.Nip === nip);
                                     if (employee) {
                                         employeeInfo = {
                                             nip: employee.Nip,
                                             name: employee.Nama || 'N/A',
                                             email: employee.Email || 'N/A',
                                             divisi: employee['Kode Divisi'] || 'N/A',
                                             bagian: employee['Kode Bagian'] || 'N/A'
                                         };
                                     }
                                 });
                             }

                             if (employeeInfo) {
                                 console.log('Employee info:', employeeInfo);
                                 employeeData.push(employeeInfo);
                             }
                         });

                         console.log('Employee data collected:', employeeData);

                         // Update table
                         tableBody.innerHTML = '';

                         if (employeeData.length > 0) {
                             employeeData.forEach(function(employee, index) {
                                 console.log('Adding row for employee:', employee);
                                 const row = document.createElement('tr');
                                 row.innerHTML = `
                                     <td>${index + 1}</td>
                                     <td>${employee.nip}</td>
                                     <td>${employee.name}</td>
                                     <td>${employee.email}</td>
                                     <td>${employee.divisi}</td>
                                     <td>${employee.bagian}</td>
                                     <td>
                                         <button class="btn btn-sm btn-danger" onclick="removeSelectedEmployee('${employee.nip}')" title="Hapus">
                                             <i class="mdi mdi-delete"></i>
                                         </button>
                                     </td>
                                 `;
                                 tableBody.appendChild(row);
                             });
                         }
                     } else {
                         selectedParticipantsList.style.display = 'none';
                     }
                 }

                 function removeSelectedEmployee(nip) {
                     const index = selectedEmployees.indexOf(nip);
                     if (index > -1) {
                         selectedEmployees.splice(index, 1);

                         // Update checkbox
                         const checkbox = document.querySelector(`input[value="${nip}"]`);
                         if (checkbox) {
                             checkbox.checked = false;
                         }

                         // Update card appearance
                         updateCardAppearance();

                         // Update table
                         updateSelectedParticipantsTable();
                     }
                 }

                 function saveAllParticipants() {
                     console.log('Saving all participants:', selectedEmployees);

                     if (selectedEmployees.length === 0) {
                         Swal.fire({
                             icon: 'warning',
                             title: 'Peringatan!',
                             text: 'Pilih minimal satu karyawan'
                         });
                         return;
                     }

                     Swal.fire({
                         title: 'Konfirmasi',
                         text: `Yakin ingin mendaftarkan ${selectedEmployees.length} peserta ke training ini?`,
                         icon: 'question',
                         showCancelButton: true,
                         confirmButtonColor: '#28a745',
                         cancelButtonColor: '#6c757d',
                         confirmButtonText: 'Ya, Daftarkan!',
                         cancelButtonText: 'Batal'
                     }).then((result) => {
                         if (result.isConfirmed) {
                             console.log('Sending data:', {
                                 employee_ids: selectedEmployees
                             });

                             $.ajax({
                                 url: '{{ route('hr.training.management.register-employees', $training->id) }}',
                                 method: 'POST',
                                 data: {
                                     employee_ids: selectedEmployees,
                                     _token: '{{ csrf_token() }}'
                                 },
                                 success: function(response) {
                                     console.log('Response:', response);
                                     console.log('SweetAlert available:', typeof Swal !== 'undefined');

                                     if (response.success) {
                                         if (typeof Swal !== 'undefined') {
                                             Swal.fire({
                                                 icon: 'success',
                                                 title: 'Berhasil!',
                                                 text: response.message,
                                                 confirmButtonText: 'OK'
                                             }).then(() => {
                                                 location.reload();
                                             });
                                         } else {
                                             alert(response.message);
                                             location.reload();
                                         }
                                     } else {
                                         if (typeof Swal !== 'undefined') {
                                             Swal.fire({
                                                 icon: 'error',
                                                 title: 'Gagal!',
                                                 text: response.message,
                                                 confirmButtonText: 'OK'
                                             });
                                         } else {
                                             alert('Gagal: ' + response.message);
                                         }
                                     }
                                 },
                                 error: function(xhr) {
                                     console.log('Error response:', xhr.responseText);
                                     console.log('SweetAlert available in error:', typeof Swal !== 'undefined');

                                     try {
                                         const response = JSON.parse(xhr.responseText);
                                         if (typeof Swal !== 'undefined') {
                                             Swal.fire({
                                                 icon: 'error',
                                                 title: 'Error!',
                                                 text: 'Error: ' + response.message,
                                                 confirmButtonText: 'OK'
                                             });
                                         } else {
                                             alert('Error: ' + response.message);
                                         }
                                     } catch (e) {
                                         console.log('Error parsing response:', e);
                                         if (typeof Swal !== 'undefined') {
                                             Swal.fire({
                                                 icon: 'error',
                                                 title: 'Error!',
                                                 text: 'Terjadi kesalahan pada server',
                                                 confirmButtonText: 'OK'
                                             });
                                         } else {
                                             alert('Terjadi kesalahan pada server');
                                         }
                                     }
                                 }
                             });
                         }
                     });
                 }

                function removeParticipant(participantId) {
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Yakin ingin menghapus peserta ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('hr.training.management.remove-employee', [$training->id, ':participantId']) }}'
                                    .replace(':participantId', participantId),
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: response.message
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal!',
                                            text: response.message
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    const response = JSON.parse(xhr.responseText);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: 'Error: ' + response.message
                                    });
                                }
                            });
                        }
                    });
                }

                 // Reset form function
                 function resetForm() {
                     selectedEmployees = [];
                     allEmployeeData = {};
                     document.getElementById('department_filter').value = '';
                     document.getElementById('employeeList').style.display = 'none';
                     document.getElementById('selectedParticipantsList').style.display = 'none';
                     document.getElementById('employeeCards').innerHTML = '';
                     document.getElementById('selectedParticipantsTableBody').innerHTML = '';
                 }

                 // Event listeners
                 // Registration type change will be handled by updateSelectedParticipantsTable
            </script>
        @endsection
