@extends('layouts.app')

@section('title', $job->title . ' - Job Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">{{ $job->title }}</h1>
                <div>
                    <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-warning btn-sm me-2">
                        <i class="fas fa-edit"></i> Edit Job
                    </a>
                    <a href="{{ route('recruiter.jobs.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Jobs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Overview -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Job Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Company:</strong> {{ $job->company }}</p>
                            <p><strong>Location:</strong> {{ $job->location }}</p>
                            <p><strong>Type:</strong> {{ ucfirst($job->job_type) }}</p>
                            <p><strong>Salary Range:</strong> {{ $job->salary_range ?: 'Not specified' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $job->status === 'published' ? 'success' : ($job->status === 'draft' ? 'secondary' : 'danger') }}">
                                    {{ ucfirst($job->status) }}
                                </span>
                            </p>
                            <p><strong>Created:</strong> {{ $job->created_at->format('M d, Y') }}</p>
                            <p><strong>Deadline:</strong> {{ $job->application_deadline ? $job->application_deadline->format('M d, Y') : 'No deadline' }}</p>
                            <p><strong>Interview Duration:</strong> {{ $job->interview_duration }} minutes</p>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Job Description</h6>
                        <p class="text-muted">{{ $job->description ?: 'No description provided.' }}</p>
                    </div>
                    
                    <div class="mt-3">
                        <h6>Requirements</h6>
                        <p class="text-muted">{{ $job->requirements ?: 'No requirements specified.' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Total Applications</span>
                            <span class="badge bg-primary">{{ $stats['total_applications'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Completed Interviews</span>
                            <span class="badge bg-success">{{ $stats['completed_interviews'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $stats['total_applications'] > 0 ? ($stats['completed_interviews'] / $stats['total_applications']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Pending Reviews</span>
                            <span class="badge bg-warning">{{ $stats['pending_reviews'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: {{ $stats['completed_interviews'] > 0 ? ($stats['pending_reviews'] / $stats['completed_interviews']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Average Rating</span>
                            <span class="badge bg-info">{{ number_format($stats['average_rating'], 1) }}/5</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: {{ $stats['average_rating'] ? ($stats['average_rating'] / 5) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interview Questions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Interview Questions</h6>
                    <a href="{{ route('admin.questions.create', ['job_id' => $job->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Add Question
                    </a>
                </div>
                <div class="card-body">
                    @if($job->questions->count() > 0)
                        <div class="row">
                            @foreach($job->questions as $question)
                                <div class="col-md-6 mb-3">
                                    <div class="card border-left-{{ $question->type === 'video' ? 'primary' : 'info' }} shadow h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title">Question {{ $question->order }}</h6>
                                                <span class="badge bg-{{ $question->type === 'video' ? 'primary' : 'info' }}">
                                                    {{ ucfirst($question->type) }}
                                                </span>
                                            </div>
                                            <p class="card-text">{{ $question->question_text }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i> {{ $question->time_limit }}s
                                                </small>
                                                <div>
                                                    <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-question-circle fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No questions added</h5>
                            <p class="text-gray-500">Add interview questions to start receiving video responses.</p>
                            <a href="{{ route('admin.questions.create', ['job_id' => $job->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Question
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Applications -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Applications</h6>
                    <div>
                        <a href="{{ route('recruiter.jobs.applications', $job) }}" class="btn btn-sm btn-info me-2">
                            <i class="fas fa-list"></i> View All
                        </a>
                        <a href="{{ route('recruiter.jobs.analytics', $job) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-chart-bar"></i> Analytics
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($applications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Candidate</th>
                                        <th>Applied</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Reviews</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $application)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <img class="rounded-circle" src="https://ui-avatars.com/api/?name={{ urlencode($application->candidate->full_name) }}&background=007bff&color=fff&size=32" alt="{{ $application->candidate->full_name }}">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6 class="mb-0">{{ $application->candidate->full_name }}</h6>
                                                        <small class="text-muted">{{ $application->candidate->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $application->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $application->status_color }}">
                                                    {{ ucfirst($application->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $application->progress_color }}" role="progressbar" 
                                                         style="width: {{ $application->progress_percentage }}%">
                                                        {{ $application->completed_responses }}/{{ $application->job->questions->count() }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($application->reviews->count() > 0)
                                                    <span class="badge bg-success">{{ $application->reviews->count() }}</span>
                                                @else
                                                    <span class="badge bg-secondary">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('recruiter.applications.show', $application) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($application->status === 'completed' && !$application->reviews->contains('reviewer_id', auth()->id()))
                                                        <a href="{{ route('recruiter.applications.review', $application) }}" class="btn btn-sm btn-warning" title="Review">
                                                            <i class="fas fa-star"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-tie fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No applications yet</h5>
                            <p class="text-gray-500">Candidates will appear here once they apply for this job.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
