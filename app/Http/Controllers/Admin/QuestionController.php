<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function create(Job $job)
    {
        return view('admin.questions.create', compact('job'));
    }

    public function store(Request $request, Job $job)
    {
        $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:video,text',
            'time_limit' => 'required|integer|min:10|max:600',
            'max_attempts' => 'required|integer|min:1|max:10',
            'order' => 'required|integer|min:0',
            'is_required' => 'required|boolean',
            'instructions' => 'nullable|string',
        ]);

        $question = $job->questions()->create([
            'question_text' => $request->question_text,
            'type' => $request->type,
            'time_limit' => $request->time_limit,
            'max_attempts' => $request->max_attempts,
            'order' => $request->order,
            'is_required' => $request->is_required,
            'instructions' => $request->instructions,
        ]);

        return redirect()->route('admin.jobs.show', $job)
            ->with('success', 'Question added successfully!');
    }

    public function edit(Job $job, Question $question)
    {
        return view('admin.questions.edit', compact('job', 'question'));
    }

    public function update(Request $request, Job $job, Question $question)
    {
        $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:video,text',
            'time_limit' => 'required|integer|min:10|max:600',
            'max_attempts' => 'required|integer|min:1|max:10',
            'order' => 'required|integer|min:0',
            'is_required' => 'required|boolean',
            'instructions' => 'nullable|string',
        ]);

        $question->update([
            'question_text' => $request->question_text,
            'type' => $request->type,
            'time_limit' => $request->time_limit,
            'max_attempts' => $request->max_attempts,
            'order' => $request->order,
            'is_required' => $request->is_required,
            'instructions' => $request->instructions,
        ]);

        return redirect()->route('admin.jobs.show', $job)
            ->with('success', 'Question updated successfully!');
    }

    public function destroy(Job $job, Question $question)
    {
        $question->delete();
        return redirect()->route('admin.jobs.show', $job)
            ->with('success', 'Question deleted successfully!');
    }

    public function reorder(Request $request, Job $job)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|exists:questions,id',
            'questions.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->questions as $questionData) {
            $question = $job->questions()->find($questionData['id']);
            if ($question) {
                $question->update(['order' => $questionData['order']]);
            }
        }

        return response()->json(['message' => 'Questions reordered successfully!']);
    }
}
