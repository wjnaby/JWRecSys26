@echo off
echo Stopping MySQL service to free port 3306 for XAMPP...
echo.
net stop MySQL
if %errorlevel% equ 0 (
    echo.
    echo MySQL stopped successfully!
    echo You can now start MySQL in XAMPP Control Panel.
) else (
    echo.
    echo Failed - Make sure you Run this file as Administrator:
    echo 1. Right-click this file
    echo 2. Select "Run as administrator"
)
echo.
pause
