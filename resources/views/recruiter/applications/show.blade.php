@extends('layouts.app')

@section('title', 'Application - ' . $application->candidate->full_name)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Application Review</h1>
                <div>
                    @if($application->status === 'completed' && !$userReview)
                        <a href="{{ route('recruiter.applications.review', $application) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-star"></i> Review Application
                        </a>
                    @endif
                    <a href="{{ route('recruiter.jobs.show', $application->job) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Job
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Overview -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Application Overview</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Candidate Information</h6>
                            <div class="d-flex align-items-center mb-3">
                                <img class="rounded-circle me-3" src="https://ui-avatars.com/api/?name={{ urlencode($application->candidate->full_name) }}&background=007bff&color=fff&size=64" alt="{{ $application->candidate->full_name }}">
                                <div>
                                    <h5 class="mb-0">{{ $application->candidate->full_name }}</h5>
                                    <p class="mb-0 text-muted">{{ $application->candidate->email }}</p>
                                    @if($application->candidate->phone)
                                        <p class="mb-0 text-muted">{{ $application->candidate->phone }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Application Details</h6>
                            <p><strong>Job:</strong> {{ $application->job->title }}</p>
                            <p><strong>Applied:</strong> {{ $application->created_at->format('M d, Y H:i') }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $application->status_color }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </p>
                            <p><strong>Progress:</strong> 
                                {{ $application->completed_responses }}/{{ $application->job->questions->count() }} responses
                            </p>
                        </div>
                    </div>
                    
                    @if($application->resume_path)
                        <div class="mt-3">
                            <h6>Resume</h6>
                            <a href="{{ asset('storage/' . $application->resume_path) }}" class="btn btn-sm btn-primary" target="_blank">
                                <i class="fas fa-file-pdf"></i> View Resume
                            </a>
                        </div>
                    @endif
                    
                    @if($application->cover_letter)
                        <div class="mt-3">
                            <h6>Cover Letter</h6>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-0">{{ $application->cover_letter }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Review Status</h6>
                </div>
                <div class="card-body">
                    @if($userReview)
                        <div class="alert alert-success">
                            <h6 class="alert-heading">Your Review</h6>
                            <p class="mb-1">Rating: {{ $userReview->rating }}/5</p>
                            <p class="mb-0">Decision: {{ ucfirst($userReview->decision) }}</p>
                            <small class="text-muted">Reviewed {{ $userReview->reviewed_at->diffForHumans() }}</small>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <h6 class="alert-heading">Pending Review</h6>
                            <p class="mb-0">You haven't reviewed this application yet.</p>
                        </div>
                    @endif
                    
                    @if($overallRating)
                        <div class="mt-3">
                            <h6>Overall Rating</h6>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= round($overallRating) ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <span>{{ number_format($overallRating, 1) }}/5</span>
                            </div>
                            <small class="text-muted">Based on {{ $application->reviews->count() }} review(s)</small>
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <h6>Reviews</h6>
                        @foreach($application->reviews as $review)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="fw-bold">{{ $review->reviewer->full_name }}</small>
                                    <small class="text-muted">{{ $review->reviewed_at->diffForHumans() }}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star fa-sm {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                    <span class="ms-1 small">({{ $review->rating }}/5)</span>
                                </div>
                                <small class="text-muted">{{ ucfirst($review->decision) }}</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interview Responses -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Interview Responses</h6>
                </div>
                <div class="card-body">
                    @if($application->responses->count() > 0)
                        <div class="accordion" id="responsesAccordion">
                            @foreach($application->responses as $index => $response)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="respHeading{{ $index }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#respCollapse{{ $index }}">
                                            Question {{ $response->question->order }} - {{ $response->response_type === 'video' ? 'Video' : 'Text' }} Response
                                            <span class="ms-auto me-2">
                                                @if($response->rating)
                                                    <i class="fas fa-star text-warning"></i> {{ $response->rating }}/5
                                                @endif
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="respCollapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#responsesAccordion">
                                        <div class="accordion-body">
                                            <h6 class="mb-3">{{ $response->question->question_text }}</h6>
                                            
                                            @if($response->response_type === 'video')
                                                <div class="text-center mb-3">
                                                    <video controls class="img-fluid rounded" style="max-height: 400px; background: #000;">
                                                        <source src="{{ route('video.stream', $response) }}" type="video/webm">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                </div>
                                            @else
                                                <div class="alert alert-light">
                                                    <p class="mb-0">{{ $response->response_data }}</p>
                                                </div>
                                            @endif
                                            
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i> Duration: {{ $response->duration }} seconds
                                                    </small>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar"></i> Submitted: {{ $response->created_at->format('M d, Y H:i') }}
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            @if($response->comment)
                                                <div class="mt-3">
                                                    <h6>Reviewer Comment</h6>
                                                    <div class="alert alert-info">
                                                        <p class="mb-0">{{ $response->comment }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-video fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">No responses yet</h5>
                            <p class="text-gray-500">The candidate hasn't completed any interview questions.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    @if($application->reviews->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Detailed Reviews</h6>
                    </div>
                    <div class="card-body">
                        @foreach($application->reviews as $review)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="mb-1">{{ $review->reviewer->full_name }}</h6>
                                            <div class="d-flex align-items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                                <span class="ms-2">({{ $review->rating }}/5)</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-{{ $review->decision === 'proceed' ? 'success' : ($review->decision === 'reject' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($review->decision) }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $review->reviewed_at->format('M d, Y H:i') }}</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6>Comments</h6>
                                        <p class="mb-0">{{ $review->comments }}</p>
                                    </div>
                                    
                                    @if($review->feedback)
                                        <div>
                                            <h6>Additional Feedback</h6>
                                            <p class="mb-0">{{ $review->feedback }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
