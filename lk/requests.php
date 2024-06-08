<?

$ACCESS_LEVEL = 1;
$CURRENT_FILE = 'requests';

include("init.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/offer.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/user.php");
require_once($_SERVER['DOCUMENT_ROOT']."/models/vacancy.php");

// API обработчики
$data = json_decode(file_get_contents("php://input"));

if(isset($_POST["action"])){
    $status = 0;
    if($_POST['token'] != $_SESSION['token']){
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden", true, 403);
        die(); 
    }
    switch($_POST["action"]){
        case "send_offer":
            if((int) $_POST['vacancy_id'] > 0){
                if($_SETTINGS['send_offer_price'] > 0)
                    $vacancy_purchased = Vacancy::GetVacancy($link, (int) $_POST['vacancy_id'], $_SESSION['id'])['vacancy'][0]['purchased'] > 0 ? true : false;
                if(empty(trim($_POST['offer_request']))){
                    $_POST['offer_request'] = "Этот пользователь прислал вам предложение";
                }
                $result = False;
                if($_SETTINGS['send_offer_price'] > 0 && !$vacancy_purchased){
                    if(User::Payment($link, $_SESSION['id'], $_SETTINGS['send_offer_price'])){
                        $result = Offer::SendOffer($link, (int) $_POST['vacancy_id'], $_POST['offer_request']);
                        if(!($result === True)){
                            User::MoneyBack($link, $_SESSION['id'], $_SETTINGS['worker_contact_price']);
                        }
                    }
                    else {
                        $result = "LOW_BALANCE";
                    }
                }
                else{
                    $result = Offer::SendOffer($link, (int) $_POST['vacancy_id'], $_POST['offer_request']);
                }
                if($result === True){
                    $notify = '
                    <div class="alert background-success notify">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="icofont icofont-close-line-circled text-white"></i>
                        </button>
                        <strong>Успех!</strong> Запрос успешно отправлен
                    </div>';
                }
                else if($result == "LOW_BALANCE"){
                    $notify = '
                    <div class="alert background-danger notify">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="icofont icofont-close-line-circled text-white"></i>
                        </button>
                        
                        <strong>Ошибка!</strong> Недостаточно средств на балансе
                    </div>';
                }
                else if((int) $result > 0 && !($result === False)){
                    $notify = '
                    <div class="alert background-danger notify">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="icofont icofont-close-line-circled text-white"></i>
                        </button>
                        
                        <strong>Ошибка!</strong> Вы уже отправили запрос на эту вакансию
                    </div>';
                }
                else{
                    $notify = '
                    <div class="alert background-danger notify">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="icofont icofont-close-line-circled text-white"></i>
                        </button>
                        
                        <strong>Ошибка!</strong> Отправить запрос не удалось
                    </div>';
                }
            }
            else{
                $notify = '
                <div class="alert background-danger notify">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="icofont icofont-close-line-circled text-white"></i>
                    </button>
                    
                    <strong>Ошибка!</strong> Отправить запрос не удалось
                </div>';
            }
            break;
        default:
            header($_SERVER['SERVER_PROTOCOL']." 400 Bad Request");
            die();
    }
}

$action = "view";
$sql = Offer::GetUserListSQL($_SESSION['id']);
list($responses) = MultiQuery($link, $sql);
$OFFER_STATUS = Array("<b>Рассматривается</b>", "<b style='color: darkgreen;'>Принят</b>", "<b style='color: darkred;'>Отклонен</b>");

$user = User::GetWorker($link, $_SESSION['id']);
$filters = $user['filters'];
$user = $user['user'];

include("../views/lk_view/header.php"); 

include("../views/lk_view/offers/show_requests_list.php");

include("../views/lk_view/footer.php"); 

?>