<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationHelper
{
    /**
     * Validate and return validated data or throw exception
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return array
     * @throws ValidationException
     */
    public static function validateOrThrow(array $data, array $rules, array $messages = [])
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Validate and return validation result
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return \Illuminate\Validation\Validator
     */
    public static function validate(array $data, array $rules, array $messages = [])
    {
        return Validator::make($data, $rules, $messages);
    }

    /**
     * Check if data passes validation
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return bool
     */
    public static function passes(array $data, array $rules, array $messages = [])
    {
        return Validator::make($data, $rules, $messages)->passes();
    }

    /**
     * Check if data fails validation
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return bool
     */
    public static function fails(array $data, array $rules, array $messages = [])
    {
        return Validator::make($data, $rules, $messages)->fails();
    }

    /**
     * Get validation errors
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return array
     */
    public static function errors(array $data, array $rules, array $messages = [])
    {
        return Validator::make($data, $rules, $messages)->errors()->all();
    }

    /**
     * Validate email format
     *
     * @param string $email
     * @return bool
     */
    public static function isValidEmail(string $email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate phone number format (basic validation)
     *
     * @param string $phone
     * @return bool
     */
    public static function isValidPhone(string $phone)
    {
        return preg_match('/^[\d\s\-\+\(\)]{10,20}$/', $phone) === 1;
    }

    /**
     * Validate password strength
     *
     * @param string $password
     * @return bool
     */
    public static function isStrongPassword(string $password)
    {
        // At least 8 characters, one uppercase, one lowercase, one number, one special character
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password) === 1;
    }

    /**
     * Validate file upload
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param array $allowedMimes
     * @param int $maxSizeInMB
     * @return bool
     */
    public static function isValidFile($file, array $allowedMimes = [], int $maxSizeInMB = 10)
    {
        if (!$file || !$file->isValid()) {
            return false;
        }

        if ($file->getSize() > $maxSizeInMB * 1024 * 1024) {
            return false;
        }

        if (!empty($allowedMimes) && !in_array($file->getMimeType(), $allowedMimes)) {
            return false;
        }

        return true;
    }

    /**
     * Validate video file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $maxSizeInMB
     * @return bool
     */
    public static function isValidVideo($file, int $maxSizeInMB = 100)
    {
        $allowedMimes = [
            'video/mp4',
            'video/mpeg',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-ms-wmv',
            'video/webm',
        ];

        return self::isValidFile($file, $allowedMimes, $maxSizeInMB);
    }

    /**
     * Validate image file
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $maxSizeInMB
     * @return bool
     */
    public static function isValidImage($file, int $maxSizeInMB = 5)
    {
        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/bmp',
        ];

        return self::isValidFile($file, $allowedMimes, $maxSizeInMB);
    }

    /**
     * Sanitize input data
     *
     * @param array $data
     * @return array
     */
    public static function sanitize(array $data)
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            }
            return $value;
        }, $data);
    }

    /**
     * Validate URL format
     *
     * @param string $url
     * @return bool
     */
    public static function isValidUrl(string $url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate date format
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function isValidDate(string $date, string $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Validate time format
     *
     * @param string $time
     * @return bool
     */
    public static function isValidTime(string $time)
    {
        return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time) === 1;
    }

    /**
     * Validate numeric range
     *
     * @param mixed $value
     * @param int $min
     * @param int $max
     * @return bool
     */
    public static function isInRange($value, int $min, int $max)
    {
        return is_numeric($value) && $value >= $min && $value <= $max;
    }

    /**
     * Validate required fields in array
     *
     * @param array $data
     * @param array $requiredFields
     * @return array
     */
    public static function validateRequired(array $data, array $requiredFields)
    {
        $missing = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        return $missing;
    }

    /**
     * Get common validation messages
     *
     * @return array
     */
    public static function commonMessages()
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
            'after' => 'The :attribute must be a date after :date.',
            'before' => 'The :attribute must be a date before :date.',
            'integer' => 'The :attribute must be an integer.',
            'numeric' => 'The :attribute must be a number.',
            'boolean' => 'The :attribute field must be true or false.',
            'array' => 'The :attribute must be an array.',
            'image' => 'The :attribute must be an image.',
            'mimes' => 'The :attribute must be a file of type: :values.',
            'file' => 'The :attribute must be a file.',
            'size' => 'The :attribute must be :size kilobytes.',
            'between' => 'The :attribute must be between :min and :max kilobytes.',
        ];
    }
}
