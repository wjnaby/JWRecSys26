# Run this script as Administrator to free port 3306 for XAMPP
# Right-click PowerShell -> "Run as Administrator" then run: powershell -ExecutionPolicy Bypass -File "path\to\this\script.ps1"

Write-Host "Stopping MySQL service to free port 3306..." -ForegroundColor Yellow
try {
    Stop-Service -Name "MySQL" -Force -ErrorAction Stop
    Write-Host "MySQL service stopped successfully!" -ForegroundColor Green
    Write-Host "You can now start MySQL in XAMPP Control Panel." -ForegroundColor Green
} catch {
    Write-Host "Error: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "Make sure you ran this script as Administrator:" -ForegroundColor Yellow
    Write-Host "1. Right-click PowerShell or Command Prompt" -ForegroundColor Cyan
    Write-Host "2. Select 'Run as administrator'" -ForegroundColor Cyan
    Write-Host "3. Navigate to the project folder and run this script again" -ForegroundColor Cyan
}
pause
