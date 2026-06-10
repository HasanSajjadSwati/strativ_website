<#
  Starts the Strativ local dev environment (portable stack — no admin required):
    1. MariaDB server (data dir under /stack/data)
    2. WordPress PHP dev server at http://localhost:8080

  Usage:  powershell -ExecutionPolicy Bypass -File scripts\start-dev.ps1
  Stop:   close this window, or Ctrl+C, then run scripts\stop-dev.ps1
#>

$ErrorActionPreference = 'Stop'
$root    = Split-Path $PSScriptRoot -Parent
$stack   = Join-Path $root 'stack'
$wpPath  = Join-Path $root 'wp'
$mariadbd = Join-Path $stack 'mariadb\bin\mariadbd.exe'
$myIni    = Join-Path $stack 'data\my.ini'

# Start MariaDB if not already listening on 3306
$dbUp = Test-NetConnection -ComputerName 127.0.0.1 -Port 3306 -WarningAction SilentlyContinue -InformationLevel Quiet
if (-not $dbUp) {
    Write-Host 'Starting MariaDB...' -ForegroundColor Cyan
    Start-Process $mariadbd -ArgumentList "--defaults-file=`"$myIni`"" -WindowStyle Hidden
    Start-Sleep 3
} else {
    Write-Host 'MariaDB already running.' -ForegroundColor Green
}

Write-Host 'Starting WordPress at http://localhost:8080 ...' -ForegroundColor Cyan
& (Join-Path $stack 'wp.bat') server --host=localhost --port=8080 --path=$wpPath
