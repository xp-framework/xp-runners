@echo off

set TARGET=%TMP%\.xp

echo ===^> Downloading Windows runners to %TARGET%
xpi download bin/windows %TARGET%
if ERRORLEVEL 1 goto end

echo ===^> Extracting into %~p0
xcopy /y /q %TARGET%\* "%~p0"

rmdir /s /q %TARGET%
echo ===^> Done, runners have been updated

:end
