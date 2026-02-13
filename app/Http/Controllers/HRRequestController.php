<?php

namespace App\Http\Controllers;

use App\Models\ApprovalSetting;
use App\Models\EmployeeRequest;
use App\Models\MasterAbsenceSetting;
use App\Models\OvertimeEmployee;
use App\Models\SplRequest;
use App\Models\User;
use App\Services\ApprovalService;
use App\Http\Controllers\HRApprovalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HRRequestController extends Controller
{
    /**
     * Display a listing of employee requests
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        // dd($user);
        // Eager load relationships to prevent N+1 queries
        $query = EmployeeRequest::with([
            'employee',
            'supervisor',
            'hr',
            'manager',
            'head'
        ]);

        // Get filter type and date filters from request
        $filterType = $request->get('filter_type', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

        // dd($dateFrom, $dateTo);

        // Jika ada filter (pencarian), hanya tampilkan pengajuan milik user sendiri
        // Jika tidak ada filter, gunakan logika berdasarkan role
        if (!empty($filterType) || !empty($dateFrom) || !empty($dateTo)) {
            // Ada filter/pencarian: hanya pengajuan milik user sendiri
            $query->where('employee_id', $user->id);
        } else {
            // Tidak ada filter: gunakan logika berdasarkan role
            if ($user->isHR()) {
                // HR bisa lihat semua pengajuan
                $query->whereIn('status', [
                    EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                    EmployeeRequest::STATUS_HR_APPROVED,
                    EmployeeRequest::STATUS_HR_REJECTED,
                    EmployeeRequest::STATUS_HEAD_APPROVED
                ]);
            } elseif ($user->supervisor_id) {
                // Supervisor bisa lihat pengajuan dari bawahannya
                $query->where('supervisor_id', $user->id);
            } else {
                // Karyawan biasa hanya bisa lihat pengajuan sendiri
                $query->where('employee_id', $user->id);
            }
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan jenis pengajuan
        if ($request->has('type') && $request->type !== '') {
            $query->where('request_type', $request->type);
        }

        // Filter berdasarkan tanggal
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Load overtime entries if needed
        $overtimeEntries = collect();
        if ($filterType === '' || $filterType === 'overtime') {
            $overtimeQuery = \App\Models\OvertimeEntry::where(function($query) use ($user) {
                $query->where('employee_id', $user->id)
                    ->orWhere('divisi_id', $user->divisi);
            });

            if ($dateFrom) {
                $overtimeQuery->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $overtimeQuery->whereDate('created_at', '<=', $dateTo);
            }

            $overtimeEntries = $overtimeQuery->orderBy('created_at', 'desc')->get();
        }

        // Load vehicle requests if needed
        $vehicleRequests = collect();
        if ($filterType === '' || $filterType === 'vehicle') {
            $vehicleQuery = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
                ->where(function($query) use ($user) {
                    $query->where('employee_id', $user->id)
                        ->orWhere('divisi_id', $user->divisi);
                });

            if ($dateFrom) {
                $vehicleQuery->whereDate('request_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $vehicleQuery->whereDate('request_date', '<=', $dateTo);
            }

            $vehicleRequests = $vehicleQuery->orderBy('created_at', 'desc')->get();
        }

        // Load asset requests if needed
        $assetRequests = collect();
        if ($filterType === '' || $filterType === 'asset') {
            $assetQuery = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
                ->where(function($query) use ($user) {
                    $query->where('employee_id', $user->id)
                        ->orWhere('divisi_id', $user->divisi);
                });

            if ($dateFrom) {
                $assetQuery->whereDate('request_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $assetQuery->whereDate('request_date', '<=', $dateTo);
            }

            $assetRequests = $assetQuery->orderBy('created_at', 'desc')->get();
        }

        // Load SPL requests if needed
        $splRequests = collect();
        if ($filterType === '' || $filterType === 'spl') {
            $splQuery = SplRequest::where(function($query) use ($user) {
                if ($user->isHR()) {
                    // HR bisa lihat semua SPL
                } else {
                    // Supervisor hanya bisa lihat SPL yang dibuatnya atau divisinya
                    $query->where('supervisor_id', $user->id)
                        ->orWhere('divisi_id', $user->divisi);
                }
            });

            if ($dateFrom) {
                $splQuery->whereDate('request_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $splQuery->whereDate('request_date', '<=', $dateTo);
            }

            $splRequests = $splQuery->with('supervisor', 'employees')->orderBy('created_at', 'desc')->get();
        }

        // Get statistics for dashboard
        $stats = $this->getDashboardStats($user);

        // dd($stats);

        // Get departments for filter
        $departments = DB::connection('mysql7')
            ->table('masterdivisi')
            ->where('Begda', '<=', now())
            ->where(function($q) {
                $q->whereNull('Endda')
                  ->orWhere('Endda', '>=', now());
            })
            ->select('Kode Divisi as id', 'Nama Divisi as name')
            ->orderBy('Nama Divisi')
            ->get();

        // dd($departments);
        // Get approval history for current user (EmployeeRequest)
        $employeeRequestHistory = EmployeeRequest::with(['employee', 'supervisor', 'head', 'manager', 'hr', 'general'])
            ->where(function($query) use ($user) {
                // Supervisor approvals
                $query->where(function($q) use ($user) {
                    $q->where('supervisor_id', $user->id)
                      ->where(function($subQ) {
                          $subQ->whereNotNull('supervisor_approved_at')
                               ->orWhereNotNull('supervisor_rejected_at');
                      });
                })
                // Head approvals
                ->orWhere(function($q) use ($user) {
                    $q->where('head_id', $user->id)
                      ->where(function($subQ) {
                          $subQ->whereNotNull('head_approved_at')
                               ->orWhereNotNull('head_rejected_at');
                      });
                })
                // Manager approvals
                ->orWhere(function($q) use ($user) {
                    $q->where('manager_id', $user->id)
                      ->where(function($subQ) {
                          $subQ->whereNotNull('manager_approved_at')
                               ->orWhereNotNull('manager_rejected_at');
                      });
                })
                // HR approvals
                ->orWhere(function($q) use ($user) {
                    if ($user->isHR()) {
                        $q->where(function($subQ) {
                            $subQ->whereNotNull('hr_approved_at')
                                 ->orWhereNotNull('hr_rejected_at');
                        });
                    }
                })
                // General Manager approvals
                ->orWhere(function($q) use ($user) {
                    $q->where('general_id', $user->id)
                      ->where(function($subQ) {
                          $subQ->whereNotNull('general_approved_at')
                               ->orWhereNotNull('general_rejected_at');
                      });
                });
            })
            ->get()
            ->map(function($request) use ($user) {
                // Determine approval role and status
                $approvalRole = null;
                $approvalStatus = null;
                $approvalDate = null;
                $approvalNotes = null;

                if ($request->supervisor_id == $user->id) {
                    if ($request->supervisor_approved_at) {
                        $approvalRole = 'SPV';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->supervisor_approved_at;
                        $approvalNotes = $request->supervisor_notes;
                    } elseif ($request->supervisor_rejected_at) {
                        $approvalRole = 'SPV';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->supervisor_rejected_at;
                        $approvalNotes = $request->supervisor_notes;
                    }
                } elseif ($request->head_id == $user->id) {
                    if ($request->head_approved_at) {
                        $approvalRole = 'HEAD DIVISI';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->head_approved_at;
                        $approvalNotes = $request->head_notes;
                    } elseif ($request->head_rejected_at) {
                        $approvalRole = 'HEAD DIVISI';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->head_rejected_at;
                        $approvalNotes = $request->head_notes;
                    }
                } elseif ($request->manager_id == $user->id) {
                    if ($request->manager_approved_at) {
                        $approvalRole = 'MANAGER';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->manager_approved_at;
                        $approvalNotes = $request->manager_notes;
                    } elseif ($request->manager_rejected_at) {
                        $approvalRole = 'MANAGER';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->manager_rejected_at;
                        $approvalNotes = $request->manager_notes;
                    }
                } elseif ($user->isHR() && ($request->hr_approved_at || $request->hr_rejected_at)) {
                    if ($request->hr_approved_at) {
                        $approvalRole = 'HRD';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->hr_approved_at;
                        $approvalNotes = $request->hr_notes;
                    } elseif ($request->hr_rejected_at) {
                        $approvalRole = 'HRD';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->hr_rejected_at;
                        $approvalNotes = $request->hr_notes;
                    }
                } elseif ($request->general_id == $user->id) {
                    if ($request->general_approved_at) {
                        $approvalRole = 'GENERAL MANAGER';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->general_approved_at;
                        $approvalNotes = $request->general_notes;
                    } elseif ($request->general_rejected_at) {
                        $approvalRole = 'GENERAL MANAGER';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->general_rejected_at;
                        $approvalNotes = $request->general_notes;
                    }
                }

                return [
                    'request' => $request,
                    'request_type' => 'employee_request',
                    'approval_role' => $approvalRole,
                    'approval_status' => $approvalStatus,
                    'approval_date' => $approvalDate,
                    'approval_notes' => $approvalNotes,
                ];
            })
            ->filter(function($item) {
                return $item['approval_role'] !== null;
            });

        if (!$employeeRequestHistory || $employeeRequestHistory->isEmpty()) {
            $employeeRequestHistory = collect();
        }

        // dd($employeeRequestHistory);

        // Get approval history for Vehicle/Asset requests
        $vehicleAssetHistory = \App\Models\VehicleAssetRequest::where(function($query) use ($user) {
                // Manager approvals
                $query->where(function($q) use ($user) {
                    $q->where('manager_id', $user->id)
                      ->whereNotNull('manager_at');
                })
                // General Manager approvals
                ->orWhere(function($q) use ($user) {
                    $q->where('general_id', $user->id)
                      ->where(function($subQ) {
                          $subQ->whereNotNull('general_approved_at')
                               ->orWhereNotNull('general_rejected_at');
                      });
                })
                // HRGA approvals (jika user adalah HRGA)
                ->orWhere(function($q) use ($user) {
                    if ($user->isHR()) {
                        $q->whereNotNull('hrga_at');
                    }
                });
            })
            ->get()
            ->map(function($request) use ($user) {
                // Determine approval role and status
                $approvalRole = null;
                $approvalStatus = null;
                $approvalDate = null;
                $approvalNotes = null;

                if ($request->manager_id == $user->id && $request->manager_at) {
                    // Cek status untuk menentukan approved atau rejected
                    if ($request->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED) {
                        $approvalRole = 'MANAGER';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->manager_at;
                        $approvalNotes = $request->manager_notes;
                    } elseif ($request->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_REJECTED) {
                        $approvalRole = 'MANAGER';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->manager_at;
                        $approvalNotes = $request->manager_notes;
                    }
                } elseif ($request->general_id == $user->id) {
                    if ($request->general_approved_at) {
                        $approvalRole = 'GENERAL MANAGER';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->general_approved_at;
                        $approvalNotes = $request->general_notes;
                    } elseif ($request->general_rejected_at) {
                        $approvalRole = 'GENERAL MANAGER';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->general_rejected_at;
                        $approvalNotes = $request->general_notes;
                    }
                } elseif ($user->isHR() && $request->hrga_at) {
                    if ($request->status === \App\Models\VehicleAssetRequest::STATUS_HRGA_APPROVED) {
                        $approvalRole = 'HRGA';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->hrga_at;
                        $approvalNotes = $request->hrga_notes;
                    } elseif ($request->status === \App\Models\VehicleAssetRequest::STATUS_HRGA_REJECTED) {
                        $approvalRole = 'HRGA';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->hrga_at;
                        $approvalNotes = $request->hrga_notes;
                    }
                }

                return [
                    'request' => $request,
                    'request_type' => 'vehicle_asset',
                    'approval_role' => $approvalRole,
                    'approval_status' => $approvalStatus,
                    'approval_date' => $approvalDate,
                    'approval_notes' => $approvalNotes,
                ];
            })
            ->filter(function($item) {
                return $item['approval_role'] !== null;
            });

        // dd($vehicleAssetHistory);
        // Merge and sort by approval date
        $approvalHistory = $employeeRequestHistory->merge($vehicleAssetHistory)
            ->sortByDesc(function($item) {
                return $item['approval_date'] ? $item['approval_date']->timestamp : 0;
            })
            ->values();

        // dd('1');

        return view('hr.requests.index', compact('requests', 'stats', 'filterType', 'dateFrom', 'dateTo', 'overtimeEntries', 'vehicleRequests', 'assetRequests', 'splRequests', 'departments', 'approvalHistory'));
    }

    /**
     * Display guide/panduan penggunaan sistem perizinan
     */
    public function guide()
    {
        return view('hr.requests.guide');
    }

    public function indexPrd(Request $request)
    {
        $user = Auth::user();
        // Eager load relationships to prevent N+1 queries
        $query = EmployeeRequest::with([
            'employee',
            'supervisor',
            'hr',
            'manager',
            'head'
        ]);

        // Get filter type and date filters from request
        $filterType = $request->get('filter_type', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');

        // Jika ada filter (pencarian), hanya tampilkan pengajuan milik user sendiri
        // Jika tidak ada filter, gunakan logika berdasarkan role
        if (!empty($filterType) || !empty($dateFrom) || !empty($dateTo)) {
            // Ada filter/pencarian: hanya pengajuan milik user sendiri
            $query->where('employee_id', $user->id);
        } else {
            // Tidak ada filter: gunakan logika berdasarkan role
            if ($user->isHR()) {
                // HR bisa lihat semua pengajuan
                $query->whereIn('status', [
                    EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                    EmployeeRequest::STATUS_HR_APPROVED,
                    EmployeeRequest::STATUS_HR_REJECTED
                ]);
            } elseif ($user->supervisor_id) {
                // Supervisor bisa lihat pengajuan dari bawahannya
                $query->where('supervisor_id', $user->id);
            } else {
                // Karyawan biasa hanya bisa lihat pengajuan sendiri
                $query->where('employee_id', $user->id);
            }
        }

        // Filter berdasarkan status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan jenis pengajuan
        if ($request->has('type') && $request->type !== '') {
            $query->where('request_type', $request->type);
        }

        // Filter berdasarkan tanggal
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Load overtime entries if needed
        $overtimeEntries = collect();
        if ($filterType === '' || $filterType === 'overtime') {
            $overtimeQuery = \App\Models\OvertimeEntry::where(function($query) use ($user) {
                $query->where('employee_id', $user->id)
                    ->orWhere('divisi_id', $user->divisi);
            });

            if ($dateFrom) {
                $overtimeQuery->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $overtimeQuery->whereDate('created_at', '<=', $dateTo);
            }

            $overtimeEntries = $overtimeQuery->orderBy('created_at', 'desc')->get();
        }

        // Load vehicle requests if needed
        $vehicleRequests = collect();
        if ($filterType === '' || $filterType === 'vehicle') {
            $vehicleQuery = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
                ->where(function($query) use ($user) {
                    $query->where('employee_id', $user->id)
                        ->orWhere('divisi_id', $user->divisi);
                });

            if ($dateFrom) {
                $vehicleQuery->whereDate('request_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $vehicleQuery->whereDate('request_date', '<=', $dateTo);
            }

            $vehicleRequests = $vehicleQuery->orderBy('created_at', 'desc')->get();
        }

        // Load asset requests if needed
        $assetRequests = collect();
        if ($filterType === '' || $filterType === 'asset') {
            $assetQuery = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
                ->where(function($query) use ($user) {
                    $query->where('employee_id', $user->id)
                        ->orWhere('divisi_id', $user->divisi);
                });

            if ($dateFrom) {
                $assetQuery->whereDate('request_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $assetQuery->whereDate('request_date', '<=', $dateTo);
            }

            $assetRequests = $assetQuery->orderBy('created_at', 'desc')->get();
        }

        // Load SPL requests if needed
        $splRequests = collect();
        if ($filterType === '' || $filterType === 'spl') {
            $splQuery = SplRequest::where(function($query) use ($user) {
                if ($user->isHR()) {
                    // HR bisa lihat semua SPL
                } else {
                    // Supervisor hanya bisa lihat SPL yang dibuatnya atau divisinya
                    $query->where('supervisor_id', $user->id)
                        ->orWhere('divisi_id', $user->divisi);
                }
            });

            if ($dateFrom) {
                $splQuery->whereDate('request_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $splQuery->whereDate('request_date', '<=', $dateTo);
            }

            $splRequests = $splQuery->with('supervisor', 'employees')->orderBy('created_at', 'desc')->get();
        }

        // Get statistics for dashboard
        $stats = $this->getDashboardStats($user);

        // dd($stats);

        // Get departments for filter
        $departments = DB::connection('mysql7')
            ->table('masterdivisi')
            ->where('Begda', '<=', now())
            ->where(function($q) {
                $q->whereNull('Endda')
                  ->orWhere('Endda', '>=', now());
            })
            ->select('Kode Divisi as id', 'Nama Divisi as name')
            ->orderBy('Nama Divisi')
            ->get();

        // Get approval history for current user (EmployeeRequest)
        $employeeRequestHistory = EmployeeRequest::with(['employee', 'supervisor', 'head', 'manager', 'hr', 'general'])
            ->where(function($query) use ($user) {
                // Supervisor approvals
                $query->where(function($q) use ($user) {
                    $q->where('supervisor_id', $user->id)
                      ->where(function($subQ) {
                          $subQ->whereNotNull('supervisor_approved_at')
                               ->orWhereNotNull('supervisor_rejected_at');
                      });
                })
                // Head approvals
                ->orWhere(function($q) use ($user) {
                    $q->where('head_id', $user->id)
                      ->where(function($subQ) {
                          $subQ->whereNotNull('head_approved_at')
                               ->orWhereNotNull('head_rejected_at');
                      });
                })
                // Manager approvals
                ->orWhere(function($q) use ($user) {
                    $q->where('manager_id', $user->id)
                      ->where(function($subQ) {
                          $subQ->whereNotNull('manager_approved_at')
                               ->orWhereNotNull('manager_rejected_at');
                      });
                })
                // HR approvals
                ->orWhere(function($q) use ($user) {
                    if ($user->isHR()) {
                        $q->where(function($subQ) {
                            $subQ->whereNotNull('hr_approved_at')
                                 ->orWhereNotNull('hr_rejected_at');
                        });
                    }
                })
                // General Manager approvals
                ->orWhere(function($q) use ($user) {
                    $q->where('general_id', $user->id)
                      ->where(function($subQ) {
                          $subQ->whereNotNull('general_approved_at')
                               ->orWhereNotNull('general_rejected_at');
                      });
                });
            })
            ->get()
            ->map(function($request) use ($user) {
                // Determine approval role and status
                $approvalRole = null;
                $approvalStatus = null;
                $approvalDate = null;
                $approvalNotes = null;

                if ($request->supervisor_id == $user->id) {
                    if ($request->supervisor_approved_at) {
                        $approvalRole = 'SPV';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->supervisor_approved_at;
                        $approvalNotes = $request->supervisor_notes;
                    } elseif ($request->supervisor_rejected_at) {
                        $approvalRole = 'SPV';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->supervisor_rejected_at;
                        $approvalNotes = $request->supervisor_notes;
                    }
                } elseif ($request->head_id == $user->id) {
                    if ($request->head_approved_at) {
                        $approvalRole = 'HEAD DIVISI';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->head_approved_at;
                        $approvalNotes = $request->head_notes;
                    } elseif ($request->head_rejected_at) {
                        $approvalRole = 'HEAD DIVISI';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->head_rejected_at;
                        $approvalNotes = $request->head_notes;
                    }
                } elseif ($request->manager_id == $user->id) {
                    if ($request->manager_approved_at) {
                        $approvalRole = 'MANAGER';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->manager_approved_at;
                        $approvalNotes = $request->manager_notes;
                    } elseif ($request->manager_rejected_at) {
                        $approvalRole = 'MANAGER';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->manager_rejected_at;
                        $approvalNotes = $request->manager_notes;
                    }
                } elseif ($user->isHR() && ($request->hr_approved_at || $request->hr_rejected_at)) {
                    if ($request->hr_approved_at) {
                        $approvalRole = 'HRD';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->hr_approved_at;
                        $approvalNotes = $request->hr_notes;
                    } elseif ($request->hr_rejected_at) {
                        $approvalRole = 'HRD';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->hr_rejected_at;
                        $approvalNotes = $request->hr_notes;
                    }
                } elseif ($request->general_id == $user->id) {
                    if ($request->general_approved_at) {
                        $approvalRole = 'GENERAL MANAGER';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->general_approved_at;
                        $approvalNotes = $request->general_notes;
                    } elseif ($request->general_rejected_at) {
                        $approvalRole = 'GENERAL MANAGER';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->general_rejected_at;
                        $approvalNotes = $request->general_notes;
                    }
                }

                return [
                    'request' => $request,
                    'request_type' => 'employee_request',
                    'approval_role' => $approvalRole,
                    'approval_status' => $approvalStatus,
                    'approval_date' => $approvalDate,
                    'approval_notes' => $approvalNotes,
                ];
            })
            ->filter(function($item) {
                return $item['approval_role'] !== null;
            });

        // Get approval history for Vehicle/Asset requests
        $vehicleAssetHistory = \App\Models\VehicleAssetRequest::where(function($query) use ($user) {
                // Manager approvals
                $query->where(function($q) use ($user) {
                    $q->where('manager_id', $user->id)
                      ->whereNotNull('manager_at');
                })
                // General Manager approvals
                ->orWhere(function($q) use ($user) {
                    $q->where('general_id', $user->id)
                      ->where(function($subQ) {
                          $subQ->whereNotNull('general_approved_at')
                               ->orWhereNotNull('general_rejected_at');
                      });
                })
                // HRGA approvals (jika user adalah HRGA)
                ->orWhere(function($q) use ($user) {
                    if ($user->isHR()) {
                        $q->whereNotNull('hrga_at');
                    }
                });
            })
            ->get()
            ->map(function($request) use ($user) {
                // Determine approval role and status
                $approvalRole = null;
                $approvalStatus = null;
                $approvalDate = null;
                $approvalNotes = null;

                if ($request->manager_id == $user->id && $request->manager_at) {
                    // Cek status untuk menentukan approved atau rejected
                    if ($request->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED) {
                        $approvalRole = 'MANAGER';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->manager_at;
                        $approvalNotes = $request->manager_notes;
                    } elseif ($request->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_REJECTED) {
                        $approvalRole = 'MANAGER';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->manager_at;
                        $approvalNotes = $request->manager_notes;
                    }
                } elseif ($request->general_id == $user->id) {
                    if ($request->general_approved_at) {
                        $approvalRole = 'GENERAL MANAGER';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->general_approved_at;
                        $approvalNotes = $request->general_notes;
                    } elseif ($request->general_rejected_at) {
                        $approvalRole = 'GENERAL MANAGER';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->general_rejected_at;
                        $approvalNotes = $request->general_notes;
                    }
                } elseif ($user->isHR() && $request->hrga_at) {
                    if ($request->status === \App\Models\VehicleAssetRequest::STATUS_HRGA_APPROVED) {
                        $approvalRole = 'HRGA';
                        $approvalStatus = 'approved';
                        $approvalDate = $request->hrga_at;
                        $approvalNotes = $request->hrga_notes;
                    } elseif ($request->status === \App\Models\VehicleAssetRequest::STATUS_HRGA_REJECTED) {
                        $approvalRole = 'HRGA';
                        $approvalStatus = 'rejected';
                        $approvalDate = $request->hrga_at;
                        $approvalNotes = $request->hrga_notes;
                    }
                }

                return [
                    'request' => $request,
                    'request_type' => 'vehicle_asset',
                    'approval_role' => $approvalRole,
                    'approval_status' => $approvalStatus,
                    'approval_date' => $approvalDate,
                    'approval_notes' => $approvalNotes,
                ];
            })
            ->filter(function($item) {
                return $item['approval_role'] !== null;
            });

        // Merge and sort by approval date
        $approvalHistory = $employeeRequestHistory->merge($vehicleAssetHistory)
            ->sortByDesc(function($item) {
                return $item['approval_date'] ? $item['approval_date']->timestamp : 0;
            })
            ->values();

        return view('hr.requests.index', compact('requests', 'stats', 'filterType', 'dateFrom', 'dateTo', 'overtimeEntries', 'vehicleRequests', 'assetRequests', 'splRequests', 'departments', 'approvalHistory'));
    }

    // public function indexPrd(Request $request)
    // {
    //     $user = Auth::user();
    //     // Eager load relationships to prevent N+1 queries
    //     $query = EmployeeRequest::with([
    //         'employee',
    //         'supervisor',
    //         'hr',
    //         'manager',
    //         'head'
    //     ]);

    //     // Filter berdasarkan role user
    //     if ($user->isHR()) {
    //         // HR bisa lihat semua pengajuan
    //         $query->whereIn('status', [
    //             EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
    //             EmployeeRequest::STATUS_HR_APPROVED,
    //             EmployeeRequest::STATUS_HR_REJECTED
    //         ]);
    //     } elseif ($user->supervisor_id) {
    //         // Supervisor bisa lihat pengajuan dari bawahannya
    //         $query->where('supervisor_id', $user->id);
    //     } else {
    //         // Karyawan biasa hanya bisa lihat pengajuan sendiri
    //         $query->where('employee_id', $user->id);
    //     }

    //     // Filter berdasarkan status
    //     if ($request->has('status') && $request->status !== '') {
    //         $query->where('status', $request->status);
    //     }

    //     // Filter berdasarkan jenis pengajuan
    //     if ($request->has('type') && $request->type !== '') {
    //         $query->where('request_type', $request->type);
    //     }

    //     // Filter berdasarkan tanggal
    //     if ($request->has('date_from') && $request->date_from) {
    //         $query->whereDate('created_at', '>=', $request->date_from);
    //     }
    //     if ($request->has('date_to') && $request->date_to) {
    //         $query->whereDate('created_at', '<=', $request->date_to);
    //     }

    //     $requests = $query->orderBy('created_at', 'desc')->paginate(15);

    //     // Get filter type and date filters from request
    //     $filterType = $request->get('filter_type', '');
    //     $dateFrom = $request->get('date_from', '');
    //     $dateTo = $request->get('date_to', '');

    //     // Load overtime entries if needed
    //     $overtimeEntries = collect();
    //     if ($filterType === '' || $filterType === 'overtime') {
    //         $overtimeQuery = \App\Models\OvertimeEntry::where(function($query) use ($user) {
    //             $query->where('employee_id', $user->id)
    //                 ->orWhere('divisi_id', $user->divisi);
    //         });

    //         if ($dateFrom) {
    //             $overtimeQuery->whereDate('created_at', '>=', $dateFrom);
    //         }
    //         if ($dateTo) {
    //             $overtimeQuery->whereDate('created_at', '<=', $dateTo);
    //         }

    //         $overtimeEntries = $overtimeQuery->orderBy('created_at', 'desc')->get();
    //     }

    //     // Load vehicle requests if needed
    //     $vehicleRequests = collect();
    //     if ($filterType === '' || $filterType === 'vehicle') {
    //         $vehicleQuery = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
    //             ->where(function($query) use ($user) {
    //                 $query->where('employee_id', $user->id)
    //                     ->orWhere('divisi_id', $user->divisi);
    //             });

    //         if ($dateFrom) {
    //             $vehicleQuery->whereDate('request_date', '>=', $dateFrom);
    //         }
    //         if ($dateTo) {
    //             $vehicleQuery->whereDate('request_date', '<=', $dateTo);
    //         }

    //         $vehicleRequests = $vehicleQuery->orderBy('created_at', 'desc')->get();
    //     }

    //     // Load asset requests if needed
    //     $assetRequests = collect();
    //     if ($filterType === '' || $filterType === 'asset') {
    //         $assetQuery = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
    //             ->where(function($query) use ($user) {
    //                 $query->where('employee_id', $user->id)
    //                     ->orWhere('divisi_id', $user->divisi);
    //             });

    //         if ($dateFrom) {
    //             $assetQuery->whereDate('request_date', '>=', $dateFrom);
    //         }
    //         if ($dateTo) {
    //             $assetQuery->whereDate('request_date', '<=', $dateTo);
    //         }

    //         $assetRequests = $assetQuery->orderBy('created_at', 'desc')->get();
    //     }

    //     // Load SPL requests if needed
    //     $splRequests = collect();
    //     if ($filterType === '' || $filterType === 'spl') {
    //         $splQuery = SplRequest::where(function($query) use ($user) {
    //             if ($user->isHR()) {
    //                 // HR bisa lihat semua SPL
    //             } else {
    //                 // Supervisor hanya bisa lihat SPL yang dibuatnya atau divisinya
    //                 $query->where('supervisor_id', $user->id)
    //                     ->orWhere('divisi_id', $user->divisi);
    //             }
    //         });

    //         if ($dateFrom) {
    //             $splQuery->whereDate('request_date', '>=', $dateFrom);
    //         }
    //         if ($dateTo) {
    //             $splQuery->whereDate('request_date', '<=', $dateTo);
    //         }

    //         $splRequests = $splQuery->with('supervisor', 'employees')->orderBy('created_at', 'desc')->get();
    //     }

    //     // Get statistics for dashboard
    //     $stats = $this->getDashboardStats($user);

    //     // Get departments for filter
    //     $departments = DB::connection('mysql7')
    //         ->table('masterdivisi')
    //         ->where('Begda', '<=', now())
    //         ->where(function($q) {
    //             $q->whereNull('Endda')
    //               ->orWhere('Endda', '>=', now());
    //         })
    //         ->select('Kode Divisi as id', 'Nama Divisi as name')
    //         ->orderBy('Nama Divisi')
    //         ->get();

    //     return view('hr.requests.index-prd', compact('requests', 'stats', 'filterType', 'dateFrom', 'dateTo', 'overtimeEntries', 'vehicleRequests', 'assetRequests', 'splRequests', 'departments'));
    // }

    /**
     * Check if date is a holiday in masterkalender or weekend
     */
    private function checkHolidayDate($date)
    {
        try {
            $carbonDate = \Carbon\Carbon::parse($date);
            $isWeekend = $carbonDate->isWeekend();
            $dayName = $isWeekend ? ($carbonDate->isSaturday() ? 'Sabtu' : 'Minggu') : null;

            $holiday = DB::connection('mysql7')->table('masterkalender')->where('Tgl', '=', $date)->first();

            // dd($holiday);

            Log::info('Holiday result: ' . ($holiday ? json_encode($holiday) : 'null'));

            if ($holiday) {
                // If found in masterkalender, prioritize company holiday
                return [
                    'is_holiday' => true,
                    'holiday_name' => $holiday->Keterangan,
                    'holiday_type' => 'company_holiday',
                    'overtime_code' => $holiday->{'Kode Lembur'} ?? 'OFF'
                ];
            }

            // 2. If not in masterkalender, check if it's weekend
            if ($isWeekend) {
                return [
                    'is_holiday' => true,
                    'holiday_name' => "Hari {$dayName}",
                    'holiday_type' => 'weekend',
                    'overtime_code' => 'OFF'
                ];
            }

            return [
                'is_holiday' => false,
                'holiday_name' => null,
                'holiday_type' => null,
                'overtime_code' => null
            ];

        } catch (\Exception $e) {
            Log::error('Error checking holiday date: ' . $e->getMessage());
            return [
                'is_holiday' => false,
                'holiday_name' => null,
                'holiday_type' => null,
                'overtime_code' => null
            ];
        }
    }

    private function checkHolidayRange($startDate, $endDate)
    {
        $holidays = [];

        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->startOfDay();

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $result = $this->checkHolidayDate($date->toDateString());

                if (!empty($result['is_holiday'])) {
                    $holidays[] = [
                        'date' => $date->toDateString(),
                        'date_label' => $date->format('d/m/Y'),
                        'holiday_name' => $result['holiday_name'] ?? null,
                        'holiday_type' => $result['holiday_type'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error checking holiday range: ' . $e->getMessage());
        }

        return $holidays;
    }

    /**
     * Get dashboard statistics
     * Count pending requests based on user's role and division:
     * - SPV (jabatan 5): hanya divisi yang SAMA
     * - HEAD (jabatan 4): hanya divisi yang SAMA
     * - MANAGER (jabatan 3): hanya divisi yang SAMA
     * - HR: semua divisi
     * - Karyawan: hanya request sendiri
     */
    private function getDashboardStats($user)
    {
        // dd('getDashboardStats');
        $approvalService = new ApprovalService();

        // Hitung pending per approval level menggunakan ApprovalService
        // Menggunakan logika yang SAMA dengan supervisorPending(), headPending(), dll
        // ApprovalService akan otomatis cek ApprovalSetting dan DivisiApprovalSetting (untuk absence)

        // SPV pending (untuk jabatan 5)
        // 1. Permohonan tukar shift divisi yang sama
        // 2. Permohonan tidak masuk kerja divisi yang sama (dicek di tb_divisi_approval_setting)
        // 3. Permohonan kendaraan dan inventaris divisi yang sama
        // PENTING: getPendingRequestsForUser sudah memfilter berdasarkan approval chain,
        // tapi perlu tambahan pengecekan canApproveRequest untuk memastikan user benar-benar bisa approve
        $spvPending = 0;
        if ((int) $user->jabatan === 5) {
            $allPending = ApprovalService::getPendingRequestsForUser($user, null, null);
            $approvalController = new HRApprovalController();
            $employeeRequestPending = $allPending->filter(function($request) use ($user, $approvalController) {
                // Get employee's divisi untuk validasi tambahan
                $employeeDivisi = null;
                if ($request->employee_id) {
                    $employee = \App\Models\User::find($request->employee_id);
                    $employeeDivisi = $employee ? $employee->divisi : null;
                }

                // Validasi: request harus EmployeeRequest, type shift_change/absence, status pending
                // Dan divisi employee harus sama dengan divisi SPV (untuk memastikan consistency)
                if (!($request instanceof EmployeeRequest
                    && in_array($request->request_type, ['shift_change', 'absence'])
                    && $request->status === EmployeeRequest::STATUS_PENDING
                    && $employeeDivisi == $user->divisi)) {
                    return false;
                }

                // PENTING: Cek apakah user benar-benar bisa approve request ini
                // Ini sama dengan logika di supervisorPending() yang menggunakan canApproveRequest()
                return $approvalController->canApproveRequest($user, $request);
            })->count();

            // Hitung VehicleAssetRequest (kendaraan dan inventaris) untuk SPV
            // PENTING: Mengikuti approval flow dari ApprovalSetting untuk 'vehicle_asset'
            $vehicleAssetFlow = ApprovalSetting::getApprovalFlow('vehicle_asset');
            $spvInVehicleFlow = false;
            $spvApprovalOrder = null;

            foreach ($vehicleAssetFlow as $setting) {
                if ($setting->role_key === 'spv_division' ||
                    ($setting->role_key === 'head_division' && (int) $user->jabatan === 5 && $setting->isUserAllowedToApprove($user))) {
                    $spvInVehicleFlow = true;
                    $spvApprovalOrder = $setting->approval_order;
                    break;
                }
            }

            $vehicleRequestPending = 0;
            $assetRequestPending = 0;

            if ($spvInVehicleFlow) {
                // Jika SPV ada di flow, cek apakah approval order sudah sampai ke SPV
                if ($spvApprovalOrder == 1) {
                    // SPV adalah approver pertama, query yang belum di-approve manager
                    $vehicleRequestPending = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
                        ->where('divisi_id', $user->divisi)
                        ->whereNull('manager_at')
                        ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER)
                        ->count();

                    $assetRequestPending = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
                        ->where('divisi_id', $user->divisi)
                        ->whereNull('manager_at')
                        ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER)
                        ->count();
                } else {
                    // SPV bukan approver pertama, perlu cek apakah approval sebelumnya sudah selesai
                    // Untuk VehicleAssetRequest, jika SPV di order > 1, berarti ada level sebelumnya
                    // Tapi karena VehicleAssetRequest hanya punya manager_at, mungkin SPV tidak pernah di order > 1
                    // Untuk sekarang, skip jika SPV bukan order 1
                    $vehicleRequestPending = 0;
                    $assetRequestPending = 0;
                }
            }

            // Hitung SPL pending untuk SPV
            $splPending = 0;
            try {
                $splApprovalService = new \App\Services\SplApprovalService();
                $splPendingRequests = $splApprovalService::getPendingRequestsForUser($user);
                $splPending = $splPendingRequests->count();
            } catch (\Exception $e) {
                \Log::error('Error counting SPL pending for SPV: ' . $e->getMessage());
            }

            $spvPending = $employeeRequestPending + $vehicleRequestPending + $assetRequestPending + $splPending;
        }

        // Head pending (untuk jabatan 4)
        // 1. Permohonan tukar shift divisi yang sama (yang sudah diapprove oleh SPV jika SPV ada di flow)
        // 2. Permohonan tidak masuk kerja divisi yang sama (dicek di tb_divisi_approval_setting)
        // EXCEPTION: Head Divisi Produksi (4) juga menghitung request dari Prepress (3)
        $headPending = 0;
        if ((int) $user->jabatan === 4) {
            // EXCEPTION: Head Divisi Produksi (4) juga menghitung request dari Prepress (3)
            $employeeIds = User::where('divisi', $user->divisi)->pluck('id');
            if ($user->divisi == 4) {
                $prepressEmployeeIds = User::where('divisi', 3)->pluck('id');
                $employeeIds = $employeeIds->merge($prepressEmployeeIds);
            }

            foreach (['shift_change', 'absence'] as $requestType) {
                // Untuk absence, gunakan divisi head untuk mendapatkan approval flow yang sesuai
                // EXCEPTION: Untuk Head Produksi (4), gunakan divisi 4 untuk mendapatkan approval flow (karena PREPRESS menggunakan alur PRODUKSI)
                $divisiParam = ($requestType === 'absence') ? $user->divisi : null;
                $flow = ApprovalSetting::getApprovalFlow($requestType, $divisiParam);

                // Get divisi approval setting untuk absence
                $divisiSetting = null;
                if ($requestType === 'absence') {
                    $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $user->divisi)
                        ->where('is_active', true)
                        ->first();
                }

                // Pastikan HEAD memang ada di flow
                $headSetting = null;
                foreach ($flow as $setting) {
                    if ($setting->role_key === 'head_division') {
                        $headSetting = $setting;
                        break;
                    }
                }
                if (!$headSetting) {
                    continue; // HEAD tidak ada di flow, skip request type ini
                }

                $order = $headSetting->approval_order;
                $query = EmployeeRequest::whereIn('employee_id', $employeeIds)
                    ->where('request_type', $requestType)
                    ->whereNull('head_approved_at')
                    ->whereNull('head_rejected_at')
                    // Exclude request yang sudah di-approve/reject oleh HRD atau sudah selesai
                    ->whereNull('hr_approved_at')
                    ->whereNull('hr_rejected_at')
                    ->whereNotIn('status', [
                        EmployeeRequest::STATUS_HR_APPROVED,
                        EmployeeRequest::STATUS_HR_REJECTED,
                        EmployeeRequest::STATUS_CANCELLED
                    ]);

                if ($order == 1) {
                    // Head adalah approver pertama, belum ada approval sebelumnya
                    $query->whereNull('supervisor_approved_at')
                          ->whereNull('supervisor_rejected_at');
                } else {
                    // Semua approver sebelum HEAD harus sudah approved
                    // PENTING: Hanya cek manager jika manager ada di flow SEBELUM HEAD DAN enabled (untuk absence)
                    $query->where(function($q) use ($flow, $order, $requestType, $divisiSetting) {
                        for ($i = 1; $i < $order; $i++) {
                            $prev = $flow->firstWhere('approval_order', $i);
                            if (!$prev) { continue; }

                            if ($prev->role_key === 'spv_division') {
                                $q->whereNotNull('supervisor_approved_at')
                                  ->whereNull('supervisor_rejected_at');
                            } elseif ($prev->role_key === 'manager') {
                                // Cek apakah manager enabled (untuk absence) atau ada di flow (untuk shift_change)
                                $managerEnabled = true;
                                if ($requestType === 'absence' && $divisiSetting) {
                                    $managerEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                                }

                                // Hanya cek manager_approved_at jika manager enabled/ada di flow
                                if ($managerEnabled) {
                                    // Manager enabled: pastikan manager sudah approve
                                    $q->whereNotNull('manager_approved_at')
                                      ->whereNull('manager_rejected_at');
                                } else {
                                    // Manager tidak enabled: pastikan manager_approved_at masih NULL
                                    // (karena manager tidak akan approve request yang tidak ada di chain-nya)
                                    $q->whereNull('manager_approved_at')
                                      ->whereNull('manager_rejected_at');
                                }
                            }
                        }
                    });
                }

                // Count dan tambahkan ke headPending
                $headPending += $query->count();
            }

            // Vehicle/Asset: HEAD DIVISI biasanya tidak handle vehicle/asset, tapi cek dulu apakah HEAD ada di flow
            $vehicleAssetFlow = ApprovalSetting::getApprovalFlow('vehicle_asset');
            $headInVehicleFlow = false;
            $headApprovalOrder = null;

            foreach ($vehicleAssetFlow as $setting) {
                if ($setting->role_key === 'head_division') {
                    $headInVehicleFlow = true;
                    $headApprovalOrder = $setting->approval_order;
                    break;
                }
            }

            if ($headInVehicleFlow) {
                // PENTING: Untuk HEAD PRODUKSI (jabatan 4, divisi 4), jangan hitung request dari HEAD PRODUKSI sendiri
                // Karena request dari HEAD PRODUKSI langsung ke General Manager, bukan ke HEAD biasa
                $headProduksiIds = [];
                if ((int) $user->jabatan === 4 && (int) $user->divisi === 4) {
                    $headProduksiIds = User::where('jabatan', 4)->where('divisi', 4)->pluck('id')->toArray();
                }

                if ($headApprovalOrder == 1) {
                    // HEAD adalah approver pertama untuk vehicle/asset
                    $vehicleQuery = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
                        ->where('divisi_id', $user->divisi)
                        ->whereNull('manager_at')
                        ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER);

                    $assetQuery = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
                        ->where('divisi_id', $user->divisi)
                        ->whereNull('manager_at')
                        ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER);

                    // Filter out request dari HEAD PRODUKSI jika user adalah HEAD PRODUKSI
                    if (!empty($headProduksiIds)) {
                        $vehicleQuery->whereNotIn('employee_id', $headProduksiIds);
                        $assetQuery->whereNotIn('employee_id', $headProduksiIds);
                    }

                    $vehicleRequestPending = $vehicleQuery->count();
                    $assetRequestPending = $assetQuery->count();

                    $headPending += $vehicleRequestPending + $assetRequestPending;
                } else {
                    // HEAD bukan approver pertama, cek apakah semua approver sebelumnya sudah approve
                    // Untuk vehicle/asset, biasanya order: 1 = Manager, 2 = HEAD (atau sebaliknya)
                    // Jika HEAD order > 1, berarti harus menunggu Manager approve dulu
                    $vehicleQuery = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
                        ->where('divisi_id', $user->divisi)
                        ->whereNotNull('manager_at')
                        ->whereNull('general_approved_at')
                        ->whereNull('general_rejected_at')
                        ->where('status', \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED);

                    $assetQuery = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
                        ->where('divisi_id', $user->divisi)
                        ->whereNotNull('manager_at')
                        ->whereNull('general_approved_at')
                        ->whereNull('general_rejected_at')
                        ->where('status', \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED);

                    // Filter out request dari HEAD PRODUKSI jika user adalah HEAD PRODUKSI
                    if (!empty($headProduksiIds)) {
                        $vehicleQuery->whereNotIn('employee_id', $headProduksiIds);
                        $assetQuery->whereNotIn('employee_id', $headProduksiIds);
                    }

                    $vehicleRequestPending = $vehicleQuery->count();
                    $assetRequestPending = $assetQuery->count();

                    $headPending += $vehicleRequestPending + $assetRequestPending;
                }
            }

            // Hitung SPL pending untuk HEAD
            // dd('head');
            $splPending = 0;
            try {
                $splApprovalService = new \App\Services\SplApprovalService();

                $splPendingRequests = $splApprovalService::getPendingRequestsForUser($user);
                // dd($splPendingRequests);
                // dd($splPendingRequests);
                $splPending = $splPendingRequests->count();
            } catch (\Exception $e) {
                \Log::error('Error counting SPL pending for HEAD: ' . $e->getMessage());
            }

            $headPending += $splPending;
        }

        // dd($headPending);

        // Manager pending (untuk jabatan 3)
        // 1. Permohonan tukar shift divisi yang sama (yang sudah diapprove oleh SPV/HEAD)
        // 2. Permohonan tidak masuk kerja divisi yang sama (yang sudah diapprove oleh urutan sebelum manager)
        // 3. Permohonan kendaraan dan inventaris divisi yang sama
        // Menggunakan query langsung seperti di managerPending() karena ApprovalService tidak menangani manager yang tidak ada di flow
        $managerPending = 0;
        if ((int) $user->jabatan === 3) {
            $employeeIds = User::where('divisi', $user->divisi)->pluck('id');
            $employeeRequestPending = 0;

            Log::debug('=== MANAGER PENDING COUNTER DEBUG ===');
            Log::debug('Manager Divisi: ' . $user->divisi);
            Log::debug('Manager Jabatan: ' . $user->jabatan);
            Log::debug('Employee IDs: ' . $employeeIds->implode(','));

            foreach (['shift_change', 'absence'] as $requestType) {
                // Untuk absence, gunakan divisi manager untuk mendapatkan approval flow yang sesuai
                $divisiParam = ($requestType === 'absence') ? $user->divisi : null;
                $flow = ApprovalSetting::getApprovalFlow($requestType, $divisiParam);
                // dd($flow);

                Log::debug("Processing {$requestType}");

                // Pastikan MANAGER memang ada di flow
                $managerSetting = null;

                if ($requestType === 'shift_change') {
                    // Untuk shift_change: manager juga bisa membaca role 'head_division'
                    foreach ($flow as $setting) {
                        if ($setting->role_key === 'manager' ||
                            ($setting->role_key === 'head_division' && $setting->isUserAllowedToApprove($user))) {
                            $managerSetting = $setting;
                            Log::debug("ManagerSetting found for {$requestType}: role_key={$managerSetting->role_key}, order={$managerSetting->approval_order}");
                            break;
                        }
                    }
                } else {
                    // Untuk absence: hanya membaca role 'manager' (logika lama)
                    foreach ($flow as $setting) {
                        if ($setting->role_key === 'manager') {
                            $managerSetting = $setting;
                            Log::debug("ManagerSetting found for {$requestType}: role_key={$managerSetting->role_key}, order={$managerSetting->approval_order}");
                            break;
                        }
                    }
                }

                if (!$managerSetting) {
                    Log::debug("No managerSetting found for {$requestType}, skipping");
                    continue;
                }

                $order = $managerSetting->approval_order;
                $query = EmployeeRequest::whereIn('employee_id', $employeeIds)
                    ->where('request_type', $requestType)
                    ->whereNull('manager_approved_at')
                    ->whereNull('manager_rejected_at');

                if ($order == 1) {
                    // Manager adalah approver pertama, semua level sebelum manager harus NULL
                    $query->whereNull('supervisor_approved_at')
                          ->whereNull('supervisor_rejected_at')
                          ->whereNull('head_approved_at')
                          ->whereNull('head_rejected_at');
                } else {
                    // Semua approver sebelum MANAGER harus sudah approved
                    $query->where(function($q) use ($flow, $order) {
                        for ($i = 1; $i < $order; $i++) {
                            $prev = $flow->firstWhere('approval_order', $i);
                            if (!$prev) { continue; }
                            if ($prev->role_key === 'spv_division') {
                                $q->whereNotNull('supervisor_approved_at')
                                  ->whereNull('supervisor_rejected_at');
                            } elseif ($prev->role_key === 'head_division') {
                                $q->whereNotNull('head_approved_at')
                                  ->whereNull('head_rejected_at');
                            }
                        }
                    });
                }

                // Get SQL query for debugging
                $sql = $query->toSql();
                $bindings = $query->getBindings();
                Log::debug("Query SQL for {$requestType}: " . $sql);
                Log::debug("Bindings: " . json_encode($bindings));

                $results = $query->get(['id', 'request_number', 'status', 'current_approval_order', 'supervisor_approved_at', 'head_approved_at', 'manager_approved_at']);
                $count = $results->count();
                $ids = $results->pluck('id')->implode(', ');
                $requestNumbers = $results->pluck('request_number')->implode(', ');

                Log::debug("Found {$count} requests for {$requestType}");
                Log::debug("Request IDs: {$ids}");
                Log::debug("Request Numbers: {$requestNumbers}");

                foreach ($results as $req) {
                    Log::debug("  - Request ID {$req->id} ({$req->request_number}): status={$req->status}, current_order={$req->current_approval_order}, spv_approved=" . ($req->supervisor_approved_at ?? 'NULL') . ", head_approved=" . ($req->head_approved_at ?? 'NULL') . ", manager_approved=" . ($req->manager_approved_at ?? 'NULL'));
                }

                $employeeRequestPending += $count;
            }

            \Log::debug("Total employee request pending: {$employeeRequestPending}");
            \Log::debug('=== END MANAGER PENDING COUNTER DEBUG ===');

            // Hitung VehicleAssetRequest (kendaraan dan inventaris) untuk Manager
            // Cek apakah MANAGER ada di approval flow untuk vehicle_asset
            $vehicleAssetFlow = ApprovalSetting::getApprovalFlow('vehicle_asset');
            $managerInVehicleFlow = false;

            foreach ($vehicleAssetFlow as $setting) {
                if ($setting->role_key === 'manager' ||
                    ($setting->role_key === 'head_division' && $setting->isUserAllowedToApprove($user))) {
                    $managerInVehicleFlow = true;
                    break;
                }
            }

            $vehicleRequestPending = 0;
            $assetRequestPending = 0;

            if ($managerInVehicleFlow) {
                $vehicleRequestPending = \App\Models\VehicleAssetRequest::where('request_type', 'vehicle')
                    ->where('divisi_id', $user->divisi)
                    ->whereNull('manager_at')
                    ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER)
                    ->count();

                $assetRequestPending = \App\Models\VehicleAssetRequest::where('request_type', 'asset')
                    ->where('divisi_id', $user->divisi)
                    ->whereNull('manager_at')
                    ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER)
                    ->count();
            }

            // Hitung SPL pending untuk Manager
            $splPending = 0;
            try {
                $splApprovalService = new \App\Services\SplApprovalService();
                $splPendingRequests = $splApprovalService::getPendingRequestsForUser($user);
                $splPending = $splPendingRequests->count();
            } catch (\Exception $e) {
                \Log::error('Error counting SPL pending for Manager: ' . $e->getMessage());
            }

            $managerPending = $employeeRequestPending + $vehicleRequestPending + $assetRequestPending + $splPending;
        }

        // General Manager pending (untuk divisi 13)
        $generalManagerPending = 0;
        if ($user->divisi == 13) {
            // Hitung EmployeeRequest (shift_change dan absence) yang memiliki general_id = user.id dan belum di-approve/reject
            $employeeRequestPending = EmployeeRequest::where('general_id', $user->id)
                ->whereNull('general_approved_at')
                ->whereNull('general_rejected_at')
                ->whereIn('request_type', ['shift_change', 'absence'])
                ->count();

            // Hitung VehicleAssetRequest (kendaraan dan inventaris) yang memiliki general_id = user.id dan belum di-approve/reject
            $vehicleRequestPending = \App\Models\VehicleAssetRequest::where('general_id', $user->id)
                ->whereNull('general_approved_at')
                ->whereNull('general_rejected_at')
                ->where('request_type', 'vehicle')
                ->count();

            $assetRequestPending = \App\Models\VehicleAssetRequest::where('general_id', $user->id)
                ->whereNull('general_approved_at')
                ->whereNull('general_rejected_at')
                ->where('request_type', 'asset')
                ->count();

            $generalManagerPending = $employeeRequestPending + $vehicleRequestPending + $assetRequestPending;
        }

        // HR pending (untuk HR)
        $hrPending = 0;
        if ($user->isHR()) {
            $allPending = ApprovalService::getPendingRequestsForUser($user, null, null);
            $hrPending = $allPending->filter(function($request) {
                return $request instanceof EmployeeRequest
                    && in_array($request->request_type, ['shift_change', 'absence'])
                    && $request->canBeApprovedByHR();
            })->count();

            // Tambahkan VehicleAssetRequest yang pending untuk HR
            // Cek apakah HR ada di approval flow untuk vehicle_asset
            $vehicleAssetFlow = \App\Models\ApprovalSetting::getApprovalFlow('vehicle_asset');
            $hrInVehicleFlow = false;

            foreach ($vehicleAssetFlow as $setting) {
                if ($setting->role_key === 'hr' && $setting->isUserAllowedToApprove($user)) {
                    $hrInVehicleFlow = true;
                    break;
                }
            }

            if ($hrInVehicleFlow) {
                // Hitung VehicleAssetRequest yang sudah manager_approved tapi belum hrga_approved
                // Status harus manager_approved dan hrga_at masih null (belum di-approve atau reject HRGA)
                $vehicleAssetPending = \App\Models\VehicleAssetRequest::where('status', \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED)
                    ->whereNull('hrga_at')
                    ->count();

                $hrPending += $vehicleAssetPending;
            }

            // Hitung SPL pending untuk HR
            // HR mengambil SPL yang sudah head_approved atau manager_approved dan belum di-approve HR
            $splPending = 0;
            try {
                $splApprovalService = new \App\Services\SplApprovalService();
                // Gunakan logika yang sama dengan hrPending() untuk konsistensi
                $splPendingRequests = \App\Models\SplRequest::with([
                    'supervisor',
                    'supervisor.divisiUser',
                    'divisi',
                    'employees',
                    'head',
                    'manager',
                    'hrd'
                ])->whereIn('status', [
                    \App\Models\SplRequest::STATUS_HEAD_APPROVED,
                    \App\Models\SplRequest::STATUS_MANAGER_APPROVED
                ])
                ->whereNull('hrd_approved_at')
                ->whereNull('hrd_rejected_at')
                ->orderBy('created_at', 'desc')
                ->get()
                ->filter(function ($spl) use ($user, $splApprovalService) {
                    // Filter menggunakan service untuk memastikan HR adalah approver berikutnya
                    $chain = $splApprovalService->getApprovalChain($spl);
                    $userId = $user->id;

                    foreach ($chain as $level => $approverData) {
                        $users = $approverData['users'] ?? collect();
                        $roleKeyInChain = $approverData['role_key'] ?? null;

                        if ($roleKeyInChain === 'hr' && $users->contains('id', $userId)) {
                            // Cek apakah level sebelumnya sudah di-approve
                            $previousLevelsApproved = true;
                            foreach ($chain as $prevLevel => $prevApproverData) {
                                if ($prevLevel === $level) {
                                    break;
                                }
                                $prevRoleKey = $prevApproverData['role_key'] ?? null;
                                $prevApproved = false;
                                if ($prevRoleKey === 'spv_division') {
                                    $prevApproved = true;
                                } elseif ($prevRoleKey === 'head_division') {
                                    // head_division bisa di-approve oleh HEAD atau MANAGER
                                    // Cek apakah HEAD atau MANAGER sudah approve
                                    $prevApproved = !is_null($spl->head_approved_at) || !is_null($spl->manager_approved_at);
                                } elseif ($prevRoleKey === 'manager') {
                                    $prevApproved = !is_null($spl->manager_approved_at);
                                }
                                if (!$prevApproved) {
                                    $previousLevelsApproved = false;
                                    break;
                                }
                            }
                            return $previousLevelsApproved &&
                                   is_null($spl->hrd_approved_at) &&
                                   is_null($spl->hrd_rejected_at);
                        }
                    }
                    return false;
                });

                $splPending = $splPendingRequests->count();
            } catch (\Exception $e) {
                \Log::error('Error counting SPL pending for HR: ' . $e->getMessage());
            }

            $hrPending += $splPending;
        }

        // Total pending untuk user ini (backward compatible)
        $totalPending = $spvPending + $headPending + $managerPending + $generalManagerPending + $hrPending;

        // Jika bukan approver, hitung manual untuk karyawan biasa
        if ($totalPending === 0 && !$user->isHR() && !in_array((int) $user->jabatan, [3, 4, 5])) {
            $totalPending = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
                ->where('employee_id', $user->id)
                ->where('status', EmployeeRequest::STATUS_PENDING)
                ->count();
        }

        // ============================================
        // COUNT PENGAJUAN (GLOBAL - Semua Divisi)
        // ============================================
        // Total requests GLOBAL (semua divisi, tidak terfilter)
        $totalRequests = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])->count();

        // Approved requests GLOBAL (semua divisi)
        $approvedRequests = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
            ->where('status', EmployeeRequest::STATUS_HR_APPROVED)
            ->count();

        // Rejected requests GLOBAL (semua divisi)
        $rejectedRequests = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
            ->whereIn('status', [
                EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
                EmployeeRequest::STATUS_MANAGER_REJECTED,
                EmployeeRequest::STATUS_HR_REJECTED
            ])
            ->count();

        // Pending requests GLOBAL (semua divisi, semua status pending)
        $pendingRequestsGlobal = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
            ->where('status', EmployeeRequest::STATUS_PENDING)
            ->count();

        return [
            // Count Pengajuan (GLOBAL - Semua Divisi)
            'total_requests' => $totalRequests,           // Global: semua pengajuan
            'pending_requests' => $pendingRequestsGlobal, // Global: semua pending (bukan hanya yang bisa di-approve user)
            'approved_requests' => $approvedRequests,     // Global: semua approved
            'rejected_requests' => $rejectedRequests,     // Global: semua rejected

            // Pending Approval (Per Divisi sesuai Role)
            'spv_pending' => $spvPending,       // Per divisi: pending untuk SPV (termasuk SPL)
            'head_pending' => $headPending,     // Per divisi: pending untuk HEAD (termasuk SPL)
            'manager_pending' => $managerPending, // Per divisi: pending untuk Manager (termasuk SPL)
            'general_manager_pending' => $generalManagerPending, // Global: pending untuk General Manager (divisi 13)
            'hr_pending' => $hrPending,         // Global: pending untuk HR (semua divisi, termasuk SPL)
        ];
    }



    // private function getDashboardStats($user)
    // {
    //     // Build base query for employee requests
    //     $query = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence']);

    //     // Filter based on user role
    //     if ($user->isHR()) {
    //         // HR sees requests that need HR approval
    //         $query->where('status', EmployeeRequest::STATUS_SUPERVISOR_APPROVED);
    //     } elseif ($user->jabatan == 5) {
    //         // SPV (jabatan 5) - hanya melihat request dari subordinates
    //         $query->where('supervisor_id', $user->id)
    //               ->where('status', EmployeeRequest::STATUS_PENDING);
    //     } elseif ($user->jabatan == 4) {
    //         // HEAD (jabatan 4) - melihat request yang butuh head approval
    //         $query->where(function($q) use ($user) {
    //             $q->where('head_id', $user->id)
    //               ->orWhere('divisi_id', $user->divisi);
    //         })->where('status', EmployeeRequest::STATUS_PENDING);
    //     } elseif ($user->jabatan == 3) {
    //         // Manager (jabatan 3) - melihat request yang butuh manager approval
    //         $query->where('manager_id', $user->id)
    //               ->where('status', EmployeeRequest::STATUS_PENDING);
    //     } else {
    //         // Regular employee - hanya melihat request sendiri
    //         $query->where('employee_id', $user->id);
    //     }

    //     // Count pending requests
    //     $totalPending = $query->count();

    //     // Count approved requests (based on role)
    //     if ($user->isHR()) {
    //         $approvedRequests = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
    //             ->where('status', EmployeeRequest::STATUS_HR_APPROVED)
    //             ->count();
    //     } elseif ($user->hasSupervisor()) {
    //         $approvedRequests = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
    //             ->where('supervisor_id', $user->id)
    //             ->where('status', EmployeeRequest::STATUS_SUPERVISOR_APPROVED)
    //             ->count();
    //     } else {
    //         $approvedRequests = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
    //             ->where('employee_id', $user->id)
    //             ->where('status', EmployeeRequest::STATUS_HR_APPROVED)
    //             ->count();
    //     }

    //     // Count rejected requests
    //     if ($user->isHR()) {
    //         $rejectedRequests = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
    //             ->where('status', EmployeeRequest::STATUS_HR_REJECTED)
    //             ->count();
    //     } elseif ($user->hasSupervisor()) {
    //         $rejectedRequests = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
    //             ->where('supervisor_id', $user->id)
    //             ->whereIn('status', [
    //                 EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
    //                 EmployeeRequest::STATUS_HR_REJECTED
    //             ])
    //             ->count();
    //     } else {
    //         $rejectedRequests = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
    //             ->where('employee_id', $user->id)
    //             ->whereIn('status', [
    //                 EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
    //                 EmployeeRequest::STATUS_HR_REJECTED
    //             ])
    //             ->count();
    //     }

    //     // Count total requests (based on role)
    //     if ($user->isHR()) {
    //         $totalRequests = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
    //             ->whereIn('status', [
    //                 EmployeeRequest::STATUS_PENDING,
    //                 EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
    //                 EmployeeRequest::STATUS_HR_APPROVED,
    //                 EmployeeRequest::STATUS_HR_REJECTED
    //             ])
    //             ->count();
    //     } elseif ($user->hasSupervisor()) {
    //         $totalRequests = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
    //             ->where('supervisor_id', $user->id)
    //             ->count();
    //     } else {
    //         $totalRequests = EmployeeRequest::whereIn('request_type', ['shift_change', 'absence'])
    //             ->where('employee_id', $user->id)
    //             ->count();
    //     }

    //     return [
    //         'total_requests' => $totalRequests,
    //         'pending_requests' => $totalPending,
    //         'approved_requests' => $approvedRequests,
    //         'rejected_requests' => $rejectedRequests
    //     ];
    // }

    /**
     * Display report for all HR requests (Form Karyawan, Lembur, Kendaraan, Inventaris)
     */
    public function report(Request $request)
    {
        $user = Auth::user();

        // dd($user);

        // Get all data from different modules
        $allData = collect();

        // dd($allData);

        // 1. Employee Requests (Form Karyawan)
        $employeeQuery = \App\Models\EmployeeRequest::query();

        // Get employee IDs from the same division for non-HR users
        $divisionEmployeeIds = [];
        if (!$user->isHR()) {
            $divisionEmployeeIds = \App\Models\User::where('divisi', $user->divisi)
                ->pluck('id')
                ->toArray();
        }

        // Filter employee requests: HR lihat semua, non-HR hanya divisi yang sama
        if ($user->isHR() || $user->divisi == 13) {
            // HR bisa lihat semua pengajuan
            $employeeQuery->whereIn('status', [
                \App\Models\EmployeeRequest::STATUS_PENDING,
                \App\Models\EmployeeRequest::STATUS_SUPERVISOR_APPROVED,
                \App\Models\EmployeeRequest::STATUS_SUPERVISOR_REJECTED,
                \App\Models\EmployeeRequest::STATUS_MANAGER_APPROVED,
                \App\Models\EmployeeRequest::STATUS_MANAGER_REJECTED,
                \App\Models\EmployeeRequest::STATUS_HR_APPROVED,
                \App\Models\EmployeeRequest::STATUS_HR_REJECTED
            ]);
        } else {
            // Non-HR hanya lihat pengajuan dari divisi yang sama
            if (!empty($divisionEmployeeIds)) {
                $employeeQuery->whereIn('employee_id', $divisionEmployeeIds);
            } else {
                // Jika tidak ada employee di divisi yang sama, return empty
                $employeeQuery->where('employee_id', 0);
            }
        }

        // Apply filters to employee requests
        if ($request->filled('status')) {
            $employeeQuery->where('status', $request->status);
        }
        if ($request->filled('employee_name')) {
            // Get employee IDs that match the name search
            $searchQuery = \App\Models\User::where('name', 'like', '%' . $request->employee_name . '%');

            // Jika bukan HR, filter juga berdasarkan divisi
            if (!$user->isHR() && !empty($divisionEmployeeIds)) {
                $searchQuery->whereIn('id', $divisionEmployeeIds);
            }

            $searchEmployeeIds = $searchQuery->pluck('id')->toArray();
            if (!empty($searchEmployeeIds)) {
                $employeeQuery->whereIn('employee_id', $searchEmployeeIds);
            } else {
                // If no employees found, return empty result
                $employeeQuery->where('employee_id', 0);
            }
        }
        if ($request->filled('date_from')) {
            $employeeQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $employeeQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // dd($request->all());

        // Filter by module (only show employee requests if form_karyawan is selected)
        $showEmployeeRequests = !$request->filled('request_type') || $request->request_type === 'form_karyawan';

        $employeeRequests = $showEmployeeRequests ? $employeeQuery->get()->map(function($request) {
            // dd($request);
            return [
                'id' => $request->id,
                'type' => 'form_karyawan',
                'type_label' => $request->request_type_label,
                'request_number' => $request->request_number,
                'employee_name' => $request->employee->name ?? 'N/A',
                'status' => $request->status,
                'status_label' => $request->status_label,
                'status_badge_class' => $request->status_badge_class,
                'created_at' => $request->created_at,
                'approved_at' => $request->hr_approved_at ?? $request->supervisor_approved_at ?? $request->supervisor_rejected_at ?? $request->hr_rejected_at,
                'notes' => $request->hr_notes ?? $request->supervisor_notes,
                'module' => 'Form Karyawan',
                'detail_route' => route('hr.requests.show', $request->id)
            ];
        }) : collect();

        // 2. Overtime Entries (Data Lembur)
        $overtimeQuery = \App\Models\OvertimeEntry::query();
        if ($user->isHR()) {
            // HR bisa lihat semua
            $overtimeQuery->whereIn('status', [
                \App\Models\OvertimeEntry::STATUS_PENDING_SPV,
                \App\Models\OvertimeEntry::STATUS_SPV_APPROVED,
                \App\Models\OvertimeEntry::STATUS_SPV_REJECTED,
                \App\Models\OvertimeEntry::STATUS_HEAD_APPROVED,
                \App\Models\OvertimeEntry::STATUS_HEAD_REJECTED,
                \App\Models\OvertimeEntry::STATUS_HRGA_APPROVED,
                \App\Models\OvertimeEntry::STATUS_HRGA_REJECTED
            ]);
        } else {
            // Non-HR hanya lihat dari divisi yang sama
            $overtimeQuery->where('divisi_id', $user->divisi);
        }

        // Apply filters to overtime entries
        if ($request->filled('status')) {
            $overtimeQuery->where('status', $request->status);
        }
        if ($request->filled('employee_name')) {
            $overtimeQuery->where('employee_name', 'like', '%' . $request->employee_name . '%');
        }
        if ($request->filled('date_from')) {
            $overtimeQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $overtimeQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by module (only show overtime if lembur is selected)
        $showOvertimeRequests = !$request->filled('request_type') || $request->request_type === 'lembur';

        $overtimeRequests = $showOvertimeRequests ? $overtimeQuery->get()->map(function($request) {
            $statusLabels = [
                \App\Models\OvertimeEntry::STATUS_PENDING_SPV => 'Pending SPV',
                \App\Models\OvertimeEntry::STATUS_SPV_APPROVED => 'Disetujui SPV',
                \App\Models\OvertimeEntry::STATUS_SPV_REJECTED => 'Ditolak SPV',
                \App\Models\OvertimeEntry::STATUS_HEAD_APPROVED => 'Disetujui Head',
                \App\Models\OvertimeEntry::STATUS_HEAD_REJECTED => 'Ditolak Head',
                \App\Models\OvertimeEntry::STATUS_HRGA_APPROVED => 'Disetujui HRGA',
                \App\Models\OvertimeEntry::STATUS_HRGA_REJECTED => 'Ditolak HRGA',
            ];

            $statusBadgeClasses = [
                \App\Models\OvertimeEntry::STATUS_PENDING_SPV => 'bg-warning',
                \App\Models\OvertimeEntry::STATUS_SPV_APPROVED => 'bg-info',
                \App\Models\OvertimeEntry::STATUS_SPV_REJECTED => 'bg-danger',
                \App\Models\OvertimeEntry::STATUS_HEAD_APPROVED => 'bg-success',
                \App\Models\OvertimeEntry::STATUS_HEAD_REJECTED => 'bg-danger',
                \App\Models\OvertimeEntry::STATUS_HRGA_APPROVED => 'bg-success',
                \App\Models\OvertimeEntry::STATUS_HRGA_REJECTED => 'bg-danger',
            ];

            return [
                'id' => $request->id,
                'type' => 'lembur',
                'type_label' => 'Data Lembur',
                'request_number' => 'SPL-' . str_pad($request->id, 4, '0', STR_PAD_LEFT),
                'employee_name' => $request->employee_name,
                'status' => $request->status,
                'status_label' => $statusLabels[$request->status] ?? $request->status,
                'status_badge_class' => $statusBadgeClasses[$request->status] ?? 'bg-secondary',
                'created_at' => $request->created_at,
                'approved_at' => $request->hrga_at ?? $request->head_at ?? $request->spv_at,
                'notes' => $request->hrga_notes ?? $request->head_notes ?? $request->spv_notes,
                'module' => 'Data Lembur',
                'detail_route' => route('hr.spl.show', $request->id)
            ];
        }) : collect();

        // 3. Vehicle Asset Requests (Kendaraan & Inventaris)
        $vehicleAssetQuery = \App\Models\VehicleAssetRequest::query();
        if ($user->isHR()) {
            // HR bisa lihat semua
            $vehicleAssetQuery->whereIn('status', [
                \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER,
                \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED,
                \App\Models\VehicleAssetRequest::STATUS_MANAGER_REJECTED,
                \App\Models\VehicleAssetRequest::STATUS_HRGA_APPROVED,
                \App\Models\VehicleAssetRequest::STATUS_HRGA_REJECTED
            ]);
        } else {
            // Non-HR hanya lihat dari divisi yang sama
            $vehicleAssetQuery->where('divisi_id', $user->divisi);
        }

        // Apply filters to vehicle asset requests
        if ($request->filled('status')) {
            $vehicleAssetQuery->where('status', $request->status);
        }
        if ($request->filled('employee_name')) {
            $vehicleAssetQuery->where('employee_name', 'like', '%' . $request->employee_name . '%');
        }
        if ($request->filled('date_from')) {
            $vehicleAssetQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $vehicleAssetQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by module (only show vehicle/asset if selected)
        $showVehicleAssetRequests = !$request->filled('request_type') ||
            $request->request_type === 'vehicle' ||
            $request->request_type === 'asset';

        if ($request->filled('request_type')) {
            if ($request->request_type === 'vehicle') {
                $vehicleAssetQuery->where('request_type', 'vehicle');
            } elseif ($request->request_type === 'asset') {
                $vehicleAssetQuery->where('request_type', 'asset');
            }
        }

        $vehicleAssetRequests = $showVehicleAssetRequests ? $vehicleAssetQuery->get()->map(function($request) {
            $statusLabels = [
                \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER => 'Pending Manager',
                \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED => 'Disetujui Manager',
                \App\Models\VehicleAssetRequest::STATUS_MANAGER_REJECTED => 'Ditolak Manager',
                \App\Models\VehicleAssetRequest::STATUS_HRGA_APPROVED => 'Disetujui HRGA',
                \App\Models\VehicleAssetRequest::STATUS_HRGA_REJECTED => 'Ditolak HRGA',
            ];

            $statusBadgeClasses = [
                \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER => 'bg-warning',
                \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED => 'bg-info',
                \App\Models\VehicleAssetRequest::STATUS_MANAGER_REJECTED => 'bg-danger',
                \App\Models\VehicleAssetRequest::STATUS_HRGA_APPROVED => 'bg-success',
                \App\Models\VehicleAssetRequest::STATUS_HRGA_REJECTED => 'bg-danger',
            ];

            return [
                'id' => $request->id,
                'type' => $request->request_type,
                'type_label' => $request->request_type === 'vehicle' ? 'Permintaan Kendaraan' : 'Permintaan Inventaris',
                'request_number' => ($request->request_type === 'vehicle' ? 'VH-' : 'AS-') . str_pad($request->id, 4, '0', STR_PAD_LEFT),
                'employee_name' => $request->employee_name,
                'status' => $request->status,
                'status_label' => $statusLabels[$request->status] ?? $request->status,
                'status_badge_class' => $statusBadgeClasses[$request->status] ?? 'bg-secondary',
                'created_at' => $request->created_at,
                'approved_at' => $request->hrga_at ?? $request->manager_at,
                'notes' => $request->hrga_notes ?? $request->manager_notes,
                'module' => $request->request_type === 'vehicle' ? 'Permintaan Kendaraan' : 'Permintaan Inventaris',
                'detail_route' => route('hr.vehicle-asset.show', ['id' => $request->id])
            ];
        }) : collect();

        // Combine all data
        $allData = $allData->merge($employeeRequests)->merge($overtimeRequests)->merge($vehicleAssetRequests);

        // Sort by created_at desc (put null values at the end)
        $allData = $allData->sortByDesc(function ($item) {
            return $item['created_at'] ?? '0';
        });

        // Get statistics
        $stats = [
            'total' => $allData->count(),
            'form_karyawan' => $allData->where('module', 'Form Karyawan')->count(),
            'lembur' => $allData->where('module', 'Data Lembur')->count(),
            'kendaraan' => $allData->where('module', 'Permintaan Kendaraan')->count(),
            'inventaris' => $allData->where('module', 'Permintaan Inventaris')->count(),
        ];

        // Get requests by module
        $requestsByModule = $allData->groupBy('module')->map(function ($group) {
            return $group->count();
        });

        // Get requests by status
        $requestsByStatus = $allData->groupBy('status')->map(function ($group) {
            return $group->count();
        });

        // Get requests by month
        $requestsByMonth = $allData->filter(function ($request) {
            return isset($request['created_at']) && $request['created_at'] !== null;
        })->groupBy(function ($request) {
            return $request['created_at']->format('Y-m');
        })->map(function ($group) {
            return $group->count();
        });

        // Get filter options
        $filterOptions = [
            'modules' => [
                'form_karyawan' => 'Form Karyawan',
                'lembur' => 'Data Lembur',
                'vehicle' => 'Permintaan Kendaraan',
                'asset' => 'Permintaan Inventaris',
            ],
            'request_types' => [
                'shift_change' => 'Tukar Shift',
                'absence' => 'Tidak Masuk Kerja',
                'lembur' => 'Data Lembur',
                'vehicle' => 'Permintaan Kendaraan',
                'asset' => 'Permintaan Inventaris',
            ],
            'statuses' => [
                'pending' => 'Pending',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                'supervisor_approved' => 'Disetujui Supervisor',
                'supervisor_rejected' => 'Ditolak Supervisor',
                'hr_approved' => 'Disetujui HR',
                'hr_rejected' => 'Ditolak HR',
                'spv_approved' => 'Disetujui SPV',
                'spv_rejected' => 'Ditolak SPV',
                'head_approved' => 'Disetujui Head',
                'head_rejected' => 'Ditolak Head',
                'hrga_approved' => 'Disetujui HRGA',
                'hrga_rejected' => 'Ditolak HRGA',
                'manager_approved' => 'Disetujui Manager',
                'manager_rejected' => 'Ditolak Manager',
            ],
        ];

        return view('hr.requests.report', compact(
            'allData',
            'stats',
            'requestsByModule',
            'requestsByStatus',
            'requestsByMonth',
            'filterOptions'
        ));
    }

    /**
     * Get calendar events for all HR requests
     */
    public function calendarEvents(Request $request)
    {
        $user = Auth::user();
        $events = [];

        Log::info('Calendar events requested by user: ' . $user->id);

        // 1. Employee Requests (Form Karyawan) - Absence & Shift Change
        // Global access - ALL users can see ALL employee requests
        $employeeQuery = \App\Models\EmployeeRequest::query();
        $employeeRequests = $employeeQuery->get();
        Log::info('Employee requests count: ' . $employeeRequests->count());

        // Log all requests for debugging
        foreach ($employeeRequests as $req) {
            Log::info('Request ID: ' . $req->id . ', Type: ' . $req->request_type . ', Status: ' . $req->status);
        }

        foreach ($employeeRequests as $request) {
            $requestData = $request->request_data;
            $employee = $request->employee;

            // Debug logging
            Log::info('Processing request ID: ' . $request->id . ', Type: ' . $request->request_type);
            Log::info('Request data: ', $requestData);

            if ($request->request_type === 'absence') {
                // Absence requests - handle date range (date_start to date_end)
                Log::info('Processing absence request ID: ' . $request->id);

                $dateStart = null;
                $dateEnd = null;

                // Check for date_start and date_end first (new format)
                if (isset($requestData['date_start'])) {
                    try {
                        $dateStart = Carbon::parse($requestData['date_start']);
                        Log::info('Found date_start: ' . $requestData['date_start']);
                    } catch (\Exception $e) {
                        Log::warning('Failed to parse date_start: ' . $e->getMessage());
                    }
                }

                if (isset($requestData['date_end'])) {
                    try {
                        $dateEnd = Carbon::parse($requestData['date_end']);
                        Log::info('Found date_end: ' . $requestData['date_end']);
                    } catch (\Exception $e) {
                        Log::warning('Failed to parse date_end: ' . $e->getMessage());
                    }
                }

                // Fallback to old format if date_start/date_end not found
                if (!$dateStart) {
                    if (isset($requestData['absence_date'])) {
                        $dateStart = Carbon::parse($requestData['absence_date']);
                        $dateEnd = $dateStart;
                    } elseif (isset($requestData['date'])) {
                        $dateStart = Carbon::parse($requestData['date']);
                        $dateEnd = $dateStart;
                    } elseif (isset($requestData['start_date'])) {
                        $dateStart = Carbon::parse($requestData['start_date']);
                        $dateEnd = $dateStart;
                    }
                }

                // If no dateEnd, use dateStart
                if ($dateStart && !$dateEnd) {
                    $dateEnd = $dateStart;
                }

                if ($dateStart && $dateEnd) {
                    // Iterate through all dates in the range
                    $currentDate = $dateStart->copy();
                    while ($currentDate->lte($dateEnd)) {
                        $events[] = [
                            'id' => 'absence_' . $request->id . '_' . $currentDate->format('Y-m-d'),
                            'start' => $currentDate->format('Y-m-d'),
                            'extendedProps' => [
                                'type' => 'absence',
                                'request_id' => $request->id,
                                'request_number' => $request->request_number
                            ]
                        ];
                        $currentDate->addDay();
                    }
                    Log::info('Created absence events for date range: ' . $dateStart->format('Y-m-d') . ' to ' . $dateEnd->format('Y-m-d'));
                } else {
                    Log::info('No valid date found for absence request ID: ' . $request->id);
                }
            } elseif ($request->request_type === 'shift_change') {
                // Shift change requests - show on the shift date
                // Handle different date fields based on scenario type
                $scenarioType = $requestData['scenario_type'] ?? 'exchange';
                $shiftDates = [];

                if ($scenarioType === 'holiday') {
                    // For holiday scenario, show both holiday work date and compensatory date
                    if (isset($requestData['holiday_work_date'])) {
                        try {
                            $shiftDates[] = Carbon::parse($requestData['holiday_work_date']);
                        } catch (\Exception $e) {
                            Log::warning('Failed to parse holiday_work_date: ' . $e->getMessage());
                        }
                    }
                    if (isset($requestData['compensatory_date'])) {
                        try {
                            $shiftDates[] = Carbon::parse($requestData['compensatory_date']);
                        } catch (\Exception $e) {
                            Log::warning('Failed to parse compensatory_date: ' . $e->getMessage());
                        }
                    }
                } else {
                    // For self and exchange scenarios, use the 'date' field
                    if (isset($requestData['date'])) {
                        try {
                            $shiftDates[] = Carbon::parse($requestData['date']);
                        } catch (\Exception $e) {
                            Log::warning('Failed to parse shift date: ' . $e->getMessage());
                        }
                    } elseif (isset($requestData['shift_date'])) {
                        try {
                            $shiftDates[] = Carbon::parse($requestData['shift_date']);
                        } catch (\Exception $e) {
                            Log::warning('Failed to parse shift_date: ' . $e->getMessage());
                        }
                    }
                }

                // Create events for each date found
                foreach ($shiftDates as $shiftDate) {
                    $events[] = [
                        'id' => 'shift_' . $request->id . '_' . $shiftDate->format('Y-m-d'),
                        'start' => $shiftDate->format('Y-m-d'),
                        'extendedProps' => [
                            'type' => 'shift_change',
                            'request_id' => $request->id,
                            'request_number' => $request->request_number,
                            'scenario_type' => $scenarioType
                        ]
                    ];
                }
            }
        }

        // 2. Overtime Entries (Data Lembur)
        // Global access - ALL users can see ALL overtime entries
        $overtimeQuery = \App\Models\OvertimeEntry::query();
        $overtimeEntries = $overtimeQuery->get();
        Log::info('Overtime entries count: ' . $overtimeEntries->count());

        foreach ($overtimeEntries as $entry) {
            $events[] = [
                'id' => 'overtime_' . $entry->id,
                'start' => $entry->request_date->format('Y-m-d'),
                'extendedProps' => [
                    'type' => 'overtime',
                    'request_id' => $entry->id
                ]
            ];
        }

        // 3. Vehicle Asset Requests (Kendaraan & Inventaris)
        // Global access - ALL users can see ALL vehicle/asset requests
        $vehicleAssetQuery = \App\Models\VehicleAssetRequest::query();
        $vehicleAssetRequests = $vehicleAssetQuery->get();
        Log::info('Vehicle asset requests count: ' . $vehicleAssetRequests->count());

        foreach ($vehicleAssetRequests as $request) {
            $startDate = $request->start_date;
            $endDate = $request->end_date ?? $startDate;

            // Handle date range for vehicle/asset requests
            $currentDate = Carbon::parse($startDate);
            $endDateCarbon = Carbon::parse($endDate);

            while ($currentDate->lte($endDateCarbon)) {
                $events[] = [
                    'id' => ($request->request_type === 'vehicle' ? 'vehicle_' : 'asset_') . $request->id . '_' . $currentDate->format('Y-m-d'),
                    'start' => $currentDate->format('Y-m-d'),
                    'extendedProps' => [
                        'type' => $request->request_type, // Will be normalized to 'inventory' in grouping
                        'request_id' => $request->id
                    ]
                ];
                $currentDate->addDay();
            }
        }

        Log::info('Total events generated: ' . count($events));
        Log::info('Events data: ', $events);

        // Group events by date and type, then count and collect details
        $groupedEvents = [];
        $eventDetails = []; // Store detailed info for each date+type combination

        foreach ($events as $event) {
            $date = $event['start'];
            $type = $event['extendedProps']['type'] ?? 'unknown';
            $requestId = $event['extendedProps']['request_id'] ?? null;

            // Normalize type names
            $typeKey = $type;
            if ($type === 'vehicle' || $type === 'asset') {
                $typeKey = 'inventory'; // Gabungkan vehicle dan asset jadi inventory
            }

            if (!isset($groupedEvents[$date])) {
                $groupedEvents[$date] = [
                    'absence' => 0,
                    'shift_change' => 0,
                    'overtime' => 0,
                    'inventory' => 0,
                ];
                $eventDetails[$date] = [
                    'absence' => [],
                    'shift_change' => [],
                    'overtime' => [],
                    'inventory' => [],
                ];
            }

            if (isset($groupedEvents[$date][$typeKey])) {
                $groupedEvents[$date][$typeKey]++;

                // Store request ID for fetching details later
                if ($requestId) {
                    $eventDetails[$date][$typeKey][] = $requestId;
                }
            }
        }

        // Convert grouped data to calendar events format
        $calendarEvents = [];
        foreach ($groupedEvents as $date => $counts) {
            // Create separate event for each type that has count > 0
            if ($counts['absence'] > 0) {
                // Remove duplicate request IDs
                $requestIds = array_unique($eventDetails[$date]['absence'] ?? []);
                $calendarEvents[] = [
                    'id' => 'absence_' . $date,
                    'title' => $counts['absence'],
                    'start' => $date,
                    'end' => $date,
                    'allDay' => true,
                    'display' => 'list-item',
                    'extendedProps' => [
                        'type' => 'absence',
                        'count' => $counts['absence'],
                        'date' => $date,
                        'request_ids' => array_values($requestIds) // Re-index array
                    ]
                ];
            }

            if ($counts['shift_change'] > 0) {
                $requestIds = array_unique($eventDetails[$date]['shift_change'] ?? []);
                $calendarEvents[] = [
                    'id' => 'shift_' . $date,
                    'title' => $counts['shift_change'],
                    'start' => $date,
                    'end' => $date,
                    'allDay' => true,
                    'display' => 'list-item',
                    'extendedProps' => [
                        'type' => 'shift_change',
                        'count' => $counts['shift_change'],
                        'date' => $date,
                        'request_ids' => array_values($requestIds)
                    ]
                ];
            }

            if ($counts['overtime'] > 0) {
                $requestIds = array_unique($eventDetails[$date]['overtime'] ?? []);
                $calendarEvents[] = [
                    'id' => 'overtime_' . $date,
                    'title' => $counts['overtime'],
                    'start' => $date,
                    'end' => $date,
                    'allDay' => true,
                    'display' => 'list-item',
                    'extendedProps' => [
                        'type' => 'overtime',
                        'count' => $counts['overtime'],
                        'date' => $date,
                        'request_ids' => array_values($requestIds)
                    ]
                ];
            }

            if ($counts['inventory'] > 0) {
                $requestIds = array_unique($eventDetails[$date]['inventory'] ?? []);
                $calendarEvents[] = [
                    'id' => 'inventory_' . $date,
                    'title' => $counts['inventory'],
                    'start' => $date,
                    'end' => $date,
                    'allDay' => true,
                    'display' => 'list-item',
                    'extendedProps' => [
                        'type' => 'inventory',
                        'count' => $counts['inventory'],
                        'date' => $date,
                        'request_ids' => array_values($requestIds)
                    ]
                ];
            }
        }

        return response()->json($calendarEvents);
    }

    /**
     * Get request details for calendar modal
     */
    public function getCalendarRequestDetails(Request $request)
    {
        $date = $request->input('date');
        $type = $request->input('type');
        $requestIdsInput = $request->input('request_ids', []);

        // Convert to array if it's a string (comma-separated)
        if (is_string($requestIdsInput)) {
            $requestIds = array_filter(array_map('trim', explode(',', $requestIdsInput)));
        } elseif (is_array($requestIdsInput)) {
            $requestIds = array_filter($requestIdsInput);
        } else {
            $requestIds = [];
        }

        if (!$date || !$type || empty($requestIds)) {
            return response()->json(['success' => false, 'message' => 'Invalid parameters']);
        }

        // Convert all IDs to integers
        $requestIds = array_map('intval', $requestIds);

        $details = [];

        try {
            if ($type === 'absence' || $type === 'shift_change') {
                // Employee Requests
                $employeeRequests = EmployeeRequest::whereIn('id', $requestIds)
                    ->with('employee:id,name,divisi')
                    ->get();

                foreach ($employeeRequests as $req) {
                    $requestData = $req->request_data;

                    // Build scenario-specific notes for shift change
                    if ($req->request_type === 'shift_change') {
                        $scenarioType = $requestData['scenario_type'] ?? 'exchange';
                        $scenarioLabels = [
                            'self' => 'Tukar Shift Diri Sendiri',
                            'exchange' => 'Tukar Shift dengan Rekan Kerja',
                            'holiday' => 'Tukar Shift karena Hari Merah'
                        ];
                        $scenarioLabel = $scenarioLabels[$scenarioType] ?? 'Tukar Shift';

                        $notes = "[{$scenarioLabel}] ";

                        if ($scenarioType === 'self') {
                            $originalTime = ($requestData['original_start_time'] ?? '') . '-' . ($requestData['original_end_time'] ?? '');
                            $newTime = ($requestData['new_start_time'] ?? '') . '-' . ($requestData['new_end_time'] ?? '');
                            $notes .= "Jam: {$originalTime} → {$newTime}";
                            if (isset($requestData['purpose'])) {
                                $notes .= ". Alasan: " . $requestData['purpose'];
                            }
                        } elseif ($scenarioType === 'exchange') {
                            $applicantTime = ($requestData['applicant_start_time'] ?? '') . '-' . ($requestData['applicant_end_time'] ?? '');
                            $substituteName = $requestData['substitute_name'] ?? '-';
                            $substituteTime = ($requestData['substitute_start_time'] ?? '') . '-' . ($requestData['substitute_end_time'] ?? '');
                            $notes .= "Pemohon: {$applicantTime}, Pengganti: {$substituteName} ({$substituteTime})";
                            if (isset($requestData['purpose'])) {
                                $notes .= ". Alasan: " . $requestData['purpose'];
                            }
                        } elseif ($scenarioType === 'holiday') {
                            $holidayDate = $requestData['holiday_work_date'] ?? '';
                            $compensatoryDate = $requestData['compensatory_date'] ?? '';
                            $workHours = $requestData['work_hours'] ?? '';
                            $notes .= "Kerja: {$holidayDate}, OFF: {$compensatoryDate}, Jam: {$workHours}";
                            if (isset($requestData['purpose'])) {
                                $notes .= ". Alasan: " . $requestData['purpose'];
                            }
                        }
                    } else {
                        // For absence types, use the existing logic
                        $notes = $requestData['purpose'] ?? $requestData['reason'] ?? $requestData['description'] ?? '-';
                    }

                    // Get divisi name if available
                    $divisiName = $req->employee->divisi ?? '-';
                    if ($req->employee && $req->employee->divisiUser) {
                        $divisiName = $req->employee->divisiUser->divisi ??
                                     $req->employee->divisiUser->nama_divisi ??
                                     'Divisi ' . ($req->employee->divisi ?? '-');
                    } elseif ($req->employee && $req->employee->divisi) {
                        $divisiName = 'Divisi ' . $req->employee->divisi;
                    }

                    // Get status label from model (menggunakan approval flow)
                    $statusLabel = $req->status_label;

                    // KASUS KHUSUS: Untuk tukar shift, jika di approval flow ada head_division tapi head tidak ada,
                    // dan sudah masuk ke manager, gabungkan label menjadi "Menunggu Approval HEAD/MANAGER"
                    if ($req->request_type === 'shift_change' &&
                        str_contains(strtolower($statusLabel), 'menunggu approval')) {

                        // Get approval flow untuk shift_change
                        $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow('shift_change');

                        // Cek apakah ada head_division di flow
                        $headInFlow = false;
                        $managerInFlow = false;

                        foreach ($approvalFlow as $setting) {
                            if ($setting->role_key === 'head_division') {
                                $headInFlow = true;
                            }
                            if ($setting->role_key === 'manager') {
                                $managerInFlow = true;
                            }
                        }

                        // Jika head ada di flow dan manager juga ada
                        if ($headInFlow && $managerInFlow) {
                            // Cek apakah ada user dengan jabatan HEAD (4) untuk divisi tersebut
                            $employeeDivisi = $req->employee->divisi ?? null;
                            $hasHeadUser = false;

                            if ($employeeDivisi) {
                                $hasHeadUser = \App\Models\User::where('divisi', $employeeDivisi)
                                    ->where('jabatan', 4) // HEAD
                                    ->exists();
                            }

                            // Jika head tidak ada user-nya dan status label mengandung "HEAD DIVISI" atau "MANAGER"
                            if (!$hasHeadUser &&
                                (str_contains(strtolower($statusLabel), 'head divisi') ||
                                 str_contains(strtolower($statusLabel), 'manager'))) {

                                // Gabungkan label menjadi "Menunggu Approval HEAD/MANAGER"
                                $statusLabel = 'Menunggu Approval HEAD/MANAGER';
                            }
                        }
                    }

                    $details[] = [
                        'id' => $req->id,
                        'request_number' => $req->request_number,
                        'employee_name' => $req->employee->name ?? 'N/A',
                        'employee_divisi' => $divisiName,
                        'status' => $req->status,
                        'status_label' => $statusLabel,
                        'notes' => $notes,
                    ];
                }
            } elseif ($type === 'overtime') {
                // Overtime Entries
                $overtimeEntries = \App\Models\OvertimeEntry::whereIn('id', $requestIds)
                    ->get();

                foreach ($overtimeEntries as $entry) {
                    // Determine status label for OvertimeEntry
                    $statusLabel = $entry->status;
                    $statusLabels = [
                        \App\Models\OvertimeEntry::STATUS_PENDING_SPV => 'Menunggu Approval SPV',
                        \App\Models\OvertimeEntry::STATUS_SPV_APPROVED => 'Disetujui SPV',
                        \App\Models\OvertimeEntry::STATUS_SPV_REJECTED => 'Ditolak SPV',
                        \App\Models\OvertimeEntry::STATUS_HEAD_APPROVED => 'Disetujui HEAD DIVISI',
                        \App\Models\OvertimeEntry::STATUS_HEAD_REJECTED => 'Ditolak HEAD DIVISI',
                        \App\Models\OvertimeEntry::STATUS_HRGA_APPROVED => 'Disetujui HRGA',
                        \App\Models\OvertimeEntry::STATUS_HRGA_REJECTED => 'Ditolak HRGA',
                    ];

                    if (isset($statusLabels[$entry->status])) {
                        $statusLabel = $statusLabels[$entry->status];
                    }

                    $details[] = [
                        'id' => $entry->id,
                        'request_number' => 'SPL-' . str_pad($entry->id, 4, '0', STR_PAD_LEFT),
                        'employee_name' => $entry->employee_name,
                        'employee_divisi' => $entry->divisi ?? '-',
                        'status' => $entry->status,
                        'status_label' => $statusLabel,
                        'notes' => $entry->job_description ?? '-',
                    ];
                }
            } elseif ($type === 'inventory') {
                // Vehicle/Asset Requests
                $vehicleAssetRequests = \App\Models\VehicleAssetRequest::whereIn('id', $requestIds)
                    ->get();

                foreach ($vehicleAssetRequests as $req) {
                    // Determine status label for VehicleAssetRequest
                    $statusLabel = $req->status;
                    $statusLabels = [
                        \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER => 'Menunggu Approval MANAGER',
                        \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED => 'Disetujui MANAGER',
                        \App\Models\VehicleAssetRequest::STATUS_MANAGER_REJECTED => 'Ditolak MANAGER',
                        \App\Models\VehicleAssetRequest::STATUS_HRGA_APPROVED => 'Disetujui HRGA',
                        \App\Models\VehicleAssetRequest::STATUS_HRGA_REJECTED => 'Ditolak HRGA',
                    ];

                    // Check if General Manager is involved (priority check)
                    if (!is_null($req->general_approved_at)) {
                        // General Manager sudah approve, cek HRGA
                        if (!is_null($req->hrga_at)) {
                            if (!is_null($req->hrga_approved_at)) {
                                $statusLabel = 'Disetujui HRGA';
                            } else {
                                $statusLabel = 'Ditolak HRGA';
                            }
                        } else {
                            $statusLabel = 'Menunggu Approval HRGA';
                        }
                    } elseif (!is_null($req->general_rejected_at)) {
                        // General Manager sudah reject
                        $statusLabel = 'Ditolak GENERAL MANAGER';
                    } elseif (!is_null($req->general_id) && is_null($req->general_approved_at) && is_null($req->general_rejected_at)) {
                        // General Manager ditunjuk tapi belum approve/reject
                        $statusLabel = 'Menunggu Approval GENERAL MANAGER';
                    } elseif (isset($statusLabels[$req->status])) {
                        // Use predefined status label
                        $statusLabel = $statusLabels[$req->status];
                    }

                    $details[] = [
                        'id' => $req->id,
                        'request_number' => ($req->request_type === 'vehicle' ? 'VH-' : 'AS-') . str_pad($req->id, 4, '0', STR_PAD_LEFT),
                        'employee_name' => $req->employee_name,
                        'employee_divisi' => $req->divisi ?? '-',
                        'status' => $req->status,
                        'status_label' => $statusLabel,
                        'notes' => $req->notes ?? $req->destination ?? '-',
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error fetching calendar request details: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error fetching details']);
        }

        return response()->json(['success' => true, 'data' => $details]);
    }

    /**
     * Show the form for creating a new request
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'shift_change');

        // Redirect overtime requests to new overtime module
        if ($type === 'overtime') {
            return redirect()->route('hr.overtime.create');
        }

        // Redirect vehicle/asset requests to new vehicle-asset module
        if ($type === 'vehicle' || $type === 'asset') {
            return redirect()->route('hr.vehicle-asset.create', ['type' => $type]);
        }

        // Get reference data based on request type
        $data = $this->getReferenceData($request);

        return view('hr.requests.create', compact('type', 'data'));
    }

    /**
     * Store a newly created request
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        try {
            $validatedData = $this->validateRequestData($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error validating request data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat validasi: ' . $e->getMessage()
            ], 500);
        }

        // Check if date is a holiday for shift_change requests
        if ($validatedData['request_type'] === 'shift_change') {
            $scenarioType = $validatedData['scenario_type'] ?? 'exchange';

            // Auto-convert 00:00 to 23:59 for all end_time fields
            $endTimeFields = [
                'original_end_time',
                'new_end_time',
                'applicant_end_time',
                'substitute_end_time'
            ];

            foreach ($endTimeFields as $field) {
                if (isset($validatedData[$field]) && $validatedData[$field] === '00:00') {
                    $validatedData[$field] = '23:59';
                    // Also update in request_data if it exists
                    if (isset($validatedData['request_data']) && isset($validatedData['request_data'][$field])) {
                        $validatedData['request_data'][$field] = '23:59';
                    }
                }
            }

            // Update request_data after conversion (in case request_data is created later)
            if (isset($validatedData['request_data'])) {
                foreach ($endTimeFields as $field) {
                    if (isset($validatedData['request_data'][$field]) && $validatedData['request_data'][$field] === '00:00') {
                        $validatedData['request_data'][$field] = '23:59';
                    }
                }
            }

            // Only check for holiday in self and exchange scenarios
            // For holiday scenario, user is intentionally working on holiday, so skip the check
            if ($scenarioType !== 'holiday') {
                $dateToCheck = $validatedData['date'] ?? null;

                if ($dateToCheck) {
                    $holidayCheck = $this->checkHolidayDate($dateToCheck);
                    if ($holidayCheck['is_holiday']) {
                        $holidayType = $holidayCheck['holiday_type'];
                        $holidayName = $holidayCheck['holiday_name'];

                        // Customize message based on holiday type
                        if ($holidayType === 'weekend') {
                            $message = "Tanggal yang dipilih adalah {$holidayName}. Apakah Anda ingin melanjutkan pengajuan tukar shift?";
                        } else {
                            $message = "Tanggal yang dipilih adalah hari libur: {$holidayName}. Apakah Anda ingin melanjutkan pengajuan tukar shift?";
                        }

                        return response()->json([
                            'success' => false,
                            'is_holiday' => true,
                            'holiday_name' => $holidayName,
                            'holiday_type' => $holidayType,
                            'message' => $message
                        ]);
                    }
                }
            }
        }

        if ($validatedData['request_type'] === 'absence') {
            $dateStart = $validatedData['request_data']['date_start'] ?? null;
            $dateEnd = $validatedData['request_data']['date_end'] ?? null;

            if ($dateStart && $dateEnd) {
                $holidayDates = $this->checkHolidayRange($dateStart, $dateEnd);

                if (!empty($holidayDates)) {
                    $holidayDescriptions = collect($holidayDates)->map(function ($item) {
                        $dateLabel = $item['date_label'] ?? Carbon::parse($item['date'])->format('d/m/Y');
                        $name = $item['holiday_name'] ?? 'Hari Libur';
                        return $dateLabel . ' (' . $name . ')';
                    })->implode(', ');

                    $message = "Rentang tanggal yang dipilih mengandung hari libur: {$holidayDescriptions}. Apakah Anda ingin melanjutkan pengajuan tidak masuk kerja?";

                    return response()->json([
                        'success' => false,
                        'is_holiday' => true,
                        'holiday_type' => 'absence_range',
                        'holiday_dates' => $holidayDates,
                        'message' => $message
                    ]);
                }
            }
        }

        DB::connection('pgsql2')->beginTransaction();

        try {
            // Update request_data with converted 00:00 to 23:59 for shift_change
            if ($validatedData['request_type'] === 'shift_change' && isset($validatedData['request_data'])) {
                $endTimeFields = [
                    'original_end_time',
                    'new_end_time',
                    'applicant_end_time',
                    'substitute_end_time'
                ];

                foreach ($endTimeFields as $field) {
                    if (isset($validatedData['request_data'][$field]) && $validatedData['request_data'][$field] === '00:00') {
                        $validatedData['request_data'][$field] = '23:59';
                    }
                }
            }

            // Prepare replacement person data for shift change
            $replacementPersonId = null;
            if ($validatedData['request_type'] === EmployeeRequest::TYPE_SHIFT_CHANGE && isset($validatedData['substitute_id'])) {
                $replacementPersonId = $validatedData['substitute_id'];
            }

            // Create main request
            $employeeRequest = EmployeeRequest::create([
                'request_type' => $validatedData['request_type'],
                'employee_id' => $user->id,
                'supervisor_id' => $user->supervisor_id,
                'request_data' => $validatedData['request_data'],
                'notes' => $validatedData['notes'] ?? null,
                'attachment_path' => $validatedData['attachment_path'] ?? null,
                'replacement_person_id' => $replacementPersonId,
                'status' => EmployeeRequest::STATUS_PENDING
            ]);

            // Initialize approval chain using new division-based system
            $approvalService = new ApprovalService();
            $approvalService->initializeApprovals($employeeRequest);

            // KHUSUS UNTUK MANAGER: Set approver ke General Manager (divisi 13) untuk semua perizinan
            if ((int) $user->jabatan === 3) {
                $this->handleManagerRequestApproval($employeeRequest, $user);
            }

            // KHUSUS UNTUK HEAD PRODUKSI: Set approver ke General Manager (divisi 13) untuk semua perizinan
            // Karena Manager Produksi tidak ada, approval langsung ke General Manager
            if ((int) $user->jabatan === 4 && (int) $user->divisi === 4) {
                $this->handleHeadProduksiRequestApproval($employeeRequest, $user);
            }

            // Auto-approval untuk SPV (jabatan 5) - berlaku untuk semua jenis request
            if ((int) $user->jabatan === 5) {
                $this->handleAutoApprovalForSPV($employeeRequest, $user, $approvalService);
            }

            // Auto-approval untuk HEAD (jabatan 4) - berlaku untuk semua jenis request
            // EXCEPTION: HEAD PRODUKSI (divisi 4) tidak auto-approve karena approval langsung ke General Manager
            if ((int) $user->jabatan === 4 && (int) $user->divisi !== 4) {
                $this->handleAutoApprovalForHEAD($employeeRequest, $user, $approvalService);
            }

            // Auto-approval untuk MANAGER (jabatan 3) - berlaku untuk semua jenis request
            // CATATAN: Jika MANAGER perlu General Manager approve dulu, ini akan di-handle oleh handleManagerRequestApproval()
            // Tapi jika MANAGER adalah approver di flow (bukan perlu General Manager), maka auto-approve
            if ((int) $user->jabatan === 3) {
                $this->handleAutoApprovalForManager($employeeRequest, $user, $approvalService);
            }

            // Auto-approval untuk HRD (divisi 7 atau is_hr = true) - berlaku untuk semua jenis request
            if ($user->isHR()) {
                $this->handleAutoApprovalForHRD($employeeRequest, $user, $approvalService);
            }

            // Auto-approval untuk absence request berdasarkan jabatan pembuat (untuk HEAD jabatan 4)
            // CATATAN: handleAutoApprovalForAbsence() masih digunakan untuk logika khusus absence yang sudah ada
            if ($validatedData['request_type'] === 'absence') {
                $this->handleAutoApprovalForAbsence($employeeRequest, $user, $approvalService);
            }

            // Store overtime employees if request type is overtime
            if ($validatedData['request_type'] === EmployeeRequest::TYPE_OVERTIME && isset($validatedData['overtime_employees'])) {
                foreach ($validatedData['overtime_employees'] as $employeeData) {
                    \App\Models\OvertimeEmployee::create([
                        'request_id' => $employeeRequest->id,
                        'employee_id' => $employeeData['employee_id'],
                        'employee_name' => $employeeData['employee_name'],
                        'department' => $employeeData['department'],
                        'start_time' => $employeeData['start_time'],
                        'end_time' => $employeeData['end_time'],
                        'job_description' => $employeeData['job_description'],
                        'is_signed' => false,
                        'signed_at' => null
                    ]);
                }
            }

            // Send notifications to first approvers
            try {
                $notificationService = new \App\Services\EmployeeRequestNotificationService($approvalService);
                $notificationService->notifyFirstApprovers($employeeRequest);
            } catch (\Exception $e) {
                \Log::error('Failed to send notification to first approvers', [
                    'request_id' => $employeeRequest->id,
                    'error' => $e->getMessage()
                ]);
                // Continue with the process even if notification fails
            }

            DB::connection('pgsql2')->commit();

            return response()->json([
                'success' => true,
                'redirect' => route('hr.requests.show', $employeeRequest->id),
                'message' => 'Pengajuan berhasil dibuat dengan nomor: ' . $employeeRequest->request_number
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::connection('pgsql2')->rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store request with holiday confirmation
     */
    public function storeWithConfirmation(Request $request)
    {
        $user = Auth::user();

        try {
            $validatedData = $this->validateRequestData($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error validating request data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat validasi: ' . $e->getMessage()
            ], 500);
        }

        DB::connection('pgsql2')->beginTransaction();

        try {
            // Update request_data with converted 00:00 to 23:59 for shift_change
            if ($validatedData['request_type'] === 'shift_change' && isset($validatedData['request_data'])) {
                $endTimeFields = [
                    'original_end_time',
                    'new_end_time',
                    'applicant_end_time',
                    'substitute_end_time'
                ];

                foreach ($endTimeFields as $field) {
                    if (isset($validatedData['request_data'][$field]) && $validatedData['request_data'][$field] === '00:00') {
                        $validatedData['request_data'][$field] = '23:59';
                    }
                }
            }

            // Prepare replacement person data for shift change
            $replacementPersonId = null;
            if ($validatedData['request_type'] === EmployeeRequest::TYPE_SHIFT_CHANGE && isset($validatedData['substitute_id'])) {
                $replacementPersonId = $validatedData['substitute_id'];
            }

            // Create main request
            $employeeRequest = EmployeeRequest::create([
                'request_type' => $validatedData['request_type'],
                'employee_id' => $user->id,
                'supervisor_id' => $user->supervisor_id,
                'request_data' => $validatedData['request_data'],
                'notes' => $validatedData['notes'] ?? null,
                'attachment_path' => $validatedData['attachment_path'] ?? null,
                'replacement_person_id' => $replacementPersonId,
                'status' => EmployeeRequest::STATUS_PENDING
            ]);

            // Initialize approval chain using new division-based system
            $approvalService = new ApprovalService();
            $approvalService->initializeApprovals($employeeRequest);

            // KHUSUS UNTUK MANAGER: Set approver ke General Manager (divisi 13) untuk semua perizinan
            if ((int) $user->jabatan === 3) {
                $this->handleManagerRequestApproval($employeeRequest, $user);
            }

            // KHUSUS UNTUK HEAD PRODUKSI: Set approver ke General Manager (divisi 13) untuk semua perizinan
            // Karena Manager Produksi tidak ada, approval langsung ke General Manager
            if ((int) $user->jabatan === 4 && (int) $user->divisi === 4) {
                $this->handleHeadProduksiRequestApproval($employeeRequest, $user);
            }

            // Auto-approval untuk SPV (jabatan 5) - berlaku untuk semua jenis request
            if ((int) $user->jabatan === 5) {
                $this->handleAutoApprovalForSPV($employeeRequest, $user, $approvalService);
            }

            // Auto-approval untuk HEAD (jabatan 4) - berlaku untuk semua jenis request
            // EXCEPTION: HEAD PRODUKSI (divisi 4) tidak auto-approve karena approval langsung ke General Manager
            if ((int) $user->jabatan === 4 && (int) $user->divisi !== 4) {
                $this->handleAutoApprovalForHEAD($employeeRequest, $user, $approvalService);
            }

            // Auto-approval untuk MANAGER (jabatan 3) - berlaku untuk semua jenis request
            // CATATAN: Jika MANAGER perlu General Manager approve dulu, ini akan di-handle oleh handleManagerRequestApproval()
            // Tapi jika MANAGER adalah approver di flow (bukan perlu General Manager), maka auto-approve
            if ((int) $user->jabatan === 3) {
                $this->handleAutoApprovalForManager($employeeRequest, $user, $approvalService);
            }

            // Auto-approval untuk HRD (divisi 7 atau is_hr = true) - berlaku untuk semua jenis request
            if ($user->isHR()) {
                $this->handleAutoApprovalForHRD($employeeRequest, $user, $approvalService);
            }

            // Auto-approval untuk absence request berdasarkan jabatan pembuat (untuk HEAD jabatan 4)
            // CATATAN: handleAutoApprovalForAbsence() masih digunakan untuk logika khusus absence yang sudah ada
            if ($validatedData['request_type'] === 'absence') {
                $this->handleAutoApprovalForAbsence($employeeRequest, $user, $approvalService);
            }

            // Store overtime employees if request type is overtime
            if ($validatedData['request_type'] === EmployeeRequest::TYPE_OVERTIME && isset($validatedData['overtime_employees'])) {
                foreach ($validatedData['overtime_employees'] as $employeeData) {
                    \App\Models\OvertimeEmployee::create([
                        'request_id' => $employeeRequest->id,
                        'employee_id' => $employeeData['employee_id'],
                        'employee_name' => $employeeData['employee_name'],
                        'department' => $employeeData['department'],
                        'start_time' => $employeeData['start_time'],
                        'end_time' => $employeeData['end_time'],
                        'job_description' => $employeeData['job_description'],
                        'is_signed' => false,
                        'signed_at' => null
                    ]);
                }
            }

            // Send notifications to first approvers
            try {
                $notificationService = new \App\Services\EmployeeRequestNotificationService($approvalService);
                $notificationService->notifyFirstApprovers($employeeRequest);
            } catch (\Exception $e) {
                \Log::error('Failed to send notification to first approvers', [
                    'request_id' => $employeeRequest->id,
                    'error' => $e->getMessage()
                ]);
                // Continue with the process even if notification fails
            }

            DB::connection('pgsql2')->commit();

            return response()->json([
                'success' => true,
                'redirect' => route('hr.requests.show', $employeeRequest->id),
                'message' => 'Pengajuan berhasil dibuat dengan nomor: ' . $employeeRequest->request_number
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::connection('pgsql2')->rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle auto-approval untuk absence request berdasarkan jabatan pembuat
     * - Supervisor (jabatan 5): auto approve untuk dirinya sendiri
     * - Head (jabatan 4): auto approve untuk dirinya sendiri
     * - Manager (jabatan 3): set approver dengan divisi 13 (hardcode)
     */
    private function handleAutoApprovalForAbsence(EmployeeRequest $request, User $user, ApprovalService $approvalService)
    {
        $jabatan = (int) $user->jabatan;
        $divisi = (int) $user->divisi;
        $updateData = [];

        // Supervisor (jabatan 5): auto approve untuk dirinya sendiri
        // Berlaku untuk semua divisi, termasuk PREPRESS
        if ($jabatan === 5) {
            $updateData['supervisor_id'] = $user->id;
            $updateData['supervisor_approved_at'] = now();
            $updateData['supervisor_notes'] = 'Auto-approved oleh supervisor';

            // KHUSUS DIVISI PREPRESS (3): Auto-approve SPV untuk semua pengajuan dari divisi PREPRESS (bukan hanya SPV sendiri)
            if ($divisi === 3) {
                // Jika user yang submit adalah SPV, sudah di-handle di atas
                // Jika user bukan SPV, cari SPV dari divisi PREPRESS dan auto-approve
                if ($request->employee_id !== $user->id) {
                    $spvPrepress = User::on('pgsql')
                        ->where('divisi', 3)
                        ->where('jabatan', 5)
                        ->first();

                    if ($spvPrepress) {
                        $updateData['supervisor_id'] = $spvPrepress->id;
                        $updateData['supervisor_approved_at'] = now();
                        $updateData['supervisor_notes'] = 'Auto-approved oleh supervisor (Divisi PREPRESS)';
                    }
                }
            }
        }
        // Head (jabatan 4): auto approve untuk dirinya sendiri
        // EXCEPTION: HEAD PRODUKSI (divisi 4) tidak auto-approve karena approval langsung ke General Manager
        elseif ($jabatan === 4 && $divisi !== 4) {
            $updateData['head_id'] = $user->id;
            $updateData['head_approved_at'] = now();
            $updateData['head_notes'] = 'Auto-approved oleh head';
        }
        // Manager (jabatan 3): tidak perlu set approver di sini karena sudah di-handle oleh handleManagerRequestApproval()
        // Logika untuk MANAGER sudah dipindah ke handleManagerRequestApproval() agar berlaku untuk semua jenis perizinan

        // Update request jika ada perubahan
        if (!empty($updateData)) {
            $request->update($updateData);

            // Refresh request untuk mendapatkan data terbaru
            $request->refresh();

            // Update status request setelah auto-approval (hanya jika ada approval, bukan hanya set approver)
            if (isset($updateData['supervisor_approved_at']) || isset($updateData['head_approved_at'])) {
                $approvalService->updateRequestStatus($request);

                // Pastikan status di-update dengan benar
                $request->refresh();

                \Log::info('Auto-approval untuk absence request - Status updated', [
                    'request_id' => $request->id,
                    'employee_id' => $user->id,
                    'jabatan' => $jabatan,
                    'divisi' => $divisi,
                    'status' => $request->status,
                    'supervisor_approved_at' => $request->supervisor_approved_at,
                    'head_approved_at' => $request->head_approved_at,
                    'update_data' => $updateData
                ]);
            }

            \Log::info('Auto-approval untuk absence request', [
                'request_id' => $request->id,
                'employee_id' => $user->id,
                'jabatan' => $jabatan,
                'divisi' => $divisi,
                'update_data' => $updateData
            ]);
        }
    }

    /**
     * Handle auto-approval untuk SPV (jabatan 5) yang membuat pengajuan
     * Berlaku untuk semua jenis request (shift_change, absence, dll)
     * Mengikuti urutan approval flow
     */
    private function handleAutoApprovalForSPV(EmployeeRequest $request, User $user, ApprovalService $approvalService)
    {
        // Cek apakah user adalah SPV (jabatan 5)
        if ((int) $user->jabatan !== 5) {
            return;
        }

        // Get approval flow untuk request type ini
        $requesterDivisi = $user->divisi;
        $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($request->request_type, $requesterDivisi);

        // Cari SPV di approval flow
        $spvSetting = $approvalFlow->firstWhere('role_key', 'spv_division');

        if (!$spvSetting) {
            // SPV tidak ada di approval flow untuk request type ini
            \Log::info('SPV tidak ada di approval flow', [
                'request_id' => $request->id,
                'request_type' => $request->request_type,
                'user_id' => $user->id
            ]);
            return;
        }

        // Cek apakah SPV adalah approver pertama (order 1)
        $spvOrder = $spvSetting->approval_order;

        if ($spvOrder !== 1) {
            // SPV bukan approver pertama, tidak bisa auto-approve
            \Log::info('SPV bukan approver pertama, tidak bisa auto-approve', [
                'request_id' => $request->id,
                'request_type' => $request->request_type,
                'user_id' => $user->id,
                'spv_order' => $spvOrder
            ]);
            return;
        }

        // Untuk ABSENCE, cek apakah SPV enabled di DivisiApprovalSetting
        if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
            $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $requesterDivisi)
                ->where('is_active', true)
                ->first();

            if ($divisiSetting) {
                $spvEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                if (!$spvEnabled) {
                    // SPV disabled untuk divisi ini
                    \Log::info('SPV disabled untuk divisi ini', [
                        'request_id' => $request->id,
                        'request_type' => $request->request_type,
                        'user_id' => $user->id,
                        'divisi' => $requesterDivisi
                    ]);
                    return;
                }
            }
        }

        // Cek apakah user diizinkan untuk approve (menggunakan isUserAllowedToApprove)
        $context = ['requester_divisi' => $requesterDivisi];
        if (!$spvSetting->isUserAllowedToApprove($user, $context)) {
            \Log::info('SPV tidak diizinkan untuk approve request ini', [
                'request_id' => $request->id,
                'request_type' => $request->request_type,
                'user_id' => $user->id,
                'divisi' => $requesterDivisi
            ]);
            return;
        }

        // Auto-approve sebagai SPV
        $updateData = [
            'supervisor_id' => $user->id,
            'supervisor_approved_at' => now(),
            'supervisor_notes' => 'Auto-approved oleh supervisor',
            'current_approval_order' => $spvOrder
        ];

        // Update request
        $request->update($updateData);

        // Refresh request untuk mendapatkan data terbaru
        $request->refresh();

        // Update status request setelah auto-approval
        $approvalService->updateRequestStatus($request);

        // Pastikan status di-update dengan benar
        $request->refresh();

        \Log::info('Auto-approval untuk SPV request', [
            'request_id' => $request->id,
            'request_type' => $request->request_type,
            'employee_id' => $user->id,
            'jabatan' => $user->jabatan,
            'divisi' => $requesterDivisi,
            'status' => $request->status,
            'current_approval_order' => $request->current_approval_order,
            'supervisor_approved_at' => $request->supervisor_approved_at
        ]);
    }

    /**
     * Handle auto-approval untuk HEAD (jabatan 4) yang membuat pengajuan
     * Berlaku untuk semua jenis request (shift_change, absence, dll)
     * Mengikuti urutan approval flow
     */
    private function handleAutoApprovalForHEAD(EmployeeRequest $request, User $user, ApprovalService $approvalService)
    {
        if ((int) $user->jabatan !== 4) {
            return;
        }

        $requesterDivisi = $user->divisi;
        $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($request->request_type, $requesterDivisi);
        $headSetting = $approvalFlow->firstWhere('role_key', 'head_division');

        if (!$headSetting) {
            return;
        }

        $headOrder = $headSetting->approval_order;

        // Cek apakah semua approver sebelum HEAD sudah approve
        $allPreviousApproved = true;
        for ($i = 1; $i < $headOrder; $i++) {
            $prevSetting = $approvalFlow->firstWhere('approval_order', $i);
            if ($prevSetting) {
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $requesterDivisi)
                        ->where('is_active', true)
                        ->first();

                    if ($divisiSetting) {
                        $prevRoleKey = $prevSetting->role_key;
                        $isPrevLevelEnabled = true;

                        if ($prevRoleKey === 'spv_division') {
                            $isPrevLevelEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                        } elseif ($prevRoleKey === 'manager') {
                            $isPrevLevelEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                        }

                        if (!$isPrevLevelEnabled) {
                            continue;
                        }
                    }
                }

                $prevApproved = false;
                if ($prevSetting->role_key === 'spv_division') {
                    $prevApproved = !is_null($request->supervisor_approved_at);
                } elseif ($prevSetting->role_key === 'manager') {
                    $prevApproved = !is_null($request->manager_approved_at);
                }

                if (!$prevApproved) {
                    $allPreviousApproved = false;
                    break;
                }
            }
        }

        if (!$allPreviousApproved) {
            return;
        }

        if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
            $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $requesterDivisi)
                ->where('is_active', true)
                ->first();

            if ($divisiSetting) {
                $headEnabled = $divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true;
                if (!$headEnabled) {
                    return;
                }
            }
        }

        $context = ['requester_divisi' => $requesterDivisi];
        if (!$headSetting->isUserAllowedToApprove($user, $context)) {
            return;
        }

        $updateData = [
            'head_id' => $user->id,
            'head_approved_at' => now(),
            'head_notes' => 'Auto-approved oleh head',
            'current_approval_order' => $headOrder
        ];

        $request->update($updateData);
        $request->refresh();
        $approvalService->updateRequestStatus($request);
        $request->refresh();
    }

    /**
     * Handle auto-approval untuk MANAGER (jabatan 3) yang membuat pengajuan
     * Berlaku untuk semua jenis request (shift_change, absence, dll)
     * Mengikuti urutan approval flow
     * CATATAN: Jika MANAGER perlu General Manager approve dulu, ini akan di-handle oleh handleManagerRequestApproval()
     */
    private function handleAutoApprovalForManager(EmployeeRequest $request, User $user, ApprovalService $approvalService)
    {
        if ((int) $user->jabatan !== 3) {
            return;
        }

        $requesterDivisi = $user->divisi;
        $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($request->request_type, $requesterDivisi);
        $managerSetting = $approvalFlow->firstWhere('role_key', 'manager');

        if (!$managerSetting) {
            return;
        }

        $managerOrder = $managerSetting->approval_order;

        // Cek apakah semua approver sebelum MANAGER sudah approve
        $allPreviousApproved = true;
        for ($i = 1; $i < $managerOrder; $i++) {
            $prevSetting = $approvalFlow->firstWhere('approval_order', $i);
            if ($prevSetting) {
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $requesterDivisi)
                        ->where('is_active', true)
                        ->first();

                    if ($divisiSetting) {
                        $prevRoleKey = $prevSetting->role_key;
                        $isPrevLevelEnabled = true;

                        if ($prevRoleKey === 'spv_division') {
                            $isPrevLevelEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                        } elseif ($prevRoleKey === 'head_division') {
                            $isPrevLevelEnabled = $divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true;
                        }

                        if (!$isPrevLevelEnabled) {
                            continue;
                        }
                    }
                }

                $prevApproved = false;
                if ($prevSetting->role_key === 'spv_division') {
                    $prevApproved = !is_null($request->supervisor_approved_at);
                } elseif ($prevSetting->role_key === 'head_division') {
                    $prevApproved = !is_null($request->head_approved_at);
                }

                if (!$prevApproved) {
                    $allPreviousApproved = false;
                    break;
                }
            }
        }

        if (!$allPreviousApproved) {
            return;
        }

        if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
            $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $requesterDivisi)
                ->where('is_active', true)
                ->first();

            if ($divisiSetting) {
                $managerEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                if (!$managerEnabled) {
                    return;
                }
            }
        }

        $context = ['requester_divisi' => $requesterDivisi];
        if (!$managerSetting->isUserAllowedToApprove($user, $context)) {
            return;
        }

        $updateData = [
            'manager_id' => $user->id,
            'manager_approved_at' => now(),
            'manager_notes' => 'Auto-approved oleh manager',
            'current_approval_order' => $managerOrder
        ];

        $request->update($updateData);
        $request->refresh();
        $approvalService->updateRequestStatus($request);
        $request->refresh();
    }

    /**
     * Handle auto-approval untuk HRD yang membuat pengajuan
     * Berlaku untuk semua jenis request (shift_change, absence, dll)
     * Mengikuti urutan approval flow
     */
    private function handleAutoApprovalForHRD(EmployeeRequest $request, User $user, ApprovalService $approvalService)
    {
        if (!$user->isHR()) {
            return;
        }

        $requesterDivisi = $user->divisi;
        $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($request->request_type, $requesterDivisi);
        $hrSetting = $approvalFlow->firstWhere('role_key', 'hr');

        if (!$hrSetting) {
            return;
        }

        $hrOrder = $hrSetting->approval_order;

        // Cek apakah semua approver sebelum HR sudah approve
        $allPreviousApproved = true;
        for ($i = 1; $i < $hrOrder; $i++) {
            $prevSetting = $approvalFlow->firstWhere('approval_order', $i);
            if ($prevSetting) {
                if ($request->request_type === EmployeeRequest::TYPE_ABSENCE) {
                    $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $requesterDivisi)
                        ->where('is_active', true)
                        ->first();

                    if ($divisiSetting) {
                        $prevRoleKey = $prevSetting->role_key;
                        $isPrevLevelEnabled = true;

                        if ($prevRoleKey === 'spv_division') {
                            $isPrevLevelEnabled = $divisiSetting->spv_enabled == 1 || $divisiSetting->spv_enabled === true;
                        } elseif ($prevRoleKey === 'head_division') {
                            $isPrevLevelEnabled = $divisiSetting->head_enabled == 1 || $divisiSetting->head_enabled === true;
                        } elseif ($prevRoleKey === 'manager') {
                            $isPrevLevelEnabled = $divisiSetting->manager_enabled == 1 || $divisiSetting->manager_enabled === true;
                        }

                        if (!$isPrevLevelEnabled) {
                            continue;
                        }
                    }
                }

                // Cek apakah previous level sudah approve (dengan support multiple allowed_jabatan)
                $prevApproved = false;
                $prevRoleKey = $prevSetting->role_key;
                $prevAllowedJabatan = $prevSetting->allowed_jabatan ?? [];

                if (!empty($prevAllowedJabatan) && is_array($prevAllowedJabatan) && count($prevAllowedJabatan) > 1) {
                    foreach ($prevAllowedJabatan as $jabatan) {
                        if ((int) $jabatan === 3 && !is_null($request->manager_approved_at)) {
                            $prevApproved = true;
                            break;
                        } elseif ((int) $jabatan === 4 && !is_null($request->head_approved_at)) {
                            $prevApproved = true;
                            break;
                        } elseif ((int) $jabatan === 5 && !is_null($request->supervisor_approved_at)) {
                            $prevApproved = true;
                            break;
                        }
                    }
                } else {
                    if ($prevRoleKey === 'spv_division') {
                        $prevApproved = !is_null($request->supervisor_approved_at);
                    } elseif ($prevRoleKey === 'head_division') {
                        $prevApproved = !is_null($request->head_approved_at);
                    } elseif ($prevRoleKey === 'manager') {
                        $prevApproved = !is_null($request->manager_approved_at);
                    }
                }

                if (!$prevApproved) {
                    $allPreviousApproved = false;
                    break;
                }
            }
        }

        if (!$allPreviousApproved) {
            return;
        }

        $context = ['requester_divisi' => $requesterDivisi];
        if (!$hrSetting->isUserAllowedToApprove($user, $context)) {
            return;
        }

        $updateData = [
            'hr_id' => $user->id,
            'hr_approved_at' => now(),
            'hr_notes' => 'Auto-approved oleh HRD',
            'current_approval_order' => $hrOrder
        ];

        $request->update($updateData);
        $request->refresh();
        $approvalService->updateRequestStatus($request);
        $request->refresh();
    }

    /**
     * Handle approval untuk semua perizinan yang dibuat oleh MANAGER (jabatan 3)
     * Semua perizinan dari MANAGER harus diapprove oleh General Manager (divisi 13) dulu, baru ke HRD
     * Berlaku untuk: absence, shift_change, dan semua jenis perizinan lainnya
     */
    private function handleManagerRequestApproval(EmployeeRequest $request, User $user)
    {
        // Cari General Manager dengan divisi 13
        // Prioritas: manager (3) dulu, baru head (4)
        $generalManager = User::on('pgsql')
            ->where('divisi', 13)
            ->whereIn('jabatan', [3, 4])
            ->where('jabatan', '!=', 7) // Kecualikan KARYAWAN
            ->orderBy('jabatan', 'asc') // Prioritas manager (3) dulu
            ->first();

        if ($generalManager) {
            // Set general_id untuk General Manager approval
            $request->update([
                'general_id' => $generalManager->id
            ]);

            \Log::info('General Manager approver di-set untuk request dari MANAGER', [
                'request_id' => $request->id,
                'request_type' => $request->request_type,
                'employee_id' => $user->id,
                'employee_jabatan' => $user->jabatan,
                'general_manager_id' => $generalManager->id,
                'general_manager_jabatan' => $generalManager->jabatan,
                'general_manager_name' => $generalManager->name
            ]);
        } else {
            \Log::warning('General Manager tidak ditemukan: User dengan divisi 13 tidak ditemukan', [
                'request_id' => $request->id,
                'request_type' => $request->request_type,
                'employee_id' => $user->id
            ]);
        }
    }

    /**
     * Handle approval untuk semua perizinan yang dibuat oleh HEAD PRODUKSI (jabatan 4, divisi 4)
     * Semua perizinan dari HEAD PRODUKSI harus diapprove oleh General Manager (divisi 13) dulu, baru ke HRD
     * CUSTOM: Karena Manager Produksi tidak ada, approval langsung ke General Manager (skip manager)
     * Berlaku untuk: absence, shift_change, dan semua jenis perizinan lainnya
     */
    private function handleHeadProduksiRequestApproval(EmployeeRequest $request, User $user)
    {
        // Cari General Manager dengan divisi 13
        // Prioritas: manager (3) dulu, baru head (4)
        $generalManager = User::on('pgsql')
            ->where('divisi', 13)
            ->whereIn('jabatan', [3, 4])
            ->where('jabatan', '!=', 7) // Kecualikan KARYAWAN
            ->orderBy('jabatan', 'asc') // Prioritas manager (3) dulu
            ->first();

        if ($generalManager) {
            // Set general_id untuk General Manager approval
            $request->update([
                'general_id' => $generalManager->id
            ]);

            \Log::info('General Manager approver di-set untuk request dari HEAD PRODUKSI', [
                'request_id' => $request->id,
                'request_type' => $request->request_type,
                'employee_id' => $user->id,
                'employee_jabatan' => $user->jabatan,
                'employee_divisi' => $user->divisi,
                'general_manager_id' => $generalManager->id,
                'general_manager_jabatan' => $generalManager->jabatan,
                'general_manager_name' => $generalManager->name
            ]);
        } else {
            \Log::warning('General Manager tidak ditemukan: User dengan divisi 13 tidak ditemukan', [
                'request_id' => $request->id,
                'request_type' => $request->request_type,
                'employee_id' => $user->id,
                'employee_jabatan' => $user->jabatan,
                'employee_divisi' => $user->divisi
            ]);
        }
    }

    /**
     * Display the specified request
     */
    public function show($id)
    {
        $user = Auth::user();
        // Eager load relationships to prevent N+1 queries
        $request = EmployeeRequest::with([
            'employee',
            'supervisor',
            'hr',
            'manager',
            'head',
            'general',
            'replacementPerson',
            'employee.jabatanUser',
            'employee.divisiUser'
        ])->findOrFail($id);
        // dd($request);

        // Check if user has permission to view this request
        // if (!$this->canViewRequest($user, $request)) {
        //     abort(403, 'Anda tidak memiliki akses untuk melihat pengajuan ini.');
        // }

        // Build approval history menggunakan method dari HRApprovalController
        $approvalHistory = [];
        $approvalFlow = [];
        try {
            $approvalController = app(HRApprovalController::class);
            $approvalHistory = $approvalController->buildApprovalHistory($request);

            // Get approval flow untuk ditampilkan di view
            $requesterDivisi = $request->employee ? $request->employee->divisi : null;
            $approvalFlow = \App\Models\ApprovalSetting::getApprovalFlow($request->request_type, $requesterDivisi);
        } catch (\Exception $e) {
            // Jika error, tetap tampilkan halaman tapi tanpa approval history
            \Log::warning('Error getting approval history: ' . $e->getMessage());
        }

        // Check if current user can approve this request
        $canApprove = false;
        try {
            $canApprove = $approvalController->canApproveRequest($user, $request);
        } catch (\Exception $e) {
            // If error, canApprove remains false
            \Log::warning('Error checking canApprove: ' . $e->getMessage());
        }

        return view('hr.requests.show', compact('request', 'approvalHistory', 'approvalFlow', 'canApprove'));
    }

    /**
     * Show the form for editing a request (only for pending requests)
     */
    public function edit($id)
    {
        $user = Auth::user();
        $employeeRequest = EmployeeRequest::findOrFail($id);

        // Only employee who created the request can edit, and only if pending
        if ($employeeRequest->employee_id !== $user->id || $employeeRequest->status !== EmployeeRequest::STATUS_PENDING) {
            abort(403, 'Anda tidak dapat mengedit pengajuan ini.');
        }

        // Create a request object with the type for getReferenceData
        $dummyRequest = new \Illuminate\Http\Request();
        $dummyRequest->query->set('type', $employeeRequest->request_type);

        $data = $this->getReferenceData($dummyRequest);
        // Merge with existing request data
        $data = array_merge($data, $employeeRequest->request_data);
        // Add substitute_id if exists
        if ($employeeRequest->replacement_person_id) {
            $data['substitute_id'] = $employeeRequest->replacement_person_id;
        }

        return view('hr.requests.edit', ['request' => $employeeRequest, 'data' => $data]);
    }

    /**
     * Update the specified request
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $employeeRequest = EmployeeRequest::findOrFail($id);

        // Only employee who created the request can update, and only if pending
        if ($employeeRequest->employee_id !== $user->id || $employeeRequest->status !== EmployeeRequest::STATUS_PENDING) {
            abort(403, 'Anda tidak dapat mengedit pengajuan ini.');
        }

        $validatedData = $this->validateRequestData($request);

        DB::connection('pgsql2')->beginTransaction();

        try {
            // Update main request
            $employeeRequest->update([
                'request_data' => $validatedData['request_data'],
                'notes' => $validatedData['notes'] ?? null,
                'attachment_path' => $validatedData['attachment_path'] ?? null,
            ]);

            // TODO: Implementasi update overtime employees dan asset usage log bisa ditambahkan nanti

            DB::connection('pgsql2')->commit();

            return redirect()->route('hr.requests.show', $employeeRequest->id)
                          ->with('success', 'Pengajuan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollback();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a request
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $request = EmployeeRequest::findOrFail($id);

        // Only employee who created the request can cancel
        if ($request->employee_id !== $user->id) {
            abort(403, 'Anda tidak dapat membatalkan pengajuan ini.');
        }

        if (!$request->canBeCancelled()) {
            return back()->with('error', 'Pengajuan tidak dapat dibatalkan pada status saat ini.');
        }

        $request->update(['status' => EmployeeRequest::STATUS_CANCELLED]);

        // Send notification
        // $this->sendRequestNotifications($request, 'cancelled');

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    /**
     * Approve request (for supervisors and HR)
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $employeeRequest = EmployeeRequest::findOrFail($id);

        $validatedData = $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        // Use HRApprovalController to handle approval (it has the correct logic)
        $approvalController = app(HRApprovalController::class);

        DB::connection('pgsql2')->beginTransaction();

        try {
            // Check if user can approve this request
            if (!$approvalController->canApproveRequest($user, $employeeRequest)) {
                return back()->with('error', 'Anda tidak dapat menyetujui pengajuan ini.');
            }

            // Process approval using HRApprovalController method
            $approvalController->approveRequest($user, $employeeRequest, $validatedData['notes'] ?? null);

            // Send notifications
            // $this->sendRequestNotifications($employeeRequest, 'approved');

            DB::connection('pgsql2')->commit();

            return back()->with('success', 'Pengajuan berhasil disetujui.');

        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject request (for supervisors and HR)
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        // Eager load relationships to prevent N+1 queries
        $employeeRequest = EmployeeRequest::with([
            'employee',
            'supervisor',
            'hr',
            'manager',
            'head'
        ])->findOrFail($id);

        $validatedData = $request->validate([
            'notes' => 'required|string|max:1000'
        ]);

        // Use HRApprovalController to handle rejection (it has the correct logic)
        $approvalController = app(HRApprovalController::class);

        DB::connection('pgsql2')->beginTransaction();

        try {
            // Check if user can reject this request
            if (!$approvalController->canApproveRequest($user, $employeeRequest)) {
                return back()->with('error', 'Anda tidak dapat menolak pengajuan ini.');
            }

            // Process rejection using HRApprovalController method
            $approvalController->rejectRequest($user, $employeeRequest, $validatedData['notes']);

            // Send notifications
            // $this->sendRequestNotifications($employeeRequest, 'rejected');

            DB::connection('pgsql2')->commit();

            return back()->with('success', 'Pengajuan berhasil ditolak.');

        } catch (\Exception $e) {
            DB::connection('pgsql2')->rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get reference data for forms
     */
    private function getReferenceData(Request $request)
    {
        // dd($request->all());
        $type = $request->input('type') ?? $request->query('type');
        $data = [];

        // dd($type);
        switch ($type) {
            case EmployeeRequest::TYPE_SHIFT_CHANGE:
                // Data untuk shift change - ambil data lengkap dari masteremployee dan masterdivisi
                $data['shifts'] = collect();

                // Ambil data lengkap dari masteremployee berdasarkan nama user yang login
                try {
                    $user = Auth::user();
                    // dd($user);
                    $employee = DB::connection('mysql7')
                        ->table('masteremployee')
                        ->leftJoin('masterdivisi', 'masteremployee.Kode Divisi', '=', 'masterdivisi.Kode Divisi')
                        ->where('masteremployee.Nama', $user->name)
                        ->where('masteremployee.Begda', '<=', now())
                        ->where(function($q) {
                            $q->whereNull('masteremployee.Endda')
                              ->orWhere('masteremployee.Endda', '>=', now());
                        })
                        ->where('masterdivisi.Begda', '<=', now())
                        ->where(function($q) {
                            $q->whereNull('masterdivisi.Endda')
                              ->orWhere('masterdivisi.Endda', '>=', now());
                        })
                        ->orderByDesc('masteremployee.Begda')
                        ->select('masteremployee.Nip', 'masteremployee.Nama', 'masteremployee.Kode Divisi', 'masterdivisi.Nama Divisi as DivisiNama')
                        ->first();
                    // dd($employee);


                    $data['employee_nip'] = $employee ? $employee->Nip : null;
                    $data['employee_name'] = $employee ? $employee->Nama : $user->name;
                    $data['employee_department'] = $employee ? $employee->DivisiNama : '';

                    // Ambil list employee di divisi yang sama untuk dropdown pengganti
                    if ($employee && $employee->{'Kode Divisi'}) {
                        $sameDivisionEmployees = DB::connection('mysql7')
                            ->table('masteremployee')
                            ->leftJoin('masterdivisi', 'masteremployee.Kode Divisi', '=', 'masterdivisi.Kode Divisi')
                            ->where('masteremployee.Kode Divisi', $employee->{'Kode Divisi'})
                            ->where('masteremployee.Nama', '!=', $employee->Nama) // Exclude current user
                            ->where('masteremployee.Begda', '<=', now())
                            ->where(function($q) {
                                $q->whereNull('masteremployee.Endda')
                                  ->orWhere('masteremployee.Endda', '>=', now());
                            })
                            ->where('masterdivisi.Begda', '<=', now())
                            ->where(function($q) {
                                $q->whereNull('masterdivisi.Endda')
                                  ->orWhere('masterdivisi.Endda', '>=', now());
                            })
                            ->orderBy('masteremployee.Nama')
                            ->select('masteremployee.Nip', 'masteremployee.Nama', 'masteremployee.Kode Divisi', 'masterdivisi.Nama Divisi as DivisiNama')
                            ->get();
                        // dd($sameDivisionEmployees);

                        // Get user IDs for each employee from users table
                        $employeeNames = $sameDivisionEmployees->pluck('Nama')->toArray();
                        // dd($employeeNames);
                        $users = \App\Models\User::on('pgsql')
                            ->whereIn('name', $employeeNames)
                            ->select('id', 'name')
                            ->get()
                            ->keyBy('name');

                        // dd($users);

                        // Add user_id to each employee
                        $data['same_division_employees'] = $sameDivisionEmployees->map(function ($emp) use ($users) {
                            $user = $users->get($emp->Nama);
                            return [
                                'id' => $user ? $user->id : null,
                                'nip' => $emp->Nip,
                                'name' => $emp->Nama,
                                'division_code' => $emp->{'Kode Divisi'},
                                'division_name' => $emp->DivisiNama
                            ];
                        })->filter(function ($emp) {
                            // Filter out employees without user ID
                            return $emp['id'] !== null;
                        })->values();



                    } else {
                        $data['same_division_employees'] = collect();
                    }
                } catch (\Exception $e) {
                    Log::error('Error getting employee data: ' . $e->getMessage());
                    $data['employee_nip'] = null;
                    $data['employee_name'] = $user->name;
                    $data['employee_department'] = '';
                    $data['same_division_employees'] = collect();
                }
                break;

            case EmployeeRequest::TYPE_ABSENCE:
                // Data untuk absence - ambil sisa cuti dan jenis kelamin dari database
                $data['absenceTypes'] = collect();
                $user = Auth::user();
                $data['remainingAnnualLeave'] = $user->getRemainingAnnualLeave();

                // Ambil absence settings dari master_absence_settings
                try {
                    $absenceSettings = MasterAbsenceSetting::active()->get();
                    $data['absenceSettings'] = $absenceSettings->mapWithKeys(function ($setting) {
                        return [
                            $setting->absence_type => [
                                'min_deadline_days' => $setting->min_deadline_days,
                                'max_deadline_days' => $setting->max_deadline_days,
                                'attachment_required' => $setting->attachment_required,
                                'deadline_text' => $setting->deadline_text,
                                'description' => $setting->description,
                                'master_sub_absence' => $setting->master_sub_absence,
                            ]
                        ];
                    })->toArray();
                } catch (\Exception $e) {
                    Log::error('Error getting absence settings: ' . $e->getMessage());
                    $data['absenceSettings'] = [];
                }

                // Ambil kategori cuti khusus dari master_cuty_khusus_categories
                try {
                    $cutyKhususCategories = \App\Models\MasterCutyKhususCategory::active()->get();
                    $data['cutyKhususCategories'] = $cutyKhususCategories;
                } catch (\Exception $e) {
                    Log::error('Error getting cuty khusus categories: ' . $e->getMessage());
                    $data['cutyKhususCategories'] = collect();
                }

                // Ambil jenis kelamin dari masteremployee
                try {
                    $employee = DB::connection('mysql7')
                        ->table('masteremployee')
                        ->where('Nama', $user->name)
                        ->where('Begda', '<=', now())
                        ->where(function($q) {
                            $q->whereNull('Endda')
                              ->orWhere('Endda', '>=', now());
                        })
                        ->orderByDesc('Begda')
                        ->select('Jenis Kelamin')
                        ->first();

                    $data['employee_gender'] = $employee ? ($employee->{'Jenis Kelamin'} ?? '') : '';
                } catch (\Exception $e) {
                    Log::error('Error getting employee gender: ' . $e->getMessage());
                    $data['employee_gender'] = '';
                }
                break;

            case EmployeeRequest::TYPE_OVERTIME:
                // SPV hanya bisa memilih karyawan dari divisi mereka
                $user = Auth::user();
                $employees = User::on('pgsql')
                    ->where('divisi', $user->divisi)
                    ->where('id', '!=', $user->id) // Exclude supervisor sendiri
                    ->with('divisiUser') // Load divisi relation
                    ->orderBy('name')
                    ->get(['id', 'name', 'divisi']);

                // Map employees dengan divisi name
                $data['employees'] = $employees->map(function($emp) {
                    return [
                        'id' => $emp->id,
                        'name' => $emp->name,
                        'divisi' => $emp->divisi,
                        'divisi_name' => $emp->divisiUser ? $emp->divisiUser->nama_divisi : ''
                    ];
                });
                break;

            case EmployeeRequest::TYPE_VEHICLE_ASSET:
                // Data untuk vehicle/asset - bisa ditambahkan nanti
                $data['vehicles'] = collect();
                $data['assets'] = collect();
                break;
        }

        return $data;
    }

    /**
     * Validate request data based on type
     */
    private function validateRequestData(Request $request)
    {
        $type = $request->input('request_type');

        // Auto-convert 00:00 to 23:59 for all end_time fields BEFORE validation (for shift_change)
        if ($type === EmployeeRequest::TYPE_SHIFT_CHANGE) {
            $endTimeFields = [
                'original_end_time',
                'new_end_time',
                'applicant_end_time',
                'substitute_end_time'
            ];

            foreach ($endTimeFields as $field) {
                $value = $request->input($field);
                // Check for 00:00 in various formats
                if ($value === '00:00' || $value === '00.00' || $value === '0:00' || $value === '0.00') {
                    // Convert to '23:59' before validation
                    $request->merge([$field => '23:59']);
                }
            }

            // Log conversion for debugging
            \Log::debug('Shift change time conversion', [
                'original_end_time' => $request->input('original_end_time'),
                'new_end_time' => $request->input('new_end_time'),
                'applicant_end_time' => $request->input('applicant_end_time'),
                'substitute_end_time' => $request->input('substitute_end_time'),
            ]);
        }

        $rules = [
            'request_type' => 'required|in:shift_change,absence,overtime,vehicle_asset',
            'notes' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ];

        // Add specific validation rules based on request type
        switch ($type) {
            case EmployeeRequest::TYPE_SHIFT_CHANGE:
                // Base rules for all shift change scenarios
                $rules = array_merge($rules, [
                    'scenario_type' => 'required|in:self,exchange,holiday',
                    'applicant_name' => 'nullable|string|max:255',
                    'applicant_department' => 'nullable|string|max:255',
                    'applicant_nip' => 'nullable|string|max:50',
                    'purpose' => 'nullable|string|max:500',
                ]);

                // Scenario-specific rules will be added dynamically based on scenario_type
                // after initial validation, but we'll add them here as conditional rules
                $scenarioType = $request->input('scenario_type', 'exchange');

                if ($scenarioType === 'self') {
                    // Scenario 1: Self shift change
                    $rules = array_merge($rules, [
                        'date' => 'required|date_format:Y-m-d',
                        'original_start_time' => 'required|date_format:H:i',
                        'original_end_time' => 'required|date_format:H:i',
                        'new_start_time' => 'required|date_format:H:i',
                        'new_end_time' => 'required|date_format:H:i',
                    ]);
                } elseif ($scenarioType === 'exchange') {
                    // Scenario 2: Exchange with colleague
                    $rules = array_merge($rules, [
                        'date' => 'required|date_format:Y-m-d',
                        'applicant_start_time' => 'required|date_format:H:i',
                        'applicant_end_time' => 'required|date_format:H:i',
                        'purpose' => 'required|string|max:500',
                        'substitute_id' => 'required|integer|exists:users,id',
                        'substitute_name' => 'required|string|max:255',
                        'substitute_department' => 'nullable|string|max:255',
                        'substitute_nip' => 'nullable|string|max:50',
                        'substitute_start_time' => 'required|date_format:H:i',
                        'substitute_end_time' => 'required|date_format:H:i',
                        'substitute_purpose' => 'required|string|max:500',
                    ]);
                } elseif ($scenarioType === 'holiday') {
                    // Scenario 3: Holiday work / compensatory leave
                    $rules = array_merge($rules, [
                        'holiday_work_date' => 'required|date_format:Y-m-d',
                        'compensatory_date' => 'required|date_format:Y-m-d|after:holiday_work_date',
                        'applicant_start_time' => 'required|date_format:H:i',
                        'applicant_end_time' => 'required|date_format:H:i',
                        'purpose' => 'required|string|max:500',
                        'work_hours' => 'required|numeric|min:0.5|max:24',
                    ]);
                }
                break;

            case EmployeeRequest::TYPE_ABSENCE:
                $rules = array_merge($rules, [
                    'name' => 'required|string|max:255',
                    'department' => 'required|string|max:255',
                    'absence_type' => 'required|string|max:255',
                    'duration_days' => 'required|integer|min:1',
                    'date_start' => 'required|date',
                    'date_end' => 'required|date|after_or_equal:date_start',
                    'remaining_annual_leave' => 'nullable|integer|min:0',
                    // Conditional fields
                    'cuti_khusus_category' => 'nullable|string|max:255',
                    'cuti_khusus_other_text' => 'nullable|string|max:500',
                    'dinas_purpose' => 'nullable|string|max:255',
                    'ijin_description' => 'nullable|string|max:500', // Keterangan akan dikategorikan oleh HR
                    'purpose' => 'nullable|string|max:500',
                    'hpl_date' => 'nullable|date', // HPL untuk Cuti Hamil
                ]);
                break;

            case EmployeeRequest::TYPE_OVERTIME:
                $rules = array_merge($rules, [
                    'date' => 'required|date',
                    'location' => 'required|string|max:255',
                    'overtime_employees' => 'required|array|min:1',
                    'overtime_employees.*.employee_id' => 'required|exists:users,id',
                    'overtime_employees.*.employee_name' => 'required|string|max:255',
                    'overtime_employees.*.department' => 'required|string|max:255',
                    'overtime_employees.*.start_time' => 'required|date_format:H:i',
                    'overtime_employees.*.end_time' => 'required|date_format:H:i|after:overtime_employees.*.start_time',
                    'overtime_employees.*.job_description' => 'required|string|max:500'
                ]);
                break;

            case EmployeeRequest::TYPE_VEHICLE_ASSET:
                $rules = array_merge($rules, [
                    'name' => 'required|string|max:255',
                    'department' => 'required|string|max:255',
                    'vehicle_item_type' => 'required|string|max:255',
                    'license_plate' => 'nullable|string|max:50',
                    'purpose_type' => 'required|in:Dinas,Pengiriman,Pribadi',
                    'description' => 'required|string|max:500',
                    'destination' => 'required|string|max:255',
                    'date' => 'required|date'
                ]);
                break;
        }

        $validatedData = $request->validate($rules);

        // Custom validation for absence deadline and fields
        if ($type === EmployeeRequest::TYPE_ABSENCE) {
            $errors = [];
            $absenceType = $validatedData['absence_type'] ?? '';
            $dateStart = isset($validatedData['date_start']) ? \Carbon\Carbon::parse($validatedData['date_start']) : null;
            $today = \Carbon\Carbon::today();
            $daysUntilStart = $dateStart ? $today->diffInDays($dateStart, false) : null;

            // Get absence settings from database
            $absenceSettings = MasterAbsenceSetting::where('absence_type', $absenceType)
                ->where('is_active', true)
                ->first();

            // Validate deadline based on absence type from database
            if ($dateStart && $daysUntilStart !== null && $absenceSettings) {
                $minDeadlineDays = $absenceSettings->min_deadline_days;
                $maxDeadlineDays = $absenceSettings->max_deadline_days;

                // Validate minimum deadline
                if ($minDeadlineDays !== null && $daysUntilStart < $minDeadlineDays) {
                    $minDate = $today->copy()->addDays($minDeadlineDays);
                    $deadlineType = $minDeadlineDays >= 0 ? 'H+' . $minDeadlineDays : 'H' . $minDeadlineDays;

                    if ($minDeadlineDays > 0) {
                        $errors['date_start'][] = "Pengajuan {$absenceType} harus minimal {$deadlineType} ({$minDeadlineDays} hari sebelum tanggal izin). Tanggal izin minimal " . $minDate->format('d/m/Y');
                    } else if ($minDeadlineDays < 0) {
                        $errors['date_start'][] = "Pengajuan {$absenceType} harus dilakukan maksimal {$deadlineType} (" . abs($minDeadlineDays) . " hari sebelum tanggal izin). Tanggal izin minimal " . $minDate->format('d/m/Y');
                    }
                }

                // Validate maximum deadline
                // if ($maxDeadlineDays !== null && $daysUntilStart > $maxDeadlineDays) {
                //     $maxDate = $today->copy()->addDays($maxDeadlineDays);
                //     $deadlineType = $maxDeadlineDays >= 0 ? 'H+' . $maxDeadlineDays : 'H' . $maxDeadlineDays;
                //     $errors['date_start'][] = "Pengajuan {$absenceType} maksimal {$deadlineType} ({$maxDeadlineDays} hari setelah tanggal izin). Tanggal izin maksimal " . $maxDate->format('d/m/Y');
                // }

                // Special validation for CUTI TAHUNAN
                if ($absenceType === 'CUTI TAHUNAN') {
                    $remainingLeave = $validatedData['remaining_annual_leave'] ?? 0;
                    $durationDays = $validatedData['duration_days'] ?? 0;
                    if ($remainingLeave < $durationDays) {
                        $errors['duration_days'][] = 'Sisa cuti tahunan tidak mencukupi. Sisa cuti: ' . $remainingLeave . ' hari';
                    }
                }

                // Special validation for CUTI HAMIL
                if ($absenceType === 'CUTI HAMIL') {
                    if (empty($validatedData['hpl_date'])) {
                        $errors['hpl_date'][] = 'HPL (Hari Perkiraan Lahir) wajib diisi untuk Cuti Hamil';
                    } else {
                        $hplDate = \Carbon\Carbon::parse($validatedData['hpl_date']);
                        $daysBefore = $absenceSettings->min_deadline_days ?? 45; // Default 45 days
                        $daysAfter = $absenceSettings->max_deadline_days ?? 45; // Default 45 days

                        $minDate = $hplDate->copy()->subDays($daysBefore);
                        $maxDate = $hplDate->copy()->addDays($daysAfter);

                        if ($dateStart && ($dateStart->lt($minDate) || $dateStart->gt($maxDate))) {
                            $errors['date_start'][] = 'Tanggal izin harus dalam rentang ' . $daysBefore . ' hari sebelum HPL sampai ' . $daysAfter . ' hari setelah HPL (' . $minDate->format('d/m/Y') . ' - ' . $maxDate->format('d/m/Y') . ')';
                        }
                    }
                }

                // Special validation for CUTI KHUSUS
                if ($absenceType === 'CUTI KHUSUS') {
                    if (empty($validatedData['cuti_khusus_category'])) {
                        $errors['cuti_khusus_category'][] = 'Kategori Cuti Khusus wajib dipilih';
                    } else if ($validatedData['cuti_khusus_category'] === 'Lainnya' && empty($validatedData['cuti_khusus_other_text'])) {
                        $errors['cuti_khusus_other_text'][] = 'Keterangan Cuti Khusus wajib diisi jika memilih Lainnya';
                    } else {
                        // Validate duration sesuai kategori dari database
                        $category = $validatedData['cuti_khusus_category'];

                        // Get master_sub_absence from database
                        $subAbsence = null;
                        if ($absenceSettings && $absenceSettings->master_sub_absence) {
                            $masterSubAbsence = is_array($absenceSettings->master_sub_absence)
                                ? $absenceSettings->master_sub_absence
                                : json_decode($absenceSettings->master_sub_absence, true);

                            $subAbsence = collect($masterSubAbsence)->firstWhere('name', $category);
                        }

                        if ($subAbsence && isset($subAbsence['duration_days']) && $category !== 'Lainnya') {
                            $requiredDuration = $subAbsence['duration_days'];
                            $submittedDuration = $validatedData['duration_days'] ?? 0;
                            if ($submittedDuration != $requiredDuration) {
                                $errors['duration_days'][] = 'Durasi untuk kategori ' . $category . ' harus ' . $requiredDuration . ' hari';
                            }
                        }
                    }
                }
            }

            // If there are custom validation errors, throw ValidationException
            if (!empty($errors)) {
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
            }
        }

        // Custom validation for shift change time
        if ($type === EmployeeRequest::TYPE_SHIFT_CHANGE) {
            $errors = [];
            $user = Auth::user();
            $scenarioType = $validatedData['scenario_type'] ?? 'exchange';

            // Determine which date field to use based on scenario
            $requestDate = null;
            $dateField = null;

            if ($scenarioType === 'holiday') {
                $requestDate = $validatedData['holiday_work_date'] ?? null;
                $dateField = 'holiday_work_date';
            } else {
                $requestDate = $validatedData['date'] ?? null;
                $dateField = $scenarioType === 'self' ? 'date' : 'date';
            }

            // 1. Validate date range: H-1, Hari H, atau H+1 (untuk tukar shift)
            // DISABLED: Validasi tanggal sudah di-set hardcode di frontend untuk H-1, Hari H, H+1
            // Tidak perlu validasi tambahan di backend karena sudah di-handle di form
            /*
            if ($requestDate) {
                try {
                    $requestDateObj = \Carbon\Carbon::parse($requestDate)->startOfDay();
                    $today = \Carbon\Carbon::today();
                    $yesterday = $today->copy()->subDay(); // H-1
                    $tomorrow = $today->copy()->addDay(); // H+1

                    // Hitung selisih hari
                    $daysUntilStart = $today->diffInDays($requestDateObj, false);

                    // Validasi: hanya bisa H-1, Hari H (hari ini), atau H+1
                    if ($daysUntilStart < -1) {
                        // Lebih dari 1 hari yang lalu
                        $errors[$dateField] = ['Tidak bisa mengajukan tukar shift untuk tanggal yang sudah lewat lebih dari 1 hari. Tanggal tukar shift minimal ' . $yesterday->format('d/m/Y')];
                    } elseif ($daysUntilStart > 1) {
                        // Lebih dari 1 hari ke depan
                        $errors[$dateField] = ['Tidak bisa mengajukan tukar shift untuk tanggal lebih dari H+1 (1 hari setelah hari ini). Tanggal tukar shift maksimal ' . $tomorrow->format('d/m/Y')];
                    }
                    // Jika $daysUntilStart antara -1 sampai 1, berarti valid (H-1, Hari H, atau H+1)
                } catch (\Exception $e) {
                    $errors[$dateField] = ['Format tanggal tidak valid: ' . $e->getMessage()];
                }
            }
            */

            // Scenario-specific validations
            if ($scenarioType === 'exchange') {
                // 3. Validate substitute is not the same as applicant
                if (isset($validatedData['substitute_id'])) {
                    if ($validatedData['substitute_id'] == $user->id) {
                        $errors['substitute_name'] = ['Pengganti tidak boleh sama dengan pemohon'];
                    }
                }

                // 4. Validate substitute is from the same department
                if (isset($validatedData['substitute_department']) && isset($validatedData['applicant_department'])) {
                    if ($validatedData['substitute_department'] !== $validatedData['applicant_department']) {
                        $errors['substitute_name'] = ['Pengganti harus dari departemen yang sama dengan pemohon'];
                    }
                }

                // 5. Check for overlapping requests for applicant
                if (isset($validatedData['date'])) {
                    $dateToCheck = $validatedData['date'];

                    // Check if applicant already has a request on this date
                    $existingApplicantRequest = EmployeeRequest::where('employee_id', $user->id)
                        ->whereIn('request_type', ['shift_change', 'absence'])
                        ->where('status', '!=', 'cancelled')
                        ->whereDate('created_at', '>=', $dateToCheck)
                        ->whereRaw("request_data->>'date' = ?", [$dateToCheck])
                        ->first();

                    if ($existingApplicantRequest) {
                        $errors['date'] = ['Anda sudah memiliki pengajuan (tukar shift/tidak masuk kerja) pada tanggal tersebut'];
                    }

                    // 6. Check if substitute has an approved absence on this date
                    if (isset($validatedData['substitute_id'])) {
                        $substituteAbsence = EmployeeRequest::where('employee_id', $validatedData['substitute_id'])
                            ->where('request_type', 'absence')
                            ->whereIn('status', ['pending', 'supervisor_approved', 'manager_approved', 'hr_approved'])
                            ->whereRaw("request_data->>'date_start' <= ?", [$dateToCheck])
                            ->whereRaw("request_data->>'date_end' >= ?", [$dateToCheck])
                            ->first();

                        if ($substituteAbsence) {
                            $errors['substitute_name'] = ['Pengganti sedang mengajukan cuti/izin pada tanggal tersebut'];
                        }

                        // 7. Check if substitute already has a shift change on this date
                        $substituteShiftChange = EmployeeRequest::where('employee_id', $validatedData['substitute_id'])
                            ->where('request_type', 'shift_change')
                            ->where('status', '!=', 'cancelled')
                            ->whereRaw("request_data->>'date' = ?", [$dateToCheck])
                            ->first();

                        if ($substituteShiftChange) {
                            $errors['substitute_name'] = ['Pengganti sudah memiliki pengajuan tukar shift pada tanggal tersebut'];
                        }
                    }
                }
            } elseif ($scenarioType === 'self' || $scenarioType === 'holiday') {
                // Check for overlapping requests for applicant (self and holiday scenarios)
                if ($requestDate) {
                    $dateToCheck = $requestDate;

                    // Check if applicant already has a request on this date
                    $existingApplicantRequest = EmployeeRequest::where('employee_id', $user->id)
                        ->whereIn('request_type', ['shift_change', 'absence'])
                        ->where('status', '!=', 'cancelled')
                        ->whereDate('created_at', '>=', $dateToCheck)
                        ->where(function($query) use ($dateToCheck) {
                            $query->whereRaw("request_data->>'date' = ?", [$dateToCheck])
                                  ->orWhereRaw("request_data->>'holiday_work_date' = ?", [$dateToCheck]);
                        })
                        ->first();

                    if ($existingApplicantRequest) {
                        $errors[$dateField] = ['Anda sudah memiliki pengajuan (tukar shift/tidak masuk kerja) pada tanggal tersebut'];
                    }
                }

                // For holiday scenario, also check compensatory date
                if ($scenarioType === 'holiday' && isset($validatedData['compensatory_date'])) {
                    $compensatoryDate = $validatedData['compensatory_date'];

                    // Check if applicant already has a request on the compensatory date
                    $existingCompensatoryRequest = EmployeeRequest::where('employee_id', $user->id)
                        ->whereIn('request_type', ['shift_change', 'absence'])
                        ->where('status', '!=', 'cancelled')
                        ->whereDate('created_at', '>=', $compensatoryDate)
                        ->where(function($query) use ($compensatoryDate) {
                            $query->whereRaw("request_data->>'date' = ?", [$compensatoryDate])
                                  ->orWhereRaw("request_data->>'compensatory_date' = ?", [$compensatoryDate]);
                        })
                        ->first();

                    if ($existingCompensatoryRequest) {
                        $errors['compensatory_date'] = ['Anda sudah memiliki pengajuan pada tanggal pengganti (OFF) tersebut'];
                    }
                }
            }

            // Validate original time (for self scenario)
            if (isset($validatedData['original_start_time']) && isset($validatedData['original_end_time'])) {
                $startTime = \Carbon\Carbon::createFromFormat('H:i', $validatedData['original_start_time']);
                $endTime = \Carbon\Carbon::createFromFormat('H:i', $validatedData['original_end_time']);

                if ($endTime->lte($startTime)) {
                    $errors['original_end_time'] = ['Jam selesai harus lebih dari jam mulai'];
                }
            }

            // Validate new time (for self scenario)
            if (isset($validatedData['new_start_time']) && isset($validatedData['new_end_time'])) {
                $startTime = \Carbon\Carbon::createFromFormat('H:i', $validatedData['new_start_time']);
                $endTime = \Carbon\Carbon::createFromFormat('H:i', $validatedData['new_end_time']);

                if ($endTime->lte($startTime)) {
                    $errors['new_end_time'] = ['Jam selesai baru harus lebih dari jam mulai baru'];
                }
            }

            // Validate applicant time
            if (isset($validatedData['applicant_start_time']) && isset($validatedData['applicant_end_time'])) {
                $startTime = \Carbon\Carbon::createFromFormat('H:i', $validatedData['applicant_start_time']);
                $endTime = \Carbon\Carbon::createFromFormat('H:i', $validatedData['applicant_end_time']);

                if ($endTime->lte($startTime)) {
                    $errors['applicant_end_time'] = ['Jam selesai harus lebih dari jam mulai'];
                }
            }

            // Validate substitute time
            if (isset($validatedData['substitute_start_time']) && isset($validatedData['substitute_end_time'])) {
                $startTime = \Carbon\Carbon::createFromFormat('H:i', $validatedData['substitute_start_time']);
                $endTime = \Carbon\Carbon::createFromFormat('H:i', $validatedData['substitute_end_time']);

                if ($endTime->lte($startTime)) {
                    $errors['substitute_end_time'] = ['Jam selesai pengganti harus lebih dari jam mulai pengganti'];
                }
            }

            // If there are custom validation errors, throw ValidationException
            if (!empty($errors)) {
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
            }
        }

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('hr_attachments', $filename, 'public');
            $validatedData['attachment_path'] = $path;
        }

        // Prepare request data for JSON storage
        $requestData = [];
        foreach ($validatedData as $key => $value) {
            if (!in_array($key, ['request_type', 'notes', 'attachment_path', 'overtime_employees', 'substitute_id'])) {
                $requestData[$key] = $value;
            }
        }

        // Auto-convert 00:00 to 23:59 for all end_time fields in request_data (for shift_change)
        if ($type === EmployeeRequest::TYPE_SHIFT_CHANGE) {
            $endTimeFields = [
                'original_end_time',
                'new_end_time',
                'applicant_end_time',
                'substitute_end_time'
            ];

            foreach ($endTimeFields as $field) {
                if (isset($requestData[$field]) && $requestData[$field] === '00:00') {
                    $requestData[$field] = '23:59';
                    // Also update in validatedData for consistency
                    if (isset($validatedData[$field])) {
                        $validatedData[$field] = '23:59';
                    }
                }
            }
        }

        // Format date_range from date_start and date_end for backward compatibility
        if (isset($validatedData['date_start']) && isset($validatedData['date_end'])) {
            $dateStart = \Carbon\Carbon::parse($validatedData['date_start'])->format('d/m/Y');
            $dateEnd = \Carbon\Carbon::parse($validatedData['date_end'])->format('d/m/Y');
            $requestData['date_range'] = $dateStart . ' s/d ' . $dateEnd;
        }

        $validatedData['request_data'] = $requestData;

        return $validatedData;
    }


    /**
     * Send request notifications
     */
    private function sendRequestNotifications(EmployeeRequest $request, $action = 'submitted')
    {
        // TODO: Implementasi notifikasi bisa ditambahkan nanti
        // Untuk sekarang, kita skip notifikasi dulu agar form bisa berfungsi

        // Log untuk debugging
        // \Log::info("Request {$request->request_number} {$action} by user {$request->employee_id}");

        // Bisa ditambahkan email notification nanti
        // Mail::to($request->supervisor->email)->send(new RequestNotification($request));
    }


    /**
     * Check if user can view request
     */
    private function canViewRequest(User $user, EmployeeRequest $request)
    {
        // HR selalu bisa melihat semua request
        if ($user->isHR()) {
            return true;
        }

        // Employee yang membuat request bisa melihat
        if ($request->employee_id === $user->id) {
            return true;
        }

        // Supervisor yang melakukan approval bisa melihat
        if ($request->supervisor_id === $user->id) {
            return true;
        }

        // HEAD DIVISI yang melakukan approval bisa melihat
        if ($request->head_id === $user->id) {
            return true;
        }

        // MANAGER yang melakukan approval bisa melihat
        if ($request->manager_id === $user->id) {
            return true;
        }

        // GENERAL MANAGER yang melakukan approval bisa melihat
        if ($request->general_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Get department absence statistics for chart
     */
    public function getDepartmentAbsenceStats(Request $request)
    {
        try {
            $departmentId = $request->get('department_id', '');
            $month = $request->get('month', date('Y-m')); // Format: YYYY-MM

            // Parse month to get year and month
            $yearMonth = explode('-', $month);
            $year = $yearMonth[0] ?? date('Y');
            $monthNum = $yearMonth[1] ?? date('m');

            // Build query for EmployeeRequest where request_type is absence
            $query = EmployeeRequest::where('request_type', 'absence');

            // Filter by month
            $query->whereYear('created_at', $year)
                  ->whereMonth('created_at', $monthNum);

            // Get all absence requests with request_data
            $absenceRequests = $query->get(['id', 'employee_id', 'request_data', 'created_at']);

            // If no absence requests found, return empty data
            if ($absenceRequests->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            // Group by department from request_data JSON
            $departmentStats = [];

            foreach ($absenceRequests as $req) {
                // Get request_data (already cast as array by model)
                $requestData = $req->request_data ?? [];

                // Get department from request_data
                // Try multiple possible field names: 'department', 'applicant_department'
                $department = $requestData['department'] ??
                             $requestData['applicant_department'] ??
                             null;

                // Skip if no department found in request_data
                if (empty($department)) {
                    continue;
                }

                // If filtering by specific department (by name)
                if ($departmentId && $departmentId !== '') {
                    // Get department name from masterdivisi if ID provided
                    $filteredDepartmentName = null;
                    if (is_numeric($departmentId)) {
                        $filteredDepartmentName = DB::connection('mysql7')
                            ->table('masterdivisi')
                            ->where('Kode Divisi', $departmentId)
                            ->where('Begda', '<=', now())
                            ->where(function($q) {
                                $q->whereNull('Endda')
                                  ->orWhere('Endda', '>=', now());
                            })
                            ->value('Nama Divisi');
                    }

                    // Compare with filtered department name or exact match
                    if ($filteredDepartmentName && $department !== $filteredDepartmentName) {
                        continue;
                    } elseif (!$filteredDepartmentName && $department !== $departmentId) {
                        continue;
                    }
                }

                // Count by department name
                if (!isset($departmentStats[$department])) {
                    $departmentStats[$department] = 0;
                }
                $departmentStats[$department]++;
            }

            // Sort by count descending
            arsort($departmentStats);

            // Format for chart
            $chartData = [];
            foreach ($departmentStats as $department => $count) {
                $chartData[] = [
                    'department' => $department,
                    'count' => $count
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting department absence stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug method untuk memeriksa mengapa request tidak muncul di counter SPV
     */
    public function debugSpvCounter($requestId)
    {
        $user = Auth::user();
        $request = EmployeeRequest::with(['employee'])->findOrFail($requestId);

        $approvalService = new ApprovalService();

        // Get approval chain
        $chain = [];
        try {
            $chain = $approvalService->getApprovalChain($request);
        } catch (\Exception $e) {
            $chain = ['error' => $e->getMessage()];
        }

        // Get pending requests for user
        $allPending = ApprovalService::getPendingRequestsForUser($user, null, null);
        $isInPendingList = $allPending->contains('id', $requestId);

        // Check filter conditions
        $employeeDivisi = $request->employee ? $request->employee->divisi : null;
        $passesFilter = false;

        if ($request instanceof EmployeeRequest) {
            $passesFilter = in_array($request->request_type, ['shift_change', 'absence'])
                && $request->status === EmployeeRequest::STATUS_PENDING
                && $employeeDivisi == $user->divisi;
        }

        // Check DivisiApprovalSetting for absence
        $divisiSetting = null;
        if ($request->request_type === 'absence' && $employeeDivisi) {
            $divisiSetting = \App\Models\DivisiApprovalSetting::where('divisi_id', $employeeDivisi)
                ->where('is_active', true)
                ->first();
        }

        // Check if SPV is in chain
        $spvInChain = false;
        $spvUsers = collect();
        foreach ($chain as $level => $data) {
            if (isset($data['role_key']) && $data['role_key'] === 'spv_division') {
                $spvInChain = true;
                $spvUsers = $data['users'] ?? collect();
                break;
            }
        }

        // Check if current user is SPV in chain
        $userIsSpvInChain = $spvUsers->contains('id', $user->id);

        return response()->json([
            'request' => [
                'id' => $request->id,
                'request_type' => $request->request_type,
                'status' => $request->status,
                'employee_id' => $request->employee_id,
                'employee_divisi' => $employeeDivisi,
                'supervisor_approved_at' => $request->supervisor_approved_at,
                'supervisor_rejected_at' => $request->supervisor_rejected_at,
            ],
            'user' => [
                'id' => $user->id,
                'jabatan' => $user->jabatan,
                'divisi' => $user->divisi,
            ],
            'approval_chain' => array_map(function($level, $data) {
                return [
                    'level' => $level,
                    'role_key' => $data['role_key'] ?? null,
                    'approval_order' => $data['approval_order'] ?? null,
                    'user_ids' => isset($data['users']) ? $data['users']->pluck('id')->toArray() : [],
                ];
            }, array_keys($chain), $chain),
            'divisi_setting' => $divisiSetting ? [
                'divisi_id' => $divisiSetting->divisi_id,
                'spv_enabled' => $divisiSetting->spv_enabled,
                'head_enabled' => $divisiSetting->head_enabled,
                'manager_enabled' => $divisiSetting->manager_enabled,
                'is_active' => $divisiSetting->is_active,
            ] : null,
            'checks' => [
                'is_in_pending_list' => $isInPendingList,
                'passes_filter' => $passesFilter,
                'spv_in_chain' => $spvInChain,
                'user_is_spv_in_chain' => $userIsSpvInChain,
                'employee_divisi_matches' => $employeeDivisi == $user->divisi,
                'status_is_pending' => $request->status === EmployeeRequest::STATUS_PENDING,
                'request_type_valid' => in_array($request->request_type, ['shift_change', 'absence']),
            ],
            'spv_users_in_chain' => $spvUsers->map(function($u) {
                return [
                    'id' => $u->id,
                    'divisi' => $u->divisi,
                    'jabatan' => $u->jabatan,
                ];
            })->toArray(),
        ], 200, [], JSON_PRETTY_PRINT);
    }
}
