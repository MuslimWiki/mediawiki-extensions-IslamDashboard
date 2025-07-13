# Troubleshooting Guide

This guide provides solutions to common issues you might encounter while using or developing the IslamDashboard extension.

## Table of Contents

1. [Installation Issues](#installation-issues)
2. [Widget Loading Problems](#widget-loading-problems)
3. [JavaScript Errors](#javascript-errors)
4. [CSS/Styling Issues](#cssstyling-issues)
5. [API Problems](#api-problems)
6. [Performance Issues](#performance-issues)
7. [Debugging Tools](#debugging-tools)
8. [Getting Help](#getting-help)

## Installation Issues

### Extension not showing up after installation

1. **Verify installation**:
   ```bash
   php maintenance/update.php
   ```

2. **Check LocalSettings.php**:
   ```php
   wfLoadExtension( 'IslamDashboard' );
   ```

3. **Check PHP error logs** for any fatal errors during extension loading:
   ```bash
   tail -f /var/log/apache2/error.log  # Adjust path as needed
   ```

### Database update errors

If you encounter database-related errors:

1. Run the update script:
   ```bash
   php maintenance/update.php
   ```

2. If that fails, check for specific schema updates:
   ```bash
   php maintenance/run.php extensions/IslamDashboard/maintenance/update.php --quick
   ```

## Widget Loading Problems

### Widget not appearing

1. **Check widget registration**:
   - Verify the widget is properly registered in `extension.json`
   - Check that the widget class exists and is properly named

2. **Check permissions**:
   ```php
   // In your widget class
   public function isAllowed() {
       return true; // Or your permission logic
   }
   ```

3. **Check browser console** for JavaScript errors (see [Debugging Tools](#debugging-tools))

### Widget content not updating

1. **Check caching**:
   - Clear browser cache (Ctrl+F5)
   - Clear MediaWiki cache:
     ```bash
     php maintenance/rebuildLocalisationCache.php
     php maintenance/rebuildFileCache.php
     ```

2. **Check AJAX requests** in browser developer tools (Network tab)

## JavaScript Errors

### Common errors and solutions

1. **$ is not defined**
   - Make sure jQuery is properly loaded before your scripts
   - Use `jQuery` instead of `$` or wrap your code in:
     ```javascript
     ( function( $ ) {
         // Your code here
     }( jQuery ) );
     ```

2. **mw is not defined**
   - Make sure `mediawiki` is in your ResourceLoader module dependencies
   - Wrap your code in:
     ```javascript
     mw.loader.using( 'mediawiki.api' ).then( function() {
         // Your code here
     } );
     ```

3. **Uncaught TypeError: Cannot read property 'foo' of undefined**
   - Check for null/undefined values before accessing properties
   - Use optional chaining (`?.`) if supported

## CSS/Styling Issues

### Styles not applying

1. **Check ResourceLoader debug mode**:
   - Append `?debug=true` to the URL
   - Check if your LESS files are being compiled correctly

2. **Check for CSS specificity issues**
   - Use browser developer tools to inspect elements
   - Look for overridden styles (struck-through in the styles panel)

3. **Verify LESS variables** are defined before use

### RTL (Right-to-Left) issues

1. Check if the `dir="rtl"` attribute is set on the HTML element
2. Look for hardcoded `left`/`right` values that should use `@start`/`@end` in LESS
3. Test with RTL languages (e.g., Arabic)

## API Problems

### Authentication errors

1. **Check API permissions** in `extension.json`:
   ```json
   "APIModules": {
       "islamdashboard": "MediaWiki\\Extension\\IslamDashboard\\Api\\ApiIslamDashboard"
   },
   "APIMetaDataUpdate": true,
   "AvailableRights": [
       "islamdashboard-api"
   ]
   ```

2. **Verify token handling** in your API modules

### CORS issues

1. Ensure proper CORS headers are set:
   ```php
   $response->header( 'Access-Control-Allow-Origin', '*' );
   // Or for specific domains:
   $response->header( 'Access-Control-Allow-Origin', 'https://yourdomain.com' );
   ```

## Performance Issues

### Slow page loads

1. **Check ResourceLoader debug info**:
   - Append `?debug=true&debug=1` to the URL
   - Look for slow-loading modules

2. **Optimize database queries**:
   - Use `EXPLAIN` on slow queries
   - Add appropriate indexes
   - Cache results when possible

3. **Profile PHP execution**:
   ```bash
   php -d xdebug.profiler_enable=1 -d xdebug.profiler_output_dir=/tmp index.php
   ```

### Memory issues

1. **Increase PHP memory limit** in `php.ini`:
   ```ini
   memory_limit = 256M
   ```

2. **Check for memory leaks** in your code
   - Unset large variables when done
   - Close database connections
   - Use generators for large datasets

## Debugging Tools

### Browser Developer Tools

1. **Console**: Check for JavaScript errors
2. **Network tab**: Monitor API requests and responses
3. **Elements tab**: Inspect HTML and CSS
4. **Performance tab**: Identify performance bottlenecks

### MediaWiki Debugging

1. **Enable debug logging** in `LocalSettings.php`:
   ```php
   $wgDebugLogFile = "$IP/debug.log";
   $wgShowExceptionDetails = true;
   $wgShowDBErrorBacktrace = true;
   $wgShowSQLErrors = true;
   ```

2. **Debug toolbar**:
   - Install the "DebugBar" extension
   - Use `wfDebugLog()` for custom logging

### Xdebug

1. **Set up Xdebug** for step debugging
2. **Configure your IDE** for remote debugging
3. **Set breakpoints** in your code

## Getting Help

If you can't resolve your issue:

1. **Check the documentation**:
   - [Configuration Guide](development/configuration.md)
   - [Widget Development](development/widgets.md)
   - [API Documentation](api/endpoints.md)

2. **Search existing issues**:
   - [GitHub Issues](https://github.com/your-repo/IslamDashboard/issues)
   - [MediaWiki Support](https://www.mediawiki.org/wiki/Support)

3. **Create a new issue** with:
   - Steps to reproduce
   - Expected vs. actual behavior
   - Screenshots if applicable
   - Browser/OS versions
   - PHP/MediaWiki versions
   - Error logs

4. **Ask for help** on:
   - [MediaWiki Support](https://www.mediawiki.org/wiki/Support)
   - [Stack Overflow](https://stackoverflow.com/questions/tagged/mediawiki)
   - [MediaWiki IRC](https://www.mediawiki.org/wiki/IRC/Channels)

## Common Error Messages

| Error Message | Possible Cause | Solution |
|--------------|----------------|-----------|
| "Widget class not found" | Missing or incorrect class name | Check widget registration and class namespace |
| "Permission denied" | Missing user rights | Verify user permissions |
| "Database connection failed" | Incorrect DB credentials | Check LocalSettings.php |
| "Invalid CSRF token" | Form token mismatch | Ensure proper token handling in forms |
| "Resource limit exceeded" | Too many requests or heavy queries | Optimize queries, implement caching |

## Tips for Reporting Issues

1. **Be specific** about what you're trying to do
2. **Include version numbers** of all relevant software
3. **Share relevant code snippets**
4. **Describe what you've tried** already
5. **Check if the issue is reproducible** in a clean environment

## Contributing Fixes

If you've found and fixed an issue, please consider contributing your fix back to the project:

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Submit a pull request with a clear description of the issue and your solution

## Emergency Recovery

If something goes critically wrong:

1. **Disable the extension** by commenting out the line in `LocalSettings.php`
2. **Restore from backup** if available
3. **Roll back database changes** if needed:
   ```bash
   php maintenance/run.php extensions/IslamDashboard/maintenance/rollback.php
   ```

Remember to always back up your data before making significant changes!
