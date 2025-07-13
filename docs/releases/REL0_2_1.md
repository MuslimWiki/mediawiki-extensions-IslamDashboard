# IslamDashboard v0.2.1 Release Notes

## Fixed
- **Dashboard Layout**: Resolved issue where main content appeared below the navigation instead of beside it
- **CSS Output**: Eliminated raw CSS output in the page by moving styles to dedicated LESS files
- **HTML Structure**: Corrected HTML structure for proper flexbox layout and nesting

## Changed
- **Code Organization**: Moved all inline styles to dedicated LESS files for better maintainability
- **Resource Management**: Updated ResourceLoader configuration for optimized style loading
- **Responsive Design**: Improved responsive behavior across different screen sizes
- **Performance**: Enhanced rendering performance with optimized CSS selectors

## Technical Details
- **Version Bump**: 0.2.0 → 0.2.1 (Patch version bump for bug fixes)
- **Compatibility**: Maintains compatibility with MediaWiki 1.43+
- **Dependencies**: No new dependencies added

## Upgrade Instructions
1. Pull the latest changes from the repository
2. Run `composer update` to ensure all dependencies are up to date
3. Clear your browser cache to ensure new styles are loaded

## Known Issues
- Some widget functionality may require additional updates (to be addressed in future releases)
- Mobile navigation menu may need further refinement

## Contributors
- @[Your GitHub Username] - Lead developer

## Full Changelog
See [CHANGELOG.md](CHANGELOG.md) for a complete list of changes.
