# Islam Ecosystem Architecture

## Overview
The Islam Ecosystem is a collection of interoperable MediaWiki extensions designed to provide a comprehensive Islamic knowledge platform. This document outlines the architecture, components, and their relationships within the ecosystem.

## Core Components

### 1. IslamDashboard (Core)
- **Purpose**: Central hub for user activities and content management
- **Core Features**:
  - User profile management
  - Dashboard layout and widget system
  - Basic content contribution tracking
  - Notifications center
  - Basic security headers and input validation
  - API endpoints for core functionality
  - Theme and layout customization
  - User preferences storage

### 2. IslamSecurity (Security Layer)
- **Purpose**: Central security infrastructure for the Muslim.Wiki platform
- **Key Responsibilities**:
  - Rate limiting and API protection
  - Authentication security (brute force protection, 2FA)
  - Security headers and CSP management
  - Security monitoring and alerts
  - IP management (blocking/allowlisting)
  - Security.txt implementation
  - Security contact management
  - Audit logging
  - Session management

### 3. IslamSkin
- **Purpose**: The visual foundation for the Muslim.Wiki platform
- **Features**:
  - Responsive design for all devices
  - RTL language support
  - Prayer times display
  - Qibla direction indicator
  - Customizable color schemes
  - Theme system integration
  - Layout management

### 4. IslamAchievements (Future Extension)
- **Purpose**: Gamification and recognition system
- **Features**:
  - Badge and achievement system
  - Milestone tracking
  - Community recognition
  - Contribution incentives

### 5. IslamToDo (Future Extension)
- **Purpose**: Task and goal management
- **Features**:
  - Personal task lists
  - Content contribution goals
  - Reminders and due dates
  - Progress tracking

### 6. IslamBookmarks (Future Extension)
- **Purpose**: Content saving and organization
- **Features**:
  - Save articles and resources
  - Collection management
  - Tags and categories
  - Offline access (future)

## Feature Matrix by Component

### Core Features
| Feature | IslamDashboard | IslamSecurity | IslamSkin | Future Extensions |
|---------|----------------|----------------|-----------|-------------------|
| **User Interface** | Dashboard, Widgets | Security UI | Full UI Layer | Extension-specific UIs |
| **User Management** | Profile, Preferences | Auth, Sessions | - | Social features |
| **Content** | Basic tracking | - | - | Advanced content types |
| **Security** | Basic validation | Advanced security | - | - |
| **Integration** | Core APIs | Security APIs | Theming | Extension APIs |

### Security Responsibility Matrix
| Feature | IslamDashboard | IslamSecurity | Notes |
|---------|----------------|----------------|-------|
| Basic Security Headers | Implements | - | Core security headers |
| CSP Headers | Implements | Manages | Dashboard implements, Security manages policies |
| Input Validation | Basic | Advanced | Dashboard handles basic, Security handles complex |
| Rate Limiting | - | ✓ | Handled by IslamSecurity |
| Brute Force Protection | - | ✓ | Centralized protection |
| Security Logging | Basic logs | Comprehensive logs | IslamSecurity provides detailed security logging |
| Security Notifications | Displays | Generates | IslamSecurity generates, Dashboard displays |
| Security.txt | - | ✓ | Managed by IslamSecurity |
| IP Management | - | ✓ | Centralized IP blocking/allowlisting |

### Data Flow
1. All requests pass through IslamSecurity middleware
2. IslamSecurity performs security checks and logging
3. Valid requests are passed to IslamDashboard
4. Dashboard enforces additional security policies
5. Security events are logged and monitored

## Integration Architecture

### Core Integration Points
1. **Dashboard Widget System**
   - Extensions register widgets through WidgetFactory
   - Widgets are permission-aware and context-sensitive
   - Supports multiple widget types (info, interactive, data)

2. **Navigation System**
   - Centralized navigation management
   - Extensions can register menu items
   - Role-based access control for menu items
   - Context-aware menu rendering

3. **User Profile Integration**
   - Extensions can extend user profiles
   - Standard sections for achievements, activity, preferences
   - Responsive design for all profile components

4. **API Gateway**
   - Centralized API endpoint management
   - Standardized request/response formats
   - Rate limiting and authentication
   - Webhook support for real-time updates
   - Versioning and deprecation policies

### Data Flow
1. User interactions in one component can trigger updates in others
2. Centralized event bus for cross-extension communication
3. Shared authentication and permission system

## Technical Implementation

### Dependencies
- MediaWiki 1.43+
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Composer for dependency management

### Development Guidelines
1. **Coding Standards**
   - Follow MediaWiki coding conventions
   - PSR-4 autoloading
   - Semantic versioning

2. **Security**
   - Input validation and output escaping
   - CSRF protection
   - Rate limiting for API endpoints

3. **Performance**
   - Caching strategy
   - Lazy loading of assets
   - Database query optimization

## Future Extensions

### Core Extensions (Planned)
1. **IslamAchievements** (v0.5.0)
   - Badge and achievement system
   - Milestone tracking
   - Community recognition
   - Integration with dashboard widgets

2. **IslamToDo** (v0.5.0)
   - Task creation and management
   - Content contribution goals
   - Reminders and due dates
   - Progress tracking

3. **IslamBookmarks** (v0.6.0)
   - Save articles and resources
   - Collection management
   - Tags and categories
   - Offline access (future)

### Additional Extensions (Future)
4. **IslamSocial** (Future)
   - User messaging
   - Discussion forums
   - Content sharing
   - Community features

5. **IslamQuran** (Future)
   - Quran reader
   - Tafsir integration
   - Verse linking and sharing
   - Study tools

6. **IslamPrayer** (Future)
   - Prayer time calculations
   - Adhan notifications
   - Qibla direction
   - Prayer journal

## Versioning and Compatibility
- Each extension is versioned independently
- Core compatibility matrix maintained for all components
- Backward compatibility maintained for at least 2 major versions

## Contribution Guidelines
- Documentation first approach
- Unit and integration tests required
- Code review process
- Security review for all new features

## Support and Maintenance
- Community support forums
- Regular security updates
- Documentation updates with each release
