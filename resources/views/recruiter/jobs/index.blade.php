@extends('layouts.app')

@section('title', 'My Jobs')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">My Jobs</h1>
                <a href="{{ route('admin.jobs.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Create New Job
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" action="{{ route('recruiter.jobs.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Search jobs..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sort" class="form-label">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Created Date</option>
                                <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>Title</option>
                                <option value="applications_count" {{ request('sort') === 'applications_count' ? 'selected' : '' }}>Applications</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Filter</button>
                            <a href="{{ route('recruiter.jobs.index') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Jobs Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Job Listings</h6>
                </div>
                <div class="card-body">
                    @if($jobs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Job Title</th>
                                        <th>Company</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Applications</th>
                                        <th>Completed</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jobs as $job)
                                        <tr>
                                            <td>
                                                <a href="{{ route('recruiter.jobs.show', $job) }}" class="text-decoration-none">
                                                    {{ $job->title }}
                                                </a>
                                            </td>
                                            <td>{{ $job->company }}</td>
                                            <td>{{ $job->location }}</td>
                                            <td>
                                                <span class="badge bg-{{ $job->status === 'published' ? 'success' : ($job->status === 'draft' ? 'secondary' : 'danger') }}">
                                                    {{ ucfirst($job->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $job->applications_count }}</td>
                                            <td>{{ $job->completed_applications }}</td>
                                            <td>{{ $job->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('recruiter.jobs.show', $job) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('recruiter.jobs.applications', $job) }}" class="btn btn-sm btn-primary" title="Applications">
                                                        <i class="fas fa-users"></i>
                                                    </a>
                                                    <a href="{{ route('recruiter.jobs.analytics', $job) }}" class="btn btn-sm btn-success" title="Analytics">
                                                        <i class="fas fa-chart-bar"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $jobs->firstItem() }} to {{ $jobs->lastItem() }} of {{ $jobs->total() }} entries
                            </div>
                            {{ $jobs->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-briefcase fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No jobs found</h5>
                            <p class="text-gray-500">Create your first job posting to start receiving applications.</p>
                            <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Job
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
