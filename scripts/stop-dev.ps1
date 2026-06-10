<#
  Stops the Strativ local dev environment: WordPress dev server + MariaDB.
  Usage:  powershell -ExecutionPolicy Bypass -File scripts\stop-dev.ps1
#>

$root  = Split-Path $PSScriptRoot -Parent
$stack = Join-Path $root 'stack'

Write-Host 'Stopping WordPress dev server (php.exe on :8080)...' -ForegroundColor Cyan
Get-CimInstance Win32_Process -Filter "Name = 'php.exe'" |
    Where-Object { $_.CommandLine -like '*router*' -or $_.CommandLine -like '*8080*' } |
    ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }

Write-Host 'Stopping MariaDB...' -ForegroundColor Cyan
& (Join-Path $stack 'mariadb\bin\mariadb-admin.exe') -u root shutdown 2>$null

Write-Host 'Done.' -ForegroundColor Green
