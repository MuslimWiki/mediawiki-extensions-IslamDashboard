# IslamDashboard API Endpoints

This document provides detailed information about the API endpoints available in the IslamDashboard extension.

## Table of Contents
- [Authentication](#authentication)
- [Rate Limiting](#rate-limiting)
- [Endpoints](#endpoints)
  - [Get Dashboard Data](#get-dashboard-data)
  - [Update Widget Settings](#update-widget-settings)
  - [Get User Preferences](#get-user-preferences)
  - [Save Dashboard Layout](#save-dashboard-layout)

## Authentication

All API endpoints require authentication. The extension uses MediaWiki's session-based authentication.

### Required Headers
```
Cookie: [MediaWiki session cookie]
```

## Rate Limiting

API requests are subject to rate limiting:
- 60 requests per minute per user
- 1000 requests per day per user

## Endpoints

### Get Dashboard Data

Returns the dashboard data for the current user.

**Endpoint:** `GET /api.php?action=islamdashboard-get-dashboard-data`

#### Parameters
None

#### Response
```json
{
  "dashboard": {
    "layout": "default",
    "widgets": [
      {
        "id": "welcome",
        "title": "Welcome",
        "content": "<div>Welcome message</div>",
        "settings": {}
      }
    ]
  },
  "userPreferences": {
    "theme": "light",
    "language": "en"
  }
}
```

### Update Widget Settings

Updates the settings for a specific widget.

**Endpoint:** `POST /api.php?action=islamdashboard-update-widget-settings`

#### Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| widgetId | string | Yes | The ID of the widget to update |
| settings | object | Yes | The new settings for the widget |

#### Example Request
```json
{
  "widgetId": "recent-activity",
  "settings": {
    "itemsPerPage": 10,
    "showTimestamps": true
  }
}
```

#### Response
```json
{
  "success": true,
  "message": "Widget settings updated successfully"
}
```

### Get User Preferences

Retrieves the current user's dashboard preferences.

**Endpoint:** `GET /api.php?action=islamdashboard-get-preferences`

#### Parameters
None

#### Response
```json
{
  "preferences": {
    "theme": "light",
    "language": "en",
    "notificationsEnabled": true,
    "emailNotifications": false
  }
}
```

### Save Dashboard Layout

Saves the current dashboard layout.

**Endpoint:** `POST /api.php?action=islamdashboard-save-layout`

#### Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| layout | object | Yes | The layout object containing widget positions |

#### Example Request
```json
{
  "layout": {
    "widgets": [
      {
        "id": "welcome",
        "position": {
          "x": 0,
          "y": 0,
          "w": 2,
          "h": 2
        }
      }
    ]
  }
}
```

#### Response
```json
{
  "success": true,
  "message": "Layout saved successfully"
}
```

## Error Responses

All error responses follow this format:

```json
{
  "error": {
    "code": "error_code",
    "info": "Human-readable error message",
    "details": "Additional error details (optional)"
  }
}
```

### Common Error Codes

| Code | HTTP Status | Description |
|------|-------------|-------------|
| invalid-token | 403 | Invalid or missing CSRF token |
| permission-denied | 403 | User doesn't have permission to perform the action |
| missingparam | 400 | Required parameter is missing |
| invalid-widget | 400 | Specified widget does not exist |
| ratelimited | 429 | Rate limit exceeded |
| internal_error | 500 | Internal server error |

## WebSocket Events

The dashboard also supports real-time updates via WebSocket:

### Connection
```javascript
const socket = new WebSocket('wss://example.com/wss/dashboard');
```

### Events

#### dashboard.updated
Triggered when the dashboard layout or content is updated.

```json
{
  "event": "dashboard.updated",
  "data": {
    "type": "layout|widget|preferences",
    "user": "username"
  }
}
```

#### notification.new
Triggered when a new notification is received.

```json
{
  "event": "notification.new",
  "data": {
    "id": "notification-123",
    "type": "info|warning|error|success",
    "title": "Notification Title",
    "message": "Notification message",
    "timestamp": "2023-01-01T00:00:00Z"
  }
}
```

## Versioning

API versioning is handled through the `Accept` header:

```
Accept: application/vnd.islamdashboard.v1+json
```

Current API version: `v1`
