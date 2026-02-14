# 🚀 AI-Driven Failure Analysis System

An intelligent Laravel diagnostic system that monitors application failures, parses log files, and uses real-world AI (Gemini/OpenAI) or local expert heuristics to provide instant root-cause suggestions and solutions.

---

## 🛠 Features

### 1. **Automated Monitoring**
- **Continuous Tracking**: Integrated via `FailureMonitorMiddleware` to catch any request resulting in a 400+ status code.
- **Error Detection**: Real-time detection of any `Log::error`, `critical`, or `emergency` level messages during the request lifecycle.
- **Database Persistence**: Every incident is automatically logged into the `incidents` table for historical audit.

### 2. **Multi-Layer Analysis Logic**
- **AI Intelligence Layer**:
    - **Google Gemini**: Support for `gemini-1.5-flash`, `gemini-pro`, etc.
    - **OpenAI**: Support for `gpt-3.5-turbo` and above.
    - **Smart Fallback**: If one model fails (e.g., 404 or quota), the system automatically rotates through other models.
- **Zero-Config Heuristic Engine**: 
    - A built-in expert system that works **for free** without any API keys.
    - Analyzes patterns for Database (PDO/SQLSTATE), Memory (OOM), Syntax, and Authentication errors.

### 3. **Diagnostics API**
- **Endpoint**: `GET /api/v1/diagnostics/failures`
- **Function**: Scans the last 500 lines of `laravel.log`, extracts unique errors, and consults the AI for a step-by-step resolution.

---

## 🚀 Setup & Configuration

### 1. Environment Variables
Add your API keys to `.env`:

```env
# AI Configuration
AI_API_KEY=your_gemini_or_openai_key
AI_PROVIDER=gemini  # options: gemini, openai

# Debug Mode (Adds analysis headers to responses)
APP_DEBUG=true
```

### 2. Installation
```powershell
# Run migrations to create incidents table
php artisan migrate

# Clear config to ensure fresh services
php artisan config:clear
```

### 3. Middleware Registration
Ensure `FailureMonitorMiddleware` is registered in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\App\Http\Middleware\FailureMonitorMiddleware::class);
})
```

---

## 📖 AI Implementation Details

### Prompts Strategy
The system uses a highly structured system prompt for the AI to ensure consistent JSON outputs:
> *"As a professional Laravel Engineer, analyze this error: [ErrorMessage]. Provide a JSON object with: likely_cause, confidence, next_steps (array of strings), and reasoning."*

### Observed Challenges & Mitigations

| Challenge | Mitigation |
|-----------|------------|
| **Gemini 404 Errors** | Implemented a **Multi-Model Fallback** grid that tries 6 different model/version combinations. |
| **OpenAI Quota Limits** | Added immediate **Heuristic Fallback** so the system returns a solution even when the AI API is blocked. |
| **Windows Log Performance** | Optimized `FailureAnalysisService` to slice only the last 500 lines using memory-efficient PHP methods instead of slow shell commands. |
| **Sensitive Data Leakage** | Middleware automatically masks `password`, `token`, and `secret` fields from the request snapshot before storage. |

---

## 🛠 Testing the System

### Automated Test
I've included a standalone test script to verify the AI connection:
```powershell
php test_ai.php
```

### Manual Trigger (Heuristic Test)
Run this command to simulate an error and check the log:
```powershell
php artisan tinker --execute="Log::error('SQLSTATE[42S02]: Base table not found')"
```
Then hit the API: `GET /api/v1/diagnostics/failures`

---

## 📈 Database Schema (Incidents)
| Column | Description |
|--------|-------------|
| `severity` | error, warning, critical |
| `likely_cause` | Human-readable cause |
| `confidence` | AI confidence score (0-1) |
| `reasoning` | Technical depth provided by AI/Heuristic |
| `next_steps` | JSON array of actionable fixes |
| `raw_logs` | Snapshot of the log block causing the trigger |
| `request_data` | Endpoint, Method, and masked inputs |

---
*Created by Antigravity AI @ 2026*
