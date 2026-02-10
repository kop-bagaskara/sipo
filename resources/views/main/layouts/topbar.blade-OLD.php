<header id="page-topbar">
    <div class="navbar-header">
        <!-- LOGO -->
        <div class="navbar-brand-box d-flex align-items-left">
            <a href="{{ route('dashboard') }}" class="logo">
                <i class="mdi mdi-album"></i>
                <span>
                    SCHEDULE PRODUCTION - Krisanthium
                </span>
            </a>

            <button type="button" class="btn btn-sm mr-2 font-size-16 d-lg-none header-item waves-effect waves-light"
                data-toggle="collapse" data-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex align-items-center">

            <div class="dropdown d-inline-block" style="width: 80px; ">
                <button type="button" class="btn header-item noti-icon waves-effect waves-light"
                    id="page-header-notifications-dropdown" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="true" style="width: 100px;">
                    <i class="mdi mdi-bell"></i>


                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0"
                    aria-labelledby="page-header-notifications-dropdown" x-placement="bottom-end"
                    style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-272px, 70px, 0px);">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0"> Notifications </h6>
                            </div>
                            <div class="col-auto">
                            </div>
                        </div>
                    </div>
                    <div data-simplebar="init" style="max-height: 230px;">
                        <div class="simplebar-wrapper" style="margin: 0px;">
                            <div class="simplebar-height-auto-observer-wrapper">
                                <div class="simplebar-height-auto-observer"></div>
                            </div>
                            <div class="simplebar-mask">
                                <div class="simplebar-offset" style="right: -17px; bottom: 0px;">
                                    <div class="simplebar-content-wrapper"
                                        style="height: auto; overflow: hidden scroll;">
                                        <div class="simplebar-content" style="padding: 0px;">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="simplebar-placeholder" style="width: auto; height: 272px;"></div>
                        </div>
                        <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                            <div class="simplebar-scrollbar"
                                style="transform: translate3d(0px, 0px, 0px); display: none;"></div>
                        </div>
                        <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                            <div class="simplebar-scrollbar"
                                style="transform: translate3d(0px, 0px, 0px); display: block; height: 194px;"></div>
                        </div>
                    </div>
                    <div class="p-2 border-top">
                        {{-- <a class="btn btn-sm btn-light btn-block text-center" href="{{ route('data-schedule-etask.index') }}">
                            <i class="mdi mdi-arrow-down-circle mr-1"></i> Load More..
                        </a> --}}
                    </div>
                </div>
            </div>

            <div class="dropdown d-inline-block ml-2">



                <button type="button" class="btn header-item waves-effect waves-light" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="{{ asset('new/assets/images/users/avatar-3.jpg') }}" alt="Header Avatar">
                    <span class="d-none d-sm-inline-block ml-1">{{ auth()->user()->name }}</span>
                    <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                </button>




                <div class="dropdown-menu dropdown-menu-right">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="dropdown-item d-flex align-items-center justify-content-between">Logout</button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</header>
