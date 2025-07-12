# IslamDashboard

[![License: GPL-3.0-or-later](https://img.shields.io/badge/License-GPL%20v3+-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![MediaWiki: 1.43+](https://img.shields.io/badge/MediaWiki-1.43%2B-blue.svg)](https://www.mediawiki.org/)
[![Version](https://img.shields.io/badge/Version-0.1.0-blue)](CHANGELOG.md)
[![Code Style](https://img.shields.io/badge/code%20style-MediaWiki-brightgreen)](https://www.mediawiki.org/wiki/Manual:Coding_conventions)

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
- **Extensible Widget System**: Modular widgets with Mustache templates
- **Responsive Navigation**: Collapsible navigation system
- **Dark Mode**: Built-in theme support
- **Customizable Layout**: Widget arrangement and preferences
- **Activity Tracking**: Monitor user contributions and recent activity
- **Quick Actions**: Easy access to common tasks
- **Internationalization**: Full i18n support
- **Accessibility**: WCAG 2.1 compliant components

## 🚀 Requirements

- PHP 8.0+
- MediaWiki 1.43+
- [Codex](https://doc.wikimedia.org/codex/) (for UI components)
- [Islam Skin](https://www.mediawiki.org/wiki/Skin:Islam) (recommended)
- Composer (for development)
- Node.js 16+ (for frontend development)

## 📥 Installation

1. Clone this repository into your `extensions/` directory:
   ```bash
   git clone https://github.com/MuslimWiki/IslamDashboard.git extensions/IslamDashboard
   ```

2. Add the following to your `LocalSettings.php`:
   ```php
   wfLoadExtension( 'IslamDashboard' );
   ```

3. Run the update script:
   ```bash
   php maintenance/update.php
   ```

## 🏗️ Project Structure

```
extensions/IslamDashboard/
├── docs/                     # Comprehensive documentation
│   ├── API_REFERENCE.md     # API documentation
│   ├── ARCHITECTURE.md      # System architecture
│   ├── CONFIGURATION.md     # Configuration options
│   ├── DEVELOPMENT.md       # Development guide
│   ├── NAVIGATION_SPEC.md   # Navigation system specs
│   └── WIDGETS.md           # Widget development guide
├── i18n/                    # Internationalization files
├── includes/                # Core PHP classes
│   ├── Navigation/         # Navigation system
│   └── Widgets/            # Widget implementations
├── resources/               # Frontend assets
│   ├── modules/            # Core JavaScript modules
│   ├── styles/             # Core styles and theming
│   │   └── widgets/        # Widget-specific styles
│   └── widgets/            # Widget-specific JavaScript
├── templates/               # Mustache templates
│   └── widgets/            # Widget templates
├── tests/                   # Test suites
│   ├── phpunit/           # PHPUnit tests
│   └── qunit/             # QUnit tests
├── CHANGELOG.md            # Version history
├── composer.json           # PHP dependencies
├── extension.json          # Extension manifest
└── README.md               # This file
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

Comprehensive documentation is available in the `docs/` directory:

| Document | Description |
|----------|-------------|
| [ARCHITECTURE.md](docs/ARCHITECTURE.md) | System architecture and design decisions |
| [WIDGETS.md](docs/WIDGETS.md) | Guide to creating and customizing widgets |
| [API_REFERENCE.md](docs/API_REFERENCE.md) | Available APIs, hooks, and events |
| [CONFIGURATION.md](docs/CONFIGURATION.md) | Configuration options and customization |
| [DEVELOPMENT.md](docs/DEVELOPMENT.md) | Setting up a development environment |
| [NAVIGATION_SPEC.md](docs/NAVIGATION_SPEC.md) | Navigation system specifications |

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
