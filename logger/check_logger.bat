@ECHO OFF

SET EXEName=python.exe
SET EXEFullPath=C:\logger\run_logger.cmd

TASKLIST | FINDSTR /I "%EXEName%"
IF ERRORLEVEL 1 GOTO :StartMeteoLogger
GOTO :EOF

:StartMeteoLogger
START "" "%EXEFullPath%"
GOTO :EOF
