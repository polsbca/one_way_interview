<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Question;
use App\Models\User;
use App\Services\ValidationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        $jobs = Job::with(['creator', 'applications'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.jobs.index', compact('jobs'));
    }

    public function create()
    {
        $recruiters = User::where('role', 'recruiter')->get();
        return view('admin.jobs.create', compact('recruiters'));
    }

    public function store(Request $request)
    {
        try {
            $validator = ValidationService::validateJob($request->all());
            
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $job = Job::create([
                'title' => $request->title,
                'description' => $request->description,
                'company' => $request->company,
                'location' => $request->location,
                'type' => $request->type ?? 'full_time',
                'salary_range' => $request->salary_range,
                'requirements' => $request->requirements,
                'benefits' => $request->benefits,
                'application_deadline' => $request->application_deadline,
                'max_applications' => $request->max_applications,
                'is_active' => $request->is_active ?? true,
                'created_by' => auth()->id(),
            ]);

            // Create questions if provided
            if ($request->has('questions')) {
                foreach ($request->questions as $questionData) {
                    $job->questions()->create([
                        'question_text' => $questionData['question_text'],
                        'question_type' => $questionData['question_type'],
                        'time_limit' => $questionData['time_limit'],
                        'order' => $questionData['order'],
                        'is_required' => $questionData['is_required'] ?? true,
                    ]);
                }
            }

            return redirect()->route('admin.jobs.show', $job)
                ->with('success', 'Job created successfully!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        } catch (\Exception $e) {
            \Log::error('Job creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create job. Please try again.');
        }
    }

    public function show(Job $job)
    {
        $job->load(['creator', 'questions', 'applications.candidate']);
        return view('admin.jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        $recruiters = User::where('role', 'recruiter')->get();
        return view('admin.jobs.edit', compact('job', 'recruiters'));
    }

    public function update(Request $request, Job $job)
    {
        try {
            $validator = ValidationService::validateJob($request->all(), true, $job->id);
            
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $job->update([
                'title' => $request->title,
                'description' => $request->description,
                'company' => $request->company,
                'location' => $request->location,
                'type' => $request->type ?? 'full_time',
                'salary_range' => $request->salary_range,
                'requirements' => $request->requirements,
                'benefits' => $request->benefits,
                'application_deadline' => $request->application_deadline,
                'max_applications' => $request->max_applications,
                'is_active' => $request->is_active ?? true,
            ]);

            // Update questions if provided
            if ($request->has('questions')) {
                // Remove existing questions
                $job->questions()->delete();
                
                // Create new questions
                foreach ($request->questions as $questionData) {
                    $job->questions()->create([
                        'question_text' => $questionData['question_text'],
                        'question_type' => $questionData['question_type'],
                        'time_limit' => $questionData['time_limit'],
                        'order' => $questionData['order'],
                        'is_required' => $questionData['is_required'] ?? true,
                    ]);
                }
            }

            return redirect()->route('admin.jobs.show', $job)
                ->with('success', 'Job updated successfully!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        } catch (\Exception $e) {
            \Log::error('Job update failed', [
                'error' => $e->getMessage(),
                'job_id' => $job->id,
                'user_id' => auth()->id(),
                'request_data' => $request->all(),
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update job. Please try again.');
        }
    }

    public function destroy(Job $job)
    {
        try {
            // Check if job has applications
            if ($job->applications()->exists()) {
                return redirect()->route('admin.jobs.index')
                    ->with('error', 'Cannot delete job with existing applications. Please close the job instead.');
            }
            
            $job->delete();
            
            return redirect()->route('admin.jobs.index')
                ->with('success', 'Job deleted successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Job deletion failed', [
                'error' => $e->getMessage(),
                'job_id' => $job->id,
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('admin.jobs.index')
                ->with('error', 'Failed to delete job. Please try again.');
        }
    }

    public function publish(Job $job)
    {
        try {
            $job->update(['status' => 'published']);
            
            return redirect()->route('admin.jobs.show', $job)
                ->with('success', 'Job published successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Job publication failed', [
                'error' => $e->getMessage(),
                'job_id' => $job->id,
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('admin.jobs.show', $job)
                ->with('error', 'Failed to publish job. Please try again.');
        }
    }

    public function close(Job $job)
    {
        try {
            $job->update(['status' => 'closed']);
            
            return redirect()->route('admin.jobs.show', $job)
                ->with('success', 'Job closed successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Job closing failed', [
                'error' => $e->getMessage(),
                'job_id' => $job->id,
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('admin.jobs.show', $job)
                ->with('error', 'Failed to close job. Please try again.');
        }
    }
}
