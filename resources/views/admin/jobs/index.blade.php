@extends('layouts.app')

@section('title', 'Manage Jobs')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manage Jobs</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.jobs.create') }}" class="btn btn-sm btn-outline-secondary">Create New Job</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($jobs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Applications</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.jobs.show', $job) }}">{{ $job->title }}</a>
                                    </td>
                                    <td>{{ $job->company }}</td>
                                    <td>{{ $job->location ?: 'Remote' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $job->status === 'published' ? 'success' : ($job->status === 'draft' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $job->applications->count() }}</td>
                                    <td>{{ $job->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-sm btn-outline-primary">View</a>
                                            <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                            @if($job->status === 'draft')
                                                <form action="{{ route('admin.jobs.publish', $job) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success">Publish</button>
                                                </form>
                                            @elseif($job->status === 'published')
                                                <form action="{{ route('admin.jobs.close', $job) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning">Close</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $jobs->firstItem() }} to {{ $jobs->lastItem() }} of {{ $jobs->total() }} entries
                    </div>
                    {{ $jobs->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <h4>No jobs found</h4>
                    <p class="text-muted">Create your first job to get started.</p>
                    <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">Create Job</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
