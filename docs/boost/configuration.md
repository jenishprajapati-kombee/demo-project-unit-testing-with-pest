# Laravel Boost — Configuration

## boost.json (project root)

Controls how Laravel Boost is used in this project.

| Key       | Type     | Description |
|-----------|----------|-------------|
| `agents` | string[] | Agents that use Boost (e.g. `["cursor"]`). |
| `mcp`    | boolean  | Enable the MCP server so AI/IDE can call Boost tools. |
| `herd_mcp` | boolean | Use with Laravel Herd MCP (macOS). |
| `sail`   | boolean  | Use with Laravel Sail (Docker). |

**Example (this project):**

```json
{
  "agents": ["cursor"],
  "herd_mcp": false,
  "mcp": true,
  "sail": false
}
```

## Laravel config (config/boost.php)

If you publish Boost config:

```bash
php artisan vendor:publish --tag=boost-config
```

Common options (dot notation):

| Key | Type | Description |
|-----|------|-------------|
| `boost.enabled` | bool | Master switch for Boost. |
| `boost.browser_logs_watcher` | bool | Capture browser (frontend) logs via `/_boost/browser-logs`. |
| `boost.executable_paths.php` | string | Path to `php` binary. |
| `boost.executable_paths.composer` | string | Path to `composer`. |
| `boost.executable_paths.npm` | string | Path to `npm`. |
| `boost.executable_paths.vendor_bin` | string | Path to `vendor/bin`. |

## Integration with other packages

- **Debugbar** — `config/debugbar.php` excludes `_boost/browser-logs` from certain handling so Boost can receive browser logs.
- **Telescope** — `config/telescope.php` ignores paths matching `_boost*` so Boost endpoints are not recorded in Telescope.

## Environment

Boost is a **development** tool. Keep it in `require-dev` and do not enable the MCP server or browser log ingestion in production.
