<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'One Way Interview') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .navbar-brand {
            padding-top: .75rem;
            padding-bottom: .75rem;
        }
        
        .navbar .navbar-toggler {
            top: .25rem;
            right: 1rem;
        }
        
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            .sidebar {
                margin-left: -100%;
            }
            .sidebar.show {
                margin-left: 0;
            }
        }
    </style>
    
    <!-- Font Awesome for notification icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="{{ route('admin.dashboard') }}">
            {{ config('app.name', 'One Way Interview') }}
        </a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav">
            <!-- Notifications Dropdown -->
            <div class="nav-item dropdown">
                <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell"></i>
                    <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                        0
                        <span class="visually-hidden">unread notifications</span>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="min-width: 300px; max-height: 400px; overflow-y: auto;">
                    <li><h6 class="dropdown-header">Notifications</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li id="notificationList">
                        <div class="text-center p-3">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-center" href="{{ route('notifications.index') }}">
                            <i class="bi bi-list-ul"></i> View All Notifications
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-item text-nowrap">
                <a class="nav-link px-3" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/jobs*') ? 'active' : '' }}" href="{{ route('admin.jobs.index') }}">
                                <i class="bi bi-briefcase"></i> Jobs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}" href="#">
                                <i class="bi bi-people"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/applications*') ? 'active' : '' }}" href="#">
                                <i class="bi bi-file-earmark-text"></i> Applications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}" href="#">
                                <i class="bi bi-graph-up"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}" href="#">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </li>
                    </ul>

                    <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>Quick Links</span>
                    </h6>
                    <ul class="nav flex-column mb-2">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.jobs.create') }}">
                                <i class="bi bi-plus-circle"></i> Create Job
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('notifications.index') }}">
                                <i class="bi bi-bell"></i> Notifications
                                <span id="sidebarNotificationBadge" class="badge bg-danger d-none">0</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Notification functionality
        class NotificationManager {
            constructor() {
                this.unreadCount = 0;
                this.init();
            }
            
            init() {
                this.loadUnreadCount();
                this.loadRecentNotifications();
                this.setupEventListeners();
                this.startPolling();
            }
            
            async loadUnreadCount() {
                try {
                    const response = await fetch('{{ route("notifications.unread-count") }}');
                    const data = await response.json();
                    this.updateUnreadCount(data.count);
                } catch (error) {
                    console.error('Failed to load unread count:', error);
                }
            }
            
            async loadRecentNotifications() {
                try {
                    const response = await fetch('{{ route("notifications.recent") }}');
                    const data = await response.json();
                    this.renderNotifications(data.notifications);
                } catch (error) {
                    console.error('Failed to load recent notifications:', error);
                }
            }
            
            updateUnreadCount(count) {
                this.unreadCount = count;
                const badge = document.getElementById('notificationBadge');
                const sidebarBadge = document.getElementById('sidebarNotificationBadge');
                
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('d-none');
                    sidebarBadge.textContent = count > 99 ? '99+' : count;
                    sidebarBadge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                    sidebarBadge.classList.add('d-none');
                }
            }
            
            renderNotifications(notifications) {
                const listContainer = document.getElementById('notificationList');
                
                if (notifications.length === 0) {
                    listContainer.innerHTML = '
                        <div class="text-center p-3 text-muted">
                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                            <p class="mb-0">No new notifications</p>
                        </div>
                    ';
                    return;
                }
                
                const notificationsHtml = notifications.map(notification => `
                    <li>
                        <a class="dropdown-item ${notification.data && notification.data.job_id ? 'notification-link' : ''}" 
                           href="${notification.data && notification.data.job_id ? '/admin/jobs/' + notification.data.job_id : '#'}" 
                           data-notification-id="${notification.id}">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-${notification.type_icon} text-${notification.type_color}"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <div class="fw-bold">${notification.title}</div>
                                    <div class="small text-muted">${notification.message}</div>
                                    <div class="small text-muted">${notification.created_at}</div>
                                </div>
                            </div>
                        </a>
                    </li>
                `).join('');
                
                listContainer.innerHTML = notificationsHtml;
            }
            
            setupEventListeners() {
                // Mark notification as read when clicked
                document.addEventListener('click', (e) => {
                    if (e.target.closest('.notification-link')) {
                        const notificationId = e.target.closest('.notification-link').dataset.notificationId;
                        this.markAsRead(notificationId);
                    }
                });
                
                // Refresh notifications when dropdown is shown
                document.getElementById('notificationDropdown').addEventListener('shown.bs.dropdown', () => {
                    this.loadRecentNotifications();
                });
            }
            
            async markAsRead(notificationId) {
                try {
                    await fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    });
                    this.loadUnreadCount();
                } catch (error) {
                    console.error('Failed to mark notification as read:', error);
                }
            }
            
            startPolling() {
                // Poll for new notifications every 30 seconds
                setInterval(() => {
                    this.loadUnreadCount();
                }, 30000);
            }
        }
        
        // Initialize notification manager when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new NotificationManager();
        });
    </script>
</body>
</html>
