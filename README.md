# IslamDashboard

[![License: GPL-3.0-or-later](https://img.shields.io/badge/License-GPL%20v3+-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![MediaWiki: 1.43+](https://img.shields.io/badge/MediaWiki-1.43%2B-blue.svg)](https://www.mediawiki.org/)
[![Version](https://img.shields.io/badge/Version-0.3.1-blue)](CHANGELOG.md)
[![Code Style](https://img.shields.io/badge/code%20style-MediaWiki-brightgreen)](https://www.mediawiki.org/wiki/Manual:Coding_conventions)
[![Requires IslamCore](https://img.shields.io/badge/Requires-IslamCore-orange)](https://github.com/MuslimWiki/mediawiki-extensions-IslamCore)

A modern, extensible user dashboard extension for MediaWiki, featuring modular widgets, responsive navigation, and seamless integration with the Islam Skin. The dashboard provides users with a personalized interface to access important information and perform common tasks efficiently.

## 📋 Table of Contents

- [Features](#-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Project Structure](#-project-structure)
- [Development](#-development)
- [Testing](#-testing)
- [Documentation](#-documentation)
- [Contributing](#-contributing)
- [License](#-license)
- [Acknowledgments](#-acknowledgments)

## ✨ Features

- **User-Centric Dashboard**: Personalized dashboard for each user
- **Built on IslamCore**: Leverages the powerful IslamCore framework for shared services and security
- **Extensible Widget System**: Modular widgets with Mustache templates
- **Responsive Navigation**: Collapsible navigation system
- **Dark Mode**: Built-in theme support
- **Customizable Layout**: Widget arrangement and preferences
- **Activity Tracking**: Monitor user contributions and recent activity
- **Quick Actions**: Easy access to common tasks
- **Internationalization**: Full i18n support with RTL language support
- **Accessibility**: WCAG 2.1 compliant components

## 🚀 Requirements

- PHP 8.0+
- MediaWiki 1.43+
- [IslamCore](https://github.com/MuslimWiki/mediawiki-extensions-IslamCore) (required)
- [Codex](https://doc.wikimedia.org/codex/) (for UI components)
- [Islam Skin](https://www.mediawiki.org/wiki/Skin:Islam) (recommended)
- Composer (for development)
- Node.js 16+ (for frontend development)

> **Note**: For development, ensure your environment meets all the requirements. The extension follows MediaWiki's coding standards and best practices for version 1.43+.

## 📥 Installation

### Prerequisites

1. First, install and enable [IslamCore](https://github.com/MuslimWiki/mediawiki-extensions-IslamCore) extension

### Installing IslamDashboard

1. Clone this repository into your `extensions/` directory:
   ```bash
   git clone https://github.com/MuslimWiki/IslamDashboard.git extensions/IslamDashboard
   ```

2. Add the following to your `LocalSettings.php`:
   ```php
   // Load IslamCore first
   wfLoadExtension( 'IslamCore' );

   // Then load IslamDashboard
   wfLoadExtension( 'IslamDashboard' );
   ```

3. Run the update script:
   ```bash
   php maintenance/update.php
   ```

4. Clear your browser cache and the MediaWiki cache:
   ```bash
   php maintenance/rebuildLocalisationCache.php
   php maintenance/runJobs.php
   ```

5. The dashboard will be available at `Special:IslamDashboard` for users with the appropriate permissions.

## 🔧 Configuration

After installation, you can configure the dashboard by adding the following to your LocalSettings.php:

```php
// Load IslamCore first
wfLoadExtension( 'IslamCore' );

// Then load IslamDashboard
wfLoadExtension( 'IslamDashboard' );
```

### Available Configuration Options

> **Note**: Many core configurations are now handled by IslamCore. Refer to the [IslamCore documentation](https://github.com/MuslimWiki/mediawiki-extensions-IslamCore) for more details.

## 🏗️ Project Structure

```
extensions/IslamDashboard/
├── docs/                     # Comprehensive documentation
│   ├── api/                 # API documentation
│   │   ├── endpoints.md     # API endpoints reference
│   │   └── reference.md     # API reference
│   ├── architecture/        # System architecture
│   │   ├── navigation.md    # Navigation system
│   │   └── overview.md      # High-level architecture
│   ├── development/         # Development guides
│   │   ├── configuration.md # Configuration options
│   │   ├── i18n.md         # Internationalization guide
│   │   ├── special_pages.md # Special pages documentation
│   │   ├── testing.md      # Testing guide
│   │   ├── versioning.md   # Versioning process
│   │   └── widgets.md      # Widget development
│   └── releases/           # Release notes
│       └── REL0_3_0.md     # Version 0.3.0 release notes
├── i18n/                    # Internationalization files
├── resources/              # Frontend assets
│   ├── modules/           # Core JavaScript modules
│   └── styles/            # Core styles and theming
│       └── widgets/       # Widget-specific styles
├── src/                   # PHP source code (PSR-4 autoloaded)
│   ├── Hooks/            # Hook handlers
│   ├── Navigation/       # Navigation system
│   ├── Special/          # Special pages
│   ├── Widgets/          # Widget implementations
│   └── WidgetManager.php # Widget management
├── templates/            # Mustache templates
│   └── widgets/         # Widget templates
├── tests/                # Test suites
│   ├── phpunit/         # PHPUnit tests
│   └── qunit/           # QUnit tests
├── CHANGELOG.md         # Version history
├── composer.json        # PHP dependencies
├── extension.json       # Extension manifest
└── README.md            # This file
```

## 🛠️ Development

### Prerequisites

- PHP 8.0+
- Composer
- Node.js 16+ and npm
- MediaWiki 1.43+

### Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/MuslimWiki/IslamDashboard.git
   cd IslamDashboard
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install frontend dependencies:
   ```bash
   npm install
   ```

## 🧪 Testing

### PHPUnit Tests
Run the PHPUnit test suite:
```bash
composer test
```

### QUnit Tests
Run the JavaScript test suite:
```bash
npm test
```

### Building Assets
Compile frontend assets for production:
```bash
npm run build
```

### Development Server
For frontend development with hot-reload:
```bash
npm run dev
```

## 🚀 Quick Start

1. **Install the Extension**:
   - Add to `LocalSettings.php`:
     ```php
     // Load IslamCore first
     wfLoadExtension( 'IslamCore' );

     // Then load IslamDashboard
     wfLoadExtension( 'IslamDashboard' );
     ```
   - Run the update script:
     ```bash
     php maintenance/update.php
     ```

2. **Access the Dashboard**:
   - Visit `Special:Dashboard` on your wiki
   - Or use the dashboard link in the user menu (if enabled in preferences)

3. **Manage Widgets**:
   - Click "Add Widget" to add new widgets
   - Drag and drop to rearrange widgets
   - Use the widget menu to configure or remove widgets
   - Click "Save Layout" to persist your changes

4. **Customization**:
   - Toggle between light/dark mode (if supported by your skin)
   - Configure dashboard preferences in your user preferences
   - Create custom widgets by extending the `DashboardWidget` class

## 📚 Documentation

For detailed documentation, please see the [docs](docs/) directory.

### Architecture

IslamDashboard is built on top of the IslamCore framework, which provides essential services and security features. The architecture follows these principles:

1. **Modular Design**: Components are loosely coupled and communicate through well-defined interfaces
2. **Separation of Concerns**: Clear distinction between presentation, business logic, and data access
3. **Extensibility**: Easy to add new features through hooks and extensions
4. **Security**: Built on IslamCore's security model with proper input validation and output escaping

### Migration from Previous Versions

If you're upgrading from a version prior to 0.3.1, please see the [Migration Guide](docs/MIGRATION.md) for important changes and upgrade instructions.
| [Configuration](docs/development/configuration.md) | Configuration options and customization |
| [Development Guide](docs/development/guide.md) | Setting up a development environment |
| [Navigation System](docs/architecture/navigation.md) | Navigation system specifications |
| [Special Pages](docs/development/special_pages.md) | Documentation for special pages |
| [Testing](docs/development/testing.md) | Testing guidelines and procedures |
| [Versioning](docs/development/versioning.md) | Versioning and release process |
| [Internationalization](docs/development/i18n.md) | Internationalization and localization guide |

## 🤝 Contributing

We welcome contributions from the community! Please follow these steps to contribute:

1. Read our [Contributing Guide](docs/DEVELOPMENT.md#contributing)
2. Check the [issue tracker](https://github.com/MuslimWiki/IslamDashboard/issues) for open issues
3. Fork the repository and create a feature branch
4. Submit a pull request

### Pull Request Guidelines

- Follow the [MediaWiki coding conventions](https://www.mediawiki.org/wiki/Manual:Coding_conventions)
- Write clear commit messages
- Add/update tests for new features
- Update documentation as needed
- Ensure all tests pass

## 📄 License

This project is licensed under the [GNU General Public License v3.0 or later](LICENSE).

## 🙏 Acknowledgments

- The MuslimWiki community for their support and feedback
- MediaWiki developers for the powerful extension system
- All contributors who have helped improve this project

## 💬 Get Help

- [Report an Issue](https://github.com/MuslimWiki/IslamDashboard/issues)
- [Community Forum](https://www.muslim.wiki/forum)
- [Documentation](https://www.muslim.wiki/IslamDashboard/docs)

---

Built with ❤️ by the [MuslimWiki Foundation](https://www.muslim.wiki)
