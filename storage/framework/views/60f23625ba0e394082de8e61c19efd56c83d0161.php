<aside class="left-sidebar">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="nav-small-cap">PERSONAL</li>
                <?php if(Auth::user()->jabatan != 7): ?>
                <li>
                    <a class="waves-effect waves-dark" href="<?php echo e(route('dashboard')); ?>" aria-expanded="false">
                        <i class="mdi mdi-gauge"></i><span class="hide-menu">Dashboard</span>
                    </a>
                    
                </li>
                <?php endif; ?>

                <?php if(Auth::user()->divisi == 11): ?>
                    
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-shield "></i><span class="hide-menu">Laporan Security</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            
                            <li><a href="<?php echo e(route('security.vehicle-checklist.index')); ?>">Checklist Kendaraan</a></li>
                            <li><a href="<?php echo e(route('security.goods-movement.index')); ?>">Keluar/Masuk Barang</a></li>
                            <li><a href="<?php echo e(route('security.daily-activity.index')); ?>">Laporan Harian / Jurnal</a></li>
                        </ul>
                    </li>
                <?php elseif(Auth::user()->divisi != '8'): ?>
                    <?php if(Auth::user()->jabatan == '7'): ?>
                        <li class="active">
                            <a class="waves-effect waves-dark" href="<?php echo e(route('hr.requests.index')); ?>" aria-expanded="false">
                                <i class="mdi mdi-file-document"></i><span class="hide-menu">Form Pengajuan Karyawan</span>
                            </a>

                        </li>
                        
                        <li>
                            <a class="waves-effect waves-dark" href="<?php echo e(route('ebook-pkb.index')); ?>" aria-expanded="false">
                                <i class="mdi mdi-file-pdf-box"></i><span class="hide-menu">E-Book PKB</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                                <i class="mdi mdi-book"></i><span class="hide-menu">Job Order</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <a href="<?php echo e(route('prepress.job-order.index')); ?>" aria-expanded="false">Job
                                        Prepress</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('development.development-input.form')); ?>"
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
                                        <?php if(auth()->user()->divisi == '6' || auth()->user()->divisi == '1'): ?>
                                            <li><a href="<?php echo e(route('mulai-proses.plan')); ?>">Jadwalkan Plan</a></li>
                                            <li><a href="<?php echo e(route('process.plan-first-prd')); ?>">Timeline Plan</a></li>
                                            <li><a href="<?php echo e(route('monitoring-so.index')); ?>">Monitoring SO</a></li>
                                            <li><a href="<?php echo e(route('inventory-calc-stock.index')); ?>">Inventory Calc.
                                                    Stock</a>
                                            </li>
                                        <?php endif; ?>
                                        <li><a href="<?php echo e(route('process.plan-first-table-uppic')); ?>">Plan Production</a>
                                        </li>
                                        
                                        

                                    </ul>
                                </li>
                                <li>
                                    <a class="has-arrow" href="#" aria-expanded="false">Prepress</a>
                                    <ul aria-expanded="false" class="collapse">
                                        <li><a href="<?php echo e(route('prepress.job-order.data.index')); ?>">Job Prepress</a>
                                        </li>
                                        <?php if(auth()->user()->divisi == '6'): ?>
                                            <li><a href="<?php echo e(route('prepress.planharian.index')); ?>">Plan Harian</a></li>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->divisi == '3'): ?>
                                            <?php if(auth()->user()->jabatan == '4'): ?>
                                                <li><a href="<?php echo e(route('prepress.listplan.index')); ?>">List Plan</a></li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(auth()->user()->divisi == '3'): ?>
                                            <li><a href="<?php echo e(route('prepress.listtask.index')); ?>">List Task</a></li>
                                        <?php endif; ?>
                                        <li><a href="<?php echo e(route('prepress.timelinetask.index')); ?>">Timeline Task</a></li>
                                        <li><a href="<?php echo e(route('process.plan-first-table-prepress')); ?>">Plan Production
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
                                    <a href="<?php echo e(route('label-management.index')); ?>" aria-expanded="false">Label
                                        Management</a>
                                </li>

                                <?php if(Auth::user()->divisi == '1'): ?>
                                    <li>
                                        <a href="<?php echo e(route('admin.supplier-tickets.index')); ?>"
                                            aria-expanded="false">Supplier Tickets</a>
                                    </li>
                                <?php endif; ?>

                                <li>
                                    <a href="<?php echo e(route('forecasting.index')); ?>" aria-expanded="false">Forecasting</a>
                                </li>

                                <li>
                                    <a href="<?php echo e(route('hr.portal-training.index')); ?>" aria-expanded="false">Portal Training Karyawan</a>
                                </li>

                                <li>
                                    <a class="has-arrow" href="#" aria-expanded="false">Human Resource
                                        Development</a>
                                    <ul aria-expanded="false" class="collapse">

                                        
                                        <?php if(Auth::user()->divisi == '1' || (Auth::user()->divisi == '7' && Auth::user()->jabatan != '7')): ?>


                                        <li><a href="<?php echo e(route('hr.approval-settings.index')); ?>">Master Setting Approval</a></li>
                                        <li><a href="<?php echo e(route('hr.approval-settings.divisions.index')); ?>">Setting Approval Per Divisi</a></li>
                                        <li><a href="<?php echo e(route('hr.absence-settings.index')); ?>">Master Setting Absence</a></li>
                                        <?php endif; ?>
                                        
                                        
                                        <?php if(auth()->user()->isHR() || auth()->user()->canApprove()): ?>
                                            
                                            
                                        <?php endif; ?>
                                        <li><a href="#" class="has-arrow">Training</a>
                                            <ul aria-expanded="false" class="collapse">
                                                <li><a href="<?php echo e(route('hr.training.dashboard')); ?>">Dashboard
                                                        Training</a>
                                                </li>
                                                <li><a href="<?php echo e(route('hr.training.index')); ?>">Master Training</a></li>
                                                <li><a href="<?php echo e(route('hr.training.management.index')); ?>">Manajemen
                                                        Peserta</a></li>
                                                <li><a href="<?php echo e(route('hr.training.schedule.index')); ?>">Jadwal
                                                        Training</a></li>
                                                <li><a href="<?php echo e(route('hr.training-validation.index')); ?>">Training
                                                        Validation</a>

                                            </ul>
                                        </li>
                                        <li><a href="<?php echo e(route('hr.security-master.index')); ?>">Master Security</a></li>
                                        <li><a href="#" class="has-arrow">Laporan Security</a>
                                            <ul aria-expanded="false" class="collapse">
                                                <li><a href="<?php echo e(route('security.vehicle-checklist.index')); ?>">Checklist
                                                        Kendaraan</a></li>
                                                <li><a href="<?php echo e(route('security.goods-movement.index')); ?>">Keluar/Masuk
                                                        Barang</a></li>
                                                <li><a href="<?php echo e(route('security.daily-activity.index')); ?>">Laporan
                                                        Harian
                                                        / Jurnal</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="<?php echo e(route('ebook-pkb.logs')); ?>">Log Viewer PKB</a></li>
                                        <li><a href="<?php echo e(route('hr.applicants.index')); ?>">Data Pelamar</a></li>
                                        <?php if(auth()->user()->divisi == '7' || auth()->user()->jabatan == '3'): ?>
                                            <li><a href="<?php echo e(route('hr.employee-data.index')); ?>">Data Karyawan</a></li>
                                        <?php endif; ?>

                                    </ul>
                                </li>

                                <?php if(Auth::user()->divisi == '1'): ?>
                                    
                                    <li>
                                        <a href="<?php echo e(route('paper-procurement.index')); ?>"
                                            aria-expanded="false">Pengajuan
                                            Pembelian Kertas</a>
                                    </li>
                                <?php endif; ?>

                                <li>
                                    <a href="<?php echo e(route('order-fukumi.index')); ?>" aria-expanded="false">Generate Code
                                        Fukumi</a>
                                </li>

                                
                            </ul>
                        </li>

                        <li>
                            <a class="waves-effect waves-dark" href="<?php echo e(route('hr.requests.index')); ?>"
                                aria-expanded="false">
                                <i class="mdi mdi-file"></i><span class="hide-menu">Form Perizinan</span>
                            </a>
                        </li>
                        <li>
                            <a class="waves-effect waves-dark" href="<?php echo e(route('development.rnd-workspace.index')); ?>"
                                aria-expanded="false">
                                <i class="mdi mdi-cube"></i><span class="hide-menu">Development</span>
                            </a>
                        </li>
                        <li>
                            <a class="waves-effect waves-dark" href="<?php echo e(route('ebook-pkb.index')); ?>" aria-expanded="false">
                                <i class="mdi mdi-file-pdf-box"></i><span class="hide-menu">E-Book PKB</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if(Auth::user()->divisi == '1' || Auth::user()->divisi == '8'): ?>
                    

                    
                <?php endif; ?>


                <?php if((int) Auth::user()->divisi === 1): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-file"></i><span class="hide-menu">Master</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="<?php echo e(route('working-days.index')); ?>">Hari Kerja</a></li>
                            <li><a href="<?php echo e(route('holiday-days.index')); ?>">Hari Libur</a></li>
                            <li><a href="<?php echo e(route('machine.index')); ?>">Mesin</a></li>
                            <li><a href="<?php echo e(route('database-machines.index')); ?>">Database Mesin</a></li>
                            <li><a href="<?php echo e(route('mapping-item.index')); ?>">Mapping Item</a></li>
                            <li><a href="<?php echo e(route('master-data-prepress.index')); ?>">Kategori Kerja Prepress</a></li>
                            <li><a href="<?php echo e(route('jenis-pekerjaan-prepress.index')); ?>">Jenis Pekerjaan Prepress</a>
                            </li>
                            <li>
                                <a class="has-arrow" href="#" aria-expanded="false">User Management</a>
                                <ul aria-expanded="false" class="collapse">
                                    <li><a href="<?php echo e(route('user.index')); ?>">User</a></li>
                                    <li><a href="<?php echo e(route('divisi.index')); ?>">Divisi</a></li>
                                    <li><a href="<?php echo e(route('jabatan.index')); ?>">Jabatan</a></li>
                                    <li><a href="<?php echo e(route('level.index')); ?>">Level</a></li>
                                </ul>
                            </li>
                            <li><a href="<?php echo e(route('settings.index')); ?>">Setting</a></li>
                            <li><a href="<?php echo e(route('development.master-proses')); ?>">Master Proses Development</a></li>
                            <li><a href="<?php echo e(route('development-email-notification-settings.index')); ?>">Master Email
                                    Development</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if(Auth::user()->divisi != '8' && Auth::user()->divisi != '11'): ?>

                    <?php if(AUth::user()->jabatan != '7'): ?>
                        <li>
                            <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                                <i class="mdi mdi-book-open-variant"></i>
                                <span class="hide-menu">Report</span>
                            </a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <a href="<?php echo e(route('report.plan-production.index')); ?>">Plan Production</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('report.job-order-prepress.index')); ?>">Job Order Prepress</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('report.transportation-cost.index')); ?>">Transportation Cost</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('report.work-order-percentage.index')); ?>">WO Percentage</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('report.development.index')); ?>">Development Item</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('report.work-order-good-issue.index')); ?>">WO Good Issue</a>
                                </li>
                                <li>
                                    <a href="<?php echo e(route('admin.supplier-tickets.supplier-arrival-report')); ?>">Supplier
                                        Arrival Report</a>
                                </li>
                                <li><a href="<?php echo e(route('hr.reports.index')); ?>">Human Resource Development</a></li>


                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if(Auth::user()->divisi == '1'): ?>
                    <li>
                        <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                            <i class="mdi mdi-widgets"></i>
                            <span class="hide-menu">Tools</span>
                        </a>
                        <ul aria-expanded="false" class="collapse">
                            <li><a href="<?php echo e(route('email-notification-settings.index')); ?>">Master Setting Email</a>
                            </li>
                            <li><a href="<?php echo e(route('notifications.index')); ?>">Semua Notifikasi</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                
                
                
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/layouts/topbar-nav.blade.php ENDPATH**/ ?>