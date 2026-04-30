# API Server

This is a simple API server that provides sample data for the coding challenge.

## Endpoint

- `GET /` - Returns JSON array of items with `name` and `active` properties

## Response Format

```json
[
  {"name": "Alice", "active": true},
  {"name": "Bob", "active": false},
  {"name": "Charlie", "active": true}
]
```

The API is automatically started via Docker Compose and is available at `http://localhost:8000`

