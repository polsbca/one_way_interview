@extends('layouts.app')

@section('title', 'Interview Question - ' . $question->question_text)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Interview Question</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('candidate.dashboard') }}" class="btn btn-sm btn-outline-secondary">Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Interview Progress</span>
                <span class="text-muted">{{ $application->progress_percentage }}% Complete</span>
            </div>
            <div class="progress" style="height: 10px;">
                <div class="progress-bar" role="progressbar" style="width: {{ $application->progress_percentage }}%;" aria-valuenow="{{ $application->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="text-muted mt-2">
                Question {{ $attempts + 1 }} of {{ $question->max_attempts }} for this question
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Question Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Question {{ $question->order + 1 }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h4>{{ $question->question_text }}</h4>
                        <div class="d-flex gap-2 mt-3">
                            <span class="badge bg-{{ $question->type === 'video' ? 'primary' : 'secondary' }}">
                                {{ ucfirst($question->type) }} Response
                            </span>
                            <span class="badge bg-info">
                                Time Limit: {{ $question->time_limit }} seconds
                            </span>
                            @if($question->is_required)
                                <span class="badge bg-danger">Required</span>
                            @endif
                        </div>
                    </div>

                    @if($question->instructions)
                        <div class="alert alert-info">
                            <strong>Instructions:</strong> {{ $question->instructions }}
                        </div>
                    @endif

                    <!-- Video Recording Interface -->
                    @if($question->type === 'video')
                        <div class="video-recording-container">
                            <div class="text-center mb-4">
                                <video id="preview" class="img-fluid rounded" style="max-width: 100%; max-height: 400px; background: #000;" autoplay muted></video>
                                <video id="recording" class="img-fluid rounded mt-3" style="max-width: 100%; max-height: 400px; background: #000; display: none;" controls></video>
                            </div>
                            
                            <div class="text-center">
                                <button id="startBtn" class="btn btn-primary btn-lg me-2">
                                    <i class="bi bi-camera-video"></i> Start Recording
                                </button>
                                <button id="stopBtn" class="btn btn-danger btn-lg me-2" style="display: none;">
                                    <i class="bi bi-stop-circle"></i> Stop Recording
                                </button>
                                <button id="retakeBtn" class="btn btn-warning btn-lg me-2" style="display: none;">
                                    <i class="bi bi-arrow-clockwise"></i> Retake
                                </button>
                                <button id="submitBtn" class="btn btn-success btn-lg" style="display: none;" disabled>
                                    <i class="bi bi-check-circle"></i> Submit Response
                                </button>
                            </div>
                            
                            <div class="text-center mt-3">
                                <div id="timer" class="h4 text-danger" style="display: none;">00:00</div>
                                <div id="countdown" class="h4 text-warning" style="display: none;">Time remaining: {{ $question->time_limit }}s</div>
                            </div>
                        </div>
                    @endif

                    <!-- Text Response Interface -->
                    @if($question->type === 'text')
                        <div class="text-response-container">
                            <form id="textResponseForm">
                                @csrf
                                <div class="mb-3">
                                    <textarea class="form-control" id="textResponse" rows="8" placeholder="Type your response here..." required></textarea>
                                    <div class="form-text">
                                        <span id="charCount">0</span> characters
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" class="btn btn-success btn-lg" id="submitTextBtn">
                                        <i class="bi bi-check-circle"></i> Submit Response
                                    </button>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <div id="textTimer" class="h4 text-warning">Time remaining: {{ $question->time_limit }}s</div>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Previous Responses -->
                    @if($previousResponses->count() > 0)
                        <div class="mt-4">
                            <h6>Previous Attempts</h6>
                            <div class="accordion" id="previousResponsesAccordion">
                                @foreach($previousResponses as $index => $response)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="prevHeading{{ $index }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#prevCollapse{{ $index }}">
                                                Attempt {{ $response->attempt_number }} - {{ $response->created_at->format('M d, Y H:i') }}
                                            </button>
                                        </h2>
                                        <div id="prevCollapse{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#previousResponsesAccordion">
                                            <div class="accordion-body">
                                                @if($response->response_type === 'video')
                                                    <video controls class="w-100" style="max-height: 300px;">
                                                        <source src="{{ route('video.stream', $response) }}" type="video/webm">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @else
                                                    <p>{{ $response->response_data }}</p>
                                                @endif
                                                <div class="text-muted mt-2">
                                                    Duration: {{ $response->duration }} seconds
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Interview Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Interview Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Job:</strong> {{ $application->job->title }}
                    </div>
                    <div class="mb-3">
                        <strong>Company:</strong> {{ $application->job->company }}
                    </div>
                    <div class="mb-3">
                        <strong>Question Type:</strong> {{ ucfirst($question->type) }}
                    </div>
                    <div class="mb-3">
                        <strong>Time Limit:</strong> {{ $question->time_limit }} seconds
                    </div>
                    <div class="mb-3">
                        <strong>Max Attempts:</strong> {{ $question->max_attempts }}
                    </div>
                    <div>
                        <strong>Required:</strong> {{ $question->is_required ? 'Yes' : 'No' }}
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tips</h5>
                </div>
                <div class="card-body">
                    @if($question->type === 'video')
                        <ul class="mb-0">
                            <li>Ensure good lighting and clear audio</li>
                            <li>Look directly at the camera</li>
                            <li>Speak clearly and confidently</li>
                            <li>Be concise and to the point</li>
                            <li>Test your camera and microphone before starting</li>
                        </ul>
                    @else
                        <ul class="mb-0">
                            <li>Be thorough but concise</li>
                            <li>Check your spelling and grammar</li>
                            <li>Answer the question directly</li>
                            <li>Provide specific examples when relevant</li>
                            <li>Stay within the time limit</li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let mediaRecorder;
let recordedChunks = [];
let startTime;
let timerInterval;
let countdownInterval;
let timeRemaining = {{ $question->time_limit }};

// Video recording functionality
@if($question->type === 'video')
const startBtn = document.getElementById('startBtn');
const stopBtn = document.getElementById('stopBtn');
const retakeBtn = document.getElementById('retakeBtn');
const submitBtn = document.getElementById('submitBtn');
const preview = document.getElementById('preview');
const recording = document.getElementById('recording');
const timer = document.getElementById('timer');
const countdown = document.getElementById('countdown');

async function startRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ 
            video: true, 
            audio: true 
        });
        
        preview.srcObject = stream;
        
        mediaRecorder = new MediaRecorder(stream);
        recordedChunks = [];
        
        mediaRecorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                recordedChunks.push(event.data);
            }
        };
        
        mediaRecorder.onstop = () => {
            const blob = new Blob(recordedChunks, { type: 'video/webm' });
            recording.src = URL.createObjectURL(blob);
            recording.style.display = 'block';
            preview.style.display = 'none';
            submitBtn.disabled = false;
            
            // Stop all tracks
            stream.getTracks().forEach(track => track.stop());
        };
        
        mediaRecorder.start();
        startTime = Date.now();
        
        startBtn.style.display = 'none';
        stopBtn.style.display = 'inline-block';
        countdown.style.display = 'block';
        
        // Start countdown timer
        countdownInterval = setInterval(() => {
            timeRemaining--;
            countdown.textContent = `Time remaining: ${timeRemaining}s`;
            
            if (timeRemaining <= 0) {
                stopRecording();
            }
        }, 1000);
        
        // Start recording timer
        timerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            timer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            timer.style.display = 'block';
        }, 1000);
        
    } catch (err) {
        console.error('Error accessing media devices:', err);
        alert('Error accessing camera and microphone. Please ensure you have granted the necessary permissions.');
    }
}

function stopRecording() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
    }
    
    clearInterval(timerInterval);
    clearInterval(countdownInterval);
    
    stopBtn.style.display = 'none';
    retakeBtn.style.display = 'inline-block';
    submitBtn.style.display = 'inline-block';
    timer.style.display = 'none';
    countdown.style.display = 'none';
}

function retakeRecording() {
    recording.style.display = 'none';
    recording.src = '';
    submitBtn.disabled = true;
    
    startBtn.style.display = 'inline-block';
    retakeBtn.style.display = 'none';
    submitBtn.style.display = 'none';
    
    timeRemaining = {{ $question->time_limit }};
    recordedChunks = [];
}

startBtn.addEventListener('click', startRecording);
stopBtn.addEventListener('click', stopRecording);
retakeBtn.addEventListener('click', retakeRecording);

submitBtn.addEventListener('click', async () => {
    const blob = new Blob(recordedChunks, { type: 'video/webm' });
    const formData = new FormData();
    
    formData.append('response_type', 'video');
    formData.append('response_data', 'Video response recorded');
    formData.append('duration', Math.floor((Date.now() - startTime) / 1000));
    formData.append('video_file', blob, 'response.webm');
    
    try {
        const response = await fetch('{{ route("candidate.interview.submit", [$application, $question]) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.location.href = result.next_question_url;
        } else {
            alert('Error submitting response. Please try again.');
        }
    } catch (err) {
        console.error('Error submitting response:', err);
        alert('Error submitting response. Please try again.');
    }
});
@endif

// Text response functionality
@if($question->type === 'text')
const textResponseForm = document.getElementById('textResponseForm');
const textResponse = document.getElementById('textResponse');
const charCount = document.getElementById('charCount');
const textTimer = document.getElementById('textTimer');
const submitTextBtn = document.getElementById('submitTextBtn');

textResponse.addEventListener('input', () => {
    charCount.textContent = textResponse.value.length;
});

let textTimeRemaining = {{ $question->time_limit }};
const textCountdownInterval = setInterval(() => {
    textTimeRemaining--;
    textTimer.textContent = `Time remaining: ${textTimeRemaining}s`;
    
    if (textTimeRemaining <= 0) {
        clearInterval(textCountdownInterval);
        submitTextBtn.click(); // Auto-submit when time runs out
    }
}, 1000);

textResponseForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(textResponseForm);
    formData.append('response_type', 'text');
    formData.append('response_data', textResponse.value);
    formData.append('duration', {{ $question->time_limit }} - textTimeRemaining);
    
    submitTextBtn.disabled = true;
    submitTextBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
    
    try {
        const response = await fetch('{{ route("candidate.interview.submit", [$application, $question]) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.location.href = result.next_question_url;
        } else {
            alert('Error submitting response. Please try again.');
            submitTextBtn.disabled = false;
            submitTextBtn.innerHTML = '<i class="bi bi-check-circle"></i> Submit Response';
        }
    } catch (err) {
        console.error('Error submitting response:', err);
        alert('Error submitting response. Please try again.');
        submitTextBtn.disabled = false;
        submitTextBtn.innerHTML = '<i class="bi bi-check-circle"></i> Submit Response';
    }
});
@endif
</script>
@endpush
@endsection
