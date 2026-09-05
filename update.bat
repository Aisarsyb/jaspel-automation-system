@echo off
echo Mengambil pembaruan terbaru dari GitHub...
git reset --hard
git pull origin main
echo.
echo Pembaruan selesai!
pause
