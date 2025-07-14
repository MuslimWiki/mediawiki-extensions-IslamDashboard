# Widget Lifecycle

## Version: 0.3.1

## Table of Contents
1. [Widget States](#widget-states)
2. [Lifecycle Hooks](#lifecycle-hooks)
3. [State Management](#state-management)
4. [Error Boundaries](#error-boundaries)
5. [Performance Optimization](#performance-optimization)
6. [Versioning](#versioning)
7. [Best Practices](#best-practices)

## Widget States

### Loading State
```jsx
function WidgetLoader() {
    return (
        <div className="widget-loading">
            <Spinner />
            <span>Loading widget data...</span>
        </div>
    );
}
```

### Error State
```jsx
function WidgetError({ error, onRetry }) {
    return (
        <div className="widget-error">
            <Icon name="error" />
            <p>Failed to load widget</p>
            <button onClick={onRetry}>Retry</button>
        </div>
    );
}
```

### Empty State
```jsx
function EmptyState({ onAction }) {
    return (
        <div className="widget-empty">
            <p>No data available</p>
            <button onClick={onAction}>Refresh</button>
        </div>
    );
}
```

## Lifecycle Hooks

### Server-Side (PHP)
```php
class MyWidget extends DashboardWidget {
    // Called when widget is registered
    public static function onRegistration() {
        // Register resources, permissions, etc.
    }

    // Called before widget is rendered
    public function onBeforeRender() {
        // Preload data, check permissions
    }

    // Called after widget is removed
    public function onRemoval() {
        // Clean up resources
    }
}
```

### Client-Side (JavaScript)
```jsx
class MyWidget extends React.Component {
    // Mounting phase
    componentDidMount() {
        this.loadData();
        this.setupEventListeners();
    }

    // Updating phase
    shouldComponentUpdate(nextProps, nextState) {
        // Optimize re-renders
        return !shallowEqual(this.props, nextProps) || 
               !shallowEqual(this.state, nextState);
    }

    // Unmounting phase
    componentWillUnmount() {
        this.cleanupEventListeners();
        this.cancelPendingRequests();
    }
}
```

## State Management

### Local State
```jsx
function CounterWidget() {
    const [count, setCount] = useState(0);
    
    return (
        <div>
            <p>Count: {count}</p>
            <button onClick={() => setCount(c => c + 1)}>Increment</button>
        </div>
    );
}
```

### Global State (Redux)
```jsx
// Actions
const fetchWidgetData = (widgetId) => (dispatch) => {
    dispatch({ type: 'FETCH_WIDGET_DATA_START', widgetId });
    
    return api.fetchWidgetData(widgetId)
        .then(data => 
            dispatch({ type: 'FETCH_WIDGET_DATA_SUCCESS', widgetId, data })
        )
        .catch(error => 
            dispatch({ type: 'FETCH_WIDGET_DATA_ERROR', widgetId, error })
        );
};

// Component
const ConnectedWidget = connect(
    (state, ownProps) => ({
        data: state.widgets[ownProps.widgetId]?.data,
        isLoading: state.widgets[ownProps.widgetId]?.isLoading,
        error: state.widgets[ownProps.widgetId]?.error
    }),
    { fetchData: fetchWidgetData }
)(WidgetComponent);
```

## Error Boundaries

### React Error Boundary
```jsx
class ErrorBoundary extends React.Component {
    state = { hasError: false, error: null };

    static getDerivedStateFromError(error) {
        return { hasError: true, error };
    }

    componentDidCatch(error, errorInfo) {
        logErrorToService(error, errorInfo);
    }

    render() {
        if (this.state.hasError) {
            return <ErrorFallback error={this.state.error} />;
        }
        return this.props.children;
    }
}

// Usage
<ErrorBoundary>
    <WidgetComponent />
</ErrorBoundary>
```

## Performance Optimization

### Memoization
```jsx
const ExpensiveComponent = React.memo(
    function ExpensiveComponent({ items }) {
        // Component logic
    },
    (prevProps, nextProps) => {
        // Custom comparison
        return prevProps.items.length === nextProps.items.length;
    }
);
```

### Lazy Loading
```jsx
const LazyWidget = React.lazy(() => import('./HeavyWidget'));

function Dashboard() {
    return (
        <Suspense fallback={<Spinner />}>
            <LazyWidget />
        </Suspense>
    );
}
```

## Versioning

### Widget Manifest
```json
{
    "name": "recent-changes",
    "version": "1.2.0",
    "compatibility": {
        "min": "1.0.0",
        "max": "2.0.0"
    },
    "dependencies": {
        "core": "^1.0.0",
        "api": "^2.0.0"
    }
}
```

### Version Check
```php
public function isCompatible($dashboardVersion) {
    return version_compare(
        $dashboardVersion,
        $this->compatibility['min'],
        '>='
    ) && version_compare(
        $dashboardVersion,
        $this->compatibility['max'] ?? '999.999.999',
        '<='
    );
}
```

## Best Practices

### Do:
- Keep widgets small and focused
- Handle all error states
- Use proper loading states
- Implement proper cleanup
- Test with slow networks
- Support keyboard navigation
- Follow accessibility guidelines

### Don't:
- Make widgets too complex
- Block rendering on data loading
- Ignore error states
- Leak event listeners
- Assume screen size
- Use inline styles

## Version History
- **0.3.1**: Initial version
