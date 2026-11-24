@echo off
setlocal EnableExtensions
cd /d "%~dp0"

if not exist "database\database.sqlite" (
    echo [ERROR] database\database.sqlite tidak ditemukan.
    pause
    exit /b 1
)

if not exist "database\backups" mkdir "database\backups"

for /f "delims=" %%I in ('powershell -NoProfile -Command "Get-Date -Format yyyyMMdd-HHmmss"') do set "STAMP=%%I"

copy /Y "database\database.sqlite" "database\backups\database-%STAMP%.sqlite" >nul
if errorlevel 1 (
    echo [ERROR] Backup gagal.
    pause
    exit /b 1
)

echo Backup database berhasil:
echo database\backups\database-%STAMP%.sqlite
echo.
pause
