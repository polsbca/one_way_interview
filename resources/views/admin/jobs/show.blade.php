@extends('layouts.app')

@section('title', $job->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">{{ $job->title }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-sm btn-outline-secondary">Edit Job</a>
                <a href="{{ route('admin.jobs.index') }}" class="btn btn-sm btn-outline-secondary">Back to Jobs</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Job Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Job Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Company:</strong> {{ $job->company }}
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong> 
                            <span class="badge bg-{{ $job->status === 'published' ? 'success' : ($job->status === 'draft' ? 'warning' : 'danger') }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Location:</strong> {{ $job->location ?: 'Remote' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Department:</strong> {{ $job->department ?: 'Not specified' }}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Salary Range:</strong> 
                            @if($job->salary_min && $job->salary_max)
                                ${{ number_format($job->salary_min) }} - ${{ number_format($job->salary_max) }}
                            @elseif($job->salary_min)
                                ${{ number_format($job->salary_min) }}+
                            @else
                                Not specified
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Deadline:</strong> {{ $job->deadline ? $job->deadline->format('M d, Y') : 'No deadline' }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Description:</strong>
                        <div class="mt-2">{{ nl2br(e($job->description)) }}</div>
                    </div>
                    
                    <div class="text-muted">
                        <small>Created by {{ $job->creator->full_name }} on {{ $job->created_at->format('M d, Y') }}</small>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Interview Questions</h5>
                    <a href="{{ route('admin.questions.create', $job) }}" class="btn btn-sm btn-primary">Add Question</a>
                </div>
                <div class="card-body">
                    @if($job->questions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Question</th>
                                        <th>Type</th>
                                        <th>Time Limit</th>
                                        <th>Required</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($job->questions->sortBy('order') as $question)
                                        <tr>
                                            <td>{{ $question->order + 1 }}</td>
                                            <td>{{ Str::limit($question->question_text, 50) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $question->type === 'video' ? 'primary' : 'secondary' }}">
                                                    {{ ucfirst($question->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $question->time_limit }}s</td>
                                            <td>{{ $question->is_required ? 'Yes' : 'No' }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.questions.edit', [$job, $question]) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                    <form action="{{ route('admin.questions.destroy', [$job, $question]) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted">No questions added yet.</p>
                            <a href="{{ route('admin.questions.create', $job) }}" class="btn btn-sm btn-primary">Add First Question</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Applications -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Applications ({{ $job->applications->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($job->applications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Candidate</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Applied</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($job->applications as $application)
                                        <tr>
                                            <td>{{ $application->candidate->full_name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $application->status === 'completed' ? 'success' : ($application->status === 'in_progress' ? 'warning' : 'info') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $application->progress_percentage }}%;">
                                                        {{ $application->progress_percentage }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $application->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                            </td>
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
        
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    @if($job->status === 'draft')
                        <form action="{{ route('admin.jobs.publish', $job) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2">Publish Job</button>
                        </form>
                    @elseif($job->status === 'published')
                        <form action="{{ route('admin.jobs.close', $job) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100 mb-2">Close Job</button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.questions.create', $job) }}" class="btn btn-primary w-100 mb-2">Add Question</a>
                    <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-secondary w-100">Edit Job</a>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Total Applications:</strong> {{ $job->applications->count() }}
                    </div>
                    <div class="mb-3">
                        <strong>Completed Interviews:</strong> {{ $job->applications->where('status', 'completed')->count() }}
                    </div>
                    <div class="mb-3">
                        <strong>In Progress:</strong> {{ $job->applications->where('status', 'in_progress')->count() }}
                    </div>
                    <div class="mb-3">
                        <strong>Pending Review:</strong> {{ $job->applications->where('status', 'pending_review')->count() }}
                    </div>
                    <div>
                        <strong>Total Questions:</strong> {{ $job->questions->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
