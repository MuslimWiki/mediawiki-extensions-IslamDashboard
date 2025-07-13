# Move and rename files to their appropriate locations

# Move ARCHITECTURE.md to docs/architecture/overview.md (if not already moved)
if (Test-Path "ARCHITECTURE.md") {
    Move-Item -Path "ARCHITECTURE.md" -Destination "docs/architecture/overview.md" -Force
}

# Move CONFIGURATION.md to docs/development/configuration.md (if not already moved)
if (Test-Path "CONFIGURATION.md") {
    Move-Item -Path "CONFIGURATION.md" -Destination "docs/development/configuration.md" -Force
}

# Move DEVELOPMENT.md to docs/development/guide.md (if not already moved)
if (Test-Path "DEVELOPMENT.md") {
    Move-Item -Path "DEVELOPMENT.md" -Destination "docs/development/guide.md" -Force
}

# Move and rename release files
if (Test-Path "RELEASE_0.2.0.md") {
    Move-Item -Path "RELEASE_0.2.0.md" -Destination "docs/releases/REL0_2_0.md" -Force
}

if (Test-Path "RELEASE-v0.2.1.md") {
    Move-Item -Path "RELEASE-v0.2.1.md" -Destination "docs/releases/REL0_2_1.md" -Force
}

# Move TESTING.md to docs/development/testing.md
if (Test-Path "TESTING.md") {
    Move-Item -Path "TESTING.md" -Destination "docs/development/testing.md" -Force
}

# Rename structure file to lowercase
if (Test-Path "docs/STRUCTURE.md") {
    Move-Item -Path "docs/STRUCTURE.md" -Destination "docs/structure.md" -Force
}

# If we have a temporary structure file, move it to the final location
if (Test-Path "docs/structure.md.tmp") {
    Move-Item -Path "docs/structure.md.tmp" -Destination "docs/structure.md" -Force
}
