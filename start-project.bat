@echo off

echo ================================
echo Lancement de Gotenberg...
echo ================================
start cmd /k docker run --rm -p 3000:3000 gotenberg/gotenberg:8

echo ================================
echo Lancement de Symfony...
echo ================================
symfony server:start

echo ================================
echo Ouverture du navigateur...
echo ================================
start http://127.0.0.1:8000

pause