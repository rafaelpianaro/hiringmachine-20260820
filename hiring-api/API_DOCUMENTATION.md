# HiringMachine API Documentation

## Base URL
```
http://localhost:5757/api/v1
```

## Authentication
All protected routes require a JWT token in the Authorization header:
```
Authorization: Bearer <your-token-here>
```

---

## Authentication Endpoints

### POST /auth/login
Login with email and password.

**Request:**
```json
{
    "email": "user@example.com",
    "password": "password"
}
```

**Response (200):**
```json
{
    "status": "success",
    "message": "Sucesso",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 3600,
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "role": "candidate"
        }
    }
}
```

### POST /auth/register
Register a new user.

**Request:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "candidate",
    "phone": "+55 11 99999-9999",
    "company": "Tech Company",
    "position": "Developer"
}
```

**Response (201):**
```json
{
    "status": "success",
    "message": "Sucesso",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 3600,
        "user": {
            "id": 6,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "candidate"
        }
    }
}
```

### POST /auth/logout
Logout and invalidate the token.

**Headers:** `Authorization: Bearer <token>`

**Response (200):**
```json
{
    "message": "Logout realizado com sucesso."
}
```

### POST /auth/refresh
Refresh the JWT token.

**Headers:** `Authorization: Bearer <token>`

**Response (200):**
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {...}
}
```

### GET /auth/me
Get authenticated user profile.

**Headers:** `Authorization: Bearer <token>`

**Response (200):**
```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "candidate"
    }
}
```

### PUT /auth/profile
Update user profile.

**Headers:** `Authorization: Bearer <token>`

**Request:**
```json
{
    "name": "John Updated",
    "phone": "+55 11 88888-8888"
}
```

### PUT /auth/password
Change password.

**Headers:** `Authorization: Bearer <token>`

**Request:**
```json
{
    "current_password": "oldpassword",
    "password": "newpassword",
    "password_confirmation": "newpassword"
}
```

---

## Job Endpoints

### GET /jobs
Get all active jobs (Public).

**Query Parameters:**
- `search` - Search by title or description
- `location` - Filter by location
- `remote` - Filter remote jobs (true/false)
- `type` - Filter by type (full-time, part-time, contract, internship)
- `salary_min` - Minimum salary
- `per_page` - Results per page (default: 15)
- `page` - Page number

**Response (200):**
```json
{
    "status": "success",
    "message": "Sucesso",
    "data": {
        "data": [
            {
                "id": 1,
                "title": "Senior Developer",
                "description": "Looking for experienced developer...",
                "salary_min": 8000,
                "salary_max": 15000,
                "location": "São Paulo",
                "remote": true,
                "type": "full-time",
                "company_name": "Tech Company",
                "user": {
                    "id": 2,
                    "name": "Maria Silva",
                    "company": "Tech Company"
                }
            }
        ],
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

### GET /jobs/{id}
Get job details (Public).

### POST /jobs
Create a new job (Authenticated, Recruiter).

**Headers:** `Authorization: Bearer <token>`

**Request:**
```json
{
    "title": "Senior Developer",
    "description": "Looking for experienced developer...",
    "requirements": "5+ years experience",
    "benefits": "Health insurance",
    "salary_min": 8000,
    "salary_max": 15000,
    "location": "São Paulo",
    "remote": true,
    "type": "full-time",
    "company_name": "Tech Company",
    "deadline": "2024-12-31"
}
```

### PUT /jobs/{id}
Update a job (Authenticated, Owner).

### DELETE /jobs/{id}
Delete a job (Authenticated, Owner).

### GET /jobs/my-jobs
Get jobs posted by authenticated user (Authenticated, Recruiter).

---

## Application Endpoints

### GET /applications
Get applications by authenticated user (Authenticated).

**Response (200):**
```json
{
    "status": "success",
    "data": {
        "data": [
            {
                "id": 1,
                "status": "pending",
                "cover_letter": "I am interested in...",
                "applied_at": "2024-01-15T10:30:00.000000Z",
                "job": {
                    "id": 1,
                    "title": "Senior Developer",
                    "company_name": "Tech Company",
                    "location": "São Paulo"
                }
            }
        ]
    }
}
```

### POST /applications
Apply for a job (Authenticated, Candidate).

**Request:**
```json
{
    "job_id": 1,
    "cover_letter": "I am very interested in this position...",
    "resume_path": "/path/to/resume.pdf"
}
```

### GET /applications/{id}
Get application details (Authenticated, Owner or Job Owner).

### PUT /applications/{id}/status
Update application status (Authenticated, Job Owner).

**Request:**
```json
{
    "status": "reviewed",
    "notes": "Candidate has good experience"
}
```

### DELETE /applications/{id}
Withdraw application (Authenticated, Owner).

### GET /jobs/{job_id}/applications
Get applications for a job (Authenticated, Job Owner).

---

## Recipe Endpoints

### GET /recipes
Get all published recipes (Public).

**Query Parameters:**
- `search` - Search by title or description
- `category` - Filter by category
- `difficulty` - Filter by difficulty (easy, medium, hard)
- `published` - Filter by published status (true/false)
- `my_recipes` - Get only my recipes (true/false, requires auth)
- `per_page` - Results per page (default: 15)

**Response (200):**
```json
{
    "status": "success",
    "message": "Sucesso",
    "data": {
        "data": [
            {
                "id": 1,
                "title": "Bolo de Chocolate Caseiro",
                "description": "Um delicioso bolo de chocolate...",
                "ingredients": ["2 xícaras de farinha", "..."],
                "instructions": ["Pré-aqueça o forno...", "..."],
                "prep_time": 15,
                "cook_time": 40,
                "servings": 8,
                "difficulty": "easy",
                "category": "Bolos",
                "is_published": true,
                "total_time": 55,
                "user": {
                    "id": 2,
                    "name": "Maria Silva"
                }
            }
        ],
        "current_page": 1,
        "last_page": 2,
        "per_page": 15,
        "total": 25
    }
}
```

### GET /recipes/{id}
Get recipe details with comments (Public).

### GET /recipes/category/{category}
Get recipes by category (Public).

### GET /recipes/difficulty/{difficulty}
Get recipes by difficulty (Public).

### POST /recipes
Create a new recipe (Authenticated).

**Request:**
```json
{
    "title": "Lasanha Bolonhesa",
    "description": "Lasanha tradicional italiana...",
    "ingredients": ["500g massa", "300g carne moída", "..."],
    "instructions": ["Cozinhe a massa", "..."],
    "prep_time": 30,
    "cook_time": 45,
    "servings": 6,
    "difficulty": "medium",
    "category": "Pratos Principais",
    "is_published": true
}
```

### PUT /recipes/{id}
Update a recipe (Authenticated, Owner).

### DELETE /recipes/{id}
Delete a recipe (Authenticated, Owner).

### GET /recipes/my-recipes
Get recipes by authenticated user (Authenticated).

---

## Comment Endpoints

### GET /recipes/{recipe_id}/comments
Get comments for a recipe (Public).

### POST /recipes/{recipe_id}/comments
Add a comment to a recipe (Authenticated).

**Request:**
```json
{
    "content": "Muito boa receita!",
    "rating": 5
}
```

### PUT /comments/{id}
Update a comment (Authenticated, Owner).

### DELETE /comments/{id}
Delete a comment (Authenticated, Owner or Admin).

---

## Health Check

### GET /health
Check API health status (Public).

**Response (200):**
```json
{
    "status": "healthy",
    "service": "HiringMachine API",
    "version": "1.0.0"
}
```

---

## Error Responses

### 422 - Validation Error
```json
{
    "status": "error",
    "message": "Erro de validação",
    "errors": {
        "email": ["O campo email é obrigatório."]
    }
}
```

### 401 - Unauthorized
```json
{
    "status": "error",
    "message": "Não autorizado."
}
```

### 403 - Forbidden
```json
{
    "status": "error",
    "message": "Você não tem permissão para acessar este recurso."
}
```

### 404 - Not Found
```json
{
    "status": "error",
    "message": "Recurso não encontrado."
}
```

---

## Test Users

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@hiringmachine.com | password |
| Recruiter | maria@techcompany.com | password |
| Candidate | pedro@email.com | password |

---

## Docker Services

| Service | Port |
|---------|------|
| Laravel App | http://localhost:5757 |
| PostgreSQL | localhost:5858 |
| Mailpit SMTP | localhost:5959 |
| Mailpit UI | http://localhost:8025 |

---

## Database Configuration

| Parameter | Value |
|-----------|-------|
| Host | localhost |
| Port | 5858 |
| Database | hiringmachine |
| Username | hiringmachine |
| Password | secret |
