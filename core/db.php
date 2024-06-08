<?php

include("config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/site.php");

$link = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_BASE);
if ($link == false){
    echo("Ошибка: Невозможно подключиться к MySQL - " . mysqli_connect_error());
    echo $DB_HOST, $DB_USER, $DB_PASS, $DB_BASE;
    die();
}
mysqli_set_charset($link, "utf8");

function MultiQuery($link, $sql){
    $response_list = Array();
    if (mysqli_multi_query($link, $sql)) {
        do {
            if ($result = mysqli_store_result($link)) {
                array_push($response_list, mysqli_fetch_all($result, MYSQLI_ASSOC));
            }
            if(!mysqli_more_results($link)){
                break;
            }
        } while (mysqli_next_result($link));
    }
    else{
        return False;
    }
    return $response_list;
}

function SendLog($link, $text){
    $text = htmlspecialchars($text);
    $sql = "INSERT INTO `logs` VALUES(null, '$text', CURRENT_TIMESTAMP);";
    return mysqli_query($link, $sql);
}

$_SETTINGS = Site::GetAllOptions($link);

// Авторизация по токену
if(!isset($_SESSION['id']) && isset($_COOKIE['bb33f285255ebb9089d20aaa82b56eb4']) && !empty($_COOKIE['bb33f285255ebb9089d20aaa82b56eb4'])){
    require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");
    $auth = User::Auth($link, null, null, null, $by_token = $_COOKIE['bb33f285255ebb9089d20aaa82b56eb4']);
    session_start();
    if($auth[0]){
        $data = $auth[1];
        unset($_SESSION['retoken']);
        $_SESSION['id'] = $data['id'];
        $_SESSION['admin'] = $data['admin'];
        $_SESSION['name'] = $data['name'];
        $_SESSION['mail'] = $data['mail'];
        $_SESSION['account_type'] = $data['type'];
        $_SESSION['token'] = bin2hex(random_bytes(32));
    }
}

?>