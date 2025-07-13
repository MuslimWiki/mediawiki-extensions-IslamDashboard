# Create hooks directory if it doesn't exist
$hooksDir = "hooks"
if (-not (Test-Path -Path $hooksDir)) {
    New-Item -ItemType Directory -Path $hooksDir | Out-Null
    Write-Output "Created directory: $hooksDir"
}

# Move Hooks.php to hooks directory
$sourceFile = "Hooks.php"
$destFile = "$hooksDir/Hooks.php"
if (Test-Path -Path $sourceFile) {
    Move-Item -Path $sourceFile -Destination $destFile -Force
    Write-Output "Moved $sourceFile to $destFile"
} else {
    Write-Output "$sourceFile not found in current directory"
}

# Update extension.json to reference the new location
$extensionJsonPath = "extension.json"
if (Test-Path -Path $extensionJsonPath) {
    $content = Get-Content -Path $extensionJsonPath -Raw
    $updatedContent = $content -replace '"Hooks":', '"Hooks": "hooks/Hooks.php",' + "`n  "
    $updatedContent | Set-Content -Path $extensionJsonPath -NoNewline
    Write-Output "Updated $extensionJsonPath with new Hooks.php location"
}
