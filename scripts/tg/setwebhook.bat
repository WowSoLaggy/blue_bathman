@echo off
setlocal enabledelayedexpansion

REM Load variables from .env file
for /f "usebackq tokens=1,* delims==" %%a in ("%~dp0..\..\.env") do (
    set "line=%%a"
    REM Skip comments and empty lines
    if not "!line:~0,1!"=="#" if not "%%a"=="" (
        set "%%a=%%b"
    )
)

REM Check if token was loaded
if "%BOT_TOKEN%"=="" (
    echo Error: BOT_TOKEN not found in .env file
    exit /b 1
)

echo Setting webhook for bot...
curl -F "url=https://bot.laggy.info/poruchik" https://api.telegram.org/bot%BOT_TOKEN%/setWebhook
