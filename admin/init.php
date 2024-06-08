<?php
session_start();
if(!isset($_SESSION["id"])){
    header("Location: /login.php?redirect=/admin/");
    die();
}
if($_SESSION['admin'] != 1){
    header("Location: /lk/");
    die();
}
include("../core/config.php");
include("../core/db.php");

?>