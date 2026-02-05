<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HttpResponseHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Generate CSP Nonce (PER REQUEST — CSP spec compliant)
        |--------------------------------------------------------------------------
        | - MUST be unique per response
        | - Never persisted in session
        | - Prevents XSS replay attacks
        */
        $nonce = base64_encode(random_bytes(16));

        /*
        |--------------------------------------------------------------------------
        | 2. Environment Strategy
        |--------------------------------------------------------------------------
        | Local:
        | - CSP disabled to avoid Vite HMR / Livewire dev friction
        |
        | Non-Local (UAT / PROD):
        | - CSP enabled
        | - Nonce injected
        */
        $isLocal = App::environment('local');
        $shouldApplyCsp = ! $isLocal;

        // Share nonce with Blade views
        View::share('nonce', $shouldApplyCsp ? $nonce : null);

        // Make nonce available via request attributes if needed
        if ($shouldApplyCsp) {
            $request->attributes->set('csp_nonce', $nonce);
        }

        /** @var Response $response */
        $response = $next($request);

        /*
        |--------------------------------------------------------------------------
        | 3. Security Headers (Non-Local Only)
        |--------------------------------------------------------------------------
        */
        if ($shouldApplyCsp) {
            // Remove information disclosure
            $response->headers->remove('X-Powered-By');

            // Clickjacking protection
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN', true);

            // MIME sniffing protection
            $response->headers->set('X-Content-Type-Options', 'nosniff', true);

            // Referrer policy
            $response->headers->set(
                'Referrer-Policy',
                'strict-origin-when-cross-origin',
                true
            );

            // Permissions policy
            $response->headers->set(
                'Permissions-Policy',
                'geolocation=(self)',
                true
            );

            /*
            |--------------------------------------------------------------------------
            | HSTS (HTTPS Only)
            |--------------------------------------------------------------------------
            */
            $appUrlScheme = parse_url(config('app.url'), PHP_URL_SCHEME);

            if (
                App::isProduction()
                || $request->isSecure()
                || $appUrlScheme === 'https'
            ) {
                $response->headers->set(
                    'Strict-Transport-Security',
                    'max-age=31536000; includeSubDomains',
                    true
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 4. Content Security Policy (Livewire + Alpine + Flux Safe)
            |--------------------------------------------------------------------------
            |
            | REQUIRED:
            | - 'unsafe-eval' → Alpine.js runtime requirement
            | - nonce         → Inline boot scripts (Livewire, Flux)
            |
            | IMPORTANT:
            | - NO 'unsafe-inline' → keeps nonce effective
            | - Inline scripts must be guarded to avoid re-execution
            |
            */
            $csp = implode('; ', [
                "default-src 'self'",

                "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval' 'strict-dynamic' "
                    . 'https://code.jquery.com '
                    . 'https://unpkg.com '
                    . 'https://www.google.com '
                    . 'https://www.gstatic.com',

                "style-src 'self' 'unsafe-inline' "
                    . 'https://fonts.bunny.net '
                    . 'https://unpkg.com',

                "font-src 'self' data: https://fonts.bunny.net",

                "img-src 'self' data: https://fluxui.dev https://www.google.com https://www.gstatic.com",

                "connect-src 'self' https://www.google.com https://www.gstatic.com",

                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'none'",
                "frame-src 'self' https://www.google.com",
            ]);

            $response->headers->set('Content-Security-Policy', $csp, true);

            /*
            |--------------------------------------------------------------------------
            | 5. Auto-Inject Nonce into All Script Tags
            |--------------------------------------------------------------------------
            |
            | Automatically adds nonce attribute to all <script> tags in the response
            | without requiring manual addition in Blade templates.
            |
            */
            $this->injectNonceIntoScripts($response, $nonce);

            /*
            |--------------------------------------------------------------------------
            | 6. Auto-Convert Inline Event Handlers to Alpine.js
            |--------------------------------------------------------------------------
            |
            | Automatically converts inline event handlers (onclick, onchange, etc.)
            | to Alpine.js syntax to comply with CSP without 'unsafe-inline'.
            |
            | Example:
            | <button onclick="save()"> → <button @click="save()">
            | <input onchange="update()"> → <input @change="update()">
            |
            */
            $this->convertInlineEventHandlers($response);
        }

        return $response;
    }

    /**
     * Automatically inject nonce attribute into all script tags in the response.
     */
    private function injectNonceIntoScripts(Response $response, string $nonce): void
    {
        // Only process HTML responses
        $contentType = $response->headers->get('Content-Type', '');
        if (! str_contains($contentType, 'text/html')) {
            return;
        }

        // Get response content
        $content = $response->getContent();

        if ($content === false || $content === '') {
            return;
        }

        $escapedNonce = htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8');

        /*
        |--------------------------------------------------------------------------
        | Strategy: Two-step process
        |--------------------------------------------------------------------------
        | 1. First, remove any existing nonce attributes (to avoid duplicates)
        | 2. Then, add nonce to all script tags that don't have it
        |
        */
        // Step 1: Remove existing nonce attributes
        $content = preg_replace(
            '/\s+nonce="[^"]*"/i',
            '',
            $content
        );

        // Step 2: Add nonce to all script tags (including self-closing)
        // Pattern matches: <script followed by optional attributes, then > or />
        $pattern = '/(<script)(\s+[^>]*)?(\s*\/?>)/i';

        // Use callback to handle both cases: with and without existing attributes
        $modifiedContent = preg_replace_callback($pattern, function ($matches) use ($escapedNonce) {
            $tagStart = $matches[1]; // <script
            $attributes = $matches[2] ?? ''; // existing attributes (if any)
            $tagEnd = $matches[3]; // > or />

            // Skip if nonce already exists (shouldn't happen after step 1, but safety check)
            if (stripos($attributes, 'nonce=') !== false) {
                return $tagStart . $attributes . $tagEnd;
            }

            // If attributes exist, add nonce after them; otherwise add nonce with space
            if ($attributes) {
                return $tagStart . $attributes . ' nonce="' . $escapedNonce . '"' . $tagEnd;
            }

            return $tagStart . ' nonce="' . $escapedNonce . '"' . $tagEnd;
        }, $content);

        // Only update if modification was successful
        if ($modifiedContent !== null) {
            $response->setContent($modifiedContent);
        }
    }

    /**
     * Automatically convert inline event handlers to Alpine.js syntax.
     *
     * Converts inline event handlers (onclick, onchange, etc.) to Alpine.js directives
     * to comply with CSP without requiring 'unsafe-inline'.
     */
    private function convertInlineEventHandlers(Response $response): void
    {
        // Only process HTML responses
        $contentType = $response->headers->get('Content-Type', '');
        if (! str_contains($contentType, 'text/html')) {
            return;
        }

        // Get response content
        $content = $response->getContent();

        if ($content === false || $content === '') {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Mapping: Inline Event Handlers → Alpine.js Directives
        |--------------------------------------------------------------------------
        */
        $eventHandlerMap = [
            'onclick' => '@click',
            'ondblclick' => '@dblclick',
            'onchange' => '@change',
            'oninput' => '@input',
            'onsubmit' => '@submit',
            'onfocus' => '@focus',
            'onblur' => '@blur',
            'onmouseover' => '@mouseover',
            'onmouseout' => '@mouseout',
            'onmouseenter' => '@mouseenter',
            'onmouseleave' => '@mouseleave',
            'onmousedown' => '@mousedown',
            'onmouseup' => '@mouseup',
            'onkeydown' => '@keydown',
            'onkeyup' => '@keyup',
            'onkeypress' => '@keypress',
            'onload' => '@load',
            'onerror' => '@error',
            'onresize' => '@resize',
            'onscroll' => '@scroll',
        ];

        /*
        |--------------------------------------------------------------------------
        | Strategy: Convert each inline event handler
        |--------------------------------------------------------------------------
        | Pattern matches: onEventName="..." or onEventName='...'
        | Replaces with: @eventName="..." (Alpine.js syntax)
        |
        | IMPORTANT: Only convert in HTML attributes, NOT in JavaScript code
        | inside <script> tags. We exclude script tag content.
        |
        */
        // First, split content into parts: HTML and script tag contents
        $parts = preg_split('/(<script[^>]*>.*?<\/script>)/is', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        $processedParts = [];
        foreach ($parts as $index => $part) {
            // Skip script tag contents (odd indices after split with delimiter capture)
            if ($index % 2 === 1) {
                // This is a script tag - keep it as-is
                $processedParts[] = $part;

                continue;
            }

            // This is HTML content - process event handlers
            $htmlContent = $part;
            foreach ($eventHandlerMap as $inlineHandler => $alpineDirective) {
                // Pattern: onEventName="..." or onEventName='...'
                // Only match in HTML attributes (not in script content)
                // Matches: whitespace + onEventName= + quote + content + matching quote
                $pattern = '/\s+' . preg_quote($inlineHandler, '/') . '=(["\'])(.*?)\1/i';

                $htmlContent = preg_replace_callback($pattern, function ($matches) use ($alpineDirective) {
                    $handlerValue = $matches[2]; // The value inside quotes

                    /*
                    |--------------------------------------------------------------------------
                    | Special Handling: Convert 'value' variable to Alpine.js $el.value
                    |--------------------------------------------------------------------------
                    | In inline event handlers, 'value' refers to the element's value property.
                    | In Alpine.js, we need to use $el.value or $event.target.value.
                    |
                    | Pattern: value=value.trim() → $el.value = $el.value.trim()
                    | Pattern: value.trim() → $el.value.trim()
                    |
                    */
                    // Detect if handler uses 'value' as a variable (not as a string)
                    // Match patterns like: value=, value., value.trim(), value =, etc.
                    if (preg_match('/\bvalue\s*[=.]|\bvalue\s*\(/', $handlerValue)) {
                        // Replace standalone 'value' with '$el.value'
                        // Use word boundaries to match 'value' as a whole word
                        // This handles: value=, value., value(, value =, etc.
                        $convertedValue = preg_replace(
                            '/\bvalue\b/',
                            '$el.value',
                            $handlerValue
                        );
                    } else {
                        $convertedValue = $handlerValue;
                    }

                    // Escape for use in double-quoted HTML attribute
                    $escapedValue = htmlspecialchars($convertedValue, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                    return ' ' . $alpineDirective . '="' . $escapedValue . '"';
                }, $htmlContent);
            }

            $processedParts[] = $htmlContent;
        }

        $content = implode('', $processedParts);

        // Update response content
        if ($content !== null) {
            $response->setContent($content);
        }
    }
}
