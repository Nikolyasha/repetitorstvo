<?php

session_start();
if(!isset($_SESSION["id"])){
    header("Location: /login.php?redirect=/lk/");
}
if(isset($ACCESS_LEVEL) && $_SESSION['account_type'] != $ACCESS_LEVEL){
    header("Location: /lk/");
}
include("../core/config.php");
include("../core/db.php");

?>