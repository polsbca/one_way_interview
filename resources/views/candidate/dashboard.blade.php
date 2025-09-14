@extends('layouts.app')

@section('title', 'Candidate Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Welcome, {{ auth()->user()->first_name }}!</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('candidate.jobs.index') }}" class="btn btn-sm btn-outline-secondary">Browse Jobs</a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Applications</h5>
                    <h2>{{ $stats['total_applications'] }}</h2>
                    <p class="card-text">Total submitted</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Completed</h5>
                    <h2>{{ $stats['completed_interviews'] }}</h2>
                    <p class="card-text">Interviews finished</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">In Progress</h5>
                    <h2>{{ $stats['in_progress'] }}</h2>
                    <p class="card-text">Currently active</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Pending Review</h5>
                    <h2>{{ $stats['pending_reviews'] }}</h2>
                    <p class="card-text">Awaiting feedback</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Applications -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Applications</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentApplications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Job</th>
                                        <th>Status</th>
                                        <th>Applied</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentApplications as $application)
                                        <tr>
                                            <td>{{ $application->job->title }}</td>
                                            <td>
                                                <span class="badge bg-{{ $application->status === 'completed' ? 'success' : ($application->status === 'in_progress' ? 'warning' : 'info') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $application->created_at->diffForHumans() }}</td>
                                            <td>
                                                @if($application->status === 'in_progress')
                                                    <a href="{{ route('candidate.interview.start', $application) }}" class="btn btn-sm btn-primary">Continue</a>
                                                @elseif($application->status === 'completed')
                                                    <a href="{{ route('candidate.interview.completed', $application) }}" class="btn btn-sm btn-outline-primary">View</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted">No applications yet.</p>
                            <a href="{{ route('candidate.jobs.index') }}" class="btn btn-sm btn-primary">Browse Jobs</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Available Jobs -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Available Jobs</h5>
                    <a href="{{ route('candidate.jobs.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($availableJobs->count() > 0)
                        @foreach($availableJobs as $job)
                            <div class="border-bottom pb-3 mb-3">
                                <h6 class="mb-1">{{ $job->title }}</h6>
                                <p class="text-muted mb-2">{{ $job->company }} • {{ $job->location ?: 'Remote' }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        {{ $job->questions->count() }} questions • 
                                        Deadline: {{ $job->deadline ? $job->deadline->format('M d, Y') : 'No deadline' }}
                                    </small>
                                    <a href="{{ route('candidate.jobs.show', $job) }}" class="btn btn-sm btn-outline-primary">Apply</a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted">No new jobs available at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Notifications</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        @foreach($notifications as $notification)
                            <div class="alert alert-{{ $notification->type_color }} alert-dismissible fade show" role="alert">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <i class="bi bi-{{ $notification->type_icon }} fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading">{{ $notification->title }}</h6>
                                        <p class="mb-0">{{ $notification->message }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No new notifications.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
