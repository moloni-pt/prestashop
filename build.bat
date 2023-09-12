@echo off
setlocal

echo Ola
call composer install --prefer-dist --no-progress --no-suggest --no-dev

mkdir moloni

echo Moving files
copy "index.php" "moloni\index.php" >nul
copy "moloni.php" "moloni\moloni.php" >nul
copy "logo.gif" "moloni\logo.gif" >nul
copy "logo.png" "moloni\logo.png" >nul
copy "MoloniTab.gif" "moloni\MoloniTab.gif" >nul
copy "config.xml" "moloni\config.xml" >nul
copy "config_br.xml" "moloni\config_br.xml" >nul
copy "config_pt.xml" "moloni\config_pt.xml" >nul

echo Moving controllers...
xcopy "controllers" "moloni\controllers" /E /C /I /H /Y >nul

echo Moving mails...
xcopy "mails" "moloni\mails" /E /C /I /H /Y >nul

echo Moving sql...
xcopy "sql" "moloni\sql" /E /C /I /H /Y >nul

echo Moving src...
xcopy "src" "moloni\src" /E /C /I /H /Y >null

echo Moving translations...
xcopy "translations" "moloni\translations" /E /C /I /H /Y >nul

echo Moving upgrade...
xcopy "upgrade" "moloni\upgrade" /E /C /I /H /Y >nul

echo Moving vendor...
xcopy "vendor" "moloni\vendor" /E /C /I /H /Y >nul

echo Moving views...
xcopy "views" "moloni\views" /E /C /I /H /Y >nul

echo Ziping files...
tar -a -c -f "moloni.zip" "moloni"

echo Cleaning up trash...
del /f /s /q moloni 1>nul
rmdir moloni /s /q

echo Process is done
start ./moloni.zip
pause
endlocal
