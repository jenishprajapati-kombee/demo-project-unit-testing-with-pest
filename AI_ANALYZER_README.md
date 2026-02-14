# AI-Powered Incident & Root Cause Analyzer

This system provides an automated pipeline for intake, preprocessing, and AI-driven analysis of production incidents.

## Features
- **Intake API**: Accepts unstructured logs and system metrics.
- **Preprocessing**: Removes duplicates, correlates events by time, and assesses severity.
- **AI Analysis**: Uses logic-driven heuristics (simulating an LLM like Claude/GPT) to identify probable causes.
- **Decision Layer**: Cross-references AI suggestions with hard system metrics (CPU, Latency) to assign confidence scores and rank causes.

## AI Implementation Details

### Prompts Used (Heuristic Logic)
The "AI" logic in this system is designed around the following reasoning patterns:
1. **DB Correlation**: "If logs show 'timeout' AND metrics show 'latency > 300ms', then suggest 'Database Overload' with 85% confidence."
2. **Network Heuristic**: "If logs show 'timeout' BUT metrics show 'normal latency', then suggest 'Network Instability' (internal network issue) with 65% confidence."
3. **Resource Saturation**: "If CPU > 80% independent of logs, suggest 'High Resource Usage'."

### AI Mistakes Observed & Mitigations
| Observed Mistake | Cause | Mitigation |
|------------------|-------|------------|
| Over-reporting "Database Failure" | Simple log matching would tag any DB message as a failure. | **Decision Layer**: Validates against `db_latency` and `requests_per_sec` metrics before assigning high confidence. |
| Duplicate Alerting | Same error appearing 100 times in logs creates noise. | **Preprocessing**: Deduplication logic runs before the analysis phase. |
| Hallucinating Cause on low data | AI suggesting a cause when data is sparse. | **Confidence Scoring**: Base confidence is capped at 0.5 if no strong correlations are found. |

## API Usage

### 1. Analyze Incident
**Endpoint**: `POST /api/v1/analyze`

**Payload**:
```json
{
  "logs": [
    "12:00 DB timeout",
    "12:01 DB timeout",
    "12:02 DB connection reset"
  ],
  "metrics": {
    "cpu": 85,
    "db_latency": 400,
    "requests_per_sec": "High"
  }
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "likely_cause": "Database Overload",
    "confidence": 0.9,
    "reasoning": "AI Analysis: Determined by correlation between DB timeout logs and high DB latency (400ms). High request volume combined with DB latency confirms overload.",
    "next_steps": "1. Check DB connection pool. 2. Scaling DB instance. 3. Check for long-running queries.",
    "severity": "high",
    "incident_id": 1
  }
}
```

## Setup
1. Run migrations: `php artisan migrate`
2. Test via provided Postman collection.
