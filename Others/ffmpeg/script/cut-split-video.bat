@echo off
setlocal enabledelayedexpansion

:: Get video file path from first argument
set FILE=%~1

:: Check if file exists
if not exist "%FILE%" (
    echo File not found: %FILE%
    pause
    exit /b
)

:: Ask user for parameters
set /p START=Enter seconds to trim from start (default 3):
if "!START!"=="" set START=3

set /p END=Enter seconds to trim from end (default 10):
if "!END!"=="" set END=10

set /p SEG=Enter segment length in seconds (default 60):
if "!SEG!"=="" set SEG=60

:: Call PHP script (relative to current .bat location)
php "%~dp0cut-split-video.php" "%FILE%" !START! !END! !SEG!

pause
