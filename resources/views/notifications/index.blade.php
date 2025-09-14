@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Notifications</h1>
                <div>
                    <a href="{{ route('notifications.mark-all-read') }}" class="btn btn-sm btn-success me-2" onclick="return confirm('Mark all notifications as read?')">
                        <i class="fas fa-check-double"></i> Mark All Read
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="btn-group" role="group">
                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary {{ !request('filter') ? 'active' : '' }}">
                                All ({{ auth()->user()->notifications()->count() }})
                            </a>
                            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="btn btn-outline-warning {{ request('filter') === 'unread' ? 'active' : '' }}">
                                Unread ({{ auth()->user()->notifications()->unread()->count() }})
                            </a>
                            <a href="{{ route('notifications.index', ['filter' => 'read']) }}" class="btn btn-outline-secondary {{ request('filter') === 'read' ? 'active' : '' }}">
                                Read ({{ auth()->user()->notifications()->read()->count() }})
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Your Notifications</h6>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <div class="list-group-item list-group-item-action {{ !$notification->is_read ? 'list-group-item-warning' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-{{ $notification->type_icon }} text-{{ $notification->type_color }} me-2"></i>
                                                <h6 class="mb-0">{{ $notification->title }}</h6>
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-warning ms-2">New</span>
                                                @endif
                                            </div>
                                            <p class="mb-1">{{ $notification->message }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div class="ms-3">
                                            <div class="btn-group-vertical" role="group">
                                                @if(!$notification->is_read)
                                                    <form action="{{ route('notifications.mark-read', $notification) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as read">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('notifications.mark-unread', $notification) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Mark as unread">
                                                            <i class="fas fa-envelope"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if($notification->data && isset($notification->data['job_id']))
                                                    <a href="{{ route('admin.jobs.show', $notification->data['job_id']) }}" class="btn btn-sm btn-outline-info" title="View Job">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                                
                                                @if($notification->data && isset($notification->data['application_id']))
                                                    <a href="{{ route('recruiter.applications.show', $notification->data['application_id']) }}" class="btn btn-sm btn-outline-primary" title="View Application">
                                                        <i class="fas fa-user-tie"></i>
                                                    </a>
                                                @endif
                                                
                                                <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this notification?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of {{ $notifications->total() }} entries
                            </div>
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No notifications</h5>
                            <p class="text-gray-500">You're all caught up! No notifications to show.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
