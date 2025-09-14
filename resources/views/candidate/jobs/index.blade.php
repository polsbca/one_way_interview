@extends('layouts.app')

@section('title', 'Browse Jobs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Browse Jobs</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('candidate.dashboard') }}" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('candidate.jobs.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" placeholder="Search jobs..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="location">
                            <option value="">All Locations</option>
                            <option value="remote">Remote</option>
                            <option value="new-york">New York</option>
                            <option value="san-francisco">San Francisco</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="department">
                            <option value="">All Departments</option>
                            <option value="engineering">Engineering</option>
                            <option value="marketing">Marketing</option>
                            <option value="sales">Sales</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Jobs List -->
    <div class="row">
        @if($jobs->count() > 0)
            @foreach($jobs as $job)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $job->title }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{ $job->company }}</h6>
                            
                            <div class="mb-3">
                                <span class="badge bg-primary me-2">{{ $job->location ?: 'Remote' }}</span>
                                <span class="badge bg-secondary">{{ $job->department ?: 'General' }}</span>
                            </div>
                            
                            <p class="card-text">{{ Str::limit($job->description, 150) }}</p>
                            
                            <div class="mb-3">
                                <small class="text-muted d-block">
                                    <i class="bi bi-question-circle"></i> {{ $job->questions->count() }} questions
                                </small>
                                <small class="text-muted d-block">
                                    <i class="bi bi-calendar"></i> 
                                    @if($job->deadline)
                                        Deadline: {{ $job->deadline->format('M d, Y') }}
                                    @else
                                        No deadline
                                    @endif
                                </small>
                                @if($job->salary_min || $job->salary_max)
                                    <small class="text-muted d-block">
                                        <i class="bi bi-currency-dollar"></i> 
                                        @if($job->salary_min && $job->salary_max)
                                            ${{ number_format($job->salary_min) }} - ${{ number_format($job->salary_max) }}
                                        @elseif($job->salary_min)
                                            ${{ number_format($job->salary_min) }}+
                                        @endif
                                    </small>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Posted {{ $job->created_at->diffForHumans() }}</small>
                                <a href="{{ route('candidate.jobs.show', $job) }}" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-briefcase" style="font-size: 3rem; color: #6c757d;"></i>
                    <h4 class="mt-3">No jobs found</h4>
                    <p class="text-muted">There are no available jobs matching your criteria at the moment.</p>
                    <a href="{{ route('candidate.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($jobs->count() > 0)
        <div class="d-flex justify-content-center mt-4">
            {{ $jobs->links() }}
        </div>
    @endif
</div>
@endsection
