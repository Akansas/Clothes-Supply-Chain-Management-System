@extends('layouts.app', ['activePage' => 'notifications', 'title' => 'Light Bootstrap Dashboard Laravel by Creative Tim & UPDIVISION', 'navName' => 'Notifications', 'activeButton' => 'laravel'])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Notifications</h4>
                    <p class="card-category">Here is a list of your latest notifications.</p>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <form action="{{ route('notifications.readall') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">Mark all as read</button>
                        </form>
                    </div>
                    @if($notifications->isEmpty())
                        <div class="alert alert-info">
                            <span>You have no new notifications.</span>
                        </div>
                    @else
                        <ul class="list-unstyled team-members">
                            @foreach($notifications as $notification)
                                <li>
                                    <div class="row">
                                        <div class="col-md-7 col-7">
                                            <a href="{{ $notification->data['url'] ?? '#' }}" class="notification-link" data-id="{{ $notification->id }}">
                                                {{ $notification->data['message'] }}
                                            </a>
                                            <br />
                                            <span class="text-muted"><small>{{ $notification->created_at->diffForHumans() }}</small></span>
                                        </div>
                                        <div class="col-md-3 col-3 text-right">
                                            @if(!$notification->read_at)
                                                <span class="badge badge-primary">New</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="d-flex justify-content-center">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Function to fetch unread notifications
        function fetchNotifications() {
            $.ajax({
                url: '{{ route("notifications.unread") }}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    updateNotificationUI(data);
                },
                error: function(error) {
                    console.error('Error fetching notifications:', error);
                }
            });
        }

        // Function to update the UI
        function updateNotificationUI(data) {
            // Update unread count in the sidebar
            var unreadCount = data.unread_count;
            var notificationBell = $('#notification-bell'); // You'll need to add this ID to your layout
            
            if (unreadCount > 0) {
                notificationBell.text(unreadCount).show();
            } else {
                notificationBell.hide();
            }
        }
        
        // Mark notification as read when clicked
        $('.notification-link').on('click', function(e) {
            var notificationId = $(this).data('id');
            var url = $(this).attr('href');
            
            $.ajax({
                url: '/notifications/' + notificationId + '/read',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    window.location.href = url;
                },
                error: function(err) {
                    window.location.href = url; // Still navigate even if read fails
                }
            });
            return false; // Prevent default link behavior
        });

        // Fetch notifications every 30 seconds
        setInterval(fetchNotifications, 30000);

        // Initial fetch
        fetchNotifications();
    });
</script>
@endpush