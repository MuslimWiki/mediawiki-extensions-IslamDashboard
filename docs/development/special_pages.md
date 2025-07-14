# Special Pages in IslamDashboard

## Special:IslamDashboard

### Registration

The IslamDashboard special page is registered in two places for maximum compatibility:

1. **extension.json**: Primary registration with required services
   ```json
   "SpecialPages": {
       "IslamDashboard": {
           "class": "MediaWiki\\Extension\\IslamDashboard\\Special\\SpecialIslamDashboard",
           "services": ["DBLoadBalancer"]
       }
   }
   ```

2. **Hooks.php**: Secondary registration via `onSpecialPage_initList` hook for additional services and error handling
   ```php
   public static function onSpecialPage_initList( array &$list ) {
       global $wgOut;
       
       try {
           $list['IslamDashboard'] = [
               'class' => 'MediaWiki\\Extension\\IslamDashboard\\Special\\SpecialIslamDashboard',
               'services' => [
                   'DBLoadBalancer',
                   'LinkRenderer',
                   'UserOptionsLookup',
                   'UserOptionsManager',
                   'TitleFactory',
                   'UserFactory',
                   'PermissionManager',
                   'NamespaceInfo',
                   'SpecialPageFactory'
               ]
           ];
       } catch ( \Exception $e ) {
           // Error handling...
       }
   }
   ```

### Common Issues and Solutions

1. **Special Page Not Appearing**
   - Ensure the class name matches exactly: `SpecialIslamDashboard` (not `SpecialDashboard`)
   - Check for PHP errors in the logs
   - Clear the MediaWiki cache
   - Verify the user has the required permissions

2. **Service Injection**
   - All required services must be listed in the services array
   - The constructor should type-hint these services

3. **Layout Issues**
   - The dashboard uses a three-column layout with these classes:
     - Main container: `mw-dashboard-grid`
     - Left sidebar: `mw-dashboard-sidebar`
     - Main content: `mw-dashboard-main`
     - Right sidebar: `mw-dashboard-right-sidebar`

### Debugging

1. Check the browser console for JavaScript errors
2. Check the PHP error log for server-side errors
3. Add debug logging in the `onSpecialPage_initList` hook if needed
4. Verify the special page appears in the list at `Special:SpecialPages`

### Testing

1. Verify the page loads without errors
2. Check that all widgets load correctly
3. Test responsive behavior at different screen sizes
4. Verify user permissions are enforced
