# AI-Powered Notes Management System (PHP Backend + AI)

> A modern, full-stack RESTful Notes Management System built with **Laravel 10 (PHP 8.2)**, featuring **AI-Powered Semantic Vector Search**, **AI Note Summarization**, **Rate Limiting**, **Docker**, **OpenAPI 3.0 Documentation**, **PHPUnit Test Suite**, and a high-aesthetic **Glassmorphism SPA Frontend**.

---

## 🌟 Key Features

1. **Notes CRUD APIs**:
   - `POST /api/notes`: Create note and automatically generate high-dimensional vector embeddings.
   - `GET /api/notes`: Paginated notes listing (`?page=1&limit=10`).
   - `GET /api/notes/{id}`: Fetch single note details.
   - `PUT /api/notes/{id}`: Update note content and recalculate vector embeddings.
   - `DELETE /api/notes/{id}`: Remove note.
2. **AI Semantic Vector Search**:
   - `GET /api/notes/search?query=...&limit=10`: Performs natural language conceptual vector search using **Cosine Similarity** between query embeddings and stored note vectors.
   - Supports live OpenAI embeddings (`text-embedding-3-small`) or standalone offline normalized vector generation out-of-the-box.
3. **AI Note Summarization**:
   - `POST /api/notes/{id}/summary`: Generates concise AI summaries using `gpt-4o-mini` with automatic database caching to prevent unnecessary re-computations.
4. **Interactive Glassmorphism SPA Frontend**:
   - Responsive dark-mode interface built with Tailwind CSS, Alpine.js logic, FontAwesome icons, real-time match percentage badges, AI summary modal, and modal note editors.
5. **Security & Production Readiness**:
   - 100% SQL injection prevention via Eloquent ORM & prepared statements.
   - Form Request Validation (`StoreNoteRequest`, `UpdateNoteRequest`).
   - API Rate Limiting (`throttle:60,1` middleware).
   - Docker containerization (`docker-compose.yml` with Nginx, MySQL 8, and Redis).

---

## 🏗️ Architecture & Technology Stack

```
 ┌────────────────────────────────────────────────────────┐
 │            Glassmorphism SPA UI (HTML/JS)              │
 └───────────────────────────┬────────────────────────────┘
                             │ REST API Requests
 ┌───────────────────────────▼────────────────────────────┐
 │                  Laravel 10 API Routes                 │
 │            (Throttle Rate Limiter Middleware)          │
 └──────┬────────────────────┬────────────────────┬───────┘
        │                    │                    │
 ┌──────▼──────┐      ┌──────▼──────┐      ┌──────▼──────┐
 │ NoteController│    │SearchController│   │SummaryController│
 └──────┬──────┘      └──────┬──────┘      └──────┬──────┘
        │                    │                    │
 ┌──────▼────────────────────▼────────────────────▼───────┐
 │               VectorSearchService & AiService          │
 └───────────────────────────┬────────────────────────────┘
                             │
            ┌────────────────┴────────────────┐
            │                                 │
   ┌────────▼────────┐               ┌────────▼────────┐
   │ OpenAiProvider  │               │ FallbackAiProvider│
   │ (OpenAI API)    │               │ (Offline Vector Engine)│
   └─────────────────┘               └─────────────────┘
```

- **Backend**: Laravel 10 / PHP 8.2
- **Database**: SQLite (Zero-config local) / MySQL 8.0 (Docker/XAMPP)
- **Cache**: Redis / File cache
- **DevOps**: Docker Compose + Nginx + PHP 8.2 FPM

---

## 🗄️ Database Schema (`notes` table)

| Column Name  | Type           | Description |
| :---         | :---           | :--- |
| `id`         | BIGINT (PK)    | Primary Key |
| `title`      | VARCHAR(255)   | Note title (indexed) |
| `content`    | TEXT           | Full note body |
| `tags`       | JSON           | Array of string tags (e.g. `["laravel", "ai"]`) |
| `embedding`  | JSON           | Float vector representation `[0.012, -0.045, ...]` |
| `summary`    | TEXT           | Cached AI summary |
| `created_at` | TIMESTAMP      | Creation timestamp |
| `updated_at` | TIMESTAMP      | Last modification timestamp |

---

## 🚀 Quick Setup & Local Run Instructions

### Prerequisites
- PHP 8.2+
- Composer

### 1. Local Server Run (Recommended)
```bash
# 1. Clone repository
git clone https://github.com/your-username/php-ai-notes-system.git
cd php-ai-notes-system

# 2. Copy environment configuration
cp .env.example .env

# 3. Run database migrations & seed sample data
php artisan migrate:fresh --seed

# 4. Start local development server
php artisan serve
```
Open **http://127.0.0.1:8000** in your browser to view the interactive UI and test APIs!

---

### 2. Docker Setup (Optional / Bonus)
```bash
# Build and launch application with MySQL 8 & Redis
docker-compose up -d --build
```
Access the application at **http://localhost:8000**.

---

## 📖 API Documentation & Example Requests

### 1. List Paginated Notes
```bash
curl -X GET "http://127.0.0.1:8000/api/notes?page=1&limit=5" \
  -H "Accept: application/json"
```

### 2. Create Note
```bash
curl -X POST "http://127.0.0.1:8000/api/notes" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "title": "Machine Learning Vector Embeddings",
    "content": "Vector embeddings measure semantic closeness in high dimensional vector space using Cosine Similarity.",
    "tags": ["ai", "vectors", "machine-learning"]
  }'
```

### 3. AI Semantic Vector Search
```bash
curl -X GET "http://127.0.0.1:8000/api/notes/search?query=neural+networks&limit=5" \
  -H "Accept: application/json"
```

### 4. Generate AI Note Summary
```bash
curl -X POST "http://127.0.0.1:8000/api/notes/1/summary" \
  -H "Accept: application/json"
```

---

## 🧪 Automated Testing

Run the full PHPUnit feature and unit test suite:
```bash
php artisan test
```

Result:
```text
  PASS  Tests\Unit\ExampleTest
  PASS  Tests\Feature\ExampleTest
  PASS  Tests\Feature\NoteApiTest
  ✓ it can fetch paginated notes list
  ✓ it can create a note and generate embedding
  ✓ it validates required fields when creating note
  ✓ it can fetch a single note
  ✓ it returns 404 for non existent note
  ✓ it can update a note
  ✓ it can delete a note
  ✓ it can generate an ai summary for a note
  ✓ it can perform semantic vector search

  Tests:    11 passed (73 assertions)
```

---

## 🤖 AI Usage & Verification Summary

For a detailed breakdown of prompts, code generation validation, and security auditing, please refer to [`AI_USAGE_EXPLANATION.md`](file:///d:/PHP/AI_USAGE_EXPLANATION.md).
