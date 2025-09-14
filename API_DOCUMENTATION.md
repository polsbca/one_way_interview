# API Documentation

This document provides detailed API documentation for the One Way Interview Platform.

## Authentication

All API endpoints require authentication using Bearer tokens. Include the token in the Authorization header:

```
Authorization: Bearer {your-token}
```

### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "email": "john@example.com",
            "role": "admin"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
    },
    "message": "Login successful"
}
```

### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

### Get Current User
```http
GET /api/user
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john@example.com",
        "role": "admin",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
}
```

## Jobs API

### Get All Jobs
```http
GET /api/jobs
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` (optional): Filter by status (draft, published, closed)
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "title": "Software Developer",
                "description": "We are looking for a skilled software developer...",
                "requirements": "Bachelor's degree in Computer Science...",
                "status": "published",
                "created_at": "2023-01-01T00:00:00.000000Z",
                "updated_at": "2023-01-01T00:00:00.000000Z"
            }
        ],
        "total": 1,
        "per_page": 15
    }
}
```

### Get Job Details
```http
GET /api/jobs/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Software Developer",
        "description": "We are looking for a skilled software developer...",
        "requirements": "Bachelor's degree in Computer Science...",
        "status": "published",
        "questions": [
            {
                "id": 1,
                "question": "Tell us about your experience with Laravel.",
                "time_limit": 120,
                "order": 1
            }
        ],
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    }
}
```

### Create Job (Admin Only)
```http
POST /api/jobs
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Software Developer",
    "description": "We are looking for a skilled software developer...",
    "requirements": "Bachelor's degree in Computer Science...",
    "status": "draft",
    "questions": [
        {
            "question": "Tell us about your experience with Laravel.",
            "time_limit": 120,
            "order": 1
        }
    ]
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Software Developer",
        "description": "We are looking for a skilled software developer...",
        "requirements": "Bachelor's degree in Computer Science...",
        "status": "draft",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    },
    "message": "Job created successfully"
}
```

### Update Job (Admin Only)
```http
PUT /api/jobs/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Senior Software Developer",
    "description": "We are looking for an experienced software developer...",
    "requirements": "Bachelor's degree in Computer Science and 5+ years experience...",
    "status": "published"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "title": "Senior Software Developer",
        "description": "We are looking for an experienced software developer...",
        "requirements": "Bachelor's degree in Computer Science and 5+ years experience...",
        "status": "published",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    },
    "message": "Job updated successfully"
}
```

### Delete Job (Admin Only)
```http
DELETE /api/jobs/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Job deleted successfully"
}
```

### Publish Job (Admin Only)
```http
POST /api/jobs/{id}/publish
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Job published successfully"
}
```

### Close Job (Admin Only)
```http
POST /api/jobs/{id}/close
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Job closed successfully"
}
```

## Applications API

### Get Applications
```http
GET /api/applications
Authorization: Bearer {token}
```

**Query Parameters:**
- `job_id` (optional): Filter by job ID
- `status` (optional): Filter by status (pending, reviewed, proceed, reject, hold)
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "job_id": 1,
                "user_id": 2,
                "status": "pending",
                "created_at": "2023-01-01T00:00:00.000000Z",
                "job": {
                    "id": 1,
                    "title": "Software Developer"
                },
                "candidate": {
                    "id": 2,
                    "first_name": "Jane",
                    "last_name": "Doe",
                    "email": "jane@example.com"
                }
            }
        ],
        "total": 1,
        "per_page": 15
    }
}
```

### Get Application Details
```http
GET /api/applications/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "job_id": 1,
        "user_id": 2,
        "status": "pending",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "job": {
            "id": 1,
            "title": "Software Developer",
            "description": "We are looking for a skilled software developer..."
        },
        "candidate": {
            "id": 2,
            "first_name": "Jane",
            "last_name": "Doe",
            "email": "jane@example.com"
        },
        "responses": [
            {
                "id": 1,
                "question_id": 1,
                "video_url": "https://example.com/storage/videos/response1.mp4",
                "duration": 120,
                "rating": null,
                "comment": null,
                "question": {
                    "id": 1,
                    "question": "Tell us about your experience with Laravel.",
                    "time_limit": 120
                }
            }
        ]
    }
}
```

### Create Application (Candidate Only)
```http
POST /api/applications
Authorization: Bearer {token}
Content-Type: application/json

{
    "job_id": 1
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "job_id": 1,
        "user_id": 2,
        "status": "pending",
        "created_at": "2023-01-01T00:00:00.000000Z"
    },
    "message": "Application created successfully"
}
```

### Update Application Status (Recruiter/Admin Only)
```http
PUT /api/applications/{id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "proceed"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "job_id": 1,
        "user_id": 2,
        "status": "proceed",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    },
    "message": "Application status updated successfully"
}
```

## Responses API

### Submit Video Response (Candidate Only)
```http
POST /api/responses
Authorization: Bearer {token}
Content-Type: multipart/form-data

video: [video file]
application_id: 1
question_id: 1
duration: 120
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "application_id": 1,
        "question_id": 1,
        "video_url": "https://example.com/storage/videos/response1.mp4",
        "duration": 120,
        "created_at": "2023-01-01T00:00:00.000000Z"
    },
    "message": "Response submitted successfully"
}
```

### Rate Response (Recruiter/Admin Only)
```http
POST /api/responses/{id}/rate
Authorization: Bearer {token}
Content-Type: application/json

{
    "rating": 4,
    "comment": "Good response, shows strong technical knowledge"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "rating": 4,
        "comment": "Good response, shows strong technical knowledge",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    },
    "message": "Response rated successfully"
}
```

### Get Response Details
```http
GET /api/responses/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "application_id": 1,
        "question_id": 1,
        "video_url": "https://example.com/storage/videos/response1.mp4",
        "duration": 120,
        "rating": 4,
        "comment": "Good response, shows strong technical knowledge",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "application": {
            "id": 1,
            "job_id": 1,
            "user_id": 2,
            "status": "pending"
        },
        "question": {
            "id": 1,
            "question": "Tell us about your experience with Laravel.",
            "time_limit": 120
        }
    }
}
```

## Users API

### Get Users (Admin Only)
```http
GET /api/users
Authorization: Bearer {token}
```

**Query Parameters:**
- `role` (optional): Filter by role (admin, recruiter, candidate)
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "first_name": "John",
                "last_name": "Doe",
                "email": "john@example.com",
                "role": "admin",
                "created_at": "2023-01-01T00:00:00.000000Z"
            }
        ],
        "total": 1,
        "per_page": 15
    }
}
```

### Create User (Admin Only)
```http
POST /api/users
Authorization: Bearer {token}
Content-Type: application/json

{
    "first_name": "Jane",
    "last_name": "Doe",
    "email": "jane@example.com",
    "password": "password123",
    "role": "recruiter"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "first_name": "Jane",
        "last_name": "Doe",
        "email": "jane@example.com",
        "role": "recruiter",
        "created_at": "2023-01-01T00:00:00.000000Z"
    },
    "message": "User created successfully"
}
```

### Update User (Admin Only)
```http
PUT /api/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "first_name": "Jane",
    "last_name": "Smith",
    "email": "jane.smith@example.com"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "first_name": "Jane",
        "last_name": "Smith",
        "email": "jane.smith@example.com",
        "role": "recruiter",
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    },
    "message": "User updated successfully"
}
```

### Delete User (Admin Only)
```http
DELETE /api/users/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "User deleted successfully"
}
```

## Questions API

### Get Questions
```http
GET /api/questions
Authorization: Bearer {token}
```

**Query Parameters:**
- `job_id` (optional): Filter by job ID

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "job_id": 1,
            "question": "Tell us about your experience with Laravel.",
            "time_limit": 120,
            "order": 1,
            "created_at": "2023-01-01T00:00:00.000000Z"
        }
    ]
}
```

### Create Question (Admin Only)
```http
POST /api/questions
Authorization: Bearer {token}
Content-Type: application/json

{
    "job_id": 1,
    "question": "Tell us about your experience with Laravel.",
    "time_limit": 120,
    "order": 1
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "job_id": 1,
        "question": "Tell us about your experience with Laravel.",
        "time_limit": 120,
        "order": 1,
        "created_at": "2023-01-01T00:00:00.000000Z"
    },
    "message": "Question created successfully"
}
```

### Update Question (Admin Only)
```http
PUT /api/questions/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "question": "Tell us about your experience with Laravel and PHP.",
    "time_limit": 180,
    "order": 1
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "job_id": 1,
        "question": "Tell us about your experience with Laravel and PHP.",
        "time_limit": 180,
        "order": 1,
        "created_at": "2023-01-01T00:00:00.000000Z",
        "updated_at": "2023-01-01T00:00:00.000000Z"
    },
    "message": "Question updated successfully"
}
```

### Delete Question (Admin Only)
```http
DELETE /api/questions/{id}
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "message": "Question deleted successfully"
}
```

## Dashboard API

### Get Dashboard Stats
```http
GET /api/dashboard/stats
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total_jobs": 10,
        "published_jobs": 8,
        "total_applications": 25,
        "pending_applications": 10,
        "reviewed_applications": 15,
        "total_users": 50,
        "admins": 2,
        "recruiters": 5,
        "candidates": 43
    }
}
```

### Get Recent Applications
```http
GET /api/dashboard/recent-applications
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "job_title": "Software Developer",
            "candidate_name": "Jane Doe",
            "status": "pending",
            "created_at": "2023-01-01T00:00:00.000000Z"
        }
    ]
}
```

## Error Responses

All error responses follow this format:

```json
{
    "success": false,
    "message": "Error description",
    "status": 400,
    "errors": {
        "field": ["Error message for field"]
    }
}
```

### Common HTTP Status Codes

- **200 OK**: Successful request
- **201 Created**: Resource created successfully
- **400 Bad Request**: Invalid request data
- **401 Unauthorized**: Authentication required
- **403 Forbidden**: Insufficient permissions
- **404 Not Found**: Resource not found
- **422 Unprocessable Entity**: Validation failed
- **500 Internal Server Error**: Server error

## Rate Limiting

API requests are rate limited to 60 requests per minute per authenticated user.

## File Uploads

### Video Upload Requirements
- **Max File Size**: 100MB
- **Allowed Formats**: MP4, MPEG, QuickTime, AVI, WMV, WebM
- **Content-Type**: video/mp4, video/mpeg, video/quicktime, video/x-msvideo, video/x-ms-wmv, video/webm

### Upload Endpoint
```http
POST /api/responses
Authorization: Bearer {token}
Content-Type: multipart/form-data

video: [video file]
application_id: 1
question_id: 1
duration: 120
```

## Webhooks

### Application Status Changed
When an application status is updated, a webhook can be triggered:

```json
{
    "event": "application.status_changed",
    "data": {
        "application_id": 1,
        "old_status": "pending",
        "new_status": "proceed",
        "candidate_email": "jane@example.com",
        "job_title": "Software Developer"
    }
}
```

### New Application Submitted
When a new application is submitted:

```json
{
    "event": "application.submitted",
    "data": {
        "application_id": 1,
        "candidate_name": "Jane Doe",
        "candidate_email": "jane@example.com",
        "job_title": "Software Developer"
    }
}
```

## SDK Examples

### JavaScript/Node.js
```javascript
const api = {
    baseUrl: 'https://your-domain.com/api',
    token: 'your-token',
    
    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const headers = {
            'Authorization': `Bearer ${this.token}`,
            'Content-Type': 'application/json',
            ...options.headers
        };
        
        const response = await fetch(url, {
            ...options,
            headers
        });
        
        return await response.json();
    },
    
    async getJobs() {
        return await this.request('/jobs');
    },
    
    async createJob(jobData) {
        return await this.request('/jobs', {
            method: 'POST',
            body: JSON.stringify(jobData)
        });
    }
};

// Usage
const jobs = await api.getJobs();
console.log(jobs);
```

### PHP
```php
class OneWayInterviewAPI {
    private $baseUrl;
    private $token;
    
    public function __construct($baseUrl, $token) {
        $this->baseUrl = $baseUrl;
        $this->token = $token;
    }
    
    private function request($endpoint, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    public function getJobs() {
        return $this->request('/jobs');
    }
    
    public function createJob($jobData) {
        return $this->request('/jobs', 'POST', $jobData);
    }
}

// Usage
$api = new OneWayInterviewAPI('https://your-domain.com/api', 'your-token');
$jobs = $api->getJobs();
print_r($jobs);
```

### Python
```python
import requests
import json

class OneWayInterviewAPI:
    def __init__(self, base_url, token):
        self.base_url = base_url
        self.token = token
        self.headers = {
            'Authorization': f'Bearer {token}',
            'Content-Type': 'application/json'
        }
    
    def request(self, endpoint, method='GET', data=None):
        url = f"{self.base_url}{endpoint}"
        
        if method == 'GET':
            response = requests.get(url, headers=self.headers)
        elif method == 'POST':
            response = requests.post(url, headers=self.headers, json=data)
        elif method == 'PUT':
            response = requests.put(url, headers=self.headers, json=data)
        elif method == 'DELETE':
            response = requests.delete(url, headers=self.headers)
        
        return response.json()
    
    def get_jobs(self):
        return self.request('/jobs')
    
    def create_job(self, job_data):
        return self.request('/jobs', 'POST', job_data)

# Usage
api = OneWayInterviewAPI('https://your-domain.com/api', 'your-token')
jobs = api.get_jobs()
print(jobs)
```

---

For additional support or questions, please refer to the main documentation or create an issue on GitHub.
