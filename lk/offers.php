<?

$CURRENT_FILE = 'offers';

include("init.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/offer.php");

if ($_SESSION['admin'] == 1){    
    header('Location: ../');
}

// API обработчики
$data = json_decode(file_get_contents("php://input"));

if(isset($data->action)){
    if($data->token != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    $status = 0;
    switch($data->action){
        case "reject":
            $status = 2;
            if(isset($data->offer_id) && isset($data->vacancy_id)){
                $offer = new Offer($link, (int) $data->offer_id, $_SESSION['id']);
                list($result, $code) = $offer->SetStatus($link, $status, (int) $data->vacancy_id, "", $_SESSION['id']);
                if($result){
                    header($_SERVER['SERVER_PROTOCOL']." 200 OK");
                }
                else{
                    switch($code){
                        case 1:
                            header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
                            break;
                        case 2:
                            header($_SERVER['SERVER_PROTOCOL']." 403 Forbidden");
                            break;
                        default:
                            header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
                            break;
                    }
                }
            }
            else{
                header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
            }
            break;
        case 'remove':
            if(isset($data->offer_id) && isset($data->vacancy_id)){
                $offer = new Offer($link, (int) $data->offer_id, $_SESSION['id']);
                list($result, $code) = $offer->RemoveCompany($link, (int) $data->vacancy_id, $_SESSION['id']);
                if($result){
                    header($_SERVER['SERVER_PROTOCOL']." 200 OK");
                }
                else{
                    switch($code){
                        case 1:
                            header($_SERVER['SERVER_PROTOCOL']." 403 Forbidden");
                            break;
                        case 2:
                            header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
                            break;
                        default:
                            header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
                            break;
                    }
                }
            }
            else{
                header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
            }
            break;
        case 'remove_user':
            if(isset($data->offer_id) && isset($data->vacancy_id)){
                $offer = new Offer($link, (int) $data->offer_id, $_SESSION['id']);
                list($result, $code) = $offer->RemoveWorker($link, (int) $data->vacancy_id, $_SESSION['id']);
                if($result){
                    header($_SERVER['SERVER_PROTOCOL']." 200 OK");
                }
                else{
                    switch($code){
                        case 1:
                            header($_SERVER['SERVER_PROTOCOL']." 403 Forbidden");
                            break;
                        case 2:
                            header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
                            break;
                        default:
                            header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
                            break;
                    }
                }
            }
            else{
                header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
            }
            break;
        default:
            header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
            die();
    }
    die();
}

$ACCESS_LEVEL = 2;
if(isset($ACCESS_LEVEL) && $_SESSION['account_type'] != $ACCESS_LEVEL){
    header("Location: /lk/");
}

if(isset($_POST['action'])){
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    $redirect = "offers.php";
    $sing = "?";
    if(isset($_POST['redirect']) && strlen($_POST['redirect']) != 0){
        $redirect = $_POST['redirect'];
        if(strpos($_POST['redirect'], "?")){
            $sing = "&";
        }
    }
    switch($_POST['action']){
        case 'reply':
            $offer = new Offer($link, (int) $_POST['offer_id'], $_SESSION['id']);
            $text = htmlspecialchars(mysqli_real_escape_string($link, $_POST['offer_reply']));
            list($result, $code) = $offer->SetStatus($link, (int) $_POST['offer_status'], (int) $_POST['vacancy_id'], $text, $_SESSION['id']);
            if($result){
                header("Location: ".$redirect.$sing."success");
            }
            else{
                header("Location: ".$redirect.$sing."error=".$code);
            }
            die();
        default:
            header("Location: ".$redirect);
            die();
    }
}

$action = "";

//Обработчик страниц
if(isset($_GET['reply'])){
    $action = "reply";
    $reply = new Offer($link, (int) $_GET['reply'], $_SESSION['id']);
    if($reply->id == -1){
        header("Location: offers.php?error=404");
        die();
    }
    $reply = $reply->data;
}
else{
    $action = "view";
    $sql = Offer::GetListSQL($_SESSION['id']);
    list($responses) = MultiQuery($link, $sql);
    $OFFER_STATUS = Array("<b>Новый</b>", "<b style='color: darkgreen;'>Принят</b>", "<b style='color: darkred;'>Отклонен</b>");
}

if($action == "view" && isset($_GET['success'])){
    $notify = '
        <div class="alert background-success notify">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="icofont icofont-close-line-circled text-white"></i>
            </button>
            <strong>Успех!</strong> Ответ успешно отправлен</b>
        </div>';
}
else if($action == "view" && isset($_GET['error'])){
    $notify = '
        <div class="alert background-danger notify">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <i class="icofont icofont-close-line-circled text-white"></i>
            </button>
            
            <strong>Ошибка!</strong> Код ошибки: '.$_GET['error'].'
        </div>';
}

$sql = Offer::GetNewOffersCountSQL($_SESSION['id']);
list($new_offers_count) = MultiQuery($link, $sql);
$new_offers_count = $new_offers_count[0]['new_offers_count'];

include("../views/lk_view/header.php"); 

switch ($action) {
    case 'reply':
        include("../views/lk_view/offers/reply_offer_form.php");
        break;
    default:
        include("../views/lk_view/offers/show_offer_list.php");
        break;
}

include("../views/lk_view/footer.php"); 

?>