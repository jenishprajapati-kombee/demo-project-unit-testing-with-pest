# Laravel Boost — MCP Tools Reference

These are the tools exposed by the Laravel Boost MCP server. An AI assistant or IDE that connects to Boost can call them to inspect and interact with your Laravel app.

## Application

| Tool / Purpose | Description |
|----------------|-------------|
| **Application info** | PHP version, Laravel version, DB engine, installed packages (with versions), all Eloquent models. Use on each new chat for version-aware help. |

## Artisan & Commands

| Tool / Purpose | Description |
|----------------|-------------|
| **List Artisan commands** | All registered Artisan commands (app + vendor). |

## Configuration

| Tool / Purpose | Description |
|----------------|-------------|
| **List config keys** | All config keys from `config/*.php` in dot notation. |
| **Get config** | Value of a single key, e.g. `app.name`, `database.default`. |

## Routes

| Tool / Purpose | Description |
|----------------|-------------|
| **List routes** | All registered routes (including Folio). Optional filters: method, action, name, domain, path. |

## Database

| Tool / Purpose | Description |
|----------------|-------------|
| **List database connections** | Configured connection names and the default connection. |
| **Database schema** | Table names, columns (type only or full metadata), indexes, foreign keys. Optional: filter by table name, include views/routines. |
| **Execute read-only SQL** | Run `SELECT` (and similar read-only) queries against the configured DB. |

## Logs & Errors

| Tool / Purpose | Description |
|----------------|-------------|
| **Last error** | Last exception/error from the Laravel app (backend). |
| **Application log** | Last N entries from the app log (multi-line PSR-3 aware). |
| **Browser log** | Last N entries from the browser (frontend) log. Requires browser log watcher and client sending logs to `/_boost/browser-logs`. |

## URLs

| Tool / Purpose | Description |
|----------------|-------------|
| **Get URL** | Resolve a relative path or named route to an absolute URL. |

## Documentation

| Tool / Purpose | Description |
|----------------|-------------|
| **Search docs** | Version-specific Laravel ecosystem docs (Laravel, Inertia, Pest, Livewire, Filament, Nova, Tailwind, etc.). |

## Execution

| Tool / Purpose | Description |
|----------------|-------------|
| **Tinker** | Execute PHP in the Laravel context (like `php artisan tinker`). |

---

## Usage from the AI/IDE

You don’t call these tools by name in your own code. Instead:

- In Cursor (or another MCP client), ensure the Laravel Boost MCP server is configured and running (e.g. via `boost.json` with `"mcp": true`).
- Then ask in natural language, e.g.:
  - “List Artisan commands”
  - “Boost last error”
  - “List routes”
  - “Get config app.name”
  - “List database connections”
  - “Get browser logs”
  - “Show application info”

The assistant uses the Boost MCP tools to answer. See [Quick Reference](quick-reference.md) for a short list of common requests.
