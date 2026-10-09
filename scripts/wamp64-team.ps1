[CmdletBinding()]
param(
    [ValidateSet('Check', 'Setup', 'Sync')]
    [string] $Action = 'Check',
    [string] $ProjectRoot = (Split-Path -Parent $PSScriptRoot),
    [string] $WampRoot = 'C:\wamp64'
)

$ErrorActionPreference = 'Stop'
$ProjectRoot = [System.IO.Path]::GetFullPath($ProjectRoot)
$RepositoryRoot = Split-Path -Parent $PSScriptRoot
$LocalConfig = Join-Path $ProjectRoot 'wp-config.local.php'
$TrackedConfig = Join-Path $ProjectRoot 'wp-config.php'

function Read-Setting([string] $Prompt, [string] $Default) {
    $value = Read-Host "$Prompt [$Default]"
    if ([string]::IsNullOrWhiteSpace($value)) { return $Default }
    return $value.Trim()
}

function ConvertTo-PhpLiteral([string] $Value) {
    if ($Value.Contains("`r") -or $Value.Contains("`n")) {
        throw 'Configuration values cannot contain line breaks.'
    }
    return "'" + $Value.Replace('\', '\\').Replace("'", "\'") + "'"
}

function New-WordPressSalt {
    $bytes = New-Object byte[] 48
    $random = [System.Security.Cryptography.RandomNumberGenerator]::Create()
    try { $random.GetBytes($bytes) } finally { $random.Dispose() }
    return [Convert]::ToBase64String($bytes)
}

function Get-SecureText([string] $Prompt) {
    $secure = Read-Host $Prompt -AsSecureString
    $pointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($secure)
    try { return [Runtime.InteropServices.Marshal]::PtrToStringBSTR($pointer) }
    finally { [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($pointer) }
}

function Ensure-LocalConfig {
    if (Test-Path -LiteralPath $LocalConfig) {
        Write-Host 'Existing wp-config.local.php found; preserving it.'
        return
    }

    if (!(Test-Path -LiteralPath $TrackedConfig)) {
        throw "Missing wp-config.php in $ProjectRoot."
    }

    $current = [System.IO.File]::ReadAllText($TrackedConfig)
$legacyConfig = $current -match "define\s*\(\s*'DB_NAME'" -and $current -match "define\s*\(\s*'AUTH_KEY'"
    if ($legacyConfig) {
        Copy-Item -LiteralPath $TrackedConfig -Destination $LocalConfig
        Write-Host 'Copied the existing WordPress configuration to ignored wp-config.local.php.'
        return
    }

    Write-Host 'Creating a new local configuration. Use the DB values for this WAMP installation.'
    $dbName = Read-Setting 'Database name' 'cms_job_design'
    $dbUser = Read-Setting 'Database user' 'root'
    $dbHost = Read-Setting 'Database host' 'localhost'
    $dbPassword = Get-SecureText 'Database password (press Enter if blank)'
    foreach ($value in @($dbName, $dbUser, $dbHost, $dbPassword)) {
        if ($value.Contains("`r") -or $value.Contains("`n")) { throw 'Configuration values cannot contain line breaks.' }
    }

    $lines = @(
        '<?php',
        '// Machine-specific WordPress settings. This file is ignored by Git.',
        "define( 'DB_NAME', $(ConvertTo-PhpLiteral $dbName) );",
        "define( 'DB_USER', $(ConvertTo-PhpLiteral $dbUser) );",
        "define( 'DB_PASSWORD', $(ConvertTo-PhpLiteral $dbPassword) );",
        "define( 'DB_HOST', $(ConvertTo-PhpLiteral $dbHost) );",
        "define( 'DB_CHARSET', 'utf8mb4' );",
        "define( 'DB_COLLATE', '' );",
        "define( 'AUTH_KEY', $(ConvertTo-PhpLiteral (New-WordPressSalt)) );",
        "define( 'SECURE_AUTH_KEY', $(ConvertTo-PhpLiteral (New-WordPressSalt)) );",
        "define( 'LOGGED_IN_KEY', $(ConvertTo-PhpLiteral (New-WordPressSalt)) );",
        "define( 'NONCE_KEY', $(ConvertTo-PhpLiteral (New-WordPressSalt)) );",
        "define( 'AUTH_SALT', $(ConvertTo-PhpLiteral (New-WordPressSalt)) );",
        "define( 'SECURE_AUTH_SALT', $(ConvertTo-PhpLiteral (New-WordPressSalt)) );",
        "define( 'LOGGED_IN_SALT', $(ConvertTo-PhpLiteral (New-WordPressSalt)) );",
        "define( 'NONCE_SALT', $(ConvertTo-PhpLiteral (New-WordPressSalt)) );",
        "`$table_prefix = 'wp_';",
        "define( 'WP_DEBUG', false );",
        "if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }",
        "require_once ABSPATH . 'wp-settings.php';",
        ''
    )
    $content = $lines -join "`n"
    $stream = [System.IO.File]::Open($LocalConfig, [System.IO.FileMode]::CreateNew, [System.IO.FileAccess]::Write, [System.IO.FileShare]::None)
    try {
        $bytes = [System.Text.UTF8Encoding]::new($false).GetBytes($content)
        $stream.Write($bytes, 0, $bytes.Length)
    } finally { $stream.Dispose() }
    Write-Host 'Created wp-config.local.php. The database is not created or imported by Setup.'
}

function Get-WampExecutable([string] $RelativePath, [string] $FileName, [string] $Subdirectory = '') {
    $base = Join-Path $WampRoot $RelativePath
    if (!(Test-Path -LiteralPath $base)) { return $null }
    $candidate = Get-ChildItem -LiteralPath $base -Directory -ErrorAction SilentlyContinue |
        Sort-Object Name -Descending |
        ForEach-Object { if ($Subdirectory) { Join-Path (Join-Path $_.FullName $Subdirectory) $FileName } else { Join-Path $_.FullName $FileName } } |
        Where-Object { Test-Path -LiteralPath $_ } |
        Select-Object -First 1
    return $candidate
}

function Invoke-WordPressAudit([string] $PhpExe) {
    $checkFile = Join-Path $RepositoryRoot 'database\team-check.php'
    if (!(Test-Path -LiteralPath $checkFile)) { throw "Missing $checkFile." }
    $previousRoot = $env:CMS_WORDPRESS_ROOT
    try {
        $env:CMS_WORDPRESS_ROOT = $ProjectRoot
        $raw = & $PhpExe $checkFile 2>&1
        if ($LASTEXITCODE -ne 0) { throw "WordPress audit failed: $($raw -join [Environment]::NewLine)" }
        $report = ($raw -join [Environment]::NewLine) | ConvertFrom-Json
        return $report
    } finally { $env:CMS_WORDPRESS_ROOT = $previousRoot }
}

function Read-PhpSingleQuoted([string] $Name, [string] $ConfigText) {
    $escapedName = [regex]::Escape($Name)
$pattern = "(?m)^\s*define\s*\(\s*'$escapedName'\s*,\s*'((?:\\.|[^'\\])*)'\s*\)"
    $match = [regex]::Match($ConfigText, $pattern)
    if (!$match.Success) { throw "Could not read $Name from wp-config.local.php; expected a single-quoted define." }
    $inputText = $match.Groups[1].Value
    $builder = New-Object System.Text.StringBuilder
    for ($i = 0; $i -lt $inputText.Length; $i++) {
        if ($inputText[$i] -eq '\' -and $i + 1 -lt $inputText.Length) {
            $next = $inputText[$i + 1]
            if ($next -eq '\' -or $next -eq "'") {
                [void]$builder.Append($next)
                $i++
                continue
            }
        }
        [void]$builder.Append($inputText[$i])
    }
    return $builder.ToString()
}

function ConvertTo-MySqlOption([string] $Value) {
    if ($Value.Contains("`r") -or $Value.Contains("`n")) { throw 'MySQL connection values cannot contain line breaks.' }
    return '"' + $Value.Replace('\', '\\').Replace('"', '\"') + '"'
}

if (!(Test-Path -LiteralPath $ProjectRoot)) { throw "Project root does not exist: $ProjectRoot" }
if (!(Test-Path (Join-Path $ProjectRoot 'wp-load.php')) -or !(Test-Path (Join-Path $ProjectRoot 'wp-content\themes\jobscout'))) {
    throw 'The selected root is not this WordPress JobScout project; no files or database were changed.'
}

if ($Action -eq 'Setup') {
    Ensure-LocalConfig
    Write-Host 'Setup complete. No database was created, imported, or modified.'
    exit 0
}

if (!(Test-Path -LiteralPath $LocalConfig)) {
    throw 'wp-config.local.php is missing. Run this script with -Action Setup first.'
}
$phpExe = Get-WampExecutable 'bin\php' 'php.exe'
if (!$phpExe) { throw "Could not find php.exe under $WampRoot\bin\php." }

$report = Invoke-WordPressAudit $phpExe
$report | ConvertTo-Json -Depth 6
if ($Action -eq 'Check') { exit 0 }
if (!$report.wp_job_manager_active) { throw 'WP Job Manager is not active; CMS sync stopped before backup or writes.' }
if ($report.theme -ne 'jobscout') { throw 'JobScout is not the active theme; CMS sync stopped before backup or writes.' }
if (!$report.home_is_front_page) { Write-Warning 'Home is not configured as the published front page; existing settings were preserved.' }

$mysqlDump = Get-WampExecutable 'bin\mysql' 'mysqldump.exe' 'bin'
if (!$mysqlDump) { throw "Could not find mysqldump.exe under $WampRoot\bin\mysql." }
$configText = [System.IO.File]::ReadAllText($LocalConfig)
$dbName = Read-PhpSingleQuoted 'DB_NAME' $configText
$dbUser = Read-PhpSingleQuoted 'DB_USER' $configText
$dbPassword = Read-PhpSingleQuoted 'DB_PASSWORD' $configText
$dbHost = Read-PhpSingleQuoted 'DB_HOST' $configText
if ([string]::IsNullOrWhiteSpace($dbName)) { throw 'DB_NAME is empty; sync stopped.' }

$hostName = $dbHost
$portLine = ''
if ($dbHost -match '^(?<host>[^:]+):(?<port>[0-9]+)$') {
    $hostName = $Matches.host
    $portLine = "port=$($Matches.port)`n"
}
$clientFile = Join-Path $env:TEMP ('cms-job-design-' + [guid]::NewGuid().ToString('N') + '.cnf')
$backupName = 'cms_job_design-backup-' + (Get-Date -Format 'yyyyMMdd-HHmmss') + '-' + [guid]::NewGuid().ToString('N').Substring(0, 8) + '.sql'
$backupPath = Join-Path $env:TEMP $backupName
$clientText = "[client]`nuser=$(ConvertTo-MySqlOption $dbUser)`npassword=$(ConvertTo-MySqlOption $dbPassword)`nhost=$(ConvertTo-MySqlOption $hostName)`n$portLine"
[System.IO.File]::WriteAllText($clientFile, $clientText, [System.Text.UTF8Encoding]::new($false))
try {
    & $mysqlDump "--defaults-extra-file=$clientFile" '--single-transaction' '--quick' '--no-tablespaces' $dbName "--result-file=$backupPath"
    if ($LASTEXITCODE -ne 0 -or !(Test-Path -LiteralPath $backupPath) -or (Get-Item -LiteralPath $backupPath).Length -eq 0) {
        throw 'Database backup failed; CMS sync was not run.'
    }
    Write-Host "Database backup saved outside the repository: $backupPath"

    $seedFile = Join-Path $RepositoryRoot 'database\seed-cms-content.php'
    if (!(Test-Path -LiteralPath $seedFile)) { throw "Missing seed file: $seedFile" }
    $previousRoot = $env:CMS_WORDPRESS_ROOT
    try {
        $env:CMS_WORDPRESS_ROOT = $ProjectRoot
        & $phpExe $seedFile
        if ($LASTEXITCODE -ne 0) { throw 'CMS seed failed. The pre-sync backup was retained.' }
    } finally { $env:CMS_WORDPRESS_ROOT = $previousRoot }

    $after = Invoke-WordPressAudit $phpExe
    Write-Host 'Post-sync CMS check:'
    $after | ConvertTo-Json -Depth 6
    Write-Host 'Sync uses WordPress APIs and only creates missing shared records. It does not import database/cms_job_design.sql.'
} finally {
    if (Test-Path -LiteralPath $clientFile) { Remove-Item -LiteralPath $clientFile -Force }
}
