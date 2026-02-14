# Laravel Boost — Quick Reference

Common requests you can ask an AI assistant (with Boost MCP connected) to get instant Laravel context.

## Commands

| You ask | What Boost does |
|--------|------------------|
| **boost list-artisan-commands** / List Artisan commands | Returns all registered Artisan commands. |
| **boost last-error** | Returns the last backend exception/error from the app. |
| **boost list-available-config-keys** / List config keys | Returns all config keys (dot notation). |
| **boost get-config** &lt;key&gt; | Returns value for e.g. `app.name`, `database.default`. |
| **boost list-routes** | Returns all registered routes. |
| **boost database-connections** | Returns default connection and all connection names. |
| **Get browser logs using boost** | Returns last N browser (frontend) log entries. |

## Other useful requests

- **Application info** — PHP/Laravel versions, packages, Eloquent models.
- **Database schema** — Tables, columns, indexes (with optional table filter).
- **Read app log** — Last N application log entries.
- **Resolve URL** — Get absolute URL for a path or named route.
- **Search Laravel docs** — Version-specific docs for Laravel and ecosystem packages.
- **Tinker** — Run a PHP snippet in the Laravel context.

## Prerequisites

1. Laravel Boost installed (`laravel/boost` in `require-dev`).
2. `boost.json` in project root with `"mcp": true` (and e.g. `"agents": ["cursor"]`).
3. Boost MCP server running (usually via Cursor MCP config when opening this project).
4. For **browser logs**: app running, browser open, and frontend sending logs to `/_boost/browser-logs` (browser log watcher enabled in config).

## Files

| File | Purpose |
|------|---------|
| `boost.json` | Project-level Boost/MCP and agent options. |
| `config/boost.php` | Laravel config (if published): enabled, paths, browser logs. |
| `docs/boost/` | This documentation. |
