# Implementation Plan: Built-in Error Handler (v0.1)

This document describes the scope and deliverables for the initial MVP implementation of a built-in Error Handler for Wepesi. It mirrors the PR checklist and provides reviewers with the design rationale and testing guidance.

## Overview

Provide a minimal, production-safe built-in Error Handler for Wepesi (v0.1) that:
- Captures PHP exceptions and errors automatically when registered.
- Renders a developer-friendly HTML error page in dev.
- Persists structured error events to a local file transport in production.

## Goals (v0.1)
- Register global PHP error/exception/shutdown handlers via ErrorHandler::register($config).
- Convert PHP errors to ErrorException and capture them.
- Provide DevRenderer to show an HTML error page with stack and source snippet when environment=dev.
- Implement FileTransport to write JSONL events to storage/logs/errors/ (daily rotated filename).
- Provide ErrorMiddleware to catch throwables in the HTTP pipeline and render responses.
- Offer a small API: ErrorHandler::register(), ErrorHandler::captureException(), ErrorHandler::captureMessage(), ErrorHandler::setUser().

## Non-goals (v0.1)
- Remote HTTP transport, retry queue, SQLite queue, advanced sampling/rate-limiting, breadcrumbs — to be added in later iterations.

## Files added / modified in this PR
- src/ErrorHandler.php — core registration, handlers, capture API, renderResponse
- src/ErrorMiddleware.php — middleware to catch exceptions in HTTP requests
- src/Reporter/TransportInterface.php — transport interface
- src/Reporter/FileTransport.php — file-based JSONL transport (atomic writes)
- src/Renderer/DevRenderer.php — minimal inline HTML renderer with code snippets
- config/error.php — default configuration
- storage/logs/errors/ — directory for persisted events (gitignored)
- tests/ErrorHandlerTest.php — basic unit test verifying file transport output
- IMPLEMENTATION_PLAN.md — this file (adds PR context & checklist)

## Event payload shape
{
  "id": "event_...",
  "timestamp": "ISO8601",
  "level": "error",
  "exception": {"class":"...","message":"...","stack":[{"file":"...","line":123,"function":"...","code_snippet":"..."}]},
  "request": {"method":"GET","path":"/","headers":{...}},
  "context": {},
  "user": null,
  "environment": "dev|prod"
}

## Sanitization & privacy
- Default sanitize fields: password, token, authorization, cookie
- Request bodies and headers will be sanitized before being written to disk

## Testing & validation
- Unit test ensures captureException writes one JSONL event to the file transport.
- Manual testing: enable ErrorHandler::register in public/index.php, trigger an exception — verify dev HTML page in dev and JSONL logged in prod.

## Notes for reviewers
- This commit is limited to the v0.1 MVP. Follow-up PRs will add remote transport, retry queue, breadcrumbs, and more.

Closes #142
