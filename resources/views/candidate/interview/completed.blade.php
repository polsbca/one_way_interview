@extends('layouts.app')

@section('title', 'Interview Completed')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Interview Completed</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('candidate.dashboard') }}" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    <div class="card mb-4 border-success">
        <div class="card-body text-center">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            <h3 class="mt-3">Interview Completed Successfully!</h3>
            <p class="text-muted">Thank you for completing the interview for <strong>{{ $application->job->title }}</strong> at <strong>{{ $application->job->company }}</strong>.</p>
            <p class="text-muted">Your responses have been submitted and will be reviewed by the hiring team.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Interview Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Interview Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Job Title:</strong> {{ $application->job->title }}
                        </div>
                        <div class="col-md-6">
                            <strong>Company:</strong> {{ $application->job->company }}
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Application Date:</strong> {{ $application->created_at->format('M d, Y') }}
                        </div>
                        <div class="col-md-6">
                            <strong>Completion Date:</strong> {{ $application->updated_at->format('M d, Y') }}
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Total Questions:</strong> {{ $application->responses->count() }}
                        </div>
                        <div class="col-md-6">
                            <strong>Video Responses:</strong> {{ $application->responses->where('response_type', 'video')->count() }}
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Text Responses:</strong> {{ $application->responses->where('response_type', 'text')->count() }}
                        </div>
                        <div class="col-md-6">
                            <strong>Total Duration:</strong> {{ $application->responses->sum('duration') }} seconds
                        </div>
                    </div>
                </div>
            </div>

            <!-- Responses Overview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Your Responses</h5>
                </div>
                <div class="card-body">
                    @if($application->responses->count() > 0)
                        <div class="accordion" id="responsesAccordion">
                            @foreach($application->responses as $index => $response)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="respHeading{{ $index }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#respCollapse{{ $index }}">
                                            <span class="me-2">Question {{ $index + 1 }}</span>
                                            <span class="badge bg-{{ $response->response_type === 'video' ? 'primary' : 'secondary' }} me-2">
                                                {{ ucfirst($response->response_type) }}
                                            </span>
                                            <span class="badge bg-info me-2">
                                                {{ $response->duration }}s
                                            </span>
                                            <span class="badge bg-success">
                                                Attempt {{ $response->attempt_number }}
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="respCollapse{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#responsesAccordion">
                                        <div class="accordion-body">
                                            <h6 class="mb-3">{{ $response->question->question_text }}</h6>
                                            
                                            @if($response->response_type === 'video')
                                                <div class="text-center mb-3">
                                                    <video controls class="img-fluid rounded" style="max-height: 300px; background: #000;">
                                                        <source src="{{ route('video.stream', $response) }}" type="video/webm">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                </div>
                                            @else
                                                <div class="alert alert-light">
                                                    <p class="mb-0">{{ $response->response_data }}</p>
                                                </div>
                                            @endif
                                            
                                            <div class="text-muted">
                                                <small>
                                                    Duration: {{ $response->duration }} seconds<br>
                                                    Submitted: {{ $response->created_at->format('M d, Y H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No responses found.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Next Steps -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Next Steps</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3">
                            <i class="bi bi-1-circle-fill text-primary"></i>
                        </div>
                        <div>
                            <strong>Review Process</strong>
                            <p class="text-muted mb-0">Your interview responses will be reviewed by the hiring team.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3">
                            <i class="bi bi-2-circle-fill text-primary"></i>
                        </div>
                        <div>
                            <strong>Assessment</strong>
                            <p class="text-muted mb-0">The team will evaluate your responses and qualifications.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3">
                            <i class="bi bi-3-circle-fill text-primary"></i>
                        </div>
                        <div>
                            <strong>Decision</strong>
                            <p class="text-muted mb-0">You'll be notified of the hiring decision via email.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i class="bi bi-4-circle-fill text-primary"></i>
                        </div>
                        <div>
                            <strong>Follow-up</strong>
                            <p class="text-muted mb-0">If selected, you may be contacted for additional interviews.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Application Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Application Submitted</h6>
                                <p class="text-muted mb-0">{{ $application->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Interview Completed</h6>
                                <p class="text-muted mb-0">{{ $application->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6>Under Review</h6>
                                <p class="text-muted mb-0">In progress</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary"></div>
                            <div class="timeline-content">
                                <h6>Decision</h6>
                                <p class="text-muted mb-0">Pending</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Need Help?</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">If you have any questions about your application or need technical support:</p>
                    <div class="d-grid gap-2">
                        <a href="mailto:support@company.com" class="btn btn-outline-primary">
                            <i class="bi bi-envelope"></i> Email Support
                        </a>
                        <a href="tel:+1234567890" class="btn btn-outline-primary">
                            <i class="bi bi-telephone"></i> Call Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 5px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    padding-left: 10px;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}
</style>
@endpush
@endsection
