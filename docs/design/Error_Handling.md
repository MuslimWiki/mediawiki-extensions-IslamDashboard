# Error Handling Guide

## Version: 0.3.1

## Table of Contents
1. [Error Types](#error-types)
2. [Server-Side Error Handling](#server-side-error-handling)
3. [Client-Side Error Handling](#client-side-error-handling)
4. [User-Facing Messages](#user-facing-messages)
5. [Logging Strategy](#logging-strategy)
6. [Error Recovery](#error-recovery)
7. [Monitoring and Alerting](#monitoring-and-alerting)
8. [Testing Error States](#testing-error-states)

## Error Types

### Expected Errors
```typescript
interface ExpectedError {
    code: string;       // Machine-readable error code
    message: string;    // User-friendly message
    status?: number;    // HTTP status code
    details?: any;      // Additional error details
}
```

### Unexpected Errors
```typescript
interface UnexpectedError extends Error {
    componentStack?: string;  // React component stack
    context?: any;           // Additional context
    isOperational?: boolean; // Whether the error is operational
}
```

## Server-Side Error Handling

### Error Classes
```php
class DashboardException extends Exception {
    protected $statusCode = 500;
    protected $errorCode = 'internal_error';
    
    public function __construct($message = null, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function getStatusCode() {
        return $this->statusCode;
    }
    
    public function getErrorCode() {
        return $this->errorCode;
    }
}

class PermissionDeniedException extends DashboardException {
    protected $statusCode = 403;
    protected $errorCode = 'permission_denied';
}
```

### Error Middleware
```php
public static function onApiBeforeMain( &$main ) {
    set_error_handler( [ self::class, 'handleError' ] );
    set_exception_handler( [ self::class, 'handleException' ] );
    return true;
}

public static function handleException( Throwable $e ) {
    if ( $e instanceof DashboardException ) {
        $status = $e->getStatusCode();
        $response = [
            'error' => $e->getErrorCode(),
            'message' => $e->getMessage(),
        ];
    } else {
        $status = 500;
        $response = [
            'error' => 'internal_server_error',
            'message' => wfMessage( 'error-internal' )->text(),
        ];
        
        // Log full error in debug mode
        if ( $wgDebugToolbar ) {
            $response['debug'] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
        }
    }
    
    // Send JSON response for API requests
    header( 'Content-Type: application/json' );
    http_response_code( $status );
    echo json_encode( $response );
    exit;
}
```

## Client-Side Error Handling

### Error Boundary
```jsx
class ErrorBoundary extends React.Component {
    state = { hasError: false, error: null };

    static getDerivedStateFromError(error) {
        return { hasError: true, error };
    }

    componentDidCatch(error, errorInfo) {
        // Log to error tracking service
        logErrorToService(error, errorInfo);
    }

    render() {
        if (this.state.hasError) {
            return (
                <ErrorFallback 
                    error={this.state.error}
                    onRetry={() => this.setState({ hasError: false, error: null })}
                />
            );
        }
        return this.props.children;
    }
}
```

### API Error Handling
```javascript
async function fetchWidgetData(widgetId) {
    try {
        const response = await fetch(`/api/v1/widgets/${widgetId}`);
        
        if (!response.ok) {
            const error = await response.json();
            throw new ApiError(error.message, {
                status: response.status,
                code: error.code,
                details: error.details
            });
        }
        
        return await response.json();
    } catch (error) {
        if (error instanceof ApiError) {
            // Handle API errors
            showErrorToast(error.message);
            
            // Rethrow for error boundaries
            if (error.isCritical) {
                throw error;
            }
        } else {
            // Handle network errors
            showErrorToast('Network error. Please check your connection.');
            throw error; // Let error boundary handle it
        }
    }
}
```

## User-Facing Messages

### Error Messages
```json
{
    "error-permission-denied": "You don't have permission to perform this action.",
    "error-invalid-input": "Please check your input and try again.",
    "error-not-found": "The requested resource was not found.",
    "error-too-many-requests": "Too many requests. Please try again later.",
    "error-timeout": "Request timed out. Please try again.",
    "error-unknown": "An unexpected error occurred. Please try again later."
}
```

### Error Recovery
```jsx
function ErrorFallback({ error, onRetry }) {
    return (
        <div className="error-fallback">
            <Icon name="error" size="large" />
            <h3>Something went wrong</h3>
            <p>{error.message || 'An unexpected error occurred.'}</p>
            <div className="actions">
                <Button onClick={onRetry} primary>Retry</Button>
                <Button onClick={() => window.location.reload()}>Reload Page</Button>
                {error.code === 'permission_denied' && (
                    <Button href="/login">Log In</Button>
                )}
            </div>
            {process.env.NODE_ENV === 'development' && (
                <pre className="error-details">
                    {error.stack}
                </pre>
            )}
        </div>
    );
}
```

## Logging Strategy

### Server-Side Logging
```php
function logError($message, array $context = []) {
    global $wgDebugLogGroups;
    
    $logger = MediaWiki\Logger\LoggerFactory::getInstance('IslamDashboard');
    
    if ($message instanceof Throwable) {
        $logger->error($message->getMessage(), [
            'exception' => $message,
            'file' => $message->getFile(),
            'line' => $message->getLine(),
            'trace' => $message->getTraceAsString(),
        ] + $context);
    } else {
        $logger->error($message, $context);
    }
}
```

### Client-Side Logging
```javascript
const logLevels = {
    ERROR: 'error',
    WARN: 'warn',
    INFO: 'info',
    DEBUG: 'debug'
};

function log(level, message, data = {}) {
    const timestamp = new Date().toISOString();
    const logEntry = {
        timestamp,
        level,
        message,
        ...data,
        userAgent: navigator.userAgent,
        url: window.location.href
    };
    
    // Send to server in production
    if (process.env.NODE_ENV === 'production') {
        navigator.sendBeacon('/api/log', JSON.stringify(logEntry));
    } else {
        // Log to console in development
        console[level](message, data);
    }
}

// Usage
export const logger = {
    error: (message, data) => log(logLevels.ERROR, message, data),
    warn: (message, data) => log(logLevels.WARN, message, data),
    info: (message, data) => log(logLevels.INFO, message, data),
    debug: (message, data) => log(logLevels.DEBUG, message, data)
};
```

## Monitoring and Alerting

### Error Tracking
```javascript
// Initialize error tracking service
function initErrorTracking() {
    if (window.Sentry) {
        Sentry.init({
            dsn: 'YOUR_DSN',
            environment: process.env.NODE_ENV,
            release: process.env.REACT_APP_VERSION,
            beforeSend(event) {
                // Filter out noisy errors
                if (event.message?.includes('ResizeObserver')) {
                    return null;
                }
                return event;
            }
        });
        
        // Add user context
        if (currentUser) {
            Sentry.setUser({ 
                id: currentUser.id,
                username: currentUser.username 
            });
        }
    }
}
```

## Testing Error States

### Unit Tests
```javascript
describe('Error Handling', () => {
    it('should handle 404 errors', async () => {
        fetchMock.mockResponseOnce(JSON.stringify({
            error: 'not_found',
            message: 'Widget not found'
        }), { status: 404 });
        
        const { result, waitForNextUpdate } = renderHook(
            () => useWidget('invalid-widget')
        );
        
        await waitForNextUpdate();
        
        expect(result.current.error).toEqual({
            code: 'not_found',
            message: 'Widget not found'
        });
    });
});
```

### Integration Tests
```javascript
describe('Error Boundary', () => {
    it('should catch errors in children', () => {
        const ErrorComponent = () => {
            throw new Error('Test error');
        };
        
        const { getByText } = render(
            <ErrorBoundary>
                <ErrorComponent />
            </ErrorBoundary>
        );
        
        expect(getByText('Something went wrong')).toBeInTheDocument();
    });
});
```

## Version History
- **0.3.1**: Initial version
