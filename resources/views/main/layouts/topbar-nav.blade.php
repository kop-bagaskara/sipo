<aside class="left-sidebar">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="nav-small-cap">PERSONAL</li>
                @if (Auth::user()->jabatan != 7)
                <li>
                    <a class="waves-effect waves-dark" href="{{ route('dashboard') }}" aria-expanded="false">
                        <i class="mdi mdi-gauge"></i><span class="hide-menu">Dashboard</span>
                    </a>
                    {{-- <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('dashboard') }}">Overview Data</a></li>
                        <li><a href="{{ route('dashboard.ppic') }}">Dashboard PPIC</a></li>
                        <li><a href="{{ route('dashboard.prepress') }}">Dashboard Prepress</a></li>
                        <li><a href="{{ route('dashboard.development') }}">Development Item</a></li>
                        @if(auth()->user()->divisi == 8 || auth()->user()->divisi == 1)
                        <li><a href="{{ route('dashboard.supplier') }}">Dashboard Supplier</a></li>
                        @endif
                        @if(auth()->user()->divisi == 11)
                        <li><a href="{{ route('dashboard.security') }}">Dashboard Security</a></li>
                        @endif
                    </ul> --}}
                </li>
                @endif

                @if (Auth::user()->divisi == 11)
                    {{-- Security User - Limited Access --}}
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-shield "></i><span class="hide-menu">Laporan Security</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            {{-- <li><a href="{{ route('security.vehicle-checklist.dashboard') }}">Dashboard Security</a>
                            </li> --}}
                            <li><a href="{{ route('security.vehicle-checklist.index') }}">Checklist Kendaraan</a></li>
                            <li><a href="{{ route('security.goods-movement.index') }}">Keluar/Masuk Barang</a></li>
                            <li><a href="{{ route('security.daily-activity.index') }}">Laporan Harian / Jurnal</a></li>
                        </ul>
                    </li>
                @elseif (Auth::user()->divisi != '8')
                    @if (Auth::user()->jabatan == '7')
                        <li class="active">
                            <a class="waves-effect waves-dark" href="{{ route('hr.requests.index') }}" aria-expanded="false">
                                <i class="mdi mdi-file-document"></i><span class="hide-menu">Form Pengajuan Karyawan</span>
                            </a>

                        </li>
                        {{-- PKB --}}
                        <li>
                            <a class="waves-effect waves-dark" href="{{ route('ebook-pkb.index') }}" aria-expanded="false">
                                <i class="mdi mdi-file-pdf-box"></i><span class="hide-menu">E-Book PKB</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                                <i class="mdi mdi-book"></i><span class="hide-menu">Job Order</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <a href="{{ route('prepress.job-order.index') }}" aria-expanded="false">Job
                                        Prepress</a>
                                </li>
                                <li>
                                    <a href="{{ route('development.development-input.form') }}"
                                        aria-expanded="false">Job
                                        Development</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                                <i class="mdi mdi-book-open-variant"></i><span class="hide-menu">Penjadwalan</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <a class="has-arrow" href="#" aria-expanded="false">PPIC</a>
                                    <ul aria-expanded="false" class="collapse">
                                        @if (auth()->user()->divisi == '6' || auth()->user()->divisi == '1')
                                            <li><a href="{{ route('mulai-proses.plan') }}">Jadwalkan Plan</a></li>
                                            <li><a href="{{ route('process.plan-first-prd') }}">Timeline Plan</a></li>
                                            <li><a href="{{ route('monitoring-so.index') }}">Monitoring SO</a></li>
                                            <li><a href="{{ route('inventory-calc-stock.index') }}">Inventory Calc.
                                                    Stock</a>
                                            </li>
                                        @endif
                                        <li><a href="{{ route('process.plan-first-table-uppic') }}">Plan Production</a>
                                        </li>
                                        {{-- <li><a href="{{ route('process.plan-first-table') }}">Table View</a></li> --}}
                                        {{-- <li><a href="{{ route('process.plan-first-table-plong') }}">Table View PLONG</a></li> --}}

                                    </ul>
                                </li>
                                <li>
                                    <a class="has-arrow" href="#" aria-expanded="false">Prepress</a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="{{ route('prepress.job-order.data.index') }}">Job Prepress</a>
                                        </li>
                                        @if (auth()->user()->divisi == '6')
                                            <li><a href="{{ route('prepress.planharian.index') }}">Plan Harian</a></li>
                                        @endif
                                        @if (auth()->user()->divisi == '3')
                                            @if (auth()->user()->jabatan == '4')
                                                <li><a href="{{ route('prepress.listplan.index') }}">List Plan</a></li>
                                            @endif
                                        @endif
                                        @if (auth()->user()->divisi == '3')
                                            <li><a href="{{ route('prepress.listtask.index') }}">List Task</a></li>
                                        @endif
                                        <li><a href="{{ route('prepress.timelinetask.index') }}">Timeline Task</a></li>
                                        <li><a href="{{ route('process.plan-first-table-prepress') }}">Plan Production
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                                <i class="mdi mdi-apple-safari"></i><span class="hide-menu">Operasional</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <a href="{{ route('label-management.index') }}" aria-expanded="false">Label
                                        Management</a>
                                </li>

                                @if (Auth::user()->divisi == '1')
                                    <li>
                                        <a href="{{ route('admin.supplier-tickets.index') }}"
                                            aria-expanded="false">Supplier Tickets</a>
                                    </li>
                                @endif

                                <li>
                                    <a href="{{ route('forecasting.index') }}" aria-expanded="false">Forecasting</a>
                                </li>

                                <li>
                                    <a href="{{ route('hr.portal-training.index') }}" aria-expanded="false">Portal Training Karyawan</a>
                                </li>

                                <li>
                                    <a class="has-arrow" href="#" aria-expanded="false">Human Resource
                                        Development</a>
                                    <ul aria-expanded="false" class="collapse">

                                        {{-- <li><a href="{{ route('hr.requests.index') }}">Form Pengajuan Karyawan</a></li> --}}
                                        @if (Auth::user()->divisi == '1' || (Auth::user()->divisi == '7' && Auth::user()->jabatan != '7'))


                                        <li><a href="{{ route('hr.approval-settings.index') }}">Master Setting Approval</a></li>
                                        <li><a href="{{ route('hr.approval-settings.divisions.index') }}">Setting Approval Per Divisi</a></li>
                                        <li><a href="{{ route('hr.absence-settings.index') }}">Master Setting Absence</a></li>
                                        @endif
                                        {{-- <li><a href="{{ route('hr.requests.create') }}">Buat Pengajuan Baru</a></li> --}}
                                        {{-- <li><a href="{{ route('hr.approval.dashboard') }}">Approval Dashboard</a></li> --}}
                                        @if (auth()->user()->isHR() || auth()->user()->canApprove())
                                            {{-- <li><a href="{{ route('hr.approval.dashboard') }}">Approval Dashboard</a></li> --}}
                                            {{-- <li><a href="{{ route('hr.approval.history') }}">Riwayat Approval</a></li> --}}
                                        @endif
                                        <li><a href="#" class="has-arrow">Training</a>
                                            <ul aria-expanded="false" class="collapse">
                                                <li><a href="{{ route('hr.training.dashboard') }}">Dashboard
                                                        Training</a>
                                                </li>
                                                <li><a href="{{ route('hr.training.index') }}">Master Training</a></li>
                                                <li><a href="{{ route('hr.training.management.index') }}">Manajemen
                                                        Peserta</a></li>
                                                <li><a href="{{ route('hr.training.schedule.index') }}">Jadwal
                                                        Training</a></li>
                                                <li><a href="{{ route('hr.training-validation.index') }}">Training
                                                        Validation</a>

                                            </ul>
                                        </li>
                                        <li><a href="{{ route('hr.security-master.index') }}">Master Security</a></li>
                                        <li><a href="#" class="has-arrow">Laporan Security</a>
                                            <ul aria-expanded="false" class="collapse">
                                                <li><a href="{{ route('security.vehicle-checklist.index') }}">Checklist
                                                        Kendaraan</a></li>
                                                <li><a href="{{ route('security.goods-movement.index') }}">Keluar/Masuk
                                                        Barang</a></li>
                                                <li><a href="{{ route('security.daily-activity.index') }}">Laporan
                                                        Harian
                                                        / Jurnal</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="{{ route('ebook-pkb.logs') }}">Log Viewer PKB</a></li>
                                        <li><a href="{{ route('hr.applicants.index') }}">Data Pelamar</a></li>
                                        @if (auth()->user()->divisi == '7' || auth()->user()->jabatan == '3')
                                            <li><a href="{{ route('hr.employee-data.index') }}">Data Karyawan</a></li>
                                        @endif

                                    </ul>
                                </li>

                                @if (Auth::user()->divisi == '1')
                                    {{-- Pengajuan Kertas --}}
                                    <li>
                                        <a href="{{ route('paper-procurement.index') }}"
                                            aria-expanded="false">Pengajuan
                                            Pembelian Kertas</a>
                                    </li>
                                @endif

                                <li>
                                    <a href="{{ route('order-fukumi.index') }}" aria-expanded="false">Generate Code
                                        Fukumi</a>
                                </li>

                                {{-- <li>
                                <a href="" aria-expanded="false"></a>
                            </li> --}}
                            </ul>
                        </li>

                        <li>
                            <a class="waves-effect waves-dark" href="{{ route('hr.requests.index') }}"
                                aria-expanded="false">
                                <i class="mdi mdi-file"></i><span class="hide-menu">Form Perizinan</span>
                            </a>
                        </li>
                        <li>
                            <a class="waves-effect waves-dark" href="{{ route('development.rnd-workspace.index') }}"
                                aria-expanded="false">
                                <i class="mdi mdi-cube"></i><span class="hide-menu">Development</span>
                            </a>
                        </li>
                        <li>
                            <a class="waves-effect waves-dark" href="{{ route('ebook-pkb.index') }}" aria-expanded="false">
                                <i class="mdi mdi-file-pdf-box"></i><span class="hide-menu">E-Book PKB</span>
                            </a>
                        </li>
                    @endif
                @endif

                @if (Auth::user()->divisi == '1' || Auth::user()->divisi == '8')
                    {{-- <li>
                        <a class="waves-effect waves-dark" href="{{ route('supplier-tickets.index') }}"
                            aria-expanded="false">
                            <i class="mdi mdi-truck"></i><span class="hide-menu">Supplier</span>
                        </a>
                    </li> --}}

                    {{-- <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-school"></i><span class="hide-menu">Portal Training</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{ route('hr.portal-training.master.categories.index') }}">Kategori Materi</a></li>
                            <li><a href="{{ route('hr.portal-training.master.difficulty-levels.index') }}">Tingkat Kesulitan</a></li>
                            <li><a href="{{ route('hr.portal-training.master.materials.index') }}">Materi Training</a></li>
                            <li><a href="{{ route('hr.portal-training.master.question-banks.index') }}">Bank Soal</a></li>
                            <li><a href="{{ route('hr.portal-training.master.assignments.index') }}">Training Assignments</a></li>
                        </ul>
                    </li> --}}
                @endif


                @if ((int) Auth::user()->divisi === 1)
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-file"></i><span class="hide-menu">Master</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{ route('working-days.index') }}">Hari Kerja</a></li>
                            <li><a href="{{ route('holiday-days.index') }}">Hari Libur</a></li>
                            <li><a href="{{ route('machine.index') }}">Mesin</a></li>
                            <li><a href="{{ route('database-machines.index') }}">Database Mesin</a></li>
                            <li><a href="{{ route('mapping-item.index') }}">Mapping Item</a></li>
                            <li><a href="{{ route('master-data-prepress.index') }}">Kategori Kerja Prepress</a></li>
                            <li><a href="{{ route('jenis-pekerjaan-prepress.index') }}">Jenis Pekerjaan Prepress</a>
                            </li>
                            <li>
                                <a class="has-arrow" href="#" aria-expanded="false">User Management</a>
                                <ul aria-expanded="false" class="collapse">
                                    <li><a href="{{ route('user.index') }}">User</a></li>
                                    <li><a href="{{ route('divisi.index') }}">Divisi</a></li>
                                    <li><a href="{{ route('jabatan.index') }}">Jabatan</a></li>
                                    <li><a href="{{ route('level.index') }}">Level</a></li>
                                </ul>
                            </li>
                            <li><a href="{{ route('settings.index') }}">Setting</a></li>
                            <li><a href="{{ route('development.master-proses') }}">Master Proses Development</a></li>
                            <li><a href="{{ route('development-email-notification-settings.index') }}">Master Email
                                    Development</a></li>
                        </ul>
                    </li>
                @endif
                @if (Auth::user()->divisi != '8' && Auth::user()->divisi != '11')

                    @if (AUth::user()->jabatan != '7')
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                                <i class="mdi mdi-book-open-variant"></i>
                                <span class="hide-menu">Report</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <a href="{{ route('report.plan-production.index') }}">Plan Production</a>
                                </li>
                                <li>
                                    <a href="{{ route('report.job-order-prepress.index') }}">Job Order Prepress</a>
                                </li>
                                <li>
                                    <a href="{{ route('report.transportation-cost.index') }}">Transportation Cost</a>
                                </li>
                                <li>
                                    <a href="{{ route('report.work-order-percentage.index') }}">WO Percentage</a>
                                </li>
                                <li>
                                    <a href="{{ route('report.development.index') }}">Development Item</a>
                                </li>
                                <li>
                                    <a href="{{ route('report.work-order-good-issue.index') }}">WO Good Issue</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.supplier-tickets.supplier-arrival-report') }}">Supplier
                                        Arrival Report</a>
                                </li>
                                <li><a href="{{ route('hr.reports.index') }}">Human Resource Development</a></li>


                            </ul>
                        </li>
                    @endif
                @endif
                @if (Auth::user()->divisi == '1')
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-widgets"></i>
                            <span class="hide-menu">Tools</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{ route('email-notification-settings.index') }}">Master Setting Email</a>
                            </li>
                            <li><a href="{{ route('notifications.index') }}">Semua Notifikasi</a></li>
                        </ul>
                    </li>
                @endif

                {{-- @if (Auth::user()->divisi == '1' || Auth::user()->divisi != '8')
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-account-supervisor"></i>
                            <span class="hide-menu">Admin</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="{{ route('admin.supplier-tickets.index') }}">Ticketing Supplier</a></li>
                        </ul>
                    </li>
                @endif --}}
                {{-- <li>
                    <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                        <i class="mdi mdi-widgets"></i>
                        <span class="hide-menu">Tools</span>
                    </a>
                    <ul aria-expanded="false" class="collapse">
                        <li>
                            <a class="has-arrow " href="#" aria-expanded="false">Widgets</a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="widget-apps.html">Data Widgets</a></li>
                                <li><a href="widget-data.html">Statestic Widgets</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="has-arrow " href="#" aria-expanded="false">Maps</a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="map-google.html">Google Maps</a></li>
                                <li><a href="map-vector.html">Vector Maps</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="has-arrow " href="#" aria-expanded="false">Icons</a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="icon-material.html">Material Icons</a></li>
                                <li><a href="icon-fontawesome.html">Fontawesome Icons</a></li>
                                <li><a href="icon-themify.html">Themify Icons</a></li>
                                <li><a href="icon-linea.html">Linea Icons</a></li>
                                <li><a href="icon-weather.html">Weather Icons</a></li>
                                <li><a href="icon-simple-lineicon.html">Simple Lineicons</a></li>
                                <li><a href="icon-flag.html">Flag Icons</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="has-arrow " href="#" aria-expanded="false">Charts</a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="chart-morris.html">Morris Chart</a></li>
                                <li><a href="chart-chartist.html">Chartis Chart</a></li>
                                <li><a href="chart-echart.html">Echarts</a></li>
                                <li><a href="chart-flot.html">Flot Chart</a></li>
                                <li><a href="chart-knob.html">Knob Chart</a></li>
                                <li><a href="chart-chart-js.html">Chartjs</a></li>
                                <li><a href="chart-sparkline.html">Sparkline Chart</a></li>
                                <li><a href="chart-extra-chart.html">Extra chart</a></li>
                                <li><a href="chart-peity.html">Peity Charts</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="has-arrow" href="#" aria-expanded="false">Page Layout</a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="layout-single-column.html">1 Column</a></li>
                                <li><a href="layout-fix-header.html">Fix header</a></li>
                                <li><a href="layout-fix-sidebar.html">Fix sidebar</a></li>
                                <li><a href="layout-fix-header-sidebar.html">Fixe header &amp; Sidebar</a></li>
                                <li><a href="layout-boxed.html">Boxed Layout</a></li>
                                <li><a href="layout-logo-center.html">Logo in Center</a></li>
                            </ul>
                        </li>
                        <li>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li> <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i
                            class="mdi mdi-arrange-send-backward"></i><span class="hide-menu">Multi level
                            dd</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="#">item 1.1</a></li>
                        <li><a href="#">item 1.2</a></li>
                        <li> <a class="has-arrow" href="#" aria-expanded="false">Menu 1.3</a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="#">item 1.3.1</a></li>
                                <li><a href="#">item 1.3.2</a></li>
                                <li><a href="#">item 1.3.3</a></li>
                                <li><a href="#">item 1.3.4</a></li>
                            </ul>
                        </li>
                        <li><a href="#">item 1.4</a></li>
                    </ul>
                </li> --}}
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
