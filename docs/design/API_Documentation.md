# API Documentation

## Version: 0.3.1

## Table of Contents
1. [Authentication](#authentication)
2. [Base URL](#base-url)
3. [Versioning](#versioning)
4. [Rate Limiting](#rate-limiting)
5. [Error Handling](#error-handling)
6. [Endpoints](#endpoints)
7. [WebSockets](#websockets)
8. [Webhooks](#webhooks)
9. [Deprecation Policy](#deprecation-policy)
10. [Examples](#examples)

## Authentication

### API Keys
```http
GET /api/v1/widgets
Authorization: Bearer your_api_key_here
```

### OAuth 2.0
```http
POST /oauth/token
Content-Type: application/x-www-form-urlencoded

grant_type=client_credentials&
client_id=your_client_id&
client_secret=your_client_secret
```

## Base URL
All API endpoints are relative to the base URL:
```
https://api.muslim.wiki/v1
```

For local development:
```
http://localhost:8080/api.php?action=islamdashboard&format=json
```

## Versioning
API version is included in the URL path:
```
/api/v1/...
```

## Rate Limiting
- **Authenticated requests**: 1000 requests per hour
- **Unauthenticated requests**: 100 requests per hour

### Headers
```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 987
X-RateLimit-Reset: 1617235200
```

## Error Handling

### Error Response Format
```json
{
  "error": {
    "code": "invalid_parameter",
    "message": "Invalid value for 'limit'. Must be between 1 and 100.",
    "param": "limit",
    "value": 150,
    "constraints": {
      "min": 1,
      "max": 100
    },
    "documentation_url": "https://docs.muslim.wiki/api/errors#invalid_parameter"
  }
}
```

### Common Error Codes
| Status Code | Error Code | Description |
|-------------|------------|-------------|
| 400 | invalid_request | Invalid request format |
| 401 | unauthorized | Authentication required |
| 403 | forbidden | Insufficient permissions |
| 404 | not_found | Resource not found |
| 429 | too_many_requests | Rate limit exceeded |
| 500 | server_error | Internal server error |

## Endpoints

### Widgets

#### List Widgets
```http
GET /widgets
```

**Parameters**
| Name | Type | Required | Description |
|------|------|----------|-------------|
| limit | integer | No | Number of items per page (1-100) |
| offset | integer | No | Pagination offset |
| status | string | No | Filter by status (active, inactive, all) |

**Response**
```json
{
  "data": [
    {
      "id": "recent-changes",
      "title": "Recent Changes",
      "description": "Shows recent edits across the wiki",
      "version": "1.0.0",
      "status": "active",
      "settings_schema": {
        "type": "object",
        "properties": {
          "limit": {
            "type": "integer",
            "default": 10,
            "minimum": 1,
            "maximum": 50
          }
        }
      },
      "created_at": "2023-01-01T00:00:00Z",
      "updated_at": "2023-01-01T00:00:00Z"
    }
  ],
  "pagination": {
    "total": 1,
    "limit": 20,
    "offset": 0
  }
}
```

#### Get Widget
```http
GET /widgets/{widget_id}
```

**Response**
```json
{
  "id": "recent-changes",
  "title": "Recent Changes",
  "description": "Shows recent edits across the wiki",
  "version": "1.0.0",
  "status": "active",
  "settings": {
    "limit": 10
  },
  "data": [
    {
      "id": 123,
      "title": "Main Page",
      "user": "ExampleUser",
      "timestamp": "2023-01-01T12:00:00Z",
      "comment": "Fixed typo",
      "diff_url": "/w/index.php?diff=123"
    }
  ],
  "created_at": "2023-01-01T00:00:00Z",
  "updated_at": "2023-01-01T00:00:00Z"
}
```

#### Update Widget Settings
```http
PATCH /widgets/{widget_id}
Content-Type: application/json

{
  "settings": {
    "limit": 20
  }
}
```

## WebSockets

### Connection
```javascript
const socket = new WebSocket('wss://api.muslim.wiki/v1/ws');

socket.onopen = () => {
  console.log('Connected to WebSocket');
  
  // Subscribe to widget updates
  socket.send(JSON.stringify({
    action: 'subscribe',
    channel: 'widget_updates',
    widget_id: 'recent-changes'
  }));
};

socket.onmessage = (event) => {
  const data = JSON.parse(event.data);
  console.log('Received update:', data);
};
```

## Webhooks

### Available Events
- `widget.created`
- `widget.updated`
- `widget.deleted`
- `widget.error`

### Webhook Payload
```json
{
  "event": "widget.updated",
  "data": {
    "id": "recent-changes",
    "changes": {
      "settings.limit": [10, 20]
    },
    "updated_at": "2023-01-01T12:00:00Z"
  },
  "request_id": "req_1234567890"
}
```

## Deprecation Policy

### Timeline
1. **Announcement**: Deprecated endpoints will be announced 6 months before removal
2. **Deprecation**: Endpoints marked with `Deprecation: true` header
3. **Removal**: Endpoints removed after 12 months

### Deprecation Headers
```
Deprecation: true
Sunset: Wed, 01 Jan 2025 00:00:00 GMT
Link: <https://docs.muslim.wiki/api/v2>; rel="successor-version"
```

## Examples

### cURL
```bash
# Get widget
curl -X GET \
  https://api.muslim.wiki/v1/widgets/recent-changes \
  -H "Authorization: Bearer your_api_key"

# Update widget settings
curl -X PATCH \
  https://api.muslim.wiki/v1/widgets/recent-changes \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_api_key" \
  -d '{"settings":{"limit":20}}'
```

### JavaScript (Fetch API)
```javascript
// Get widget data
async function getWidget(widgetId) {
  const response = await fetch(`/api/v1/widgets/${widgetId}`, {
    headers: {
      'Authorization': 'Bearer your_api_key',
      'Accept': 'application/json'
    }
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message);
  }
  
  return response.json();
}

// Update widget settings
async function updateWidgetSettings(widgetId, settings) {
  const response = await fetch(`/api/v1/widgets/${widgetId}`, {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer your_api_key'
    },
    body: JSON.stringify({ settings })
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message);
  }
  
  return response.json();
}
```

## Version History
- **0.3.1**: Initial version
