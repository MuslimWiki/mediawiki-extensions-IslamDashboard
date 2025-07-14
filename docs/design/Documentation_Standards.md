# Documentation Standards

## Version: 0.3.1

## Table of Contents
1. [Code Documentation](#code-documentation)
2. [API Documentation](#api-documentation)
3. [User Documentation](#user-documentation)
4. [Versioning](#versioning)
5. [Accessibility](#accessibility)

## Code Documentation

### PHP Documentation
Follow PHPDoc standard with the following requirements:

```php
/**
 * Brief summary ending with a period.
 *
 * Detailed description that can span multiple lines and include
 * HTML formatting if needed.
 *
 * @since 0.3.1
 * @param string $paramName Description of parameter
 * @param int $anotherParam Description of another parameter
 * @return string Description of return value
 * @throws InvalidArgumentException When invalid parameters are passed
 * @see RelatedClass::method()
 */
public function exampleMethod(string $paramName, int $anotherParam): string
{
    // Implementation
}
```

### JavaScript Documentation
Use JSDoc with TypeScript type annotations:

```javascript
/**
 * Performs an action with the given parameters.
 * @param {Object} options - Configuration options
 * @param {string} options.name - The name to use
 * @param {number} [options.count=1] - Optional count
 * @returns {Promise<boolean>} True if successful
 * @throws {Error} When something goes wrong
 */
async function exampleFunction({ name, count = 1 }) {
    // Implementation
}
```

### CSS/LESS Documentation
```less
/**
 * Component container
 *
 * 1. Ensure proper stacking context
 * 2. Prevent horizontal scrollbar
 */
.component {
    position: relative; /* 1 */
    overflow-x: hidden; /* 2 */
    
    // Nested elements
    &__item {
        margin: 0;
    }
}
```

## API Documentation

### Endpoint Documentation
```markdown
## GET /api.php?action=example

### Parameters
| Name | Type | Required | Description |
|------|------|----------|-------------|
| param1 | string | Yes | Description of parameter |
| param2 | integer | No | Default: 10 |

### Response
```json
{
    "success": true,
    "data": {}
}
```

### Error Responses
| Status | Description |
|--------|-------------|
| 400 | Invalid parameters |
| 403 | Permission denied |
| 404 | Resource not found |
```

## User Documentation

### Structure
1. **Overview**: Brief description of the feature
2. **Requirements**: Any prerequisites
3. **Installation**: Step-by-step guide
4. **Configuration**: Available options
5. **Usage**: Common use cases
6. **Troubleshooting**: Common issues and solutions

### Writing Style
- Use active voice
- Be concise but thorough
- Include examples
- Use numbered lists for procedures
- Use bullet points for features/options

## Versioning

### Version Format
`MAJOR.MINOR.PATCH`

- **MAJOR**: Breaking changes
- **MINOR**: New features (backward-compatible)
- **PATCH**: Bug fixes (backward-compatible)

### Changelog Format
```markdown
## [Unreleased]
### Added
- New feature X

### Changed
- Improved Y

### Fixed
- Fixed issue with Z
```

## Accessibility

### Documentation Requirements
1. Include alt text for all images
2. Use semantic HTML in examples
3. Document keyboard navigation
4. Note any known accessibility limitations

### Example
```html
<!-- Bad -->
<div onclick="doSomething()">Click me</div>

<!-- Good -->
<button type="button" onclick="doSomething()">
    <span class="visually-hidden">Perform action: </span>
    Click me
</button>
```

## Review Process

### Self-Review Checklist
- [ ] All public methods documented
- [ ] Examples provided where helpful
- [ ] Version numbers updated
- [ ] Cross-references added
- [ ] Accessibility considerations included

## Version History
- **0.3.1**: Initial version
