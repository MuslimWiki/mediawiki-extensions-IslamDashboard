# Changelog

All notable changes to the IslamDashboard extension will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.2.1] - 2025-07-13
### Fixed
- Fixed dashboard layout to properly display main content next to sidebar navigation
- Resolved issue with raw CSS being output in the page
- Corrected HTML structure for proper flexbox layout

### Changed
- Moved all inline styles to dedicated LESS files
- Updated ResourceLoader configuration for better style management
- Improved responsive behavior of dashboard components

## [Unreleased]
### Changed
- Updated hook system to use HookContainer instead of $wgHooks
- Improved test coverage for navigation and widget systems
- Enhanced documentation for hook usage and testing

## [0.1.0] - 2025-07-12
### Added
- Comprehensive widget system with Mustache template support
- Resource organization following MediaWiki conventions
- Documentation for widget development and theming
- Unit tests for core components
- PHPUnit test suite for server-side components
- QUnit tests for client-side JavaScript
- Widget configuration system
- Internationalization support for all user-facing text
- Comprehensive API documentation
- Development and contribution guidelines

### Changed
- Migrated all widget resources to `resources/widgets/`
- Organized widget styles in `resources/styles/widgets/`
- Updated all widget classes to use Mustache templates
- Improved error handling and validation
- Enhanced widget registration system
- Updated documentation to reflect new architecture
- Optimized resource loading
- Improved code organization and structure
- Standardized coding style across the codebase

## [0.0.2] - 2025-07-11
https://github.com/MuslimWiki/IslamDashboard/compare/v0.0.1...v0.0.2
### Added
- Comprehensive navigation system with collapsible sections
- NavigationManager for managing navigation structure and permissions
- NavigationRenderer for generating accessible HTML
- Client-side JavaScript for interactive navigation
- Responsive design for mobile and desktop views
- User preference persistence for navigation state
- Integration points for widgets and extensions
- Documentation for the navigation system

### Changed
- Updated SpecialDashboard to use the new navigation system
- Improved accessibility with ARIA attributes
- Enhanced user experience with smooth animations

## [0.0.1] - 2025-07-10
https://github.com/MuslimWiki/IslamDashboard/releases/tag/v0.0.1
### Added
- Initial release of IslamDashboard
- Basic dashboard layout with widget support
- Example widgets (Welcome, Recent Activity, Quick Actions)
- Widget management system
- Basic theming and styling
- Extension framework and architecture
- Documentation and configuration guides
- Internationalization support

