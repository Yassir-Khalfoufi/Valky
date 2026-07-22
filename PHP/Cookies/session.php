<?php
//creation 

session_start();
$_SESSION["login"] = "isgi0909@gmail.com";

//recupere

echo $_SESSION["login"];

//suppression de session

unset($_SESSION["login"]);
session_unset();

?>