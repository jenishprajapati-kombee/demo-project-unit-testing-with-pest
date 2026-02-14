# Laravel Boost

[Laravel Boost](https://github.com/laravel/boost) integrates your Laravel application with **MCP (Model Context Protocol)** so that AI assistants (e.g. Cursor) can inspect and interact with your app—routes, config, database, logs, Artisan commands, and more—without leaving the editor.

## What Boost Provides

- **Application context** — PHP/Laravel version, packages, Eloquent models
- **Artisan commands** — List and discover available commands
- **Configuration** — List config keys and read values (dot notation)
- **Routes** — List all registered routes (web, API, Folio)
- **Database** — Connection names, schema (tables/columns), read-only SQL
- **Logs** — Last app error and recent log entries; browser (frontend) logs
- **Docs search** — Version-specific Laravel ecosystem documentation
- **Tinker** — Run PHP in the Laravel context
- **URLs** — Resolve relative paths and named routes to absolute URLs

## Installation

Boost is already installed as a dev dependency:

```json
"require-dev": {
    "laravel/boost": "^2.1",
    ...
}
```

## Running Boost (MCP)

Boost runs as an MCP server so your IDE/AI can call its tools.

1. **Via Cursor / IDE**  
   With `boost.json` in the project root and `"mcp": true`, the MCP server is typically started automatically when the Boost MCP is configured in Cursor.

2. **Artisan**  
   Start the MCP server manually:
   ```bash
   php artisan boost:mcp
   ```

3. **boost.json**  
   This project uses:
   - `mcp: true` — Enable MCP server
   - `agents: ["cursor"]` — Use with Cursor

## Project Setup Summary

| Item        | Location / Value |
|------------|-------------------|
| Config     | Published to `config/boost.php` (if published) |
| Root config| `boost.json` in project root |
| Default    | Boost enabled; browser logs watcher enabled |

## Next Steps

- [Configuration](configuration.md) — `boost.json` and config options
- [MCP Tools Reference](mcp-tools.md) — All Boost MCP tools and parameters
- [Quick Reference](quick-reference.md) — Common “boost …” style requests
