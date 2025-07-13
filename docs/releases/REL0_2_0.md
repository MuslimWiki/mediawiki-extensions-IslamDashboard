# IslamDashboard v0.2.0 Release Notes

## 🚀 Modernized Hook System & Enhanced Testing

We're excited to announce the release of IslamDashboard v0.2.0! This update focuses on modernizing our codebase and improving test coverage to ensure a more stable and maintainable extension.

### 🔧 What's New

#### Modernized Hook System
- **Deprecated `$wgHooks` removed** - Updated to use MediaWiki's modern `HookContainer` system
- **Improved performance** - More efficient hook handling with the latest MediaWiki standards
- **Better maintainability** - Cleaner codebase following current best practices

#### Enhanced Testing
- **New test suite** - Added comprehensive tests for hook registration and behavior
- **Improved test coverage** - Expanded tests for navigation and widget systems
- **New testing helpers** - Added `DummyServicesTrait` for easier test setup

#### Documentation
- **Updated documentation** - Complete rewrite of hook usage documentation
- **Testing guidelines** - New `TESTING.md` with detailed instructions for contributors
- **API Reference** - Enhanced documentation for developers extending the dashboard

### 🛠️ Technical Details

- **Requires MediaWiki 1.43+** - Takes advantage of the latest MediaWiki features
- **Backward Compatibility** - Maintains compatibility with existing widget implementations
- **Code Quality** - Improved code organization and structure

### 📦 Installation & Upgrade

```bash
# For new installations
composer require mediawiki/islam-dashboard "0.2.0"

# For upgrades
composer update mediawiki/islam-dashboard --with-all-dependencies
```

### 📚 Documentation

Full documentation is available in the [GitHub repository](https://github.com/MuslimWiki/IslamDashboard).

### 🙏 Credits

Thank you to all contributors who helped make this release possible!

---

*For a complete list of changes, see the [CHANGELOG.md](https://github.com/MuslimWiki/IslamDashboard/blob/master/CHANGELOG.md).*
