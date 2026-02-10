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
                            <?php if(Auth::user()): ?>
                                <?php if(Auth::user()->divisi != '8'): ?>
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
                                <?php elseif(Auth::user()): ?>
                                    <li>
                                        <div class="text-center p-3">
                                            <p class="text-muted">Tidak ada notifikasi</p>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <!-- ============================================================== -->
                <!-- End Comment -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Messages -->
                <!-- ============================================================== -->
                
                <!-- ============================================================== -->
                <!-- End Messages -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Messages -->
                <!-- ============================================================== -->
                
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
                

                <!-- ============================================================== -->
                <!-- Profile -->
                <!-- ============================================================== -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href=""
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
                            src="<?php echo e(asset('sipo_krisan/public/assets/images/logo-kop.png')); ?>" alt="user"
                            class="profile-pic" /></a>
                    <div class="dropdown-menu dropdown-menu-right scale-up">
                        <ul class="dropdown-user">
                            <li>
                                <div class="dw-user-box">
                                    <div class="u-img"><img
                                            src="<?php echo e(asset('sipo_krisan/public/assets/images/logo-kop.png')); ?>"
                                            alt="user"></div>
                                    <div class="u-text">
                                        <h4><?php echo e(Auth::user()->name ?? 'Nama tidak ditemukan'); ?></h4>
                                        <p class="text-muted">
                                            <?php echo e(Auth::user()->jabatanUser->jabatan ?? 'Jabatan tidak ditemukan'); ?></p>
                                        <h5 class="text-muted">
                                            <?php echo e(Auth::user()->divisiUser->divisi ?? 'Divisi tidak ditemukan'); ?></h5>
                                    </div>
                                </div>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li>
                                <a href="<?php echo e(route('account.setting')); ?>"
                                    class="dropdown-item d-flex align-items-center"><i class="ti-user"></i>&nbsp;Account
                                    Setting</a>
                            </li>
                            <li role="separator" class="divider"></li>

                            <li>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
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
            window.location.href = '<?php echo e(route('notifications.index')); ?>';
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
            url: '<?php echo e(route('notifications.unread')); ?>',
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
            url: '<?php echo e(route('notifications.count')); ?>',
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
            url: '<?php echo e(route('notifications.mark-all-read')); ?>',
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
<?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/layouts/topbar.blade.php ENDPATH**/ ?>