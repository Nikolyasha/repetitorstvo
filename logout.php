<?

session_start();
session_destroy();
setcookie("bb33f285255ebb9089d20aaa82b56eb4", "", time() - 100);

header("Location: ".$_SERVER['HTTP_REFERER']);

?>