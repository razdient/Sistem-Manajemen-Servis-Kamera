$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $root

$script:hasError = $false

function Write-Ok($message) {
    Write-Host "[OK]   $message" -ForegroundColor Green
}

function Write-Warn($message) {
    Write-Host "[WARN] $message" -ForegroundColor Yellow
}

function Write-Err($message) {
    Write-Host "[ERR]  $message" -ForegroundColor Red
    $script:hasError = $true
}

function Find-Php {
    $command = Get-Command php -ErrorAction SilentlyContinue
    if ($command) {
        return $command.Source
    }

    $laragonRoot = 'C:\laragon\bin\php'
    if (Test-Path $laragonRoot) {
        $candidate = Get-ChildItem $laragonRoot -Directory -Filter 'php-*' -ErrorAction SilentlyContinue |
            Sort-Object Name -Descending |
            ForEach-Object { Join-Path $_.FullName 'php.exe' } |
            Where-Object { Test-Path $_ } |
            Select-Object -First 1

        if ($candidate) {
            return $candidate
        }
    }

    return $null
}

Write-Host "=== Cek Kebutuhan CameraService ===" -ForegroundColor Cyan
Write-Host "Folder: $root"
Write-Host ""

$php = Find-Php
if (-not $php) {
    Write-Err 'PHP tidak ditemukan. Install PHP 8.3+ atau Laragon.'
    exit 1
}

Write-Ok "PHP ditemukan: $php"
& $php -r "echo 'Versi PHP: '.PHP_VERSION.PHP_EOL;"

$version = & $php -r "echo PHP_VERSION;"
if ([version]$version -lt [version]'8.3.0') {
    Write-Err "PHP $version terlalu lama. Minimal PHP 8.3."
} else {
    Write-Ok "Versi PHP memenuhi syarat (8.3+)."
}

$modules = & $php -m
$requiredExtensions = @(
    'pdo_sqlite',
    'sqlite3',
    'mbstring',
    'openssl',
    'fileinfo',
    'curl',
    'dom',
    'xml',
    'tokenizer',
    'xmlwriter',
    'gd'
)

foreach ($extension in $requiredExtensions) {
    if ($modules -contains $extension) {
        Write-Ok "Extension $extension tersedia."
    } else {
        Write-Err "Extension $extension belum aktif."
    }
}

Write-Host ""

if (Test-Path (Join-Path $root '.env')) {
    Write-Ok '.env tersedia.'
} else {
    Write-Warn '.env belum ada. Buat dari .env.example lalu jalankan php artisan key:generate.'
}

if (Test-Path (Join-Path $root 'vendor\autoload.php')) {
    Write-Ok 'Dependency Composer (vendor) tersedia.'
} else {
    Write-Warn 'Folder vendor belum ada. Jalankan: composer install --no-dev --optimize-autoloader'
}

if (Test-Path (Join-Path $root 'public\build\manifest.json')) {
    Write-Ok 'Asset frontend (public/build) tersedia.'
} else {
    Write-Warn 'Asset frontend belum dibangun. Jalankan: npm install && npm run build'
}

if (Test-Path (Join-Path $root 'database\database.sqlite')) {
    Write-Ok 'Database SQLite tersedia.'
} else {
    Write-Warn 'Database SQLite belum ada. Jalankan: php artisan migrate --force'
}

$listener = Get-NetTCPConnection -LocalPort 8000 -State Listen -ErrorAction SilentlyContinue
if ($listener) {
    Write-Warn 'Port 8000 sedang dipakai. start-cameraservice.bat akan mencoba membersihkan server PHP lama.'
} else {
    Write-Ok 'Port 8000 kosong.'
}

Write-Host ""
if ($script:hasError) {
    Write-Host 'Hasil: ada kebutuhan yang belum terpenuhi.' -ForegroundColor Red
    exit 1
}

Write-Host 'Hasil: semua kebutuhan utama terpenuhi.' -ForegroundColor Green
exit 0
