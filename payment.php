<?

if(empty($_POST['pg_sig'])) { header("Location: /"); die(); }
include($_SERVER['DOCUMENT_ROOT']."/core/db.php");

$verifry = md5(implode(";", ["payment.php", $_POST['pg_order_id'], $_POST['pg_payment_id'], $_POST['pg_salt'], $_SETTINGS['paybox_pay_key_option']]));
$order_id = (int) $_POST['pg_order_id'];
$payment_id = (int) $_POST['pg_payment_id'];

if($verifry == $_POST['pg_sig']){
    $sql = "SELECT `owner_id`, `amount`, `payment_id` FROM `payments` WHERE `id` = $order_id";
    $result = mysqli_fetch_row(mysqli_query($link, $sql));
    if((int) $result[2] > 0){
        header("Location: /lk/buy.php"); die();
    }
    $sql = "UPDATE `payments` SET `payment_id`=$payment_id WHERE `id` = $order_id; UPDATE `users` SET `balance` = `balance` + {$result[1]}  WHERE `users`.`id` = {$result[0]}; ";
    if(mysqli_multi_query($link, $sql)){
        for(; mysqli_next_result($link) == 0;) continue;
        SendLog($link, "Пополнение баланса #$order_id пользователем id{$result[0]} на {$result[1]} монет");
        header("Location: /lk/buy.php?s={$result[1]}"); 
    }
    else{
        die("<div style='text-align: center;'><h1>Ошибка платежа, свяжитесь с администрацией</h1><h3>PAYMENT ID: $payment_id</h3></div>");
    }
}
else{
    die("<div style='text-align: center;'><h1>Ошибка платежа, свяжитесь с администрацией</h1><h3>PAYMENT ID: $payment_id</h3></div>");
}
