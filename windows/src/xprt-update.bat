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

echo ===^> Downloading Windows runners to %DOWNLOAD%
xpi download bin/windows "%DOWNLOAD%"
if ERRORLEVEL 1 goto end

echo ===^> Extracting into %~p0
xcopy /y /q "%DOWNLOAD%\*" "%~p0"

rmdir /s /q "%DOWNLOAD%"
echo ===^> Done, runners have been updated

:end
