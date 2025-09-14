@extends('layouts.app')

@section('title', 'Review Application - ' . $application->candidate->full_name)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Review Application</h1>
                <div>
                    <a href="{{ route('recruiter.applications.show', $application) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Application
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Candidate and Job Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Candidate</h6>
                            <div class="d-flex align-items-center">
                                <img class="rounded-circle me-3" src="https://ui-avatars.com/api/?name={{ urlencode($application->candidate->full_name) }}&background=007bff&color=fff&size=48" alt="{{ $application->candidate->full_name }}">
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
                            <h6>Job Position</h6>
                            <h5 class="mb-0">{{ $application->job->title }}</h5>
                            <p class="mb-0 text-muted">{{ $application->job->company }} • {{ $application->job->location }}</p>
                            <p class="mb-0 text-muted">Applied: {{ $application->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('recruiter.applications.submit-review', $application) }}">
        @csrf
        
        <!-- Interview Responses Review -->
        <div class="row mb-4">
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
                                            </button>
                                        </h2>
                                        <div id="respCollapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#responsesAccordion">
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
                                                
                                                <!-- Response Rating -->
                                                <div class="mt-3">
                                                    <label class="form-label">Rate this response (1-5 stars)</label>
                                                    <div class="rating-input">
                                                        <input type="hidden" name="response_ratings[{{ $index }}][response_id]" value="{{ $response->id }}">
                                                        <div class="btn-group" role="group">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <button type="button" class="btn btn-outline-warning rating-btn" data-rating="{{ $i }}" data-response-index="{{ $index }}">
                                                                    <i class="fas fa-star"></i>
                                                                </button>
                                                            @endfor
                                                        </div>
                                                        <input type="hidden" name="response_ratings[{{ $index }}][rating]" id="response_rating_{{ $index }}" value="0">
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-3">
                                                    <label class="form-label">Comment on this response (optional)</label>
                                                    <textarea class="form-control" name="response_ratings[{{ $index }}][comment]" rows="2" placeholder="Add your comments about this specific response..."></textarea>
                                                </div>
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

        <!-- Overall Review -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Overall Review</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">Overall Rating <span class="text-danger">*</span></label>
                            <div class="overall-rating">
                                <div class="btn-group" role="group">
                                    @for($i = 1; $i <= 5; $i++)
                                        <button type="button" class="btn btn-outline-warning overall-rating-btn" data-rating="{{ $i }}">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="overall_rating" value="0" required>
                                <div class="invalid-feedback">
                                    Please provide an overall rating.
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="comments" class="form-label">Review Comments <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('comments') is-invalid @enderror" id="comments" name="comments" rows="4" required placeholder="Provide your detailed review comments...">{{ old('comments') }}</textarea>
                            <div class="invalid-feedback">
                                @error('comments')
                                    {{ $message }}
                                @enderror
                            </div>
                            <small class="form-text text-muted">Minimum 10 characters required.</small>
                        </div>

                        <div class="mb-4">
                            <label for="feedback" class="form-label">Additional Feedback (optional)</label>
                            <textarea class="form-control" id="feedback" name="feedback" rows="3" placeholder="Any additional feedback for the candidate or internal notes...">{{ old('feedback') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Decision <span class="text-danger">*</span></label>
                            <div>
                                @foreach(['proceed' => 'Proceed to Next Stage', 'reject' => 'Reject Application', 'hold' => 'Put on Hold'] as $value => $label)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="decision" id="decision_{{ $value }}" value="{{ $value }}" required>
                                        <label class="form-check-label" for="decision_{{ $value }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                                <div class="invalid-feedback">
                                    Please select a decision.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Review Guidelines</h6>
                    </div>
                    <div class="card-body">
                        <h6>Rating Scale:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-star text-warning"></i> 5 - Excellent</li>
                            <li><i class="fas fa-star text-warning"></i> 4 - Good</li>
                            <li><i class="fas fa-star text-warning"></i> 3 - Average</li>
                            <li><i class="fas fa-star text-warning"></i> 2 - Below Average</li>
                            <li><i class="fas fa-star text-warning"></i> 1 - Poor</li>
                        </ul>
                        
                        <h6 class="mt-3">Consider:</h6>
                        <ul>
                            <li>Communication skills</li>
                            <li>Technical knowledge</li>
                            <li>Problem-solving approach</li>
                            <li>Cultural fit</li>
                            <li>Overall presentation</li>
                        </ul>
                        
                        <div class="alert alert-info mt-3">
                            <small>
                                <i class="fas fa-info-circle"></i> Your review will be shared with the candidate and other recruiters.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-paper-plane"></i> Submit Review
                        </button>
                        <a href="{{ route('recruiter.applications.show', $application) }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Overall rating functionality
    const overallRatingBtns = document.querySelectorAll('.overall-rating-btn');
    const overallRatingInput = document.getElementById('overall_rating');
    
    overallRatingBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            overallRatingInput.value = rating;
            
            // Update button states
            overallRatingBtns.forEach((b, index) => {
                if (index < rating) {
                    b.classList.remove('btn-outline-warning');
                    b.classList.add('btn-warning');
                } else {
                    b.classList.remove('btn-warning');
                    b.classList.add('btn-outline-warning');
                }
            });
        });
    });
    
    // Response rating functionality
    const responseRatingBtns = document.querySelectorAll('.rating-btn');
    
    responseRatingBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const rating = parseInt(this.dataset.rating);
            const responseIndex = this.dataset.responseIndex;
            const ratingInput = document.getElementById(`response_rating_${responseIndex}`);
            const btnGroup = this.closest('.btn-group');
            const buttons = btnGroup.querySelectorAll('button');
            
            ratingInput.value = rating;
            
            // Update button states
            buttons.forEach((b, index) => {
                if (index < rating) {
                    b.classList.remove('btn-outline-warning');
                    b.classList.add('btn-warning');
                } else {
                    b.classList.remove('btn-warning');
                    b.classList.add('btn-outline-warning');
                }
            });
        });
    });
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        if (overallRatingInput.value == 0) {
            event.preventDefault();
            overallRatingInput.classList.add('is-invalid');
            overallRatingInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});
</script>
@endsection
