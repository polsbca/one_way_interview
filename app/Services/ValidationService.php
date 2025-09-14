<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ValidationService
{
    /**
     * Get validation rules for job creation/update
     */
    public static function jobRules($isUpdate = false, $jobId = null)
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'company' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:full_time,part_time,contract,internship',
            'salary_range' => 'nullable|string|max:100',
            'requirements' => 'required|string|min:10',
            'benefits' => 'nullable|string',
            'application_deadline' => 'required|date|after:today',
            'max_applications' => 'nullable|integer|min:1|max:1000',
            'is_active' => 'sometimes|boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|min:10|max:500',
            'questions.*.question_type' => 'required|in:text,video',
            'questions.*.time_limit' => 'required|integer|min:30|max:300',
            'questions.*.order' => 'required|integer|min:1',
            'questions.*.is_required' => 'sometimes|boolean',
        ];
    }

    /**
     * Get validation rules for user registration
     */
    public static function userRegistrationRules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,recruiter,candidate',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get validation rules for user login
     */
    public static function userLoginRules()
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember' => 'sometimes|boolean',
        ];
    }

    /**
     * Get validation rules for application submission
     */
    public static function applicationSubmissionRules()
    {
        return [
            'job_id' => 'required|exists:jobs,id',
            'responses' => 'required|array',
            'responses.*.question_id' => 'required|exists:questions,id',
            'responses.*.response_type' => 'required|in:text,video',
            'responses.*.response_data' => 'required|string',
            'responses.*.duration' => 'required|integer|min:1',
        ];
    }

    /**
     * Get validation rules for review submission
     */
    public static function reviewSubmissionRules()
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'required|string|min:10|max:2000',
            'feedback' => 'nullable|string|max:2000',
            'decision' => 'required|in:proceed,reject,hold',
            'response_ratings' => 'nullable|array',
            'response_ratings.*.response_id' => 'required|exists:responses,id',
            'response_ratings.*.rating' => 'required|integer|min:1|max:5',
            'response_ratings.*.comment' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get validation rules for response rating
     */
    public static function responseRatingRules()
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get validation rules for profile update
     */
    public static function profileUpdateRules($userId)
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get validation rules for password change
     */
    public static function passwordChangeRules()
    {
        return [
            'current_password' => 'required|string|current_password',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Get custom validation messages
     */
    public static function customMessages()
    {
        return [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute must be a string.',
            'email' => 'The :attribute must be a valid email address.',
            'min' => 'The :attribute must be at least :min characters.',
            'max' => 'The :attribute may not be greater than :max characters.',
            'unique' => 'The :attribute has already been taken.',
            'confirmed' => 'The :attribute confirmation does not match.',
            'in' => 'The selected :attribute is invalid.',
            'exists' => 'The selected :attribute is invalid.',
            'date' => 'The :attribute must be a valid date.',
            'after' => 'The :attribute must be a date after today.',
            'integer' => 'The :attribute must be an integer.',
            'boolean' => 'The :attribute field must be true or false.',
            'array' => 'The :attribute must be an array.',
            'image' => 'The :attribute must be an image.',
            'mimes' => 'The :attribute must be a file of type: :values.',
            'current_password' => 'The current password is incorrect.',
            
            // Custom messages for specific fields
            'title.required' => 'The job title is required.',
            'description.required' => 'The job description is required.',
            'description.min' => 'The job description must be at least 10 characters.',
            'company.required' => 'The company name is required.',
            'application_deadline.after' => 'The application deadline must be a future date.',
            'questions.required' => 'At least one interview question is required.',
            'questions.*.question_text.required' => 'Each question must have text.',
            'questions.*.question_text.min' => 'Each question must be at least 10 characters.',
            'questions.*.time_limit.required' => 'Each question must have a time limit.',
            'questions.*.time_limit.min' => 'Time limit must be at least 30 seconds.',
            'questions.*.time_limit.max' => 'Time limit cannot exceed 5 minutes.',
            'responses.required' => 'Responses are required.',
            'responses.*.response_data.required' => 'Each response must have data.',
            'rating.required' => 'A rating is required.',
            'rating.min' => 'Rating must be at least 1.',
            'rating.max' => 'Rating cannot exceed 5.',
            'comments.required' => 'Comments are required.',
            'comments.min' => 'Comments must be at least 10 characters.',
            'decision.required' => 'A decision is required.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }

    /**
     * Validate data with given rules
     */
    public static function validate(array $data, array $rules, array $messages = [])
    {
        $customMessages = array_merge(self::customMessages(), $messages);
        
        return Validator::make($data, $rules, $customMessages);
    }

    /**
     * Validate job data
     */
    public static function validateJob(array $data, $isUpdate = false, $jobId = null)
    {
        return self::validate($data, self::jobRules($isUpdate, $jobId));
    }

    /**
     * Validate user registration data
     */
    public static function validateUserRegistration(array $data)
    {
        return self::validate($data, self::userRegistrationRules());
    }

    /**
     * Validate user login data
     */
    public static function validateUserLogin(array $data)
    {
        return self::validate($data, self::userLoginRules());
    }

    /**
     * Validate application submission data
     */
    public static function validateApplicationSubmission(array $data)
    {
        return self::validate($data, self::applicationSubmissionRules());
    }

    /**
     * Validate review submission data
     */
    public static function validateReviewSubmission(array $data)
    {
        return self::validate($data, self::reviewSubmissionRules());
    }

    /**
     * Validate response rating data
     */
    public static function validateResponseRating(array $data)
    {
        return self::validate($data, self::responseRatingRules());
    }

    /**
     * Validate profile update data
     */
    public static function validateProfileUpdate(array $data, $userId)
    {
        return self::validate($data, self::profileUpdateRules($userId));
    }

    /**
     * Validate password change data
     */
    public static function validatePasswordChange(array $data)
    {
        return self::validate($data, self::passwordChangeRules());
    }
}
