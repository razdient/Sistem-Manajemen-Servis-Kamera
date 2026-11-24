@echo off
setlocal EnableExtensions
cd /d "%~dp0"

set "PHP_EXE="

where php >nul 2>nul
if not errorlevel 1 set "PHP_EXE=php"

if not defined PHP_EXE if exist "C:\laragon\bin\php\php.exe" set "PHP_EXE=C:\laragon\bin\php\php.exe"

if not defined PHP_EXE (
    for /f "delims=" %%D in ('dir /b /ad /o-n "C:\laragon\bin\php\php-*" 2^>nul') do (
        if not defined PHP_EXE if exist "C:\laragon\bin\php\%%D\php.exe" set "PHP_EXE=C:\laragon\bin\php\%%D\php.exe"
    )
)

if not defined PHP_EXE (
    echo [ERROR] PHP tidak ditemukan.
    echo.
    echo Install PHP 8.3+ atau pastikan Laragon sudah terpasang.
    echo Setelah itu jalankan ulang file ini.
    pause
    exit /b 1
)

echo Menggunakan PHP:
"%PHP_EXE%" -v

echo.
echo Membersihkan server PHP lama di port 8000...
powershell -NoProfile -ExecutionPolicy Bypass -Command "$conn = Get-NetTCPConnection -LocalPort 8000 -State Listen -ErrorAction SilentlyContinue; foreach ($c in $conn) { $p = Get-CimInstance Win32_Process -Filter ('ProcessId=' + $c.OwningProcess); if ($p -and $p.Name -eq 'php.exe') { Stop-Process -Id $p.ProcessId -Force -ErrorAction SilentlyContinue; $parent = Get-CimInstance Win32_Process -Filter ('ProcessId=' + $p.ParentProcessId); if ($parent -and $parent.CommandLine -match 'artisan serve') { Stop-Process -Id $parent.ProcessId -Force -ErrorAction SilentlyContinue } } }"

powershell -NoProfile -ExecutionPolicy Bypass -Command "if (Get-NetTCPConnection -LocalPort 8000 -State Listen -ErrorAction SilentlyContinue) { exit 1 } else { exit 0 }"
if errorlevel 1 (
    echo [ERROR] Port 8000 masih dipakai aplikasi lain.
    echo Tutup aplikasi tersebut atau jalankan CameraService di port lain.
    echo.
    pause
    exit /b 1
)

echo Menjalankan CameraService di http://127.0.0.1:8000 ...
start "CameraService Server" /min "%PHP_EXE%" artisan serve --host=127.0.0.1 --port=8000
powershell -NoProfile -Command "Start-Sleep -Seconds 3"
start "" "http://127.0.0.1:8000"

endlocal
