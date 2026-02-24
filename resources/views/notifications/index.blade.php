@extends('layouts.master')

@section('title', 'Notifications')

@section('css')
<style>
    .notification-card {
        transition: all 0.3s ease;
        border-left: 4px solid #667eea;
        border-radius: 8px;
        background: white;
    }

    .notification-card.unread {
        background: linear-gradient(135deg, #f0f4ff 0%, #ffffff 100%);
        border-left-color: #667eea;
    }

    .notification-card.read {
        background: white;
        border-left-color: #e2e8f0;
        opacity: 0.7;
    }

    .notification-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .notification-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .notification-unread-dot {
        width: 10px;
        height: 10px;
        background: #667eea;
        border-radius: 50%;
        margin-left: auto;
    }
</style>
@endsection

@section('content')
<div class="main-container container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">All Notifications</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i class="fe fe-home mr-2"></i>Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Notifications</li>
            </ol>
        </div>
        <div class="page-rightheader">
            @if($unreadCount > 0)
            <form action="{{ route('notifications.mark-all-as-read') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fe fe-check mr-2"></i>Mark All as Read
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card" style="border-top: 3px solid #667eea;">
                <div class="card-body text-center">
                    <h5 class="mb-1" style="color: #667eea; font-weight: 700;">Unread</h5>
                    <h2 class="mb-0" style="font-size: 2.5rem;">{{ $unreadCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card" style="border-top: 3px solid #10b981;">
                <div class="card-body text-center">
                    <h5 class="mb-1" style="color: #10b981; font-weight: 700;">Total</h5>
                    <h2 class="mb-0" style="font-size: 2.5rem;">{{ $notifications->total() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Notification History</h5>
        </div>
        <div class="card-body p-0">
            @forelse($notifications as $notification)
            @php
                $data      = $notification->data;
                // Support both notification types:
                // - TaskStatusNotification: has 'title', 'message', 'type', 'task_id', 'task_title', 'department', 'url'
                // - Legacy (test_uploaded): has 'type' = 'test_uploaded', 'message', 'task_identifier', 'well_name', 'operator_name'
                $isTaskNotification = isset($data['task_id']);
                $notifTitle   = $data['title']   ?? ($data['type'] === 'test_uploaded' ? 'Test Uploaded' : 'Notification');
                $notifMessage = $data['message'] ?? 'No message';
                $notifType    = $data['type']    ?? 'info';
                $notifUrl     = $data['url']     ?? null;
                $iconMap = ['success' => 'fe-check-circle', 'warning' => 'fe-alert-triangle', 'danger' => 'fe-x-circle', 'info' => 'fe-info'];
                $colorMap = ['success' => '#10b981', 'warning' => '#f59e0b', 'danger' => '#ef4444', 'info' => '#667eea'];
                $icon  = $iconMap[$notifType]  ?? 'fe-bell';
                $color = $colorMap[$notifType] ?? '#667eea';
            @endphp
            <div class="notification-card {{ !$notification->read_at ? 'unread' : 'read' }} p-4 mb-3 mx-3 mt-3"
                 style="{{ !$notification->read_at ? 'border-left-color: '.$color.';' : '' }}">
                <div class="d-flex align-items-start">
                    <!-- Icon -->
                    <div class="notification-icon mr-3 flex-shrink-0" style="background: linear-gradient(135deg, {{ $color }} 0%, {{ $color }}cc 100%);">
                        <i class="fe {{ $icon }}"></i>
                    </div>

                    <!-- Content -->
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 style="margin: 0; font-weight: 700; color: #2d3748; font-size: 1.05rem;">
                                {{ $notifTitle }}
                                @if(!$notification->read_at)
                                    <span class="badge badge-pill" style="background: {{ $color }}; color: white; font-size: 0.65rem; margin-left: 6px;">New</span>
                                @endif
                            </h6>
                            @if(!$notification->read_at)
                                <div class="notification-unread-dot" style="background: {{ $color }};"></div>
                            @endif
                        </div>

                        <!-- Message -->
                        <p style="margin: 0 0 0.75rem 0; color: #64748b; line-height: 1.6;">{{ $notifMessage }}</p>

                        <!-- Task details (TaskStatusNotification) -->
                        @if($isTaskNotification)
                        <div style="background: #f8f9fa; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 0.75rem;">
                            <div class="row">
                                <div class="col-md-6">
                                    <small style="color: #64748b;"><strong>Task:</strong> {{ $data['task_title'] ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-6">
                                    <small style="color: #64748b;"><strong>Department:</strong> {{ $data['department'] ?? '—' }}</small>
                                </div>
                            </div>
                        </div>
                        @elseif(isset($data['task_identifier']))
                        {{-- Legacy test_uploaded --}}
                        <div style="background: #f8f9fa; padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 0.75rem;">
                            <div class="row">
                                <div class="col-md-6">
                                    <small style="color: #64748b;"><strong>Task:</strong> {{ $data['task_identifier'] ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-6">
                                    <small style="color: #64748b;"><strong>Well:</strong> {{ $data['well_name'] ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-6 mt-1">
                                    <small style="color: #64748b;"><strong>Operator:</strong> {{ $data['operator_name'] ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Timestamp + Actions -->
                        <div class="d-flex justify-content-between align-items-center">
                            <small style="color: #a0aec0;">
                                <i class="fe fe-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}
                            </small>

                            <div>
                                @if($notifUrl)
                                <a href="{{ $notifUrl }}" class="btn btn-sm btn-outline-secondary" style="font-size: 0.8rem;">
                                    <i class="fe fe-external-link"></i> View
                                </a>
                                @endif
                                @if(!$notification->read_at)
                                <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary" style="font-size: 0.8rem;">
                                        <i class="fe fe-check"></i> Mark as Read
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size: 0.8rem;" onclick="return confirm('Delete this notification?')">
                                        <i class="fe fe-trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div style="padding: 3rem; text-align: center; color: #a0aec0;">
                <i class="fe fe-inbox" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 1rem;"></i>
                <p style="font-size: 1.1rem; margin: 0;">No notifications yet</p>
                <small style="color: #cbd5e1;">Check back later for new updates</small>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>

<!-- Viewers Modal -->
<div class="modal fade" id="viewersModal" tabindex="-1" role="dialog" aria-labelledby="viewersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                <h5 class="modal-title" id="viewersModalLabel" style="color: white;">Who Viewed This Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewersContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showViewers(viewers) {
        let content = '';

        if (!viewers || viewers.length === 0) {
            content = '<div class="alert alert-info"><i class="fe fe-info mr-2"></i>No one has viewed this notification yet</div>';
        } else {
            content = '<div class="viewers-list">';
            viewers.forEach((viewer, index) => {
                const viewedTime = new Date(viewer.viewed_at).toLocaleString();
                content += `
                    <div class="viewer-item" style="padding: 1rem; border-bottom: 1px solid #e3e6f0; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h6 style="margin: 0 0 0.25rem 0; font-weight: 600; color: #2d3748;">${viewer.user_name}</h6>
                            <small style="color: #64748b;">
                                <i class="fe fe-tag" style="font-size: 0.7rem;"></i>
                                ${viewer.user_role}
                            </small>
                        </div>
                        <small style="color: #a0aec0;">
                            <i class="fe fe-clock" style="font-size: 0.7rem;"></i>
                            ${viewedTime}
                        </small>
                    </div>
                `;
            });
            content += '</div>';
        }

        document.getElementById('viewersContent').innerHTML = content;
    }
</script>

<style>
    .viewer-item:last-child {
        border-bottom: none;
    }
</style>
@endsection
