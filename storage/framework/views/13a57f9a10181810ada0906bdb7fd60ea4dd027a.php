

<?php $__env->startSection('title', 'Semua Notifikasi'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-bell mr-2"></i>
                        Semua Notifikasi
                    </h4>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" id="markAllRead">
                            <i class="mdi mdi-check-all mr-1"></i>
                            Tandai Semua Dibaca
                        </button>
                        <button type="button" class="btn btn-info btn-sm ml-2" id="refreshNotifications">
                            <i class="mdi mdi-refresh mr-1"></i>
                            Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="statusFilter">Filter Status:</label>
                                <select class="form-control" id="statusFilter">
                                    <option value="">Semua</option>
                                    <option value="unread">Belum Dibaca</option>
                                    <option value="read">Sudah Dibaca</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="typeFilter">Filter Tipe:</label>
                                <select class="form-control" id="typeFilter">
                                    <option value="">Semua Tipe</option>
                                    <option value="job_order_prepress">Job Order Prepress</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="notificationsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Status</th>
                                    <th>Tipe</th>
                                    <th>Judul</th>
                                    <th>Pesan</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="notificationsTableBody">
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <p class="mt-2">Memuat notifikasi...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        <nav aria-label="Notification pagination">
                            <ul class="pagination" id="pagination">
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
.notification-unread {
    background-color: #f8f9fa;
    font-weight: 600;
}

.notification-read {
    background-color: #ffffff;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.status-unread {
    background-color: #dc3545;
    color: white;
}

.status-read {
    background-color: #28a745;
    color: white;
}

.type-badge {
    background-color: #17a2b8;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
$(document).ready(function() {
    let currentPage = 1;
    let currentFilters = {
        status: '',
        type: ''
    };

    // Load notifications on page load
    loadAllNotifications();

    // Filter change events
    $('#statusFilter, #typeFilter').on('change', function() {
        currentFilters.status = $('#statusFilter').val();
        currentFilters.type = $('#typeFilter').val();
        currentPage = 1;
        loadAllNotifications();
    });

    // Mark all as read
    $('#markAllRead').on('click', function() {
        markAllNotificationsAsRead();
    });

    // Refresh button
    $('#refreshNotifications').on('click', function() {
        loadAllNotifications();
    });

    function loadAllNotifications() {
        const params = new URLSearchParams({
            page: currentPage,
            ...currentFilters
        });

        $.ajax({
            url: '<?php echo e(route("notifications.all")); ?>?' + params.toString(),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    displayAllNotifications(response.data);
                    updatePagination(response.meta);
                }
            },
            error: function() {
                $('#notificationsTableBody').html(`
                    <tr>
                        <td colspan="6" class="text-center text-danger">
                            <i class="mdi mdi-alert-circle mr-2"></i>
                            Gagal memuat notifikasi
                        </td>
                    </tr>
                `);
            }
        });
    }

    function displayAllNotifications(notifications) {
        if (notifications.length === 0) {
            $('#notificationsTableBody').html(`
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <i class="mdi mdi-information mr-2"></i>
                        Tidak ada notifikasi
                    </td>
                </tr>
            `);
            return;
        }

        let html = '';
        notifications.forEach(function(notification) {
            const data = notification.data;
            const isUnread = !notification.read_at;
            const rowClass = isUnread ? 'notification-unread' : 'notification-read';
            const statusClass = isUnread ? 'status-unread' : 'status-read';
            const statusText = isUnread ? 'Belum Dibaca' : 'Sudah Dibaca';
            const timeAgo = getTimeAgo(notification.created_at);

            html += `
                <tr class="${rowClass}" data-id="${notification.id}">
                    <td>
                        <span class="status-badge ${statusClass}">${statusText}</span>
                    </td>
                    <td>
                        <span class="type-badge">${getNotificationType(data.title)}</span>
                    </td>
                    <td>
                        <strong>${data.title}</strong>
                    </td>
                    <td>${data.message}</td>
                    <td>${timeAgo}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="${data.url}" class="btn btn-info btn-sm" title="Lihat Detail">
                                <i class="mdi mdi-eye"></i>
                            </a>
                            ${isUnread ? `
                                <button type="button" class="btn btn-success btn-sm mark-read" title="Tandai Dibaca">
                                    <i class="mdi mdi-check"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        });

        $('#notificationsTableBody').html(html);

        // Add click event for mark as read
        $('.mark-read').on('click', function() {
            const notificationId = $(this).closest('tr').data('id');
            markNotificationAsRead(notificationId);
        });
    }

    function updatePagination(meta) {
        if (meta.last_page <= 1) {
            $('#pagination').html('');
            return;
        }

        let html = '';
        
        // Previous button
        if (meta.current_page > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${meta.current_page - 1}">Previous</a></li>`;
        }

        // Page numbers
        for (let i = 1; i <= meta.last_page; i++) {
            const activeClass = i === meta.current_page ? 'active' : '';
            html += `<li class="page-item ${activeClass}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }

        // Next button
        if (meta.current_page < meta.last_page) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${meta.current_page + 1}">Next</a></li>`;
        }

        $('#pagination').html(html);

        // Pagination click events
        $('#pagination .page-link').on('click', function(e) {
            e.preventDefault();
            currentPage = parseInt($(this).data('page'));
            loadAllNotifications();
        });
    }

    function markNotificationAsRead(notificationId) {
        $.ajax({
            url: `/notifications/${notificationId}/read`,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                loadAllNotifications();
            }
        });
    }

    function markAllNotificationsAsRead() {
        $.ajax({
            url: '<?php echo e(route("notifications.mark-all-read")); ?>',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function() {
                loadAllNotifications();
            }
        });
    }

    function getNotificationType(title) {
        if (title.includes('Job Order')) return 'Job Order';
        return 'Sistem';
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
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/notifications/index.blade.php ENDPATH**/ ?>