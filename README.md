# Coding Challenge — Items Directory (PHP + React)

Fullstack solution for the coding challenge: a structured PHP backend that
fetches data from an upstream API and exposes it with filtering/sorting, plus a
React UI that lists, filters and sorts the items.

```
.
├── api/          # Provided mock API (do not modify) — GET / returns [{name, active}]
├── public/       # Backend HTTP entrypoint (GET /items)
├── src/          # Backend application code (PSR-4: App\)
├── tests/        # Pest unit tests
└── frontend/     # React + Vite + TypeScript UI
```

## Architecture overview

```
Browser ──► frontend :5173 ──► backend (internal :8080) ──► mock API (internal :8000)
            (Vite proxy /api)   filter + sort here
```

Everything runs in Docker. Only the **frontend** port (`5173`) is published to
the host — it is the single entry point. The browser talks only to the frontend;
Vite proxies `/api` to the backend over the Compose network, and the backend
reaches the mock API at `http://api:8000`. The `api` and `backend` services have
no host ports.

- The **mock API** (`api/`) returns the raw list of items.
- The **backend** (`public/` + `src/`) fetches that list and applies status
  filtering, name search and column sorting **server-side**, then returns JSON.
- The **frontend** (`frontend/`) is a thin client that drives those query params
  and renders the result.

---

## How to run

The whole stack runs with Docker Compose. Install PHP dependencies first — the
backend container mounts the project and uses the generated `vendor/`:

```bash
composer install
docker compose up -d
```

Then open **`http://localhost:5173`** — that is the only exposed port.

Services started:

- `frontend` — React UI at `http://localhost:5173` (proxies `/api` → backend)
- `backend` — application (internal only; reaches the mock API via
  `http://api:8000`, set through `UPSTREAM_API_URL`)
- `api` — mock data (internal only)

### Running the tests

```bash
composer test          # or: ./vendor/bin/pest
```

---

## API

`GET /` (served from `public/index.php`; reachable from the host through the
frontend proxy at `/api/`)

| Param    | Values                          | Default | Description                          |
|----------|---------------------------------|---------|--------------------------------------|
| `status` | `all`, `active`, `inactive`     | `all`   | Filter by active flag                |
| `search` | any string                      | `''`    | Case-insensitive substring on `name` |
| `sort`   | `name`, `active`                | `name`  | Sort column                          |
| `dir`    | `asc`, `desc`                   | `asc`   | Sort direction                       |

Response:

```json
{ "data": [ { "name": "Alice", "active": true } ] }
```

On an upstream failure the endpoint responds with HTTP `502` and
`{ "error": "..." }`. Invalid query values silently fall back to the defaults
above.

---

## Assumptions

- The upstream payload is a flat array of objects with `name` (string) and
  `active` (bool). Missing/oddly-typed fields are coerced to safe defaults
  (`name` → `''`, `active` → `false`) rather than failing the whole request.
- Item `name` is treated as unique and used as the React list key (the dataset
  is small and has no id).
- The original snippet uppercased names (`strtoupper`); I treat that as a
  presentation concern and keep the original casing in the API, letting the UI
  decide how to display it.
- No pagination/auth is needed given the dataset size and the challenge scope.

## Design decisions

- **Separation of concerns.** The original `ApiFetcher` is split into focused
  units: `Client` (HTTP/IO), `Model\Item` (data shape), `Service\ItemService`
  (business logic: filter + sort), `Http\RequestQuery` (param parsing/validation)
  and `Http\ItemController` (orchestration).
- **Dependency inversion for testability.** `ItemService` depends on
  `UpstreamApiClientInterface`, not on cURL. Tests inject a fake client, so the
  filtering/sorting logic is verified without any network access.
- **Server-side filtering/sorting.** This fulfils Part 1's requirement to extend
  the endpoint and keeps the frontend thin — it never re-implements the logic.
- **Transport-agnostic controller.** `ItemController::index()` returns
  `{status, body}` instead of echoing, so the HTTP flow is unit-testable;
  `public/index.php` only deals with headers, CORS and JSON encoding.
- **Strict typing everywhere** (`declare(strict_types=1)`) plus PHPStan/PHPCS to
  catch type and style issues early.
- **Frontend kept dependency-light.** React + Vite + TypeScript only — no UI or
  state libraries. A custom `useItems` hook owns the state, debounces the search
  (250ms) and uses `AbortController` to cancel stale requests. The table
  collapses into cards on small screens for responsiveness.

## Trade-offs / what I'd do with more time

- **Routing:** the backend treats any path as the items endpoint. With more time
  I'd add a small router (or a micro-framework like Slim) and a proper `/items`
  route with method handling.
- **Frontend data layer:** I'd introduce React Query (or SWR) for caching,
  retries and request de-duplication instead of the hand-rolled fetch hook.
- **Testing depth:** I'd add a feature test for the controller end-to-end and
  frontend component tests (Vitest + Testing Library).
- **Config:** environment handling is minimal (a couple of env vars). I'd
  centralise config and add `.env` support on both sides.
- **Observability/UX:** structured logging instead of `error_log`, plus richer
  loading/empty/error states and skeletons in the UI.
- **DX:** the whole stack (api + backend + frontend) already boots with a single
  `docker compose up`, exposing only the frontend port. Next I'd add a multi-stage
  Dockerfile that runs `composer install` inside the image (instead of relying on
  the host `vendor/`) and a production build target for the frontend served by a
  static server/nginx rather than the Vite dev server.

---

## Tooling

- `composer test` — run the Pest test suite
- `composer lint` / `composer lint:fix` — PHP_CodeSniffer (PSR-12)
- `composer analyse` — PHPStan static analysis
- `composer check` — lint + analyse + test
- `npm run build` (in `frontend/`) — type-check and production build
