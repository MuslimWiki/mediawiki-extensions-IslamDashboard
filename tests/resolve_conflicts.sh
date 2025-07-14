#!/bin/bash
# This script will help resolve merge conflicts by keeping the version from the feature branch

# List of files with conflicts
FILES=(
  "CHANGELOG.md"
  "SpecialDashboard.php"
  "extension.json"
  "src/Widgets/DashboardWidget.php"
  "src/Widgets/RecentActivityWidget.php"
  "src/Widgets/WelcomeWidget.php"
  "resources/modules/ext.islamDashboard.js"
  "resources/modules/ext.islamDashboard.navigation.js"
  "resources/modules/ext.islamDashboard.navigation.less"
  "templates/Dashboard.php"
)

# Checkout the version from feature/dashboard-layout-fix for each file
for file in "${FILES[@]}"; do
  echo "Resolving conflicts in $file..."
  git checkout feature/dashboard-layout-fix -- "$file"
  git add "$file"
done

echo "All conflicts resolved. Ready to commit."
