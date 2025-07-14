# Debugging Guide

## Version: 0.3.1

## Table of Contents
1. [Browser DevTools](#browser-devtools)
2. [PHP Debugging](#php-debugging)
3. [JavaScript Debugging](#javascript-debugging)
4. [Network Analysis](#network-analysis)
5. [Logging](#logging)
6. [Common Issues](#common-issues)
7. [Debugging Tools](#debugging-tools)

## Browser DevTools

### Console Usage
```javascript
// Basic logging
console.log('Debug info:', { data });

// Conditional logging
console.assert(condition, 'Error message');

// Grouping logs
console.group('User Profile');
console.log('Name:', user.name);
console.log('Email:', user.email);
console.groupEnd();

// Performance measurement
console.time('Data processing');
// ... code to measure
console.timeEnd('Data processing');
```

### Debugging React Components
```jsx
// Use React DevTools profiler
import { Profiler } from 'react';

function onRender(
  id,
  phase,
  actualDuration,
  baseDuration,
  startTime,
  commitTime,
  interactions
) {
  console.log('Render performance:', {
    phase,
    actualDuration,
    baseDuration
  });
}

<Profiler id="Dashboard" onRender={onRender}>
  <Dashboard />
</Profiler>
```

## PHP Debugging

### Xdebug Configuration
```ini
; php.ini
[xdebug]
zend_extension=xdebug
xdebug.mode=debug,develop
xdebug.start_with_request=yes
xdebug.client_port=9003
xdebug.client_host=host.docker.internal
xdebug.idekey=VSCODE
xdebug.log=/tmp/xdebug.log
```

### Debugging with MediaWiki
```php
// Debug logging
wfDebugLog('IslamDashboard', 'Widget loaded: ' . $widgetId);

// Stack trace
debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);

// Dump variable (for development only)
function dd($var) {
    var_dump($var);
    die();
}
```

## JavaScript Debugging

### Debugging in Browser
```javascript
// Set breakpoints in browser
function processData(data) {
    debugger; // Execution will pause here
    return data.map(item => ({
        ...item,
        processed: true
    }));
}

// Conditional breakpoint
console.log({ data }); // Right-click line number -> Add conditional breakpoint
```

### Error Handling
```javascript
// Global error handler
window.onerror = function(message, source, lineno, colno, error) {
    console.error('Global error:', { message, source, lineno, colno, error });
    return true; // Prevent default handler
};

// Unhandled promise rejections
window.addEventListener('unhandledrejection', event => {
    console.error('Unhandled rejection:', event.reason);
    event.preventDefault();
});
```

## Network Analysis

### API Request Debugging
```javascript
// Log all API requests
const originalFetch = window.fetch;
window.fetch = async (...args) => {
    console.log('Fetching:', args);
    const response = await originalFetch(...args);
    console.log('Response:', {
        url: args[0],
        status: response.status,
        headers: Object.fromEntries([...response.headers])
    });
    return response;
};
```

### Performance Profiling
```javascript
// Start profiling
console.profile('Dashboard Rendering');

// ... code to profile ...

// End profiling
console.profileEnd('Dashboard Rendering');
```

## Logging

### Server-side Logging
```php
// Different log levels
wfDebug("Debug message");
wfLogWarning("Warning message");
wfLogError("Error message");

// Structured logging
wfDebugLog(
    'IslamDashboard',
    json_encode([
        'action' => 'widget_load',
        'widgetId' => $widgetId,
        'userId' => $user->getId(),
        'timestamp' => wfTimestampNow()
    ])
);
```

### Client-side Logging
```javascript
// Log levels
console.debug('Debug message');
console.info('Info message');
console.warn('Warning message');
console.error('Error message');

// Structured logging
const log = {
    level: 'info',
    message: 'Widget initialized',
    widget: {
        id: widgetId,
        version: '1.0.0',
        settings: widgetSettings
    },
    timestamp: new Date().toISOString()
};

// Send to server or logging service
fetch('/api/log', {
    method: 'POST',
    body: JSON.stringify(log)
});
```

## Common Issues

### Widget Loading Failures
1. Check browser console for errors
2. Verify widget registration
3. Check network tab for failed requests
4. Verify permissions

### Styling Issues
1. Check for CSS specificity conflicts
2. Verify styles are loading in the correct order
3. Look for !important overrides
4. Check media queries for responsive issues

## Debugging Tools

### Recommended Extensions
- **VS Code**: PHP Debug, ESLint, Prettier
- **Chrome**: React Developer Tools, Redux DevTools
- **Firefox**: React DevTools, JSONView

### Command Line Tools
```bash
# Check PHP errors
tail -f /var/log/apache2/error.log

# Check MediaWiki debug log
tail -f /path/to/w/debug.log

# Check JavaScript errors in console
npm run lint
```

## Version History
- **0.3.1**: Initial version
