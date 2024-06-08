<? 

$CURRENT_FILE = "buy_money";
$notify = "";

include("init.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/offer.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");
if ($_SESSION['admin'] == 1){    
    header('Location: ../');
} else if ($_SETTINGS['payment_active_option'] == 'false') {
    header('Location: /lk'); 
}

function CreatePaymet($money_count, $_SETTINGS, $link){

    $money_count = (int) $money_count;

    if(empty($_SESSION['id']) || $money_count < 1){
        header("Location: /lk/buy.php"); die();
    }

    function generate_salt($length)
    {
        $arr = array('a','b','c','d','e','f',
                    'g','h','i','j','k','l',
                    'm','n','o','p','r','s',
                    't','u','v','x','y','z',
                    'A','B','C','D','E','F',
                    'G','H','I','J','K','L',
                    'M','N','O','P','R','S',
                    'T','U','V','X','Y','Z');
        $salt = "";
        for($i = 0; $i < $length; $i++){
            // Вычисляем случайный индекс массива
            $index = rand(0, count($arr) - 1);
            $salt .= $arr[$index];
        }
        return $salt;
    }

    $order_id = $_SESSION['id'];
    $amount = $_SETTINGS['coin_price'] * $money_count;
    $sql = "INSERT INTO `payments`(`owner_id`, `amount`) VALUES ({$_SESSION['id']},$amount)";
    if(mysqli_query($link, $sql)){
        $request = Array(
            'pg_merchant_id' => $_SETTINGS['paybox_id_option'],
            'pg_amount' => $_SETTINGS['coin_price'] * $money_count,
            'pg_salt' => generate_salt(10),
            'pg_order_id' => mysqli_insert_id($link),
            'pg_description' => "Покупка внутренней валюты ($money_count едениц) на сайте «{$_SETTINGS['site_name_option']}»",
            'pg_result_url' => '/lk/buy.php?s'
        );
        
        $request['pg_testing_mode'] = 1;
        
        ksort($request);
        array_unshift($request, 'payment.php');
        array_push($request, $_SETTINGS['paybox_pay_key_option']); 
        
        $request['pg_sig'] = md5(implode(';', $request));
        
        unset($request[0], $request[1]);
    
        // die(print_r(($request)));
    
        $query = http_build_query($request);
    
        echo("<div style='text-align: center;'>Payment processing</div><script>window.location = 'https://api.paybox.money/payment.php?$query';</script>");
        return true;
    }
    else{
        return false;
    }
}

if(isset($_POST['action'])){
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    if($_POST['action'] == "payment"){
        if((int) $_POST['amount'] > 0){
            if(CreatePaymet((int) $_POST['amount'], $_SETTINGS, $link)) die();
        }
    }
}

if(isset($_GET['s']) && (int) $_GET['s'] > 0){
    $notify = '
    <div class="alert background-success notify">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="icofont icofont-close-line-circled text-white"></i>
        </button>   
        <strong>Спасибо за покупку!</strong> '.$_GET['s'].' монет были зачислены на ваш счет
    </div>';
}

if($_SESSION['account_type'] == 2){
    $sql = Offer::GetNewOffersCountSQL($_SESSION['id']);
    list($new_offers_count) = MultiQuery($link, $sql);
    $new_offers_count = $new_offers_count[0]['new_offers_count'];
}
else{
    $user = User::GetWorker($link, $_SESSION['id']);
    $filters = $user['filters'];
    $user = $user['user'];
}

include("../views/lk_view/header.php"); 

include("../views/lk_view/buy_view.php");

include("../views/lk_view/footer.php"); 

?>