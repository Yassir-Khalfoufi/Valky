<?php
$login="dev103@gmail.com";

//creation de cookie
setcookie("DEV","$login", time()+3600);

//recuperer la valeur de cookie
echo $_COOKIE["DEV"];

//suppression de cookie
setcookie("DEV","$login", time()- 3600);
echo $_COOKIE[""];
?>