@extends('layouts.app')

@section('title', $job->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">{{ $job->title }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('candidate.jobs.index') }}" class="btn btn-sm btn-outline-secondary">Back to Jobs</a>
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
                            <strong>Location:</strong> {{ $job->location ?: 'Remote' }}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Department:</strong> {{ $job->department ?: 'Not specified' }}
                        </div>
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
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Application Deadline:</strong> 
                            @if($job->deadline)
                                {{ $job->deadline->format('M d, Y') }}
                                @if($job->deadline->isPast())
                                    <span class="badge bg-danger">Expired</span>
                                @endif
                            @else
                                No deadline
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Interview Questions:</strong> {{ $job->questions->count() }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Job Description:</strong>
                        <div class="mt-2">{{ nl2br(e($job->description)) }}</div>
                    </div>
                    
                    <div class="text-muted">
                        <small>Posted {{ $job->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>

            <!-- Interview Questions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Interview Questions</h5>
                </div>
                <div class="card-body">
                    @if($job->questions->count() > 0)
                        <div class="accordion" id="questionsAccordion">
                            @foreach($job->questions->sortBy('order') as $index => $question)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}">
                                            <span class="me-2">Question {{ $index + 1 }}</span>
                                            <span class="badge bg-{{ $question->type === 'video' ? 'primary' : 'secondary' }} me-2">
                                                {{ ucfirst($question->type) }}
                                            </span>
                                            <span class="badge bg-info me-2">
                                                {{ $question->time_limit }}s
                                            </span>
                                            @if($question->is_required)
                                                <span class="badge bg-danger">Required</span>
                                            @endif
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#questionsAccordion">
                                        <div class="accordion-body">
                                            <p class="mb-3">{{ $question->question_text }}</p>
                                            @if($question->instructions)
                                                <div class="alert alert-info">
                                                    <strong>Instructions:</strong> {{ $question->instructions }}
                                                </div>
                                            @endif
                                            <div class="text-muted">
                                                <small>
                                                    Time limit: {{ $question->time_limit }} seconds<br>
                                                    Maximum attempts: {{ $question->max_attempts }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No interview questions have been added yet.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Application Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Application Status</h5>
                </div>
                <div class="card-body">
                    @if($hasApplied)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> You have already applied for this job.
                        </div>
                        <a href="#" class="btn btn-secondary w-100">View Application</a>
                    @elseif($job->status !== 'published' || ($job->deadline && $job->deadline->isPast()))
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle"></i> This job is not accepting applications.
                        </div>
                    @else
                        <form action="{{ route('candidate.jobs.apply', $job) }}" method="POST" id="applicationForm">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="cover_letter" class="form-label">Cover Letter (Optional)</label>
                                <textarea class="form-control" id="cover_letter" name="cover_letter" rows="4" placeholder="Tell us why you're interested in this position..."></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="resume" class="form-label">Resume (Optional)</label>
                                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                                <div class="form-text">PDF, DOC, or DOCX files only (Max 2MB)</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Apply for this Job</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Job Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Total Questions:</strong> {{ $job->questions->count() }}
                    </div>
                    <div class="mb-3">
                        <strong>Video Questions:</strong> {{ $job->questions->where('type', 'video')->count() }}
                    </div>
                    <div class="mb-3">
                        <strong>Text Questions:</strong> {{ $job->questions->where('type', 'text')->count() }}
                    </div>
                    <div class="mb-3">
                        <strong>Estimated Time:</strong> 
                        @if($job->questions->count() > 0)
                            {{ $job->questions->sum('time_limit') / 60 }} minutes
                        @else
                            Not specified
                        @endif
                    </div>
                    <div>
                        <strong>Required Questions:</strong> {{ $job->questions->where('is_required', true)->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('applicationForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Applying...';
});
</script>
@endpush
@endsection
