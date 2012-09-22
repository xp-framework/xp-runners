@echo off

if not "%TEMP%"=="" (
  set DOWNLOAD=%TEMP%\.xp
) else (
  if not "%TMP%"=="" (
    set DOWNLOAD=%TMP%\.xp
  ) else (
    set DOWNLOAD=%~p0\.xp
  )
)
set RUNNERS=%~p0

echo ===^> Downloading Windows runners to %DOWNLOAD%
xpi download bin/windows "%DOWNLOAD%"
if ERRORLEVEL 1 goto end

echo ===^> Extracting into %RUNNERS%
xcopy /y /q "%DOWNLOAD%\*" "%RUNNERS%"

rmdir /s /q "%DOWNLOAD%"
echo ===^> Done, runners have been updated

:end
