# Internationalization (i18n) Guide

## Version: 0.3.1

## Table of Contents
1. [File Structure](#file-structure)
2. [Metadata Template](#metadata-template)
3. [Translation Guidelines](#translation-guidelines)
4. [RTL Support](#rtl-support)
5. [Plural Forms](#plural-forms)
6. [Date and Number Formatting](#date-and-number-formatting)
7. [Best Practices](#best-practices)
8. [Testing](#testing)

## File Structure

### Language Files
```
extensions/IslamDashboard/i18n/
├── en.json          # English (base language)
├── ar.json          # Arabic
├── es.json          # Spanish
└── qqq.json         # Documentation
```

### Naming Conventions
- Use lowercase with hyphens for message keys: `dashboard-widget-title`
- Group related keys with prefixes: `widget-stats-views`, `widget-stats-edits`
- Use full words, no abbreviations

## Metadata Template

### Standard Metadata
```json
{
    "@metadata": {
        "language": "English",
        "language_code": "en",
        "direction": "ltr",
        "authors": [
            "[https://example.com/username Display Name]",
            "Another Contributor"
        ],
        "note": "Additional notes about this translation"
    },
    "message-key": "Translated text"
}
```

### Field Descriptions
- **language**: Full language name in its own script
- **language_code**: ISO 639-1/639-2 language code
- **direction**: Text direction (`ltr` or `rtl`)
- **authors**: Array of contributors with optional links
- **note**: Any special notes for translators

## Translation Guidelines

### Message Formatting
```json
{
    "welcome-message": "Welcome, $1!",
    "items-count": "$1 {{PLURAL:$1|item|items}}",
    "last-updated": "Last updated: $1 at $2",
    "copyright": "© {{CURRENTYEAR}} {{SITENAME}}"
}
```

### HTML in Translations
```json
{
    "help-text": "See our <a href=\"$1\">help page</a> for more information."
}
```

## RTL Support

### CSS for RTL
```less
/* Use :lang() selector for language-specific styles */
[dir='rtl'] {
    text-align: right;
    
    .dashboard-widget {
        margin-right: 0;
        margin-left: 1rem;
    }
}
```

### JavaScript RTL Detection
```javascript
const isRTL = document.documentElement.dir === 'rtl';
const startEdge = isRTL ? 'right' : 'left';
```

## Plural Forms

### Basic Pluralization
```json
{
    "items-count": "$1 {{PLURAL:$1|item|items}}"
}
```

### Language-Specific Plurals
```json
{
    "items-count": "{{PLURAL:$1|$1 item|$1 items|No items}}"
}
```

## Date and Number Formatting

### Using MediaWiki's Formatters
```php
// In PHP
$lang = $context->getLanguage();
$formattedDate = $lang->date( $timestamp );
$formattedNumber = $lang->formatNum( 1234.567 );
```

### In JavaScript
```javascript
// Using mediawiki.language
const lang = mw.language;
const formatted = lang.convertNumber( 1234.567 );
const date = new lang.Date( new Date() );
```

## Best Practices

### Do:
- Keep messages complete and meaningful
- Include comments for context
- Use placeholders for dynamic content
- Test with long strings
- Verify RTL support

### Don't:
- Concatenate translated strings
- Assume word order
- Hardcode numbers or dates
- Use text in images

## Testing

### Manual Testing
1. Switch between languages
2. Check text expansion/contraction
3. Verify RTL layout
4. Test date/number formats
5. Check plural forms

### Automated Tests
```php
// PHPUnit test
public function testMessageKeysExist() {
    $this->assertArrayHasKey( 'welcome-message', $this->getMessages() );
}
```

## Version History
- **0.3.1**: Initial version with enhanced metadata template
