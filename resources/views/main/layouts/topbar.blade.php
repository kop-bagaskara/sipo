<!-- Notification System CSS -->
<style>
    .notification-count {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .notification-item {
        display: block;
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
        text-decoration: none;
        color: #333;
        transition: background-color 0.2s;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
        text-decoration: none;
        color: #333;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item .mail-contnet h5 {
        margin: 0 0 5px 0;
        font-size: 14px;
        font-weight: 600;
    }

    .notification-item .mail-desc {
        font-size: 12px;
        color: #6c757d;
        display: block;
        margin-bottom: 5px;
    }

    .notification-item .time {
        font-size: 11px;
        color: #adb5bd;
    }

    .drop-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
    }

    .drop-title button {
        font-size: 11px;
        padding: 4px 8px;
    }

    .spinner-border {
        width: 1.5rem;
        height: 1.5rem;
    }
</style>

<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        <!-- ============================================================== -->
        <!-- Logo -->
        <!-- ============================================================== -->
        {{-- JS when reload page, focused on logo, not element in above --}}
        <div class="navbar-header">
            <a class="navbar-brand" href="index.html">
                <span class="text-white bebas-neue font-weight-bold">SiPO - Krisanthium</span>
            </a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->
        <div class="navbar-collapse">
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav mr-auto mt-md-0">
                <!-- This is  -->
                <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark"
                        href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                <!-- ============================================================== -->
                <!-- Comment -->
                <!-- ============================================================== -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-muted text-muted waves-effect waves-dark" href=""
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <button
                            class="btn btn-sm btn-outline-primary"> <i class="mdi mdi-bell"></i>Notification
                            <div class="notify"> <span class="heartbit"></span> <span class="point"></span> </div>
                            <span class="notification-count" id="notificationCount" style="display: none;"></span>
                        </button>
                    </a>
                    <div class="dropdown-menu mailbox animated slideInUp">
                        <ul>
                            <li>
                                <div class="drop-title">
                                    <span>Notifikasi</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary float-right"
                                        id="markAllRead">
                                        Tandai Semua Dibaca
                                    </button>
                                </div>
                            </li>
                            @if (Auth::user())
                                @if (Auth::user()->divisi != '8')
                                    <li>
                                        <div class="message-center" id="notificationList">
                                            <div class="text-center p-3">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                                <p class="mt-2">Memuat notifikasi...</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="nav-link text-center" href="javascript:void(0);"
                                            id="viewAllNotifications">
                                            <strong>Lihat semua notifikasi</strong> <i class="fa fa-angle-right"></i>
                                        </a>
                                    </li>
                                @elseif (Auth::user())
                                    <li>
                                        <div class="text-center p-3">
                                            <p class="text-muted">Tidak ada notifikasi</p>
                                        </div>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                </li>
                <!-- ============================================================== -->
                <!-- End Comment -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Messages -->
                <!-- ============================================================== -->
                {{-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" id="2"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="mdi mdi-email"></i>
                        <div class="notify"> <span class="heartbit"></span> <span class="point"></span>
                        </div>
                    </a>
                    <div class="dropdown-menu mailbox animated slideInUp" aria-labelledby="2">
                        <ul>
                            <li>
                                <div class="drop-title">You have 4 new messages</div>
                            </li>
                            <li>
                                <div class="message-center">
                                    <!-- Message -->
                                    <a href="#">
                                        <div class="user-img"> <img src="{{ asset('sipo_krisan/public/news/images/users/1.jpg') }}"
                                                alt="user" class="img-circle"> <span
                                                class="profile-status online pull-right"></span> </div>
                                        <div class="mail-contnet">
                                            <h5>Pavan kumar</h5> <span class="mail-desc">Just see the my
                                                admin!</span> <span class="time">9:30 AM</span>
                                        </div>
                                    </a>
                                    <!-- Message -->
                                    <a href="#">
                                        <div class="user-img"> <img src="{{ asset('sipo_krisan/public/news/images/users/2.jpg') }}" alt="user"
                                                class="img-circle"> <span class="profile-status busy pull-right"></span>
                                        </div>
                                        <div class="mail-contnet">
                                            <h5>Sonu Nigam</h5> <span class="mail-desc">I've sung a song! See
                                                you at</span> <span class="time">9:10 AM</span>
                                        </div>
                                    </a>
                                    <!-- Message -->
                                    <a href="#">
                                        <div class="user-img"> <img src="{{ asset('sipo_krisan/public/news/images/users/3.jpg') }}"
                                                alt="user" class="img-circle"> <span
                                                class="profile-status away pull-right"></span> </div>
                                        <div class="mail-contnet">
                                            <h5>Arijit Sinh</h5> <span class="mail-desc">I am a singer!</span>
                                            <span class="time">9:08 AM</span>
                                        </div>
                                    </a>
                                    <!-- Message -->
                                    <a href="#">
                                        <div class="user-img"> <img src="{{ asset('sipo_krisan/public/news/images/users/4.jpg') }}"
                                                alt="user" class="img-circle"> <span
                                                class="profile-status offline pull-right"></span> </div>
                                        <div class="mail-contnet">
                                            <h5>Pavan kumar</h5> <span class="mail-desc">Just see the my
                                                admin!</span> <span class="time">9:02 AM</span>
                                        </div>
                                    </a>
                                </div>
                            </li>
                            <li>
                                <a class="nav-link text-center" href="javascript:void(0);"> <strong>See all
                                        e-Mails</strong> <i class="fa fa-angle-right"></i> </a>
                            </li>
                        </ul>
                    </div>
                </li> --}}
                <!-- ============================================================== -->
                <!-- End Messages -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Messages -->
                <!-- ============================================================== -->
                {{-- <li class="nav-item dropdown mega-dropdown"> <a
                        class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href=""
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                            class="mdi mdi-view-grid"></i></a>
                    <div class="dropdown-menu animated slideInUp">
                        <ul class="mega-dropdown-menu row">
                            <li class="col-lg-3 col-xlg-2 m-b-30">
                                <h4 class="m-b-20">CAROUSEL</h4>
                                <!-- CAROUSEL -->
                                <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner" role="listbox">
                                        <div class="carousel-item active">
                                            <div class="container"> <img class="d-block img-fluid"
                                                    src="{{ asset('sipo_krisan/public/news/images/big/img1.jpg') }}" alt="First slide">
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <div class="container"><img class="d-block img-fluid"
                                                    src="{{ asset('sipo_krisan/public/news/images/big/img2.jpg') }}" alt="Second slide">
                                            </div>
                                        </div>
                                        <div class="carousel-item">
                                            <div class="container"><img class="d-block img-fluid"
                                                    src="{{ asset('sipo_krisan/public/news/images/big/img3.jpg') }}" alt="Third slide">
                                            </div>
                                        </div>
                                    </div>
                                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                                        data-slide="prev"> <span class="carousel-control-prev-icon"
                                            aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span> </a>
                                    <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                                        data-slide="next"> <span class="carousel-control-next-icon"
                                            aria-hidden="true"></span>
                                        <span class="sr-only">Next</span> </a>
                                </div>
                                <!-- End CAROUSEL -->
                            </li>
                            <li class="col-lg-3 m-b-30">
                                <h4 class="m-b-20">ACCORDION</h4>
                                <!-- Accordian -->
                                <div id="accordion" class="nav-accordion" role="tablist"
                                    aria-multiselectable="true">
                                    <div class="card">
                                        <div class="card-header" role="tab" id="headingOne">
                                            <h5 class="mb-0">
                                                <a data-toggle="collapse" data-parent="#accordion"
                                                    href="#collapseOne" aria-expanded="true"
                                                    aria-controls="collapseOne">
                                                    Collapsible Group Item #1
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapseOne" class="collapse show" role="tabpanel"
                                            aria-labelledby="headingOne">
                                            <div class="card-body"> Anim pariatur cliche reprehenderit, enim
                                                eiusmod high. </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" role="tab" id="headingTwo">
                                            <h5 class="mb-0">
                                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion"
                                                    href="#collapseTwo" aria-expanded="false"
                                                    aria-controls="collapseTwo">
                                                    Collapsible Group Item #2
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapseTwo" class="collapse" role="tabpanel"
                                            aria-labelledby="headingTwo">
                                            <div class="card-body"> Anim pariatur cliche reprehenderit, enim
                                                eiusmod high life accusamus terry richardson ad squid. </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" role="tab" id="headingThree">
                                            <h5 class="mb-0">
                                                <a class="collapsed" data-toggle="collapse" data-parent="#accordion"
                                                    href="#collapseThree" aria-expanded="false"
                                                    aria-controls="collapseThree">
                                                    Collapsible Group Item #3
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapseThree" class="collapse" role="tabpanel"
                                            aria-labelledby="headingThree">
                                            <div class="card-body"> Anim pariatur cliche reprehenderit, enim
                                                eiusmod high life accusamus terry richardson ad squid. </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="col-lg-3  m-b-30">
                                <h4 class="m-b-20">CONTACT US</h4>
                                <!-- Contact -->
                                <form>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="exampleInputname1"
                                            placeholder="Enter Name">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" class="form-control" placeholder="Enter email">
                                    </div>
                                    <div class="form-group">
                                        <textarea class="form-control" id="exampleTextarea" rows="3" placeholder="Message"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-info">Submit</button>
                                </form>
                            </li>
                            <li class="col-lg-3 col-xlg-4 m-b-30">
                                <h4 class="m-b-20">List style</h4>
                                <!-- List style -->
                                <ul class="list-style-none">
                                    <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i>
                                            You can give link</a></li>
                                    <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i>
                                            Give link</a></li>
                                    <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i>
                                            Another Give link</a></li>
                                    <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i>
                                            Forth link</a></li>
                                    <li><a href="javascript:void(0)"><i class="fa fa-check text-success"></i>
                                            Another fifth link</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </li> --}}
                <!-- ============================================================== -->
                <!-- End Messages -->
                <!-- ============================================================== -->
            </ul>
            <!-- ============================================================== -->
            <!-- User profile and search -->
            <!-- ============================================================== -->
            <ul class="navbar-nav my-lg-0">
                <!-- ============================================================== -->
                <!-- Search -->
                <!-- ============================================================== -->
                {{-- <li class="nav-item hidden-sm-down search-box"> <a
                        class="nav-link hidden-sm-down text-muted waves-effect waves-dark" href="javascript:void(0)"><i
                            class="ti-search"></i></a>
                    <form class="app-search">
                        <input type="text" class="form-control" placeholder="Search & enter"> <a class="srh-btn"><i
                                class="ti-close"></i></a>
                    </form>
                </li> --}}

                <!-- ============================================================== -->
                <!-- Profile -->
                <!-- ============================================================== -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href=""
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
                            src="{{ asset('sipo_krisan/public/assets/images/logo-kop.png') }}" alt="user"
                            class="profile-pic" /></a>
                    <div class="dropdown-menu dropdown-menu-right scale-up">
                        <ul class="dropdown-user">
                            <li>
                                <div class="dw-user-box">
                                    <div class="u-img"><img
                                            src="{{ asset('sipo_krisan/public/assets/images/logo-kop.png') }}"
                                            alt="user"></div>
                                    <div class="u-text">
                                        <h4>{{ Auth::user()->name ?? 'Nama tidak ditemukan' }}</h4>
                                        <p class="text-muted">
                                            {{ Auth::user()->jabatanUser->jabatan ?? 'Jabatan tidak ditemukan' }}</p>
                                        <h5 class="text-muted">
                                            {{ Auth::user()->divisiUser->divisi ?? 'Divisi tidak ditemukan' }}</h5>
                                    </div>
                                </div>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li>
                                <a href="{{ route('account.setting') }}"
                                    class="dropdown-item d-flex align-items-center"><i class="ti-user"></i>&nbsp;Account
                                    Setting</a>
                            </li>
                            <li role="separator" class="divider"></li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center"><i
                                            class="fa fa-power-off"></i>&nbsp;Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

<!-- Notification System JavaScript -->
<script>
    $(document).ready(function() {
        // Load notification COUNT on page load (list is lazy-loaded when dropdown is opened)
        loadNotificationCount();

        // Auto-refresh notifications every 2 minutes (120000 ms)
        // Refresh hanya saat halaman di-refresh atau setiap 2 menit sekali
        setInterval(function() {
            loadNotificationCount();
            // Refresh list only if dropdown is currently open
            if (isNotificationDropdownOpen()) {
                loadNotifications();
            }
        }, 120000); // 2 menit = 120000 milliseconds

        // Mark all as read button
        $('#markAllRead').on('click', function() {
            markAllNotificationsAsRead();
        });

        // View all notifications
        $('#viewAllNotifications').on('click', function() {
            window.location.href = '{{ route('notifications.index') }}';
        });

        // Lazy-load notification list when dropdown is opened
        const $notificationDropdown = $('#notificationList').closest('.nav-item.dropdown');
        $notificationDropdown.on('shown.bs.dropdown', function() {
            loadNotifications();
        });
    });

    function isNotificationDropdownOpen() {
        const $notificationDropdown = $('#notificationList').closest('.nav-item.dropdown');
        return $notificationDropdown.hasClass('show') ||
            $notificationDropdown.find('.dropdown-menu').hasClass('show');
    }

    function loadNotifications() {
        $.ajax({
            url: '{{ route('notifications.unread') }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    displayNotifications(response.data);
                }
            },
            error: function() {
                $('#notificationList').html(
                    '<div class="text-center p-3"><p class="text-muted">Gagal memuat notifikasi</p></div>'
                );
            }
        });
    }

    function loadNotificationCount() {
        $.ajax({
            url: '{{ route('notifications.count') }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    updateNotificationCount(response.count);
                }
            }
        });
    }

    function displayNotifications(notifications) {
        if (notifications.length === 0) {
            $('#notificationList').html(
                '<div class="text-center p-3"><p class="text-muted">Tidak ada notifikasi baru</p></div>');
            return;
        }

        let html = '';
        notifications.forEach(function(notification) {
            const data = notification.data;
            const timeAgo = getTimeAgo(notification.created_at);

            html += `
                <a href="${data.url}" class="notification-item" data-id="${notification.id}">
                    <div class="btn btn-info btn-circle">
                        <i class="mdi mdi-bell"></i>
                    </div>
                    <div class="mail-contnet">
                        <h5>${data.title}</h5>
                        <span class="mail-desc">${data.message}</span>
                        <span class="time">${timeAgo}</span>
                    </div>
                </a>
            `;
        });

        $('#notificationList').html(html);

        // Add click event to mark as read
        $('.notification-item').on('click', function() {
            const notificationId = $(this).data('id');
            markNotificationAsRead(notificationId);
        });
    }

    function updateNotificationCount(count) {
        if (count > 0) {
            $('#notificationCount').text(count).show();
            $('.notify .point').show();
        } else {
            $('#notificationCount').hide();
            $('.notify .point').hide();
        }
    }

    function markNotificationAsRead(notificationId) {
        $.ajax({
            url: `/notifications/${notificationId}/read`,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                loadNotificationCount();
            }
        });
    }

    function markAllNotificationsAsRead() {
        $.ajax({
            url: '{{ route('notifications.mark-all-read') }}',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                loadNotifications();
                loadNotificationCount();
            }
        });
    }

    function getTimeAgo(timestamp) {
        const now = new Date();
        const created = new Date(timestamp);
        const diffInSeconds = Math.floor((now - created) / 1000);

        if (diffInSeconds < 60) return 'Baru saja';
        if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' menit yang lalu';
        if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' jam yang lalu';
        return Math.floor(diffInSeconds / 86400) + ' hari yang lalu';
    }
</script>
