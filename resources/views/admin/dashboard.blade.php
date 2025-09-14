@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Admin Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.jobs.create') }}" class="btn btn-sm btn-outline-secondary">Create New Job</a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Jobs</h5>
                    <h2>{{ $stats['total_jobs'] }}</h2>
                    <p class="card-text">{{ $stats['active_jobs'] }} active</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Applications</h5>
                    <h2>{{ $stats['total_applications'] }}</h2>
                    <p class="card-text">{{ $stats['pending_reviews'] }} pending review</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Candidates</h5>
                    <h2>{{ $stats['candidates'] }}</h2>
                    <p class="card-text">Registered candidates</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Recruiters</h5>
                    <h2>{{ $stats['recruiters'] }}</h2>
                    <p class="card-text">Active recruiters</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Jobs -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Jobs</h5>
                </div>
                <div class="card-body">
                    @if($recentJobs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentJobs as $job)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.jobs.show', $job) }}">{{ $job->title }}</a>
                                            </td>
                                            <td>{{ $job->company }}</td>
                                            <td>
                                                <span class="badge bg-{{ $job->status === 'published' ? 'success' : ($job->status === 'draft' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($job->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $job->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No jobs created yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Applications -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Applications</h5>
                </div>
                <div class="card-body">
                    @if($recentApplications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Candidate</th>
                                        <th>Job</th>
                                        <th>Status</th>
                                        <th>Applied</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentApplications as $application)
                                        <tr>
                                            <td>{{ $application->candidate->full_name }}</td>
                                            <td>{{ $application->job->title }}</td>
                                            <td>
                                                <span class="badge bg-{{ $application->status === 'completed' ? 'success' : ($application->status === 'in_progress' ? 'warning' : 'info') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $application->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No applications received yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
