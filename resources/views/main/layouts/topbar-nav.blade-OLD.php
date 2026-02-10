<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="mdi mdi-home-analytics"></i>Dashboard
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-pages" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-file"></i>Data <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-pages">

                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-auth"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    PPIC<div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-auth">
                                    <a href="{{ route('process.bryntum-scheduler') }}" class="dropdown-item">Timeline Plan Production</a>

                                    {{-- <a href="" class="dropdown-item">Inventory Control</a> --}}
                                </div>
                            </div>

                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-tables"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    PREPRESS <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-tables">
                                    {{-- <a href="{{ route('prepress.job-order.index') }}" class="dropdown-item">Input Job Order</a> --}}
                                    <a href="{{ route('prepress.job-order.data.index') }}" class="dropdown-item">Data Job Order</a>
                                    <a href="{{ route('prepress.data-plan.index') }}" class="dropdown-item">Plan Prepress</a>
                                </div>
                            </div>



                            {{-- <a href="{{ route('plan-first.data') }}" class="dropdown-item">Plan Awal</a> --}}
                            {{-- <a href="{{ route('plan-harian.data') }}" class="dropdown-item">Plan Harian</a> --}}
                            {{-- <a href="#" class="dropdown-item">Repeat Production</a> --}}
                            {{-- <a href="{{ route('plan.first.production') }}" class="dropdown-item">Plan New Updated</a> --}}



                        </div>
                    </li>

                    {{-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-process" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-cog"></i>Process <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-process">
                            <a href="{{ route('plan.first.production') }}" class="dropdown-item">Plan First Production</a>
                        </div>
                    </li> --}}

                    @if (Auth::user()->jabatan != 1)


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-pages" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-file-presentation-box"></i>Master <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-pages">
                            <a href="{{ route('machine.index') }}" class="dropdown-item">Machine</a>
                            <a href="{{ route('series-material.index') }}" class="dropdown-item">Series Material</a>
                            <a href="{{ route('database-machines.index') }}" class="dropdown-item">Database Machine</a>
                            <a href="{{ route('mapping-item.index') }}" class="dropdown-item">Mapping Item</a>
                            <a href="{{ route('master-data-prepress.index') }}" class="dropdown-item">Master Data Prepress</a>
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-auth"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Bagian <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-auth">
                                    <a href="{{ route('divisi.index') }}" class="dropdown-item">Divisi</a>
                                    <a href="{{ route('level.index') }}" class="dropdown-item">Level</a>
                                    <a href={{ route('jabatan.index') }} class="dropdown-item">Jabatan</a>
                                </div>
                            </div>
                            {{-- <a href="#" class="dropdown-item">Downtime Reason</a> --}}
                            <a href="{{ route('user.index') }}" class="dropdown-item">User</a>
                            <a href="#" class="dropdown-item">Setting</a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="mdi mdi-file-document-box"></i>Report
                        </a>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-pages"
                            role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-file-presentation-box"></i>Tools <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-pages">
                            <a href="{{ route('inventory-calc-stock.index') }}" class="dropdown-item">Inventory Calc.
                                Stock</a>
                            {{-- <a href="{{ route('stc.tools') }}" class="dropdown-item">Stock Transfer</a> --}}
                            {{-- <a href="#" class="dropdown-item">Create Master Material</a> --}}
                            {{-- <a href="{{ route('report-po.tools') }}" class="dropdown-item">Report Purchase Order</a> --}}
                        </div>
                    </li>
                    @endif

                </ul>
            </div>
        </nav>
    </div>
</div>
