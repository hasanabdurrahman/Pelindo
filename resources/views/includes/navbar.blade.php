<header>
    <nav class="navbar navbar-expand navbar-light navbar-top">
        <div class="container-fluid">
            <a href="#" class="burger-btn d-block">
                <i class="bi bi-justify fs-3"></i>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-lg-0">
                    <li class="nav-item dropdown me-1">
                        <a class="nav-link active dropdown-toggle text-gray-600" href="#"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class='bi bi-envelope bi-sub fs-4'></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li>
                                <h6 class="dropdown-header">Mail</h6>
                            </li>
                            <li><a class="dropdown-item" href="#">No new mail</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown me-3">
                        <a class="nav-link active dropdown-toggle text-gray-600" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                            <i class='bi bi-bell bi-sub fs-4'></i>
                            @if ($newNotifications->count() > 0)
                            <span class="badge bg-danger">{{ $newNotifications->count() }}</span>
                        @endif
                        </a>
                                                        
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="dropdownMenuButton">
                            <li class="dropdown-header">
                                <h6 col-3>Notifications</h6>

                                <a col-5 href="#" id="clearNotifications" class="dropdown-item">
                                    Clear Notifications
                                </a>        
                            
                            </li>
                            @forelse ($allNotifikasi as $notification)
                            <li class="dropdown-item notification-item" id="notif3" data-notification-id="{{ $notification->id }}">
                                <a class="d-flex align-items-center" href="javascript:void(0)" onclick="renderView(`{!! route('transaction.tasklist') !!}`">
                                    <div class="notification-icon bg-success">
                                        <i class="bi bi-file-earmark-check"></i>
                                    </div>
                                    <div class="notification-text ms-4" >
                                        <p class="notification-title font-bold">{{ $notification->transactionnumber}}</p>
                                        <p class="notification-subtitle font-thin text-sm">{{ $notification->notif}}</p>
                                    </div>
                                </a>
                            </li>
                            
                        @empty
                            <li><a class="dropdown-item" href="#">No new notification</a></li>
                        @endforelse
                        {{-- @forelse ($latestNotifikasi as $notification)
                        <li id="notifalld" style="display: none;" class="dropdown-item notification-item" data-notification-id="{{ $notification->id }}">
                            <a class="d-flex align-items-center" href="#">
                                    <div class="notification-icon bg-success">
                                        <i class="bi bi-file-earmark-check"></i>
                                    </div>
                                    <p class="notification-title font-bold">{{ $notification->transactionnumber}}</p>
                                    <p class="notification-subtitle font-thin text-sm">{{ $notification->notif}}</p>
                            </a>
                            @empty
                                <a class="dropdown-item" href="#">No new notification</a>
                            </li>
                            @endforelse --}}
                        
                        {{-- <li>
                            <a href="#" id="notifall">See all notifications</a>
                        </li> --}}

                        </ul>
                    </li>
                </ul>
                <div class="dropdown">
                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-menu d-flex">
                            <div class="user-name text-end me-3">
                                <h6 class="mb-0 text-gray-600">{{ Auth::user()->name }}</h6>
                                <p class="mb-0 text-sm text-gray-600">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="user-img d-flex align-items-center">
                                <div class="avatar avatar-md" id="avatar-container">
                                    
                                    @if (Auth::user()->picture)
                                        <img src="{{ Storage::url('employee/' . Auth::user()->picture) }}" id="avatar-image">
                                    @else
                                        <img src="{{ asset('assets/images/faces/1.jpg') }}" id="avatar-image">
                                    @endif

                                </div>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"
                        style="min-width: 11rem;">
                        <li>
                            <h6 class="dropdown-header">Hello, {{ Auth::user()->name }}!</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" onclick="renderView(`{!! route('profile') !!}`)"><i
                                    class="icon-mid bi bi-person me-2"></i> My
                                Profile</a>
                        </li>
                        {{-- <li>
                            <a class="dropdown-item" href="#"><i class="icon-mid bi bi-gear me-2"></i>
                                Settings</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#"><i class="icon-mid bi bi-wallet me-2"></i>
                                Wallet</a>
                        </li> --}}
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a href="{{ route('logout') }}" class='dropdown-item'
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="icon-mid bi bi-box-arrow-left me-2"></i>
                                <span>Log Out</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- navbar.blade.php -->

<!-- ... (other code) -->
<!-- Include jQuery from CDN -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    $(document).ready(function () {
        // Function to show 3 latest notifications
        function showLatestNotifications() {
            $('#notif3').show();
            $('#notifalld').hide();
        }

        // Event handler for 'See all notifications' link
        $('#notifall').on('click', function (e) {
            e.preventDefault();
            $('#notif3').hide();
            $('#notifalld').show();

            // Send an AJAX request to mark all notifications as read
            $.ajax({
                url: '/mark-all-as-read',
                type: 'GET',
                success: function (response) {
                    console.log('All notifications marked as read');

                    location.reload();
            
            // Redirect to the 'tasklist' page
            window.location.href = '{!! route('transaction.tasklist') !!}';
                },
                error: function (error) {
                    console.error('Failed to mark all notifications as read');
                }
            });
        });

        // Event handler for individual notifications
        $('.notification-item').on('click', function () {
            // Handle individual notification click event here
            var notificationId = $(this).data('notification-id');
            
             // Send an AJAX request to mark all notifications as read
             $.ajax({
                url: '/mark-all-as-read',
                type: 'GET',
                success: function (response) {
                    console.log('All notifications marked as read');
                    location.reload();
            
            // Redirect to the 'tasklist' page
            window.location.href = '{!! route('transaction.tasklist') !!}';
                },
                error: function (error) {
                    console.error('Failed to mark all notifications as read');
                }
            });
        });

        $('#clearNotifications').on('click', function (e) {
        e.preventDefault();

        // Send an AJAX request to clear all notifications
        $.ajax({
            url: '/clear-notifications',
            type: 'GET',
            success: function (response) {
                console.log('All notifications cleared');

                // Reload the page after clearing all notifications
                location.reload();
            },
            error: function (error) {
                console.error('Failed to clear all notifications');
            }
        });
    });

        // Initially show 3 latest notifications
        showLatestNotifications();
    });
    var notificationCount = {{ $allNotifikasi->count() }};
        if (notificationCount > 0) {
            $('#clearNotifications').show();
        } else {
            $('#clearNotifications').hide();
        }
</script>


</header>
