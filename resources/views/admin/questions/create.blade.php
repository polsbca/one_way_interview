@extends('layouts.app')

@section('title', 'Add Question - ' . $job->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Add Question</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-sm btn-outline-secondary">Back to Job</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.questions.store', $job) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question Text *</label>
                            <textarea class="form-control @error('question_text') is-invalid @enderror" id="question_text" name="question_text" rows="4" required>{{ old('question_text') }}</textarea>
                            @error('question_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="type" class="form-label">Question Type *</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="video" {{ old('type') === 'video' ? 'selected' : '' }}>Video Response</option>
                                    <option value="text" {{ old('type') === 'text' ? 'selected' : '' }}>Text Response</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="order" class="form-label">Order *</label>
                                <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $job->questions->count()) }}" min="0" required>
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="time_limit" class="form-label">Time Limit (seconds) *</label>
                                <input type="number" class="form-control @error('time_limit') is-invalid @enderror" id="time_limit" name="time_limit" value="{{ old('time_limit', 60) }}" min="10" max="600" required>
                                @error('time_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Between 10 seconds and 10 minutes</div>
                            </div>
                            <div class="col-md-6">
                                <label for="max_attempts" class="form-label">Maximum Attempts *</label>
                                <input type="number" class="form-control @error('max_attempts') is-invalid @enderror" id="max_attempts" name="max_attempts" value="{{ old('max_attempts', 1) }}" min="1" max="10" required>
                                @error('max_attempts')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="is_required" class="form-label">Is Required?</label>
                            <select class="form-select @error('is_required') is-invalid @enderror" id="is_required" name="is_required" required>
                                <option value="1" {{ old('is_required', '1') === '1' ? 'selected' : '' }}>Yes - Candidate must answer this question</option>
                                <option value="0" {{ old('is_required', '1') === '0' ? 'selected' : '' }}>No - Candidate can skip this question</option>
                            </select>
                            @error('is_required')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="instructions" class="form-label">Instructions</label>
                            <textarea class="form-control @error('instructions') is-invalid @enderror" id="instructions" name="instructions" rows="3" placeholder="Optional instructions for the candidate">{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Question Guidelines</h5>
                </div>
                <div class="card-body">
                    <h6>Question Types</h6>
                    <ul class="text-muted">
                        <li><strong>Video Response:</strong> Candidates record a video answer</li>
                        <li><strong>Text Response:</strong> Candidates type a written answer</li>
                    </ul>
                    
                    <h6>Time Limits</h6>
                    <ul class="text-muted">
                        <li>Recommended: 30-120 seconds for video questions</li>
                        <li>Recommended: 300-600 seconds for text questions</li>
                        <li>Minimum: 10 seconds</li>
                        <li>Maximum: 600 seconds (10 minutes)</li>
                    </ul>
                    
                    <h6>Best Practices</h6>
                    <ul class="text-muted">
                        <li>Keep questions clear and concise</li>
                        <li>Test questions before publishing</li>
                        <li>Consider the candidate's experience</li>
                        <li>Provide helpful instructions when needed</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
