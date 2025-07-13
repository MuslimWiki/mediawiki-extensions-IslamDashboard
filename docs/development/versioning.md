# Versioning Guide

This document outlines the versioning strategy and release process for the IslamDashboard extension.

## Version Number Format

We follow [Semantic Versioning 2.0.0](https://semver.org/): `MAJOR.MINOR.PATCH`

- **MAJOR** version for incompatible API changes
- **MINOR** version for backward-compatible functionality
- **PATCH** version for backward-compatible bug fixes

## Release Process

1. **Create a Release Branch**
   ```bash
   git checkout -b release/vX.Y.Z
   ```

2. **Update Version Numbers**
   - Update `extension.json` version
   - Update `CHANGELOG.md`
   - Create `docs/releases/RELX_Y_Z.md`

3. **Test Thoroughly**
   - Run all unit tests
   - Perform manual testing
   - Verify documentation

4. **Commit Changes**
   ```bash
   git add .
   git commit -m "Prepare release vX.Y.Z"
   ```

5. **Tag the Release**
   ```bash
   git tag -a vX.Y.Z -m "Version X.Y.Z"
   ```

6. **Merge to Main**
   ```bash
   git checkout main
   git merge --no-ff release/vX.Y.Z
   git push origin main
   git push --tags
   ```

7. **Update Development Version**
   - Update version in `extension.json` to next development version (e.g., `X.Y.Z-dev`)
   - Commit and push changes

## Release Notes Format

Each release should include a `RELX_Y_Z.md` file in the `docs/releases/` directory with:

```markdown
# IslamDashboard vX.Y.Z Release Notes

**Release Date**: YYYY-MM-DD  
**Previous Version**: vX.Y.Z  
**Type**: Major|Minor|Patch

## Overview
Brief description of the release.

## Changes

### Added
- New features

### Changed
- Changes in existing functionality

### Deprecated
- Soon-to-be removed features

### Removed
- Removed features

### Fixed
- Bug fixes

### Security
- Security-related fixes

## Upgrade Notes
Any special upgrade instructions.

## Known Issues
List of known issues, if any.

## Credits
Contributors to this release.
```

## Changelog Maintenance

Update `CHANGELOG.md` with a summary of changes for each release, following the format:

```markdown
## [X.Y.Z] - YYYY-MM-DD
### Added
- New features

### Changed
- Changes in existing functionality

### Fixed
- Bug fixes
```
