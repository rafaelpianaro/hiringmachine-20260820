# HiringMachine API Documentation

## Base URL
```
http://localhost:5757/api/v1
```

## Authentication
All protected routes require a JWT token in the Authorization header:
```
Authorization: ******
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
    "access_token": "******",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "role": "user"
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
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "status": "success",
  "message": "Sucesso",
  "data": {
    "access_token": "******",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 6,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user"
    }
  }
}
```

### POST /auth/logout
Invalidate the current API token.

**Headers:** `Authorization: ******`

**Response (200):**
```json
{
  "status": "success",
  "message": "Logout realizado com sucesso."
}
```

### POST /auth/refresh
Renew the JWT token.

**Headers:** `Authorization: ******`

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "access_token": "******",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user"
    }
  }
}
```

### GET /auth/me
Get the authenticated user profile.

**Headers:** `Authorization: ******`

**Response (200):**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user"
  }
}
```

### PUT /auth/profile
Update the authenticated user profile.

**Headers:** `Authorization: ******`

**Request:**
```json
{
  "name": "John Updated"
}
```

### PUT /auth/password
Change the current password.

**Headers:** `Authorization: ******`

**Request:**
```json
{
  "current_password": "oldpassword",
  "password": "newpassword",
  "password_confirmation": "newpassword"
}
```

---

## Recipe Endpoints

### GET /recipes
List recipes (public).

**Query Parameters:**
- `category` - filter by category
- `difficulty` - filter by difficulty (`easy`, `medium`, `hard`)
- `search` - free text search on title and description

**Response (200):**
```json
{
  "status": "success",
  "message": "Sucesso",
  "data": [
    {
      "id": 13,
      "title": "Bolo de chocolate",
      "description": "Receita simples e irresistível",
      "category": "Doces",
      "difficulty": "easy",
      "prep_time": 20,
      "cook_time": 35,
      "servings": 8,
      "ingredients": ["farinha", "chocolate", "ovo"],
      "instructions": ["Misture os ingredientes", "Asse por 35 min"],
      "average_rating": 4.5,
      "ratings_count": 4,
      "user": {
        "id": 2,
        "name": "Maria"
      },
      "ratings": [
        { "user_id": 3, "stars": 5 },
        { "user_id": 4, "stars": 4 }
      ]
    }
  ]
}
```

### GET /recipes/{id}
Fetch a recipe detail (public).

### GET /recipes/category/{category}
List recipes filtered by category.

### GET /recipes/difficulty/{difficulty}
List recipes filtered by difficulty.

### POST /recipes
Create a recipe (authenticated user).

**Headers:** `Authorization: ******`

**Request:**
```json
{
  "title": "Risoto de cogumelo",
  "description": "Receita cremosa e aromática",
  "category": "Pratos principais",
  "difficulty": "medium",
  "prep_time": 15,
  "cook_time": 30,
  "servings": 4,
  "ingredients": ["arroz", "cogumelos", "caldo"],
  "instructions": ["Refogue os cogumelos", "Adicione o arroz e o caldo"]
}
```

### PUT /recipes/{id}
Update a recipe (author only).

### DELETE /recipes/{id}
Delete a recipe (author only).

### GET /recipes/my-recipes
List recipes created by the authenticated user.

### POST /recipes/{id}/ratings
Rate a recipe (authenticated user).

**Headers:** `Authorization: ******`

**Request:**
```json
{
  "stars": 5
}
```

**Response (200):**
```json
{
  "status": "success",
  "message": "Avaliação registrada com sucesso.",
  "data": {
    "recipe_id": 13,
    "average_rating": 4.5,
    "ratings_count": 5,
    "user_rating": 5
  }
}
```

---

## Comment Endpoints

### GET /recipes/{recipe}/comments
List comments for a recipe.

### POST /recipes/{recipe}/comments
Create a comment for a recipe.

**Headers:** `Authorization: ******`

**Request:**
```json
{
  "content": "Receita deliciosa!",
  "rating": 5
}
```

### PUT /comments/{comment}
Update a comment (owner only).

### DELETE /comments/{comment}
Delete a comment (owner only).

---

## Health Check

### GET /health
Check if the API is available.

**Response (200):**
```json
{
  "status": "healthy",
  "service": "HiringMachine API",
  "version": "1.0.0"
}
```

---

## Notes
- The API uses JWT authentication.
- The public prefix is `/api/v1`.
- Roles are limited to `admin` and `user`.
- Recipe ratings are unique per user and recipe: `(recipe_id, user_id)`.
